<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat App | Premium Messaging</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
    
    @livewire('conversations-search')

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
