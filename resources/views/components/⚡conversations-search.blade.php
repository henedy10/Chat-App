<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Chat\Conversation;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $search = '';
    public $conversation = '';

    #[Computed]
    public function conversations()
    {
        return Conversation::where(function ($query) {
            $query->where('user1_id', Auth::id())
                ->whereHas('user2', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            })
            ->orWhere(function ($query) {
                $query->where('user2_id', Auth::id())
                    ->whereHas('user1', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->with(['user1','user2','lastMessage'])
            ->get();
    }

    public function uploadConversation($conversationId){
        $this->conversation = Conversation::where('id',$conversationId)->with('user1','user2','messages')->first();
    }

};
?>

<div>
    <!-- Search -->
    <div class="p-4">
        <div class="relative">
            <input type="text" wire:model.live="search" placeholder="Search conversations..."
            class="w-full bg-slate-800/50 border border-white/5 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        </div>
    </div>
    <!-- Chat List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
        @forelse ($this->conversations as $conversation)
            <div class="flex items-center p-3 space-x-4 cursor-pointer rounded-xl transition-all" wire:click="uploadConversation({{$conversation->id}})">
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-slate-700 overflow-hidden">
                        @php
                            $user = $conversation->user1_id == Auth::id()
                                ? $conversation->user2
                                : $conversation->user1;
                        @endphp

                        @if($user?->avatar)
                            <img
                                src="{{ $user->avatar }}"
                                alt="{{ $user->name }}"
                                class="w-full h-full object-cover rounded-full"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-500 text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline">
                        <h3 class="font-semibold text-sm truncate">{{$conversation->user1_id == Auth::id() ? $conversation->user2->name : $conversation->user1->name}}</h3>
                        <span class="text-xs text-slate-500">12:45 PM</span>
                    </div>
                    <p class="text-xs text-slate-400 truncate mt-0.5">{{ $conversation->lastMessage->message }}</p>
                </div>
                <div class="flex  items-end space-y-1">
                    <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-bold">2</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-4 py-20 text-center">
                <p class="text-slate-500 text-sm">There is no any conversations match!</p>
            </div>
        @endforelse
    </div>

</div>
