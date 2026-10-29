<!-- ===== NAVBAR ===== -->
<nav id="navbar" class="bg-white shadow-md fixed w-full z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('welcome') }}" wire:navigate class="flex items-center space-x-2">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                    <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="Slough SEND PCF Logo">
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-primary-700 text-lg leading-none">Slough SEND</span>
                    <span class="text-xs text-gray-500 leading-none mt-1">Parent Carer Forum</span>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}#home" class="text-gray-600 hover:text-primary-600 font-medium transition">Home</a>
                <a href="{{ route('about') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium transition">About Us</a>
                <a href="{{ route('events.index') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium transition">Events</a>
                <a href="{{ route('resource.hub') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium transition">Resources</a>
                <a href="{{ route('fathers.hub') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium transition">Fathers Hub</a>

                <!-- ===== SUPPORT DROPDOWN ===== -->
                <div class="relative group">
                    <button type="button" class="text-gray-600 hover:text-primary-600 font-medium transition flex items-center">
                        Support
                        <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Invisible bridge (pt-3) + Dropdown panel -->
                    <div class="absolute left-1/2 -translate-x-1/2 top-full pt-3 w-64 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 z-50">
                        <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-2 overflow-hidden">

                            <!-- Support Overview -->
                            <a href="{{ route('support') }}" wire:navigate class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <div>
                                    <p class="font-medium leading-none">Support Overview</p>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-none">Start here</p>
                                </div>
                            </a>

                            <!-- Jargon Buster -->
                            <a href="{{ route('jargon.index') }}" wire:navigate class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <div>
                                    <p class="font-medium leading-none">Jargon Buster</p>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-none">Decode SEND terms</p>
                                </div>
                            </a>

                            <!-- Newsletter -->
                            <a href="{{ route('newsletters.index') }}" wire:navigate class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium leading-none">Newsletter</p>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-none">Past editions & subscribe</p>
                                </div>
                            </a>

                            <!-- Services -->
                            <a href="{{ route('support.services') }}" wire:navigate class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-2 6h12M3 10c0 3.183 1.745 6 4 6m8-6c0 3.183-1.745 6-4 6" />
                                </svg>
                                <div>
                                    <p class="font-medium leading-none">Services</p>
                                    <p class="text-xs text-gray-400 mt-0.5 leading-none">Local & national support</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <a href="{{ route('contact') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium transition">Contact</a>

                @guest
                    <a href="{{ route('login') }}" wire:navigate class="text-primary-700 border border-primary-700 px-5 py-2 rounded-full hover:bg-primary-50 font-medium transition">Login</a>
                    <a href="{{ route('register') }}" wire:navigate class="bg-orange-500 text-white px-5 py-2 rounded-full hover:bg-orange-600 font-medium shadow-sm transition">Join Forum</a>
                @endguest

                @auth
                    <a href="{{ route('feed') }}" wire:navigate class="text-primary-700 border border-primary-700 px-5 py-2 rounded-full hover:bg-primary-50 font-medium transition">Feed</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded-full hover:bg-orange-600 font-medium shadow-sm transition">Logout</button>
                    </form>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button id="menuBtn" class="md:hidden text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100">
        <div class="flex flex-col p-4 space-y-3">
            <a href="{{ route('welcome') }}#home" class="text-gray-600 hover:text-primary-600 font-medium">Home</a>
            <a href="{{ route('about') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium">About Us</a>
            <a href="{{ route('events.index') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium">Events</a>
            <a href="{{ route('resource.hub') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium">Resources</a>
            <a href="{{ route('fathers.hub') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium">Fathers Hub</a>

            <!-- Mobile Support Dropdown (Alpine.js) -->
            <div x-data="{ open: false }" class="space-y-2">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-gray-600 hover:text-primary-600 font-medium">
                    <span>Support</span>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak x-transition class="pl-4 space-y-3 border-l-2 border-primary-100 ml-1">
                    <a href="{{ route('support') }}" wire:navigate class="block text-sm text-gray-600 hover:text-primary-600 font-medium">Support Overview</a>
                    <a href="{{ route('jargon.index') }}" wire:navigate class="block text-sm text-gray-600 hover:text-primary-600 font-medium">Jargon Buster</a>
                    <a href="{{ route('newsletters.index') }}" wire:navigate class="block text-sm text-gray-600 hover:text-primary-600 font-medium">Newsletter</a>
                    <a href="{{ route('support.services') }}" wire:navigate class="block text-sm text-gray-600 hover:text-primary-600 font-medium">Services</a>
                </div>
            </div>

            <a href="{{ route('contact') }}" wire:navigate class="text-gray-600 hover:text-primary-600 font-medium">Contact</a>

            <div class="flex space-x-3 pt-2">
                @guest
                    <a href="{{ route('login') }}" wire:navigate class="flex-1 text-center text-primary-700 border border-primary-700 px-4 py-2 rounded-full font-medium">Login</a>
                    <a href="{{ route('register') }}" wire:navigate class="flex-1 text-center bg-orange-500 text-white px-4 py-2 rounded-full font-medium">Join Forum</a>
                @endguest

                @auth
                    <a href="{{ route('feed') }}" wire:navigate class="flex-1 text-center text-primary-700 border border-primary-700 px-4 py-2 rounded-full font-medium">Feed</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full text-center bg-orange-500 text-white px-4 py-2 rounded-full font-medium">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
