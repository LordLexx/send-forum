<aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-100 fixed h-screen z-40">
    <div class="h-20 flex items-center px-6 border-b border-gray-100">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center space-x-2">
            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="Slough SEND PCF Logo">
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-primary-700 text-sm leading-none">Slough SEND</span>
                <span class="text-xs text-gray-500 leading-none mt-1">{{ ucfirst(auth()->user()->role) }} Panel</span>
            </div>
        </a>
    </div>

    <nav class="flex-grow p-4 space-y-1 overflow-y-auto">
        <p class="px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Main</p>
        <a href="{{ route('admin.dashboard') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
        </a>

        <p class="px-3 py-2 mt-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Management</p>

        <!-- Members Link -->
        <a href="{{ route('admin.members') }}" wire:navigate
            class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.members*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <span class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Members
            </span>
            @php $pendingMembers = \App\Models\User::where('status', 'pending')->count(); @endphp
            @if ($pendingMembers > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingMembers }}</span>
            @endif
        </a>

        <!-- Posts & Moderation Link -->
        <a href="{{ route('admin.posts') }}" wire:navigate
            class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.posts*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <span class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Posts & Moderation
            </span>
        </a>

        <!-- Categories Link -->
        <a href="{{ route('admin.categories') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.categories*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            Categories
        </a>

        <!-- Events Link -->
        <a href="{{ route('admin.events') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.events*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Events
        </a>

        <!-- Resources Link -->
        <a href="{{ route('admin.resources') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.resources*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Resources
        </a>

        <!-- Newsletters Link -->
        <a href="{{ route('admin.newsletters') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.newsletters*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Newsletters
        </a>

        <!-- Jargon Buster Link -->
        <a href="{{ route('admin.jargon') }}" wire:navigate
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.jargon*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Jargon Buster
        </a>

        <!-- Contact Messages Link -->
        <a href="{{ route('admin.contact-submissions') }}" wire:navigate
            class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.contact-submissions*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50 transition' }}">
            <span class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Contact Messages
            </span>
            @php $newContacts = \App\Models\ContactSubmission::where('is_verified', true)->where('status', 'new')->count(); @endphp
            @if ($newContacts > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $newContacts }}</span>
            @endif
        </a>

        <p class="px-3 py-2 mt-4 text-xs font-bold text-gray-400 uppercase tracking-wider">System</p>

        <!-- Main Site Link -->
        <a href="{{ route('feed') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
            </svg>
            Main Site
        </a>

        <form method="POST" action="{{ route('admin.logout') }}" class="contents">
            @csrf
            <button type="submit" class="w-full flex items-center px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </nav>
</aside>
