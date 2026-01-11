<!DOCTYPE html>
<html lang="id" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan Digital')</title>

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

    {{-- INIT THEME FROM SERVER --}}
    <script>
        (function () {
            const theme = "{{ session('theme', 'light') }}";
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body class="text-text dark:text-dark-text bg-white dark:bg-slate-950 transition-colors duration-200">

<nav class="glass sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                              C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                              C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13
                              C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent hidden sm:inline">
                        Perpustakaan Digital
                    </span>
                </a>
            </div>

             @if(request()->routeIs('home'))
             <!-- SEARCH BAR - Hidden on mobile -->
             <div class="hidden md:flex flex-1 mx-8">
                 <form action="{{ route('search') }}" method="GET" class="w-full">
                     <input type="text" name="q" placeholder="Cari buku..."
                         class="w-full px-4 py-2 rounded-lg glass-input text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                         value="{{ request('q') }}">
                 </form>
             </div>
             @endif

             <div class="flex items-center space-x-2 sm:space-x-4">
                 <!-- THEME TOGGLE -->
                 <button onclick="toggleTheme()" class="p-2 rounded-lg glass-input hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                     <svg id="icon-sun" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M12 3v1m0 16v1m9-9h-1M4 12H3
                               m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707
                               m12.728 0l-.707.707M6.343 17.657l-.707.707
                               M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                     </svg>
                     <svg id="icon-moon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M20.354 15.354A9 9 0 018.646 3.646
                               9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                     </svg>
                 </button>

                <!-- LOGOUT BUTTON - Hidden on mobile -->
                @auth
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
                @endauth

                <!-- MOBILE MENU BUTTON -->
                <button onclick="toggleDrawer()" class="sm:hidden p-2 rounded-lg glass-input hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

@yield('content')

<!-- DRAWER -->
<div id="drawerOverlay" class="drawer-overlay fixed inset-0 bg-black/50 z-50" onclick="toggleDrawer()"></div>
<div id="mobileDrawer" class="drawer fixed inset-y-0 right-0 w-72 glass z-50 flex flex-col">
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <span class="font-bold text-lg">Menu</span>
        <button onclick="toggleDrawer()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 p-4 space-y-4">
        <!-- SEARCH IN DRAWER -->
        <form action="{{ route('search') }}" method="GET" class="mb-4">
            <input type="text" name="q" placeholder="Cari buku..."
                class="w-full px-4 py-2 rounded-lg glass-input text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ request('q') }}">
        </form>

        <!-- USER INFO -->
        @auth
        <div class="flex items-center space-x-3 p-3 rounded-lg glass-input">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>
        </div>
        @endauth
    </div>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center space-x-2 p-3 rounded-lg glass-input hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500 dark:text-red-400 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
        @endauth
    </div>
</div>

 <script>
 function toggleTheme() {
     const html = document.documentElement;
     const isDark = html.classList.toggle('dark');

     fetch("{{ route('theme.toggle') }}", {
         method: "POST",
         headers: {
             "Content-Type": "application/json",
             "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
         },
         body: JSON.stringify({ theme: isDark ? 'dark' : 'light' })
     });
 }

 function toggleDrawer() {
     document.getElementById('mobileDrawer').classList.toggle('active');
     document.getElementById('drawerOverlay').classList.toggle('active');
 }
 </script>

</body>
</html>
