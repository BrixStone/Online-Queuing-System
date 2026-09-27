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
        <!-- Inject required CSS/Fonts for the cashier-login layout -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Outfit:wght@100;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/cashier-login.css') }}">

        <div class="cashier-login">
            <!-- Background -->
            <div class="background"></div>
            <div class="background-overlay"></div>

            <!-- Main Content -->
            <main class="main-body">
                <!-- Glass Login Card -->
                <section class="glass-card">
                    
                    <!-- Header -->
                    <header class="card-header">
                        <h1 class="card-title">QUEUE STATUS</h1>
                        <p class="card-subtitle">Please enter PIN to access the dashboard</p>
                    </header>

                    @if (session()->has('auth_error'))
                        <div style="color: #dc3545; text-align: center; margin-bottom: 1rem; font-weight: bold; background: rgba(255, 255, 255, 0.8); padding: 5px; border-radius: 5px;">
                            {{ session('auth_error') }}
                        </div>
                    @endif

                    <!-- Form -->
                    <form wire:submit="authenticate" class="form-actions">
                        
                        <!-- PIN Input (Replacing the static spans with a working input) -->
                        <div class="pin-box" style="padding: 0; background: transparent; border: none; box-shadow: none;">
                            <input 
                                type="password" 
                                wire:model="pinInput" 
                                style="width: 100%; text-align: center; font-size: 2rem; letter-spacing: 0.5em; padding: 15px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.3); background: rgba(255, 255, 255, 0.1); color: white; outline: none;"
                                placeholder="••••"
                                maxlength="4"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Enter Button -->
                        <button type="submit" class="enter-button">
                            Enter Dashboard
                        </button>

                    </form>
                </section>
            </main>

            <!-- Watermark -->
            <div class="watermark">
                <img class="icon-line" src="{{ asset('images/ACLC_logo.svg') }}" alt="Line">
            </div>
        </div>
    @else
        <!-- Inject required CSS/Fonts for the cashier layout -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@100;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/cashier.css') }}">
        
        <style>
            .glass-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            .glass-scrollbar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05); 
                border-radius: 10px;
            }
            .glass-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3); 
                border-radius: 10px;
            }
            .glass-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.5); 
            }
        </style>

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

                    <div class="glass-scrollbar" style="display: flex; flex-direction: column; gap: 15px; margin-top: 70px; height: calc(100% - 90px); overflow-y: auto; align-items: center; padding-bottom: 20px;">
                        @forelse($activeList as $ticket)
                            <div class="active-student" style="position: relative; left: auto; top: auto; flex-shrink: 0;">
                                <div class="student-queue-text">
                                    {{ $ticket->tracking_number }}
                                    <span class="student-queue-name">{{ $ticket->name }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="empty-message" style="margin-top: 2rem;">No one in active line</p>
                        @endforelse
                    </div>
                </section>

                <!-- HOLDING LINE -->
                <section class="glass-card holding-line">
                    <h2 class="holding-heading">Holding Line <span style="font-size: 0.8em; background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 10px;">{{ $holdingList->count() }}</span></h2>
                    <div class="holding-divider"></div>

                    <div class="glass-scrollbar" style="display: flex; flex-direction: column; gap: 15px; margin-top: 70px; height: calc(100% - 90px); overflow-y: auto; align-items: center; padding-bottom: 20px;">
                        @forelse($holdingList as $ticket)
                            <div class="active-student" style="opacity: 0.7; position: relative; left: auto; top: auto; flex-shrink: 0;">
                                <div class="student-queue-text">
                                    {{ $ticket->tracking_number }}
                                    <span class="student-queue-name">{{ $ticket->name }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="empty-message" style="margin-top: 2rem;">Holding line is empty</p>
                        @endforelse
                    </div>
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