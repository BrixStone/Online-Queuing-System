<?php

use Livewire\Component;
use App\Models\QueueTicket;
use App\Http\Controllers\QueueController;

new class extends Component {
    public $tellerName = 'Window 1'; 
    public ?QueueTicket $activeTicket = null;
    public $pinInput = '';
    public $isAuthenticated = false;
    public $isOpen = false;
    
    private $correctPin = '1234';

    public function mount()
    {
        if (session()->get('cashier_authenticated') === true) {
            $this->isAuthenticated = true;
            $this->loadActiveTicket();
            if ($this->activeTicket) {
                $this->isOpen = true;
            }
        }
    }

    public function authenticate()
    {
        if ($this->pinInput === $this->correctPin) {
            session()->put('cashier_authenticated', true);
            $this->isAuthenticated = true;
            $this->loadActiveTicket();
            $this->pinInput = '';
        } else {
            session()->flash('auth_error', 'Invalid PIN. Please try again.');
            $this->pinInput = '';
        }
    }

    public function logout()
    {
        session()->forget('cashier_authenticated');
        $this->isAuthenticated = false;
        $this->activeTicket = null;
        $this->isOpen = false;
    }

    public function toggleWindow()
    {
        $this->isOpen = !$this->isOpen;
        $controller = app(QueueController::class);
        
        if ($this->isOpen) {
            $controller->startQueue($this->tellerName);
        } else {
            $controller->stopQueue($this->tellerName);
            $this->activeTicket = null;
        }
    }

    public function loadActiveTicket()
    {
        $this->activeTicket = QueueTicket::serving()->where('assigned_teller', $this->tellerName)->first();
    }

    public function callNext()
    {
        if (!$this->isOpen) {
            session()->flash('error', 'Please open your window first!');
            return;
        }

        $next = app(QueueController::class)->callNext($this->tellerName);
        
        if (!$next) {
            session()->flash('error', 'The active line is empty!');
        }
        
        $this->loadActiveTicket();
    }

    public function completeCurrent()
    {
        app(QueueController::class)->completeCurrent($this->tellerName);
        $this->loadActiveTicket();
    }

    public function holdCurrent()
    {
        app(QueueController::class)->holdCurrent($this->tellerName);
        $this->loadActiveTicket();
    }

    public function skipCurrent()
    {
        app(QueueController::class)->skipCurrent($this->tellerName);
        $this->loadActiveTicket();
    }

    public function canMarkNoShow()
    {
        if (!$this->activeTicket || !$this->activeTicket->serving_started_at) {
            return false;
        }

        $expiresAt = $this->activeTicket->serving_started_at->copy()->addMinutes(QueueTicket::ARRIVAL_MINUTES);
        
        return now()->greaterThanOrEqualTo($expiresAt);
    }

    public function with()
    {
        return [
            'activeList' => QueueTicket::active()->get(),
            'holdingList' => QueueTicket::holding()->get()
        ];
    }
};
?>

<div wire:poll.2s>
    @if(!$isAuthenticated)
        <div class="p-8 font-sans">
            <div class="max-w-md mx-auto mt-20 bg-white p-8 rounded-xl border-2 border-gray-200 shadow-sm text-center">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Cashier Login</h2>
                <p class="text-gray-600 mb-6">Please enter your PIN to access the dashboard.</p>
                
                @if (session()->has('auth_error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-6 rounded border border-red-300 font-medium text-sm">
                        {{ session('auth_error') }}
                    </div>
                @endif

                <form wire:submit="authenticate">
                    <input 
                        type="password" 
                        wire:model="pinInput" 
                        class="w-full text-center text-3xl tracking-[1em] p-4 mb-6 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 transition"
                        placeholder="••••"
                        maxlength="4"
                        required
                        autofocus
                    >
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                        Enter Dashboard
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Inject required CSS/Fonts for the cashier layout -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@100;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/cashier.css') }}">

        <div class="cashier-page">
            <div class="background"></div>
            <div class="background-overlay"></div>

            <main class="main-body">

                <!-- HEADER -->
                <div class="window-title">
                    Cashier: {{ $tellerName }}
                </div>

                <button wire:click="toggleWindow" class="top-button close-window" style="{{ $isOpen ? 'background-color: #dc3545;' : 'background-color: #28a745;' }}">
                    {{ $isOpen ? 'Close Window' : 'Open Window' }}
                </button>

                <button wire:click="logout" class="top-button logout">
                    Log out
                </button>

                <!-- CURRENTLY SERVING -->
                <section class="glass-card cashier-window">
                    <h1 class="cashier-heading">Currently Serving</h1>
                    <div class="cashier-divider"></div>

                    @if (session()->has('error'))
                        <p style="color: #dc3545; text-align: center; margin-bottom: 1rem; font-weight: bold;">
                            {{ session('error') }}
                        </p>
                    @endif

                    @if($activeTicket)
                        <div class="active-student" style="text-align: center; margin-bottom: 2rem;">
                            <p style="font-size: 4rem; font-weight: bold; margin: 0; color: #1f2937;">{{ $activeTicket->tracking_number }}</p>
                            <p style="font-size: 1.5rem; margin: 0; color: #4b5563;">{{ $activeTicket->name }}</p>
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button wire:click="completeCurrent" class="cashier-action call-next" style="background-color: #28a745; border-color: #28a745;">
                                ✅ Complete
                            </button>
                            <button wire:click="holdCurrent" class="cashier-action skip-student" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                ⏸️ Hold
                            </button>
                            <button wire:click="skipCurrent" @if(!$this->canMarkNoShow()) disabled @endif class="cashier-action skip-student" style="background-color: #dc3545; border-color: #dc3545; {{ !$this->canMarkNoShow() ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                                🚫 No Show
                            </button>
                        </div>
                    @else
                        <p class="empty-message">No one is currently in your window.</p>
                        
                        @if($isOpen)
                            <button wire:click="callNext" class="cashier-action call-next" style="margin-top: 1rem;">
                                Call Next Student
                            </button>
                        @else
                            <p class="empty-message" style="color: #dc3545; font-weight: bold; margin-top: 1rem;">Window is closed. Open it to start.</p>
                        @endif
                    @endif
                </section>

                <!-- ACTIVE LINE -->
                <section class="glass-card active-line">
                    <h2 class="active-heading">Active Line <span style="font-size: 0.8em; background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 10px;">{{ $activeList->count() }}</span></h2>
                    <div class="active-divider"></div>

                    @forelse($activeList as $ticket)
                        <div class="active-student">
                            <div class="student-queue-text">
                                {{ $ticket->tracking_number }}
                                <span class="student-queue-name">{{ $ticket->name }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="empty-message">No one in active line</p>
                    @endforelse
                </section>

                <!-- HOLDING LINE -->
                <section class="glass-card holding-line">
                    <h2 class="holding-heading">Holding Line <span style="font-size: 0.8em; background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 10px;">{{ $holdingList->count() }}</span></h2>
                    <div class="holding-divider"></div>

                    @forelse($holdingList as $ticket)
                        <div class="active-student" style="opacity: 0.7;">
                            <div class="student-queue-text">
                                {{ $ticket->tracking_number }}
                                <span class="student-queue-name">{{ $ticket->name }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="empty-message">Holding line is empty</p>
                    @endforelse
                </section>

                <!-- STUDENT INFORMATION -->
                <section class="glass-card student-info">
                    <h2 class="student-info-heading">Student Information</h2>
                    <span class="star">★</span>
                    <div class="student-info-divider"></div>

                    @if($activeTicket && $activeTicket->student)
                        <div class="student-fields">
                            <div class="field usn-field">
                                <span class="field-label">USN Number:</span>
                                <span class="field-value">{{ $activeTicket->student->usn ?? 'N/A' }}</span>
                            </div>
                            <div class="field name-field">
                                <span class="field-label">Full Name:</span>
                                <span class="field-value">{{ $activeTicket->student->full_name ?? $activeTicket->name }}</span>
                            </div>
                            <div class="field academic-field">
                                <span class="field-label">Academic Level:</span>
                                <span class="field-value">{{ $activeTicket->student->academic_level ?? 'N/A' }}</span>
                            </div>
                            <div class="field year-field">
                                <span class="field-label">Year Level:</span>
                                <span class="field-value">{{ $activeTicket->student->year_level ?? 'N/A' }}</span>
                            </div>
                            <div class="field course-field">
                                <span class="field-label">Course:</span>
                                <span class="field-value">{{ $activeTicket->student->course ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @elseif($activeTicket)
                        <div class="student-fields">
                            <div class="field name-field">
                                <span class="field-label">Full Name:</span>
                                <span class="field-value">{{ $activeTicket->name }}</span>
                            </div>
                        </div>
                        <p class="empty-message" style="margin-top: 1rem;">No further student data linked.</p>
                    @else
                        <p class="empty-message">No student currently serving.</p>
                    @endif
                </section>

            </main>
        </div>
    @endif
</div>