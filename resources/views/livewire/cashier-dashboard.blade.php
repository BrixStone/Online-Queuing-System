<?php

use Livewire\Component;
use App\Models\QueueTicket;
use App\Http\Controllers\QueueController;

new class extends Component {
    public string $tellerName = 'Window 1';
    public ?QueueTicket $activeTicket = null;
    public string $pinInput = '';
    public bool $isAuthenticated = false;
    public bool $isOpen = false;

    // Prototype only
    private string $correctPin = '1234';

    public function mount(): void
    {
        if (session()->get('cashier_authenticated') === true) {
            $this->isAuthenticated = true;
            $this->refreshState();

            // If this window already has someone serving, treat it as open
            if ($this->activeTicket) {
                $this->isOpen = true;
            }
        }
    }

    /**
     * Runs on every Livewire request (including wire:poll).
     * Keeps currently-serving ticket in sync without clicking.
     */
    public function hydrate(): void
    {
        if ($this->isAuthenticated) {
            $this->loadActiveTicket();
        }
    }

    public function authenticate(): void
    {
        if ($this->pinInput === $this->correctPin) {
            session()->put('cashier_authenticated', true);
            $this->isAuthenticated = true;
            $this->pinInput = '';
            $this->refreshState();

            if ($this->activeTicket) {
                $this->isOpen = true;
            }
        } else {
            session()->flash('auth_error', 'Invalid PIN. Please try again.');
            $this->pinInput = '';
        }
    }

    public function logout(): void
    {
        session()->forget('cashier_authenticated');

        $this->isAuthenticated = false;
        $this->isOpen = false;
        $this->activeTicket = null;
        $this->pinInput = '';
    }

    public function toggleWindow(): void
    {
        if (!$this->isAuthenticated) {
            return;
        }

        // Prototype rule: don't close while still serving someone
        if ($this->isOpen && $this->activeTicket) {
            session()->flash('error', 'Finish, hold, or mark no-show before closing the window.');
            return;
        }

        $this->isOpen = !$this->isOpen;
        $controller = app(QueueController::class);

        if ($this->isOpen) {
            $controller->startQueue($this->tellerName);
        } else {
            $controller->stopQueue($this->tellerName);
            $this->activeTicket = null;
        }
    }

    public function callNext(): void
    {
        if (!$this->guardWindow(requireOpen: true)) {
            return;
        }

        // Already serving someone — cashier should complete/hold/skip first
        if ($this->activeTicket) {
            session()->flash('error', 'Finish the current student before calling the next.');
            return;
        }

        $next = app(QueueController::class)->callNext($this->tellerName);

        if (!$next) {
            session()->flash('error', 'The active line is empty!');
        }

        $this->refreshState();
    }

    public function completeCurrent(): void
    {
        if (!$this->guardWindow(requireOpen: true, requireActive: true)) {
            return;
        }

        app(QueueController::class)->completeCurrent($this->tellerName);
        $this->refreshState();
    }

    public function skipCurrent(): void
    {
        if (!$this->guardWindow(requireOpen: true, requireActive: true)) {
            return;
        }

        if (!$this->canMarkNoShow()) {
            session()->flash('error', 'You can only mark No Show after the arrival time has passed.');
            return;
        }

        app(QueueController::class)->skipCurrent($this->tellerName);
        $this->refreshState();
    }

    public function noShowSecondsRemaining(): ?int
    {
        if (!$this->activeTicket || !$this->activeTicket->serving_started_at) {
            return null;
        }

        $expiresAt = $this->activeTicket->serving_started_at
            ->copy()
            ->addMinutes(QueueTicket::ARRIVAL_MINUTES);

        $remaining = now()->diffInSeconds($expiresAt, false);

        // diffInSeconds(..., false) can be negative when expired
        return $remaining > 0 ? (int) $remaining : 0;
    }

    public function noShowCountdownLabel(): ?string
    {
        $seconds = $this->noShowSecondsRemaining();

        if ($seconds === null) {
            return null;
        }

        if ($seconds <= 0) {
            return 'No Show available now';
        }

        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return sprintf('No Show in %d:%02d', $minutes, $secs);
    }

    public function canMarkNoShow(): bool
    {
        $remaining = $this->noShowSecondsRemaining();

        return $remaining !== null && $remaining <= 0;
    }

    public function with(): array
    {
        return [
            'activeList'  => QueueTicket::active()->get(),
            'holdingList' => QueueTicket::holding()->get(),
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function loadActiveTicket(): void
    {
        $this->activeTicket = QueueTicket::serving()
            ->where('assigned_teller', $this->tellerName)
            ->first();
    }

    private function refreshState(): void
    {
        $this->loadActiveTicket();
    }

    /**
     * Shared guards for cashier actions.
     * Returns false when the action should abort.
     */
    private function guardWindow(bool $requireOpen = false, bool $requireActive = false): bool
    {
        if (!$this->isAuthenticated) {
            session()->flash('error', 'Please log in first.');
            return false;
        }

        if ($requireOpen && !$this->isOpen) {
            session()->flash('error', 'Please open your window first!');
            return false;
        }

        if ($requireActive && !$this->activeTicket) {
            session()->flash('error', 'No student is currently being served.');
            return false;
        }

        return true;
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
                <div class="auth-error-message">
                    {{ session('auth_error') }}
                </div>
                @endif

                <!-- Form -->
                <form wire:submit="authenticate" class="form-actions">

                    <!-- PIN Input -->
                    <div class="pin-box pin-box-container">
                        <input
                            type="password"
                            wire:model="pinInput"
                            class="pin-text-input"
                            placeholder="••••"
                            maxlength="4"
                            required
                            autofocus>
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

    <div class="cashier-page">
        <div class="background"></div>
        <div class="background-overlay"></div>

        <main class="main-body">

            <!-- HEADER -->
            <div class="window-title">
                Cashier: {{ $tellerName }}
            </div>

            <button wire:click="toggleWindow" class="top-button close-window {{ $isOpen ? 'window-open' : 'window-closed' }}">
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
                <p class="error-notification">
                    {{ session('error') }}
                </p>
                @endif

                @if($activeTicket)
                <div class="active-student-details">
                    <p class="active-student-tracking">{{ $activeTicket->tracking_number }}</p>
                    <p class="active-student-fullname">{{ $activeTicket->name }}</p>

                    @if($this->noShowCountdownLabel())
                    <p class="noshow-timer {{ $this->canMarkNoShow() ? 'noshow-ready' : 'noshow-waiting' }}">
                        {{ $this->noShowCountdownLabel() }}
                    </p>
                    @endif
                </div>

                <div class="action-buttons-container">
                    <button wire:click="completeCurrent" class="cashier-action btn-complete">
                        ✅ Complete
                    </button>

                    <button
                        wire:click="skipCurrent"
                        @disabled(!$this->canMarkNoShow())
                        class="cashier-action btn-noshow"
                        >
                        🚫 No Show
                    </button>
                </div>
                @else
                <!-- UPDATED EMPTY CONTAINER -->
                <div class="serving-empty-container">
                    <p class="list-empty-message">No one is currently in your window.</p>

                    @if($isOpen)
                    <button wire:click="callNext" class="cashier-action btn-call-next">
                        Call Next Student
                    </button>
                    @else
                    <p class="window-closed-warning">Window is closed. Open it to start.</p>
                    @endif
                </div>
                @endif
            </section>

            <!-- ACTIVE LINE -->
            <section class="glass-card active-line">
                <h2 class="active-heading">
                    Active Line
                    <span class="badge-counter">{{ $activeList->count() }}</span>
                </h2>
                <div class="active-divider"></div>

                <div class="glass-scrollbar list-container">
                    @forelse($activeList as $ticket)
                    <div class="active-student list-item">
                        <div class="student-queue-text">
                            {{ $ticket->tracking_number }}
                            <span class="student-queue-name">{{ $ticket->name }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="list-empty-message">No one in active line</p>
                    @endforelse
                </div>
            </section>

            <!-- HOLDING LINE -->
            <section class="glass-card holding-line">
                <h2 class="holding-heading">
                    Holding Line
                    <span class="badge-counter">{{ $holdingList->count() }}</span>
                </h2>
                <div class="holding-divider"></div>

                <div class="glass-scrollbar list-container">
                    @forelse($holdingList as $ticket)
                    <div class="active-student list-item list-item-holding">
                        <div class="student-queue-text">
                            {{ $ticket->tracking_number }}
                            <span class="student-queue-name">{{ $ticket->name }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="list-empty-message">Holding line is empty</p>
                    @endforelse
                </div>
            </section>

            <!-- STUDENT INFORMATION -->
            <section class="glass-card student-info">
                <h2 class="student-info-heading">Student Information</h2>

                <div class="student-info-divider"></div>

                @if($activeTicket && $activeTicket->student)
                <div class="student-fields">
                    <div class="field usn-field">
                        <span class="field-label">USN Number:</span>
                        <span class="field-value">{{ $activeTicket->student->student_number ?? 'N/A' }}</span>
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
                        <span class="field-value">{{ $activeTicket->student->course_or_strand ?? 'N/A' }}</span>
                    </div>
                </div>
                @elseif($activeTicket)
                <div class="student-fields">
                    <div class="field name-field">
                        <span class="field-label">Full Name:</span>
                        <span class="field-value">{{ $activeTicket->name }}</span>
                    </div>
                </div>
                <p class="empty-message margin-top-1">No further student data linked.</p>
                @else
                <p class="empty-message">No student currently serving.</p>
                @endif
            </section>

        </main>
    </div>
    @endif
</div>