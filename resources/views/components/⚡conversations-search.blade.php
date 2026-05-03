<?php

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Computed;

new class extends Component
{
    public $search = '';

    #[Computed]
    public function users()
    {
        return User::where('name','like',"%{$this->search}%")->where('id','!=',Auth::id())->get();
    }
};
?>

<div>
    <!-- Search -->
    <div class="p-4">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
            <input type="text" wire:model.live="search" placeholder="Search conversations..."
            class="w-full bg-slate-800/50 border border-white/5 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        </div>
    </div>
    <!-- Chat List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
        @forelse ($this->users as $user)
            <div class="flex items-center p-3 space-x-4 cursor-pointer rounded-xl transition-all">
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-slate-700 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" alt="Sarah" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline">
                        <h3 class="font-semibold text-sm truncate">{{$user->name}}</h3>
                        <span class="text-xs text-slate-500">12:45 PM</span>
                    </div>
                    <p class="text-xs text-slate-400 truncate mt-0.5">The design looks amazing! Can we go ahead with it?</p>
                </div>
                <div class="flex flex-col items-end space-y-1">
                    <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-bold">2</span>
                    </div>
                </div>
            </div>
        @empty
            <p>There is no any conversations match!</p>
        @endforelse
    </div>
</div>
