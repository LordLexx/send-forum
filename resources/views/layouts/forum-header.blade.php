@php
use Illuminate\Support\Facades\Storage;

// Determine user role text for the dropdown header
if(auth()->user()->isAdmin()) {
    $roleText = 'Admin';
} elseif(auth()->user()->isModerator()) {
    $roleText = 'Moderator';
} else {
    $roleText = 'Member';
}
@endphp

<!-- ===== NAVBAR ===== -->
<nav id="navbar" class="bg-white shadow-sm fixed w-full z-50 transition-all duration-300" x-data="{ mobileSearchOpen: false, mobileProfileOpen: false }" @keydown.escape.window="mobileSearchOpen = false; mobileProfileOpen = false">

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 gap-3">

            <!-- Logo (text hidden on mobile) -->
            <a href="{{ route('welcome') }}" class="flex items-center space-x-2 shrink-0">
                <!-- Changed to bg-white and added shadow-sm for contrast -->
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                    <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="Slough SEND PCF Logo">
                </div>
                <div class="hidden sm:flex flex-col">
                    <span class="font-bold text-primary-700 text-lg leading-none">Slough SEND</span>
                    <span class="text-xs text-gray-500 leading-none mt-1">Parent Carer Forum</span>
                </div>
            </a>

            <!-- Search & Profile (Desktop) -->
            <div class="hidden md:flex items-center space-x-6">
                <form method="GET" action="{{ route('search') }}" class="relative">
                    <input type="text" name="q" value="{{ request()->get('q', '') }}" placeholder="Search community..." class="w-64 pl-10 pr-4 py-2 rounded-full bg-gray-100 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                    <button type="submit" class="absolute left-3 top-2.5">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                </form>

                <!-- Notification Bell (Desktop) -->
                <livewire:notifications.bell />

                <!-- User Profile Dropdown (Desktop) -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center space-x-2">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-primary-200">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border-2 border-primary-200">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="font-semibold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ $roleText }}</p>
                        </div>

                        @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('admin.dashboard') ? 'text-orange-600 bg-orange-50 font-medium' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                {{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Moderator Dashboard' }}
                            </a>
                        @endif

                        <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            My Profile
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile.edit') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit Profile
                        </a>
                        <a href="{{ route('saved.posts') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('saved.posts') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            Saved Posts
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Right Side -->
            <div class="md:hidden flex items-center gap-2.5 sm:gap-3 shrink-0">
                <!-- Search Toggle Button -->
                <button @click="mobileSearchOpen = !mobileSearchOpen; mobileProfileOpen = false; if (mobileSearchOpen) { $nextTick(() => $refs.mobileSearchInput.focus()) }" class="shrink-0 p-2 text-gray-600 hover:text-primary-700 hover:bg-gray-100 rounded-full transition" aria-label="Search">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>

                <!-- Notification Bell (Mobile) -->
                <div class="shrink-0">
                    <livewire:notifications.bell />
                </div>

                <!-- Profile Dropdown (Mobile) -->
                <div class="relative shrink-0" @click.outside="mobileProfileOpen = false">
                    <button @click="mobileProfileOpen = !mobileProfileOpen; mobileSearchOpen = false" class="flex items-center shrink-0">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-primary-200">
                        @else
                            <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border-2 border-primary-200 text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </button>
                    <div x-show="mobileProfileOpen" x-cloak x-transition.origin.top.right class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ $roleText }}</p>
                        </div>

                        @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('admin.dashboard') ? 'text-orange-600 bg-orange-50 font-medium' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                <svg class="h-4 w-4 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                {{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Moderator Dashboard' }}
                            </a>
                        @endif

                        <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            My Profile
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile.edit') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit Profile
                        </a>
                        <a href="{{ route('saved.posts') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('saved.posts') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <svg class="h-4 w-4 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            Saved Posts
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="h-4 w-4 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Expandable Search Bar -->
        <div x-show="mobileSearchOpen" x-cloak x-transition class="md:hidden pb-4">
            <form method="GET" action="{{ route('search') }}" class="relative">
                <input type="text" name="q" value="{{ request()->get('q', '') }}" placeholder="Search community..." x-ref="mobileSearchInput" class="w-full pl-10 pr-10 py-2.5 rounded-full bg-gray-100 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                <button type="submit" class="absolute left-3 top-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
                <button type="button" @click="mobileSearchOpen = false" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </form>
        </div>
    </div>
</nav>