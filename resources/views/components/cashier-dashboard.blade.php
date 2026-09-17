<?php

use Livewire\Component;
use App\Models\QueueTicket;

new class extends Component {
    public $tellerName = 'Window 1'; 
    public ?QueueTicket $activeTicket = null;

    public function mount()
    {
        $this->loadActiveTicket();
    }

    public function loadActiveTicket()
    {
        $this->activeTicket = QueueTicket::where('status', 'active')
            ->where('assigned_teller', $this->tellerName)
            ->first();
    }

    public function callNext()
    {
        $nextTicket = QueueTicket::where('status', 'holding')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextTicket) {
            $nextTicket->update([
                'status' => 'active',
                'assigned_teller' => $this->tellerName
            ]);
            $this->loadActiveTicket();
        } else {
            session()->flash('error', 'The waiting line is empty!');
        }
    }

    public function completeCurrent()
    {
        if ($this->activeTicket) {
            $this->activeTicket->update(['status' => 'completed']);
            $this->activeTicket = null; 
            $this->callNext(); 
        }
    }

    public function holdCurrent()
    {
        if ($this->activeTicket) {
            $this->activeTicket->update(['status' => 'held']);
            $this->activeTicket = null;
            $this->callNext(); 
        }
    }

    public function with()
    {
        return [
            'waitingList' => QueueTicket::where('status', 'holding')
                ->orderBy('created_at', 'asc')
                ->get()
        ];
    }
};
?>

<div class="p-8 font-sans">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Cashier: {{ $tellerName }}</h1>

    @if (session()->has('error'))
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded border border-red-300 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 bg-gray-50 p-8 rounded-xl border-2 border-gray-200 shadow-sm">
            <h2 class="text-xl mb-6 font-semibold text-gray-600 uppercase tracking-wide">Currently Serving</h2>
            
            @if($activeTicket)
                <div class="mb-8 text-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-7xl font-black text-gray-900 mb-2">{{ $activeTicket->tracking_number }}</p>
                    <p class="text-2xl text-gray-600">{{ $activeTicket->name }}</p>
                </div>
                
                <div class="flex gap-4">
                    <button wire:click="completeCurrent" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                        ✅ Complete Transaction
                    </button>
                    <button wire:click="holdCurrent" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-4 rounded-lg font-bold text-lg transition shadow-md">
                        ⏸️ No Show (Hold)
                    </button>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 mb-8 text-xl">No one is currently at your window.</p>
                    <button wire:click="callNext" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-xl transition shadow-md">
                        Call Next Student
                    </button>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold mb-4 flex justify-between items-center border-b pb-2">
                Waiting Line 
                <span class="bg-blue-100 text-blue-800 text-sm py-1 px-3 rounded-full">{{ $waitingList->count() }}</span>
            </h2>
            <ul class="space-y-3">
                @forelse($waitingList as $ticket)
                    <li class="py-3 px-4 bg-gray-50 rounded border border-gray-100 flex justify-between items-center">
                        <strong class="text-lg text-gray-800">{{ $ticket->tracking_number }}</strong> 
                        <span class="text-gray-600">{{ $ticket->name }}</span>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-400 italic">Line is empty</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>