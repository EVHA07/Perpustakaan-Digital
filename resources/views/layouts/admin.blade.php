<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Perpustakaan Digital')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .drawer-overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .drawer {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .drawer.active {
            transform: translateX(0);
        }
    </style>

    {{-- INIT DARK MODE DARI SERVER --}}
    <script>
        (function () {
            const theme = "{{ session('theme', 'light') }}";
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="text-text dark:text-dark-text bg-white dark:bg-slate-950 transition-colors duration-200" data-theme="{{ session('theme', 'light') }}">
    <!-- Flash Messages for Toast -->
    @if(session('success'))
        <div data-flash-success="{{ session('success') }}"></div>
    @endif
    @if(session('error'))
        <div data-flash-error="{{ session('error') }}"></div>
    @endif
    <nav class="glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xl font-bold hidden sm:inline">Admin Panel</span>
                    </a>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <button onclick="toggleTheme()" class="p-2 rounded-lg glass-input hover:bg-surface-hover transition-all duration-300">
                        <svg id="theme-toggle-light" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="theme-toggle-dark" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <button onclick="toggleDrawer()" class="sm:hidden p-2 rounded-lg glass-input hover:bg-surface-hover transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="hidden sm:block text-gray-600 dark:text-gray-300 hover:text-white transition-colors duration-300">Siswa</a>
                    <a href="{{ route('admin.books.index') }}" class="hidden sm:block text-gray-600 dark:text-gray-300 hover:text-white transition-colors duration-300">Buku</a>
                    <span class="hidden sm:inline text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-danger hover:text-red-400 transition-colors duration-300 p-2" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer -->
    <div id="drawerOverlay" class="drawer-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50" onclick="toggleDrawer()"></div>
    <div id="mobileDrawer" class="drawer fixed inset-y-0 right-0 w-72 glass z-50 flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-border">
            <span class="font-bold text-lg">Menu</span>
            <button onclick="toggleDrawer()" class="p-2 rounded-lg hover:bg-surface-hover transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 p-4 space-y-2">
            <a href="{{ route('admin.users.index') }}" onclick="toggleDrawer()" class="flex items-center space-x-3 p-3 rounded-lg glass-input hover:bg-surface-hover transition-all duration-300">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Siswa</span>
            </a>
            <a href="{{ route('admin.books.index') }}" onclick="toggleDrawer()" class="flex items-center space-x-3 p-3 rounded-lg glass-input hover:bg-surface-hover transition-all duration-300">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Buku</span>
            </a>
            <div class="flex items-center space-x-3 p-3 rounded-lg glass-input">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
            </div>
        </div>
        <div class="p-4 border-t border-border">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 p-3 rounded-lg glass-input hover:bg-red-500/20 text-danger hover:text-red-400 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    @yield('content')

    <script src="{{ asset('js/toast.js') }}"></script>
    <script>
        // Initialize theme toggle icons based on current theme
        const isDarkMode = document.documentElement.classList.contains('dark');
        const lightIcon = document.getElementById('theme-toggle-light');
        const darkIcon = document.getElementById('theme-toggle-dark');

        if (isDarkMode) {
            lightIcon.classList.remove('hidden');
            darkIcon.classList.add('hidden');
        } else {
            lightIcon.classList.add('hidden');
            darkIcon.classList.remove('hidden');
        }

        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');

            const lightIcon = document.getElementById('theme-toggle-light');
            const darkIcon = document.getElementById('theme-toggle-dark');

            if (isDark) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                fetch('{{ route("theme.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ theme: 'dark' })
                });
            } else {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                fetch('{{ route("theme.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ theme: 'light' })
                });
            }
        }

        function toggleDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            drawer.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>
</body>
</html>
