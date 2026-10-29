<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Static support & resources page, no special data needed
};
?>

<div>
    <!-- Page-specific hover styles (kept from the template so design is identical) -->
    <style>
        .support-card-icon {
            transition: transform 0.3s ease;
        }
        .support-card:hover .support-card-icon {
            transform: scale(1.1) rotate(-5deg);
        }
    </style>

    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Guidance & Help</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Support & Resources</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">We've gathered essential information, tools, and links to support you on your SEND journey. Click on a category below to learn more.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Support Cards Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Card 1: Know Your Rights -->
                {{-- TODO: point to your sub-page route when it exists, e.g. route('support.rights') --}}
                <a href="{{ route('support.rights') }}" wire:navigate class="support-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Know Your Rights</h3>
                    <p class="text-gray-600 mb-6 flex-grow">Click to learn more about your rights and entitlements, if your child has SEND.</p>
                    <div class="text-blue-600 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Card 2: Support Services -->
                {{-- TODO: point to your sub-page route when it exists, e.g. route('support.services') --}}
                <a href="{{ route('support.services') }}" class="support-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Slough & General SEND Support Services</h3>
                    <p class="text-gray-600 mb-6 flex-grow">Links to amazing charities, services and support in Slough, close to Slough, and nationally.</p>
                    <div class="text-primary-600 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Card 3: Benefits & Finances -->
                {{-- TODO: point to your sub-page route when it exists, e.g. route('support.benefits') --}}
                <a href="{{ route('support.benefits') }}" class="support-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Benefits and Finances</h3>
                    <p class="text-gray-600 mb-6 flex-grow">Click to learn more about the costs that are involved and what you may be entitled to.</p>
                    <div class="text-orange-600 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Card 4: Jargon Buster -->
                {{-- TODO: point to your sub-page route when it exists, e.g. route('support.jargon') --}}
                <a href="{{ route('jargon.index') }}" class="support-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">SEND Jargon Buster</h3>
                    <p class="text-gray-600 mb-6 flex-grow">Click to learn about the terms and phrases you will hear on your SEND carer journey.</p>
                    <div class="text-purple-600 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Card 5: Our Resources -->
                {{-- Wired to your existing resources page (login-protected). Swap back to "#" if that's not the target. --}}
                <a href="{{ route('resource.hub') }}" wire:navigate class="support-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Resources</h3>
                    <p class="text-gray-600 mb-6 flex-grow">We have some resource documents that you can download and read that can be of benefit to you.</p>
                    <div class="text-green-600 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Card 6: Contact the Forum (Added for layout balance) -->
                {{-- TODO: point to your contact route when it exists --}}
                <a href="#" class="support-card group bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="support-card-icon w-16 h-16 bg-gray-800 rounded-2xl flex items-center justify-center text-orange-500 mb-6 border border-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Can't find what you need?</h3>
                    <p class="text-gray-400 mb-6 flex-grow">If you need specific signposting or support, reach out to our steering committee directly.</p>
                    <div class="text-orange-500 font-semibold text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        Contact Us
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

            </div>

        </div>
    </section>
</div>