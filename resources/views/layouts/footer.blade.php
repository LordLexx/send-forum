<!-- ===== CTA & FOOTER ===== -->
<footer class="bg-gray-900 text-gray-300 pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Row: Logo + Emergency Contacts + CTA -->
        <div class="grid md:grid-cols-3 gap-12 mb-16">
            <div class="md:col-span-1">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white font-bold text-xl">
                        <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="Slough SEND PCF Logo">
                    </div>
                    <span class="font-bold text-white text-lg">Slough SEND PCF</span>
                </div>
                <p class="text-sm text-gray-400">Supporting families. Empowering voices. Creating positive change
                    together. Every voice matters.</p>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Emergency Contacts</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-3 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" fill-rule="evenodd" />
                        </svg> NHS 111 - Non-emergency</li>
                    <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-3 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                clip-rule="evenodd" fill-rule="evenodd" />
                        </svg> 999 - Emergency Services</li>
                    <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" fill-rule="evenodd" />
                            <path
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" fill-rule="evenodd" />
                        </svg> Childline - 0800 1111</li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Get Involved</h4>

                @guest
                    <p class="text-sm text-gray-400 mb-4">Become a member today and join our private community.</p>
                    <a href="{{ route('register') }}" wire:navigate
                        class="inline-block bg-orange-500 text-white px-6 py-3 rounded-full font-medium hover:bg-orange-600 transition">
                        Request Access
                    </a>
                @endguest

                @auth
                    <p class="text-sm text-gray-400 mb-4">Head over to the community feed to see what's happening today.</p>
                    <a href="{{ route('feed') }}" wire:navigate
                        class="inline-block bg-orange-500 text-white px-6 py-3 rounded-full font-medium hover:bg-orange-600 transition">
                        Go to Feed
                    </a>
                @endauth
            </div>
        </div>

        <!-- Middle Row: Multi-column Link Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12 border-t border-gray-800 pt-12">

            <!-- Explore Column -->
            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Explore</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('welcome') }}#home" class="text-gray-400 hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('about') }}" wire:navigate class="text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="{{ route('events.index') }}" wire:navigate class="text-gray-400 hover:text-white transition">Events</a></li>
                    <li><a href="{{ route('resource.hub') }}" wire:navigate class="text-gray-400 hover:text-white transition">Resource Hub</a></li>
                    <li><a href="{{ route('fathers.hub') }}" wire:navigate class="text-gray-400 hover:text-emerald-400 transition inline-flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                        Fathers Hub
                    </a></li>
                    <li><a href="{{ route('contact') }}" wire:navigate class="text-gray-400 hover:text-white transition">Contact</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Support</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('support') }}" wire:navigate class="text-gray-400 hover:text-white transition">Support Overview</a></li>
                    <li><a href="{{ route('support.rights') }}" wire:navigate class="text-gray-400 hover:text-white transition">Rights & Entitlements</a></li>
                    <li><a href="{{ route('support.services') }}" wire:navigate class="text-gray-400 hover:text-white transition">Local & National Services</a></li>
                    <li><a href="{{ route('support.benefits') }}" wire:navigate class="text-gray-400 hover:text-white transition">Benefits & Finance</a></li>
                    <li><a href="{{ route('jargon.index') }}" wire:navigate class="text-gray-400 hover:text-white transition">Jargon Buster</a></li>
                    <li><a href="{{ route('newsletters.index') }}" wire:navigate class="text-gray-400 hover:text-white transition">Newsletter Archive</a></li>
                </ul>
            </div>

            <!-- Legal Column -->
            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Legal & Policies</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('privacy.policy') }}" wire:navigate class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="{{ route('cookie.policy') }}" wire:navigate class="text-gray-400 hover:text-white transition">Cookie Policy</a></li>
                    <li><a href="{{ route('policies.index') }}" wire:navigate class="text-gray-400 hover:text-white transition">Our Policies</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div>
                <h4 class="text-white font-semibold mb-4 uppercase text-sm tracking-wide">Get in Touch</h4>
                <ul class="space-y-2.5 text-sm">
                    <li class="flex items-start">
                        <svg class="h-4 w-4 mr-2.5 mt-0.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <a href="mailto:info@sloughsendpcf.org" class="text-gray-400 hover:text-white transition break-all">info@sloughsendpcf.org</a>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-4 w-4 mr-2.5 mt-0.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="text-gray-400">Slough, United Kingdom</span>
                    </li>
                </ul>

                <!-- Social Icons -->
                <div class="flex items-center space-x-3 mt-4">
                    <a href="https://www.facebook.com/profile.php?id=61588066225465" target="_blank" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition" aria-label="Facebook">
                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/sloughsendpcf" target="_blank" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition" aria-label="Instagram">
                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Slough SEND Parent Carer Forum. Funded by the Department for Education. Independent of the council and NHS.</p>
            <div class="flex items-center space-x-4">
                <a href="{{ route('privacy.policy') }}" wire:navigate class="hover:text-gray-300 transition">Privacy</a>
                <span class="text-gray-700">·</span>
                <a href="{{ route('cookie.policy') }}" wire:navigate class="hover:text-gray-300 transition">Cookies</a>
                <span class="text-gray-700">·</span>
                <a href="{{ route('policies.index') }}" wire:navigate class="hover:text-gray-300 transition">Policies</a>
            </div>
        </div>
    </div>
</footer>
