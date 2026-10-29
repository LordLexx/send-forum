<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Static rights & entitlements page, no special data needed
};
?>

<div>
    <!-- Page-specific styles (kept from the template so the anchor jumps + hover effects are identical) -->
    <style>
        /* Smooth anchor scrolling + offset for the fixed navbar */
        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 6rem; }
        .jump-card-icon { transition: transform 0.3s ease; }
        .jump-card:hover .jump-card-icon { transform: scale(1.1) rotate(-5deg); }
    </style>

    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Guidance & Help</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Know Your Rights</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">If your child has SEND, schools, councils, employers and public services all have legal duties to follow. Here's what they should provide, and what you can ask for.</p>
        </div>
    </header>

    <!-- ===== QUICK NAV (Jump to Section) ===== -->
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">On This Page</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">Jump to a Section</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Education -->
                <a href="#education" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">In Education</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Schools and EHCPs</p>
                    <div class="text-primary-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Equality Act -->
                <a href="#equality-act" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M6 21h12M5 6h14M7 6l-3 6h6l-3-6zm10 0l-3 6h6l-3-6z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Equality Act</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Discrimination Protection</p>
                    <div class="text-purple-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Information -->
                <a href="#information" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">To Information</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Involvement and Local Offer</p>
                    <div class="text-green-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Workplace -->
                <a href="#workplace" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">In the Workplace</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Flexible Working</p>
                    <div class="text-orange-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('support') }}" wire:navigate class="inline-flex items-center text-primary-700 font-medium hover:text-primary-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to all Support & Resources
                </a>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 01: EDUCATION ===== -->
    <section id="education" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl gradient-bg flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">01</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Your child's rights in education</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">What schools and local authorities should afford you, and support you with.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <!-- Schools must, by law -->
                <div class="bg-primary-50 p-8 rounded-3xl border border-primary-100">
                    <h3 class="text-xl font-bold text-primary-800 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Schools must, by law:
                    </h3>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Monitor pupil progress</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Assess needs where concerns arise</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Provide SEND support where needs are identified</li>
                        <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Review that support regularly</li>
                    </ul>
                </div>

                <!-- Remember callout -->
                <div class="bg-amber-50 p-8 rounded-3xl border border-amber-100">
                    <h3 class="text-xl font-bold text-amber-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Remember
                    </h3>
                    <p class="text-gray-700 leading-relaxed">Support does not depend on a diagnosis. A child does not need a medical diagnosis to receive SEND support. <span class="font-semibold text-amber-900">Provision should be based on identified needs.</span></p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Right to request an EHC Needs Assessment -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Right to request an EHC Needs Assessment</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Parents and carers have the legal right to request an Education, Health and Care Needs Assessment directly from the local authority.</p>
                    <p class="text-gray-600 leading-relaxed">Local authorities must review EHCPs at least annually, and involve parents and young people in the process.</p>
                </div>

                <!-- EHCP provision must be delivered -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">If an EHCP is issued, provision must be delivered</h3>
                    <p class="text-gray-600 leading-relaxed">Local authorities have a legal duty to secure the educational provision specified in an EHCP, even if services are difficult to arrange.</p>
                </div>

                <!-- Right to request a school placement -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Right to request a school placement</h3>
                    <p class="text-gray-600 leading-relaxed">Parents can request a particular school when an EHCP is issued. The local authority must name that school unless specific legal exceptions apply.</p>
                </div>

                <!-- Right to appeal -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 4l7 7-3 3-7-7 3-3zM10 7l-7 7 3 3 7-7M3 21h9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Right to appeal SEND decisions</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Parents and young people can appeal to the SEND Tribunal about:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-primary-100">Refusal to assess</span>
                        <span class="bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-primary-100">Refusal to issue a plan</span>
                        <span class="bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-primary-100">Provision in the plan</span>
                        <span class="bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-primary-100">Named school</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 02: EQUALITY ACT ===== -->
    <section id="equality-act" class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-purple-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">02</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Under the Equality Act</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Protections from disability discrimination, for your child and for you.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Protection from disability discrimination</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">Schools and public services must not treat disabled children and adults unfavourably because of their disability.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Duty to make reasonable adjustments</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">Schools and services must take reasonable steps to prevent disabled pupils being placed at a substantial disadvantage.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Protection for parents at work</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">Employees must not be treated unfairly at work because they care for a disabled child.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 03: INFORMATION & INVOLVEMENT ===== -->
    <section id="information" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-green-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">03</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">To information and involvement</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Your right to know, and to be heard.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Parents must be involved in SEND decisions</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">Schools and local authorities must involve parents in decisions about SEND support, and take their views into account.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Local authorities must publish a Local Offer</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">Every local authority must publish information about SEND services available in the area and how families can access them.</p>
                    {{-- TODO: Link to Slough Borough Council's Local Offer page (external URL) --}}
                    <a href="https://www.sloughfamilyservices.org.uk/kb5/sloughcst/directory/localoffer.page?localofferchannel=0" target="__blank" class="self-start inline-flex items-center bg-green-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-green-700 transition">
                        Slough's Local Offer
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 04: WORKPLACE ===== -->
    <section id="workplace" class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">04</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">In the workplace</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">What you can ask of your employer as a parent carer.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Good employer practice -->
                <div class="bg-orange-50 p-8 rounded-3xl border border-orange-100">
                    <h3 class="text-xl font-bold text-orange-900 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Good employer practice includes:
                    </h3>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex"><span class="text-orange-500 mr-3 mt-1 shrink-0">✔</span> Open conversations about care responsibilities and wellbeing</li>
                        <li class="flex"><span class="text-orange-500 mr-3 mt-1 shrink-0">✔</span> Emergency care plans for unpredictable SEND care needs</li>
                        <li class="flex"><span class="text-orange-500 mr-3 mt-1 shrink-0">✔</span> Adjustments to hours or duties to prevent disadvantage</li>
                    </ul>
                </div>

                <!-- Flexible working -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Right to request flexible working</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">A statutory right from your first day of employment. You can make two requests every 12 months to manage care arrangements, and employers must deal with them in a reasonable time.</p>
                </div>

                <!-- Reasonable consideration -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Employers must consider requests reasonably</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">A flexible working request can only be refused for a specific business reason set out in law, and employers must explain their decision.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-3xl p-10 md:p-14 text-center relative overflow-hidden">
                <!-- Decorative glows -->
                <div class="absolute -top-24 -right-24 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-primary-500/10 rounded-full blur-3xl"></div>

                <div class="relative">
                    <span class="inline-block bg-orange-500/20 text-orange-400 px-4 py-1 rounded-full text-sm font-medium mb-6">Not sure where to start?</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Rights only work when you can use them</h2>
                    <p class="text-gray-300 max-w-2xl mx-auto mb-8 leading-relaxed">If you're not sure where to start, or a school or service isn't listening, speak to Slough SENDIASS, read through our list of local and national support, or <a href="#" class="text-orange-400 font-medium underline decoration-orange-400/40 hover:text-orange-300">contact us</a> for further signposting.</p>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        {{-- TODO: Link to Slough SENDIASS website (external URL) --}}
                        <a href="https://www.sloughsendiass.org.uk/" target="__blank" class="bg-white text-gray-900 px-7 py-3 rounded-full font-semibold hover:bg-gray-100 transition shadow-sm">Slough SENDIASS</a>
                        {{-- TODO: Link to the Slough & General SEND Support Services sub-page when it exists --}}
                        <a href="{{ route('support.services') }}" class="bg-orange-500 text-white px-7 py-3 rounded-full font-semibold hover:bg-orange-600 transition">Slough & General SEND Support Services</a>
                    </div>

                    <p class="text-xs text-gray-500 mt-10">Funded by the Department for Education and a member of the <a href="https://nnpcf.org.uk/" target="__blank" class="text-gray-400 hover:text-primary-400 underline">National Network of Parent Carer Forums</a>.</p>
                </div>
            </div>
        </div>
    </section>
</div>