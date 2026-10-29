@php
use Illuminate\Support\Facades\Storage;
@endphp

<header class="bg-white border-b border-gray-100 h-20 flex items-center justify-between px-6 sticky top-0 z-30">
    <div>
        <h1 class="text-xl font-bold text-gray-900">@yield('page_title', 'Dashboard')</h1>
    </div>
    <div class="flex items-center space-x-4">

        <!-- Admin Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">

                <!-- Dynamic Admin Avatar -->
                @if (auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-primary-200">
                @else
                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border-2 border-primary-200">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif

                <div class="hidden sm:block text-left">
                    <p class="font-semibold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="font-semibold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                </div>

                <!-- View Site Link -->
                <a href="{{ route('feed') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    View Site
                </a>

                <!-- My Profile -->
                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    My Profile
                </a>

                <!-- Edit Profile -->
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile.edit') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Profile
                </a>

                <!-- Saved Posts -->
                <a href="{{ route('saved.posts') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('saved.posts') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    Saved Posts
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>