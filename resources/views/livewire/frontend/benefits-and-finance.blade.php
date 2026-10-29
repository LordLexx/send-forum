<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Static benefits & finance page, no special data needed
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
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Guidance & Help</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">What You Might Be<br>Entitled To</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Raising a child with SEND comes with real extra costs like therapies, equipment, lost income and more. There's more financial help available than most families realise. Here's an overview of what to look into.</p>
        </div>
    </header>

    <!-- ===== DISCLAIMER + QUICK NAV ===== -->
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Disclaimer -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-sm text-gray-600 leading-relaxed">This is not an exhaustive list and information may change. <span class="font-semibold text-amber-900">Always check the official sources before applying.</span></p>
                </div>
            </div>

            <div class="text-center mb-10">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">On This Page</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">Jump to a Section</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- For your child -->
                <a href="#your-child" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">For Your Child</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Benefits for a disabled child</p>
                    <div class="text-primary-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- For you as a carer -->
                <a href="#carer" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">For You as a Carer</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Money and protections</p>
                    <div class="text-purple-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Grants & one-off help -->
                <a href="#grants" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Grants & One-Off Help</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Charity grants for costs</p>
                    <div class="text-green-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </div>
                </a>

                <!-- Everyday savings -->
                <a href="#savings" class="jump-card group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="jump-card-icon w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12.172V5a2 2 0 012-2h2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Everyday Savings</h3>
                    <p class="text-sm text-gray-500 mt-1 flex-grow">Reductions & Exemptions</p>
                    <div class="text-orange-600 font-semibold text-sm mt-4 inline-flex items-center">
                        Explore
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:ml-2 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
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

    <!-- ===== SECTION 01: FOR YOUR CHILD ===== -->
    <section id="your-child" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl gradient-bg flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">01</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">For your child</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Benefits paid to support a disabled child or young person.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- DLA -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1 rounded-full border border-primary-100 mb-3">Under 16</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Disability Living Allowance (DLA)</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">A tax-free benefit for children under 16 who need more care, supervision or help getting around than other children their age. <span class="font-semibold text-gray-800">No diagnosis required</span> — it is based on the support your child actually needs. <span class="font-semibold text-gray-800">Not means-tested.</span> Rates range from around £30 to £195 a week (2026/27).</p>
                    <a href="https://www.gov.uk/dla-disability-living-allowance-benefit" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-primary-700 font-semibold text-sm hover:text-primary-800 transition">
                        How to apply on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- PIP -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1 rounded-full border border-primary-100 mb-3">16 and over</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Personal Independence Payment (PIP)</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">Replaces DLA when your child turns 16. They'll be invited to apply by the DWP, and it's a fresh assessment, so a previous DLA award doesn't guarantee PIP. Worth getting help from SENDIASS or Citizens Advice for the form.</p>
                    <a href="https://www.gov.uk/pip" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-primary-700 font-semibold text-sm hover:text-primary-800 transition">
                        PIP on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Motability -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-primary-50 text-primary-700 text-xs font-semibold px-3 py-1 rounded-full border border-primary-100 mb-3">Higher rate mobility</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Motability Scheme</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">If your child gets the higher rate mobility component of DLA (from age 3) or the enhanced rate of PIP, you can use that money to lease a car, powered wheelchair or scooter through Motability.</p>
                    <a href="https://www.motability.co.uk" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-primary-700 font-semibold text-sm hover:text-primary-800 transition">
                        Motability Scheme
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 02: FOR YOU AS A CARER ===== -->
    <section id="carer" class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-purple-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">02</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">For you as a carer</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Money and protections for the person doing the caring.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Carer's Allowance -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-purple-50 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full border border-purple-100 mb-3">35+ hours/week</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Carer's Allowance</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5"><span class="font-semibold text-gray-800">£86.45 a week (2026/27)</span> if you spend at least 35 hours a week caring for someone who gets DLA middle/highest care rate, PIP daily living, or Attendance Allowance. There's an earnings limit, so check before applying if you work.</p>
                    <a href="https://www.gov.uk/carers-allowance" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-purple-700 font-semibold text-sm hover:text-purple-800 transition">
                        Carer's Allowance on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Carer's Credit -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-purple-50 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full border border-purple-100 mb-3">National Insurance</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Carer's Credit</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">If you can't claim Carer's Allowance (often because you earn too much), Carer's Credit fills gaps in your National Insurance record so your State Pension isn't reduced for the years you spent caring.</p>
                    <a href="https://www.gov.uk/carers-credit" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-purple-700 font-semibold text-sm hover:text-purple-800 transition">
                        Carer's Credit on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Universal Credit elements -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200 mb-3">Often missed</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Universal Credit – carer & disabled child elements</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">If you're on UC, there's a carer element for caring 35+ hours a week, and a disabled child element paid on top of your standard UC if your child gets DLA or PIP. The disabled child element alone can be <span class="font-semibold text-gray-800">over £160 a month</span>, more if the child is severely disabled. Update your UC journal as soon as DLA is awarded.</p>
                    <a href="https://www.gov.uk/universal-credit" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-purple-700 font-semibold text-sm hover:text-purple-800 transition">
                        Universal Credit on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 03: GRANTS & ONE-OFF HELP ===== -->
    <section id="grants" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-green-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">03</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Grants & one-off help</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Charity grants for equipment, breaks, or specific costs.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Family Fund -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 mb-3">The big one</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Family Fund</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">The UK's largest grants programme for low-income families raising disabled or seriously ill children (0-17). Funds things like sensory equipment, white goods, family breaks, tablets and clothing. No diagnosis needed, and eligibility is based on care needs across at least three areas. Opens every 24 months.</p>
                    <a href="https://www.familyfund.org.uk" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-green-700 font-semibold text-sm hover:text-green-800 transition">
                        Family Fund
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Newlife -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 mb-3">Equipment</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Newlife Foundation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">Grants for specialist equipment, specialist chairs, beds, communication aids, that the NHS or council won't fund. They also run an emergency loan service for terminally ill children.</p>
                    <a href="https://newlifecharity.co.uk" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-green-700 font-semibold text-sm hover:text-green-800 transition">
                        Newlife
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Cerebra -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 mb-3">Brain conditions</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Cerebra</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">For children with conditions affecting the brain. Free sleep service, a lending library of toys and equipment, a legal entitlements service, and a parent guide library covering everything from EHCPs to challenging behaviour.</p>
                    <a href="https://cerebra.org.uk" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-green-700 font-semibold text-sm hover:text-green-800 transition">
                        Cerebra
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Caudwell Children -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 mb-3">Equipment & therapy</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Caudwell Children</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">Grants for specialist equipment, treatment (including autism assessments) and family support, for children under 18 with a disability or serious illness in households earning under £45k.</p>
                    <a href="https://www.caudwellchildren.com" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-green-700 font-semibold text-sm hover:text-green-800 transition">
                        Caudwell Children
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION 04: EVERYDAY SAVINGS ===== -->
    <section id="savings" class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start mb-10">
                <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-white font-bold text-lg mr-4 md:mr-5">04</div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Everyday savings</h2>
                    <p class="text-gray-600 mt-2 md:text-lg">Reductions and exemptions that quietly add up.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Council tax -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200 mb-3">Often missed</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Council tax disability reduction</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">If your home has been adapted for a disabled person, extra space for a wheelchair, a downstairs bathroom or bedroom because of disability, your council tax can drop by one band. Apply through Slough Borough Council, and savings can be backdated.</p>
                    {{-- TODO: Swap for the direct council tax discounts page on Slough Borough Council's site --}}
                    <a href="https://www.slough.gov.uk" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-orange-600 font-semibold text-sm hover:text-orange-700 transition">
                        Council tax discounts - Slough Council
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Blue Badge -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-orange-50 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full border border-orange-100 mb-3">Parking</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Blue Badge</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">A disabled parking permit. Automatic if your child gets the higher rate DLA mobility component. Otherwise apply through Slough Borough Council — non-physical disabilities such as autism and cognitive conditions can qualify too.</p>
                    <a href="https://www.gov.uk/blue-badge" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-orange-600 font-semibold text-sm hover:text-orange-700 transition">
                        Apply for a Blue Badge
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- DFG -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-orange-50 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full border border-orange-100 mb-3">Home adaptations</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Disabled Facilities Grant (DFG)</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5"><span class="font-semibold text-gray-800">Up to £30,000</span> from your council towards home adaptations — ramps, stairlifts, ground-floor bathrooms, widened doorways. Means-tested for adults, but <span class="font-semibold text-gray-800">not means-tested when it's for a disabled child.</span> Apply through Slough Borough Council.</p>
                    <a href="https://www.gov.uk/disabled-facilities-grants" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-orange-600 font-semibold text-sm hover:text-orange-700 transition">
                        DFG on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Free school transport -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-orange-50 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full border border-orange-100 mb-3">SEND</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Free school transport</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">If your child can't reasonably walk to their nearest suitable school because of SEND, the council must arrange free transport. Eligibility runs through Slough's Local Offer.</p>
                    {{-- TODO: Link to Slough's Local Offer page --}}
                    <a href="#" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-orange-600 font-semibold text-sm hover:text-orange-700 transition">
                        Slough Local Offer
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- VAT relief -->
                <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <span class="self-start bg-orange-50 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full border border-orange-100 mb-3">Tax exemption</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">VAT relief on disability equipment</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-grow mb-5">When buying things designed for a disabled person — adapted clothing, sensory equipment, certain mobility aids — you may not have to pay VAT. Just tick the eligibility declaration at checkout.</p>
                    <a href="https://www.gov.uk/vat-relief-for-disabled-people" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center text-orange-600 font-semibold text-sm hover:text-orange-700 transition">
                        VAT relief on Gov.uk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
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