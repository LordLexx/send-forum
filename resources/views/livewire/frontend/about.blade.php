<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Simple static page, no special data needed
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Who We Are</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Run by Parent Carers,<br>For Parent Carers.</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">We are an independent group working together with local services to improve the support on offer for children and young people with SEND in Slough.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- What is a PCF? -->
            <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">What is a Parent Carer Forum?</h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">A Parent Carer Forum (PCF) is a group of parents and carers of children and young people with special educational needs and disabilities (SEND) who work together with local services to improve the support on offer.</p>
                    <p class="text-gray-600 leading-relaxed">By championing lived experiences and strengthening partnerships with local providers, we help shape a more responsive, inclusive, and effective SEND landscape.</p>
                </div>
                <div class="bg-primary-50 p-8 rounded-3xl border border-primary-100">
                    <h3 class="text-xl font-bold text-primary-800 mb-6">In Short...</h3>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex items-start"><span class="text-primary-600 mr-3 mt-1">👥</span> Run by SEND parent carers, for parent carers</li>
                        <li class="flex items-start"><span class="text-primary-600 mr-3 mt-1">🛡️</span> Independent of the council and NHS</li>
                        <li class="flex items-start"><span class="text-primary-600 mr-3 mt-1">🏛️</span> Funded by the Department for Education</li>
                        <li class="flex items-start"><span class="text-primary-600 mr-3 mt-1">🧩</span> Part of the National Network of Parent Carer Forums</li>
                        <li class="flex items-start"><span class="text-primary-600 mr-3 mt-1">📍</span> Exists in every local authority area in England</li>
                    </ul>
                </div>
            </div>

            <!-- Vision & Values -->
            <div class="mb-24">
                <div class="text-center mb-12">
                    <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">Our Core Principles</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Our Vision & Values</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto mt-4">To ensure every child and young person with SEND has the opportunity to thrive, and every family feels heard, valued and supported.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="bg-blue-50 p-6 rounded-2xl text-center border border-blue-100">
                        <div class="text-4xl mb-4">💙</div>
                        <h3 class="font-bold text-lg text-blue-900 mb-2">Parent Voice</h3>
                        <p class="text-sm text-gray-600">Amplifying the real, lived experiences of Slough families.</p>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-2xl text-center border border-orange-100">
                        <div class="text-4xl mb-4">🧡</div>
                        <h3 class="font-bold text-lg text-orange-900 mb-2">Inclusion</h3>
                        <p class="text-sm text-gray-600">Ensuring every child and family is included in community life.</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-2xl text-center border border-green-100">
                        <div class="text-4xl mb-4">💚</div>
                        <h3 class="font-bold text-lg text-green-900 mb-2">Respect</h3>
                        <p class="text-sm text-gray-600">Valuing every family's unique journey and perspectives.</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-2xl text-center border border-purple-100">
                        <div class="text-4xl mb-4">💜</div>
                        <h3 class="font-bold text-lg text-purple-900 mb-2">Partnership</h3>
                        <p class="text-sm text-gray-600">Working hand-in-hand with local services and professionals.</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-2xl text-center border border-yellow-100">
                        <div class="text-4xl mb-4">⭐</div>
                        <h3 class="font-bold text-lg text-yellow-900 mb-2">Empowerment</h3>
                        <p class="text-sm text-gray-600">Giving parents the knowledge and confidence to advocate.</p>
                    </div>
                </div>
            </div>

            <!-- Co-production & Partners -->
            <div class="grid md:grid-cols-2 gap-12 mb-24">
                <div class="bg-gray-900 text-white p-10 rounded-3xl">
                    <h2 class="text-2xl font-bold mb-4">What is Co-production?</h2>
                    <p class="text-gray-300 mb-4 leading-relaxed">Co-production means parent carers and services planning and designing SEND support <span class="text-orange-400 font-semibold">together, as equal partners</span>, rather than services deciding and families being consulted afterwards.</p>
                    <p class="text-gray-300 leading-relaxed">We sit on regional and national groups and panels alongside health, education and local authority colleagues, bringing lived experience to the table at the point decisions are actually made.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Who We Work Alongside</h2>
                    <div class="space-y-4">
                        <div class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🏛️</div>
                            <div>
                                <h3 class="font-bold text-gray-900">Slough Borough Council</h3>
                                <p class="text-sm text-gray-500">Local government and social care.</p>
                            </div>
                        </div>
                        <div class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🏥</div>
                            <div>
                                <h3 class="font-bold text-gray-900">NHS Frimley & NHS Berkshire</h3>
                                <p class="text-sm text-gray-500">Local health and paediatric services.</p>
                            </div>
                        </div>
                        <div class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🏫</div>
                            <div>
                                <h3 class="font-bold text-gray-900">Local Schools & Settings</h3>
                                <p class="text-sm text-gray-500">Education providers across Slough.</p>
                            </div>
                        </div>
                        <div class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-2xl mr-4">🤝</div>
                            <div>
                                <h3 class="font-bold text-gray-900">National Network & DfE</h3>
                                <p class="text-sm text-gray-500">Department for Education & NNPCF.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What We Do vs What We Don't Do -->
            <div>
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Setting Clear Boundaries</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto mt-4">While we are here to amplify voices and help improve SEND services, there are some things we don't do. This helps manage expectations and keeps our community safe.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Do's -->
                    <div class="bg-primary-50 p-8 rounded-3xl border border-primary-100">
                        <h3 class="text-xl font-bold text-primary-800 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            What We Can Do
                        </h3>
                        <ul class="space-y-4 text-gray-700">
                            <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Represent your voice and experiences to those who plan and deliver SEND services.</li>
                            <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Signpost you to the right local and national support.</li>
                            <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Sit on regional and national boards as part of co-production.</li>
                            <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Run events, workshops and drop-ins for parent carers.</li>
                            <li class="flex"><span class="text-primary-600 mr-3 mt-1 shrink-0">✔</span> Gather feedback to help shape policy and service design.</li>
                        </ul>
                    </div>

                    <!-- Don'ts -->
                    <div class="bg-red-50 p-8 rounded-3xl border border-red-100">
                        <h3 class="text-xl font-bold text-red-800 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Services We Don't Provide
                        </h3>
                        <ul class="space-y-4 text-gray-700">
                            <li class="flex"><span class="text-red-500 mr-3 mt-1 shrink-0">✖</span> Individual advocacy or representing families at tribunals.</li>
                            <li class="flex"><span class="text-red-500 mr-3 mt-1 shrink-0">✖</span> Investigating complaints or acting as a formal complaints service.</li>
                            <li class="flex"><span class="text-red-500 mr-3 mt-1 shrink-0">✖</span> Counselling, therapy or peer-support groups.</li>
                            <li class="flex"><span class="text-red-500 mr-3 mt-1 shrink-0">✖</span> Medical assessments, respite care or educational interventions.</li>
                            <li class="flex"><span class="text-red-500 mr-3 mt-1 shrink-0">✖</span> Making decisions on behalf of families or promoting individual cases.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== POLICIES & FUNDING ===== -->
    <section class="py-20 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8 items-stretch">

                <!-- Policies Card -->
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">Our Policies</h2>
                        <p class="text-gray-600 mb-8 leading-relaxed">As an accountable organisation, we publish the policies that govern how we work. You’re welcome to read or download any of them below.</p>
                    </div>
                    <a href="{{ route('policies.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto bg-orange-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition shadow-md text-sm">
                        View Our Policies
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <!-- Funding Card -->
                <div class="bg-primary-50 p-10 rounded-3xl border border-primary-100 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-primary-900 mb-3">Funding & Membership</h2>
                        <p class="text-gray-700 mb-8 leading-relaxed">Funded by the Department for Education and a member of the National Network of Parent Carer Forums.</p>
                    </div>
                    <a href="https://contact.org.uk/help-for-families/parent-carer-participation/what-is-parent-carer-participation/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-full sm:w-auto bg-primary-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-primary-700 transition shadow-md text-sm">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== CTA BANNER ===== -->
    <section class="bg-gray-900 py-16">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-4">Get Involved</h2>
            <p class="text-gray-300 mb-8 text-lg">Become a member today and join our private community.</p>
            @guest
                <a href="{{ route('register') }}" wire:navigate class="inline-block bg-orange-500 text-white px-8 py-4 rounded-full font-medium hover:bg-orange-600 transition text-lg shadow-lg">Request Access</a>
            @endguest
            @auth
                <a href="{{ route('feed') }}" wire:navigate class="inline-block bg-orange-500 text-white px-8 py-4 rounded-full font-medium hover:bg-orange-600 transition text-lg shadow-lg">Go to Community Feed</a>
            @endauth
        </div>
    </section>
</div>
