<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat App | Premium Messaging</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            overflow: hidden;
        }

        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-sidebar {
            width: 380px;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
                display: none;
            }
            .chat-sidebar.active {
                display: flex;
            }
            .main-content.sidebar-active {
                display: none;
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .message-bubble-received {
            background: #1e293b;
            border-bottom-left-radius: 2px;
        }

        .message-bubble-sent {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border-bottom-right-radius: 2px;
        }

        .active-chat {
            background: rgba(99, 102, 241, 0.1);
            border-left: 3px solid #6366f1;
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside id="sidebar" class="chat-sidebar flex flex-col glass z-20">
            <!-- Sidebar Header -->
            <div class="p-6 flex items-center justify-between border-b border-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight">Chat App</h1>
                </div>
                <button class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-slate-400"></i>
                </button>
            </div>

        @livewire('conversations-search')

            <!-- Profile Footer -->
            <div class="p-4 border-t border-white/5 bg-slate-900/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-700 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&q=80&w=100&h=100" alt="Me" class="w-full h-full object-cover">
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
            <!-- Chat Header -->
            <header class="h-20 flex items-center justify-between px-8 border-b border-white/5 glass z-10">
                <div class="flex items-center space-x-4">
                    <button id="sidebar-toggle" class="md:hidden p-2 -ml-2 hover:bg-white/5 rounded-lg">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl bg-slate-700 overflow-hidden border border-white/10">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" alt="Sarah" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg leading-tight">Sarah Johnson</h2>
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

                <!-- Received Message -->
                <div class="flex items-end space-x-3 max-w-[80%]">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 overflow-hidden flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" alt="Sarah" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1">
                        <div class="message-bubble-received p-4 rounded-2xl text-sm leading-relaxed text-slate-200 shadow-sm">
                            Hey Alex! I've just finished the initial concepts for the new mobile app dashboard. Would you have a moment to check them out?
                        </div>
                        <p class="text-[10px] text-slate-500 ml-1">12:30 PM</p>
                    </div>
                </div>

                <!-- Sent Message -->
                <div class="flex items-end space-x-3 max-w-[80%] ml-auto flex-row-reverse">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 overflow-hidden flex-shrink-0 ml-3">
                        <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&q=80&w=100&h=100" alt="Me" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1 text-right">
                        <div class="message-bubble-sent p-4 rounded-2xl text-sm leading-relaxed text-white shadow-lg shadow-indigo-500/20">
                            That's awesome! Yes, definitely. Send them over or we can hop on a quick call if you prefer?
                        </div>
                        <div class="flex items-center justify-end space-x-1 mt-1 mr-1">
                            <p class="text-[10px] text-slate-500">12:32 PM</p>
                            <i data-lucide="check-check" class="w-3 h-3 text-indigo-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Received Message -->
                <div class="flex items-end space-x-3 max-w-[80%]">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 overflow-hidden flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" alt="Sarah" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1">
                        <div class="message-bubble-received p-4 rounded-2xl text-sm leading-relaxed text-slate-200 shadow-sm">
                            Great! I'll send the Figma link right now. I'm really excited about the dark mode implementation. 🚀
                        </div>
                        <p class="text-[10px] text-slate-500 ml-1">12:45 PM</p>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-6 bg-slate-950/30">
                <div class="max-w-4xl mx-auto relative group">
                    <div class="absolute inset-0 bg-indigo-500/5 blur-2xl rounded-full opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                    <div class="relative glass rounded-2xl p-2 flex items-end space-x-2 border-white/10 shadow-2xl">
                        <button type="button" onclick="document.getElementById('attachment').click()" class="p-3 hover:bg-white/5 rounded-xl transition-colors">
                            <i data-lucide="paperclip" class="w-5 h-5 text-slate-400"></i>
                            <input type="file" name="attachment" id="attachment" class="hidden">
                        </button>
                        <textarea rows="1" placeholder="Type a message..."
                            class="flex-1 bg-transparent border-none focus:ring-0 py-3 text-sm text-slate-200 placeholder-slate-500 resize-none max-h-32 custom-scrollbar"></textarea>
                        <div class="flex items-center p-1 space-x-1">
                            <button class="p-2.5 hover:bg-white/5 rounded-xl transition-colors">
                                <i data-lucide="smile" class="w-5 h-5 text-slate-400"></i>
                            </button>
                            <button class="p-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg shadow-indigo-600/30 group">
                                <i data-lucide="send" class="w-5 h-5 text-white transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Auto-scroll to bottom of messages
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;

        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        });
    </script>
    @livewireScripts
</body>
</html>
