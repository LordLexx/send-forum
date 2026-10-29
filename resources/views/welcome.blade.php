<div>
    <!-- ===== HERO SECTION ===== -->
    <header id="home" class="pt-32 pb-20 md:pt-40 md:pb-32 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <!-- Hero Background Image Overlay -->
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1597392582469-a697322d5c16?q=80&w=2070&auto=format&fit=crop');">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <div class="text-white">
                    <!-- Hero Logo -->
                    <div class="mb-8 flex justify-center">
                        <img src="{{ asset('build/assets/images/logo/hero_image-300x300.png') }}"
                             alt="Slough SEND Parent Carer Forum"
                             class="w-32 h-32 md:w-40 md:h-40 object-contain rounded-2xl bg-white/10 backdrop-blur-sm p-3 ring-2 ring-white/30 shadow-2xl">
                    </div>

                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">A Private Community for Slough Parent Carers</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">Every Voice Matters</h1>
                    <p class="text-lg text-primary-100 mb-8 leading-relaxed">Slough SEND Parent Carer Forum, is an independent voice representing local SEND families. We bring parent carers together to support each other, share experiences, and help shape the vital services our families rely on.</p>

                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
                        <!-- Updated to Orange Button -->
                        <a href="{{ route('register') }}" class="bg-orange-500 text-white px-8 py-4 rounded-full font-bold text-center hover:bg-orange-600 transition shadow-lg flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                            </svg>
                            <span>Request to Join</span>
                        </a>
                        <a href="{{ route('about') }}" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-center hover:bg-white/10 transition">
                            Learn More
                        </a>
                    </div>
                    <p class="text-primary-100 text-sm mt-6 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        100% Private & Secure · Admin Approved Accounts
                    </p>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== WHAT WE DO / IN SHORT ===== -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">What is a Parent Carer Forum?</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">We are an independent group of parents and carers of
                    children with special educational needs and disabilities (SEND). We work together with local
                    services to improve support for our families.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-start">
                <!-- Left Side: What we do -->
                <div class="bg-primary-50 p-8 rounded-3xl border border-primary-100">
                    <h3 class="text-2xl font-bold text-primary-700 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        What We Do
                    </h3>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1">✔</span> Represent your voice to those
                            who plan SEND services</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1">✔</span> Signpost you to local and
                            national support</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1">✔</span> Run events, workshops and
                            drop-ins for parent carers</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1">✔</span> Gather feedback to help shape
                            policy and strategy</li>
                    </ul>
                </div>

                <!-- Right Side: In Short Stats -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div
                            class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            👥</div>
                        <h4 class="font-bold text-lg text-gray-900 mb-1">Run By Parents</h4>
                        <p class="text-sm text-gray-500">For parent carers, independently.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div
                            class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            🏛️</div>
                        <h4 class="font-bold text-lg text-gray-900 mb-1">Funded by DfE</h4>
                        <p class="text-sm text-gray-500">Department for Education.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div
                            class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            🤝</div>
                        <h4 class="font-bold text-lg text-gray-900 mb-1">Co-production</h4>
                        <p class="text-sm text-gray-500">Working as equal partners.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div
                            class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            📍</div>
                        <h4 class="font-bold text-lg text-gray-900 mb-1">Slough Based</h4>
                        <p class="text-sm text-gray-500">Part of the National Network.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== RESOURCE HUB PREVIEW ===== -->
    <section id="resources" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div class="mb-6 md:mb-0">
                    <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">SEND Resource Hub</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Supporting you at every stage</h2>
                </div>
                <p class="text-gray-600 max-w-md">Practical information, helpful tools, and trusted guidance created by
                    parents, for parents.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <a href="{{ route('resources') }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 block group">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=800&auto=format&fit=crop"
                        alt="Education" class="h-40 w-full object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-primary-700 transition">Understanding EHCPs</h3>
                        <p class="text-gray-600 text-sm">Guidance on Education, Health and Care Plans, assessments, and
                            your legal rights.</p>
                    </div>
                </a>
                <!-- Card 2 -->
                <a href="{{ route('resources') }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 block group">
                    <img src="https://images.unsplash.com/photo-1530026405186-ed1f139313f8?q=80&w=800&auto=format&fit=crop"
                        alt="Autism & ADHD" class="h-40 w-full object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-primary-700 transition">Autism & ADHD</h3>
                        <p class="text-gray-600 text-sm">Toolkits and strategies for neurodiverse children and sensory
                            needs.</p>
                    </div>
                </a>
                <!-- Card 3 -->
                <a href="{{ route('resources') }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 block group">
                    <img src="https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=800&auto=format&fit=crop"
                        alt="Emotional Wellbeing" class="h-40 w-full object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-primary-700 transition">Emotional Wellbeing</h3>
                        <p class="text-gray-600 text-sm">Calm down toolkits, anxiety management, and emotional
                            regulation support.</p>
                    </div>
                </a>
                <!-- Card 4 -->
                <a href="{{ route('resources') }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 block group">
                    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=800&auto=format&fit=crop"
                        alt="Preparing for Adulthood" class="h-40 w-full object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-primary-700 transition">Preparing for Adulthood</h3>
                        <p class="text-gray-600 text-sm">Planning for independent living, employment, and health from
                            Year 9 onwards.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ===== MEET THE TEAM ===== -->
    <section id="team" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Meet the Steering Committee</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">We're all parents and carers of children with SEND in
                    Slough. The real work happens across every voice we listen to.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <!-- Team Member -->
                <div class="text-center">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center text-3xl font-bold text-primary-700 mb-4">
                        IA</div>
                    <h3 class="font-bold text-lg text-gray-900">Imran Anwar</h3>
                    <p class="text-sm text-primary-600 font-medium">Chair</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center text-3xl font-bold text-primary-700 mb-4">
                        CC</div>
                    <h3 class="font-bold text-lg text-gray-900">Charlotte Carty</h3>
                    <p class="text-sm text-primary-600 font-medium">Co-Chair</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center text-3xl font-bold text-primary-700 mb-4">
                        SC</div>
                    <h3 class="font-bold text-lg text-gray-900">Sharon Carty</h3>
                    <p class="text-sm text-primary-600 font-medium">Co-Chair</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center text-3xl font-bold text-primary-700 mb-4">
                        NH</div>
                    <h3 class="font-bold text-lg text-gray-900">Natasha Hughes</h3>
                    <p class="text-sm text-primary-600 font-medium">Administrator</p>
                </div>
                <div class="text-center hidden md:block">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-orange-100 flex items-center justify-center text-3xl font-bold text-orange-700 mb-4">
                        BG</div>
                    <h3 class="font-bold text-lg text-gray-900">Belinda Gomes</h3>
                    <p class="text-sm text-primary-600 font-medium">Membership Secretary</p>
                </div>
                <div class="text-center hidden md:block">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-orange-100 flex items-center justify-center text-3xl font-bold text-orange-700 mb-4">
                        SC</div>
                    <h3 class="font-bold text-lg text-gray-900">Shravani Chada</h3>
                    <p class="text-sm text-primary-600 font-medium">Membership Secretary</p>
                </div>
                <div class="text-center hidden md:block">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-orange-100 flex items-center justify-center text-3xl font-bold text-orange-700 mb-4">
                        SS</div>
                    <h3 class="font-bold text-lg text-gray-900">Shama Shahid</h3>
                    <p class="text-sm text-primary-600 font-medium">Membership Secretary</p>
                </div>
                <div class="text-center hidden md:block">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-orange-100 flex items-center justify-center text-3xl font-bold text-orange-700 mb-4">
                        AH</div>
                    <h3 class="font-bold text-lg text-gray-900">Aasia Hassan</h3>
                    <p class="text-sm text-primary-600 font-medium">Steering Committee</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== A FEW PLACES TO BEGIN ===== -->
    <section class="py-20 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">Quick Links</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-3">A Few Places to Begin</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Helpful starting points to guide you through the SEND journey.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1: Rights & Entitlements -->
                <a href="{{ route('support.rights') }}"
                   class="group bg-white rounded-2xl p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200 block relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center mb-5 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">Rights & Entitlements</h3>
                        <p class="text-sm text-gray-500 mb-5">From our families</p>
                        <span class="inline-flex items-center text-orange-600 font-semibold text-sm group-hover:gap-2 gap-1 transition-all">
                            Explore
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Card 2: SEND Support -->
                <a href="{{ route('resource.hub') }}"
                   class="group bg-white rounded-2xl p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200 block relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center mb-5 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">SEND Support</h3>
                        <p class="text-sm text-gray-500 mb-5">Guidance and Resources</p>
                        <span class="inline-flex items-center text-primary-600 font-semibold text-sm group-hover:gap-2 gap-1 transition-all">
                            Explore
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Card 3: Events -->
                <a href="{{ route('events.index') }}"
                   class="group bg-white rounded-2xl p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200 block relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center mb-5 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">Events</h3>
                        <p class="text-sm text-gray-500 mb-5">Meet-ups and Talks</p>
                        <span class="inline-flex items-center text-orange-600 font-semibold text-sm group-hover:gap-2 gap-1 transition-all">
                            Explore
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Card 4: SEND Jargon Buster -->
                <a href="{{ route('jargon.index') }}"
                   class="group bg-white rounded-2xl p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200 block relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center mb-5 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">SEND Jargon Buster</h3>
                        <p class="text-sm text-gray-500 mb-5">Find definition of keywords</p>
                        <span class="inline-flex items-center text-primary-600 font-semibold text-sm group-hover:gap-2 gap-1 transition-all">
                            Explore
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ===== CTA + SOCIAL ===== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8 items-stretch">

                <!-- Call To Action -->
                <div class="bg-gray-900 text-white p-8 md:p-10 rounded-3xl flex flex-col">
                    <h3 class="text-2xl md:text-3xl font-bold mb-3 leading-tight">
                        You're not on your own
                        <span class="text-orange-400 block sm:inline">#EveryVoiceMatters</span>
                    </h3>
                    <p class="text-gray-300 mb-8 flex-grow leading-relaxed">Our community brings parent carers together to share experiences, support each other and help to shape the future of SEND support. Find out how you can get involved, attend events and contribute to the forum.</p>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-full font-bold transition shadow-lg text-sm">View Events</a>
                        <a href="{{ route('newsletters.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-900 px-5 py-3 rounded-full font-bold transition shadow-lg text-sm">Sign up for our newsletter</a>
                        <a href="{{ route('resource.hub') }}" class="inline-flex items-center justify-center border-2 border-white text-white hover:bg-white/10 px-5 py-3 rounded-full font-bold transition text-sm">Our Resources</a>
                    </div>
                </div>

                <!-- Social -->
                <div class="bg-primary-50 p-8 md:p-10 rounded-3xl border border-primary-100 flex flex-col">
                    <h3 class="text-2xl font-bold text-primary-900 mb-2">Follow Us</h3>
                    <p class="text-gray-600 mb-6 flex-grow">You can also reach out through our social media accounts and engage with us through those platforms as well.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://www.facebook.com/profile.php?id=61588066225465" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-[#1877F2] text-white px-5 py-3 rounded-full font-medium hover:opacity-90 transition">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        <a href="https://www.instagram.com/sloughsendpcf" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white px-5 py-3 rounded-full font-medium hover:opacity-90 transition">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            Instagram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== OUR PARTNERS ===== -->
    <section class="py-16 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">Working Together</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mt-2 mb-3">Our Partners</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Proud to work alongside trusted organisations supporting SEND families in Slough.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 items-center justify-items-center">
                <div class="flex items-center justify-center h-24 w-full">
                    <img src="{{ asset('build/assets/images/logo/department_for_education-300x140.webp') }}"
                         alt="Department for Education"
                         class="max-h-20 w-auto object-contain opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center h-24 w-full">
                    <img src="{{ asset('build/assets/images/logo/NHS_Berkshire-300x140.webp') }}"
                         alt="NHS Berkshire Healthcare"
                         class="max-h-20 w-auto object-contain opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center h-24 w-full">
                    <img src="{{ asset('build/assets/images/logo/NHS_Frimley.webp') }}"
                         alt="NHS Frimley"
                         class="max-h-20 w-auto object-contain opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center h-24 w-full">
                    <img src="{{ asset('build/assets/images/logo/nnpcf-300x146.jpg') }}"
                         alt="National Network of Parent Carer Forums"
                         class="max-h-20 w-auto object-contain opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center h-24 w-full">
                    <img src="{{ asset('build/assets/images/logo/sloughcouncil-300x150.jpg') }}"
                         alt="Slough Borough Council"
                         class="max-h-20 w-auto object-contain opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition-all duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom spacer to keep separation from footer -->
    <div class="h-16 bg-gray-50"></div>
</div>
