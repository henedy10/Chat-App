<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Chat\Conversation;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\Message;

new class extends Component
{
    public $search = '';
    public $conversation;
    public $message = '';
    public $selectedConversationId = null;
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
            ->withCount('unReadMessages')
            ->get();
    }

    public function uploadConversation($conversationId){
        $this->conversation = Conversation::where('id',$conversationId)
                                        ->with(['user1','user2','messages'])
                                        ->first();
        $this->conversation->messages()->where('sender_id','!=',Auth::id())->update(['is_read'=>true]);
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversation-updated');
    }

    public function sendMessage(){
        $this->validate([
            'message' => 'required',
        ]);

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->reset('message');
    }
};
?>
<div class="flex h-screen overflow-hidden" >
<!-- Sidebar -->
<aside id="sidebar" class="chat-sidebar flex flex-col glass z-20">
    <!-- Sidebar Header -->
    <div class="p-6 flex items-center justify-between border-b border-white/5">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i data-lucide="zap" class="w-6 h-6 text-white"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Chat App</h1>
        </div>
        <button class="p-2 hover:bg-white/5 rounded-lg transition-colors" >
            <i data-lucide="plus-circle" class="w-5 h-5 text-slate-400"></i>
        </button>
    </div>
    <!-- Search -->
    <div class="p-4">
        <div class="relative">
            <input type="text" wire:model.live="search" placeholder="Search or Start conversations..."
            class="w-full bg-slate-800/50 border border-white/5 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        </div>
    </div>
    <!-- Chat List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
        @forelse ($this->conversations as $conversation)
            <div class="flex items-center p-3 space-x-4 cursor-pointer {{ $conversation->id == $selectedConversationId ? 'bg-indigo-500/10' : 'hover:bg-white/5' }} rounded-xl transition-all" wire:click="uploadConversation({{$conversation->id}})">
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
                        <span class="text-xs text-slate-500">{{ $conversation->lastMessage?->created_at->format('h:i A') ?? "N/A"}}</span>
                    </div>
                    <p class="text-xs text-slate-400 truncate mt-0.5">{{ $conversation->lastMessage->message ?? 'No messages' }}</p>
                </div>
                @if($conversation->un_read_messages_count > 0)
                    <div class="flex  items-end space-y-1">
                        <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                            <span class="text-[10px] font-bold">{{ $conversation->un_read_messages_count }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-4 py-20 text-center">
                <p class="text-slate-500 text-sm">There is no any conversations !</p>
            </div>
        @endforelse
    </div>
    <!-- Profile Footer -->
    <div class="absolute bottom-0 left-0 right-0 w-full p-4 border-t border-white/5 bg-slate-900/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-slate-700 overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img
                            src="{{ Auth::user()->avatar }}"
                            alt="{{ Auth::user()->name }}"
                            class="w-full h-full object-cover rounded-full"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-500 text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="text-left">
                    <p class="text-sm font-semibold">{{Auth::user()->name}}</p>
                    <p class="text-[10px] text-green-500">Online</p>
                </div>
            </div>
            <button class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                <i data-lucide="settings" class="w-5 h-5 text-slate-400"></i>
            </button>
        </div>
    </div>
</aside>
<!-- Main Chat Area -->
<main class="flex-1 flex flex-col h-screen relative bg-slate-950/50">
    @if($this->conversation)
    <!-- Chat Header -->
    <header class="h-20 flex items-center justify-between px-8 border-b border-white/5 glass z-10">
        <div class="flex items-center space-x-4">
            <button id="sidebar-toggle" class="md:hidden p-2 -ml-2 hover:bg-white/5 rounded-lg">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
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
            <div>
            <h2 class="font-bold text-lg leading-tight">{{ $this->conversation->user1_id == Auth::id() ? $this->conversation->user2->name : $this->conversation->user1->name }}</h2>
                <p class="text-xs text-green-500">Active now</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button class="p-2.5 hover:bg-white/5 rounded-xl transition-all group">
                <i data-lucide="phone" class="w-5 h-5 text-slate-400 group-hover:text-indigo-400"></i>
            </button>
            <button class="p-2.5 hover:bg-white/5 rounded-xl transition-all group">
                <i data-lucide="video" class="w-5 h-5 text-slate-400 group-hover:text-indigo-400"></i>
            </button>
            <button class="p-2.5 hover:bg-white/5 rounded-xl transition-all group border border-white/5">
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-400"></i>
            </button>
        </div>
    </header>

    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar" id="messages-container">
        <!-- Date Separator -->
        <div class="flex justify-center">
        <span class="px-4 py-1.5 bg-slate-900/50 border border-white/5 rounded-full text-[10px] uppercase tracking-wider font-semibold text-slate-500">
                Today, Oct 12
            </span>
        </div>
        @if($this->conversation->messages)
            @foreach($this->conversation->messages as $message)
                @if($message->sender_id != auth()->id())
                    <!-- Received Message -->
                    <div class="flex items-end space-x-3 max-w-[80%]">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 overflow-hidden flex-shrink-0">
                            @if($message->sender->avatar)
                                <img
                                    src="{{ $message->sender->avatar }}"
                                    alt="{{ $message->sender->name }}"
                                    class="w-full h-full object-cover rounded-full"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-500 text-white">
                                    {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                        <div class="message-bubble-received p-4 rounded-2xl text-sm leading-relaxed text-slate-200 shadow-sm">
                            {{ $message->message }}
                            </div>
                            <p class="text-[10px] text-slate-500 ml-1">{{ $message->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @else
                    <!-- Sent Message -->
                    <div class="flex items-end space-x-3 max-w-[80%] ml-auto flex-row-reverse">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 overflow-hidden flex-shrink-0 ml-3">
                            @if(auth()->user()->avatar)
                                <img
                                    src="{{ auth()->user()->avatar }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-full h-full object-cover rounded-full"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-500 text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1 text-right">
                        <div class="message-bubble-sent p-4 rounded-2xl text-sm leading-relaxed text-white shadow-lg shadow-indigo-500/20">
                            {{ $message->message }}
                            </div>
                            <div class="flex items-center justify-end space-x-1 mt-1 mr-1">
                                <p class="text-[10px] text-slate-500">{{ $message->created_at->format('h:i A') }}</p>
                                <i data-lucide="check-check" class="w-3 h-3 text-indigo-400"></i>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            </div>
        @else
            <div class="flex items-center justify-center h-full">
                <p class="text-slate-500">Start chatting with your friends!</p>
            </div>
        @endif
    <!-- Input Area -->
     <form wire:submit.prevent="sendMessage">
        <div class="p-6 bg-slate-950/30">
            <div class="max-w-4xl mx-auto relative group">
            <div class="absolute inset-0 bg-indigo-500/5 blur-2xl rounded-full opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                <div class="relative glass rounded-2xl p-2 flex items-end space-x-2 border-white/10 shadow-2xl">
                <button type="button" onclick="document.getElementById('attachment').click()" class="p-3 hover:bg-white/5 rounded-xl transition-colors">
                        <i data-lucide="paperclip" class="w-5 h-5 text-slate-400"></i>
                        <input type="file" name="attachment" id="attachment" class="hidden">
                </button>
                <textarea wire:model.live="message" rows="1" placeholder="Type a message..."
                        class="flex-1 bg-transparent border-none focus:ring-0 py-3 text-sm text-slate-200 placeholder-slate-500 resize-none max-h-32 custom-scrollbar"></textarea>
                        <div class="flex items-center p-1 space-x-1">
                    <button class="p-2.5 hover:bg-white/5 rounded-xl transition-colors">
                        <i data-lucide="smile" class="w-5 h-5 text-slate-400"></i>
                    </button>
                    <button type="submit" class="p-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg shadow-indigo-600/30 group">
                        <i data-lucide="send" class="w-5 h-5 text-white transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                    </button>
                </div>  
            </div>
        </div>
     </form>
    @else
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-indigo-500/10 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="messages-square" class="w-8 h-8 text-indigo-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-200">No Conversation Selected</h3>
                <p class="text-slate-400">Select a conversation from the left sidebar to start chatting.</p>
            </div>
        </div>
    @endif
</main>
</div>

@script
<script>
    function initChatUI() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        const textarea = document.querySelector('textarea');
        if (textarea && !textarea.dataset.initialized) {
            textarea.addEventListener('input', () => {
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });
            textarea.dataset.initialized = 'true';
        }
    }

    // Initialize on start
    initChatUI();

    // Listen for the custom event
    $wire.on('conversation-updated', () => {
        setTimeout(initChatUI, 50);
    });

    // Re-run on any Livewire update for this component
    Livewire.hook('morph.updated', ({ el, component }) => {
        if (component.id === $wire.__instance.id) {
            initChatUI();
        }
    });

    // Mobile Sidebar Toggle
    document.addEventListener('click', (e) => {
        if (e.target.closest('#sidebar-toggle')) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
        }
    });
</script>
@endscript
