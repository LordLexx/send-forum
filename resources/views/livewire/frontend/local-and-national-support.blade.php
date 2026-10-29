<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Static local & national support services page, no special data needed
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
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Guidance & Help</span>
            <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">Slough & General<br>SEND Support Services</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Links to amazing charities, services and support in Slough, close to Slough, and nationally.</p>
        </div>
    </header>

    <!-- ===== QUICK NAV (Jump to Section) ===== -->
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">On This Page</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">Jump to a Section</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <a href="#slough" class="jump-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center">
                    <div class="jump-card-icon w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mr-5 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-gray-900">Slough Support Services</h3>
                        <p class="text-sm text-gray-500 mt-1">Local services, charities and support close to home.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 ml-4 shrink-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>

                <a href="#national" class="jump-card group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center">
                    <div class="jump-card-icon w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mr-5 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3a10.5 10.5 0 010 18M12 3a10.5 10.5 0 000 18" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-gray-900">General SEND Support Services</h3>
                        <p class="text-sm text-gray-500 mt-1">National organisations offering advice and support.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 ml-4 shrink-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
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

    <!-- ===== SECTION: SLOUGH SUPPORT SERVICES ===== -->
    <section id="slough" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">In & Around Slough</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Slough Support Services</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto mt-4">Services, charities and groups here in Slough and just beyond.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Slough Local Offer -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/send_local_offer_logo_final_cmyk_1_display.png') }}" alt="Slough Local Offer logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Slough Local Offer</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Every local authority must publish a Local Offer explaining:</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-blue-600 mr-2.5 mt-0.5 shrink-0">✔</span> SEND education support</li>
                        <li class="flex"><span class="text-blue-600 mr-2.5 mt-0.5 shrink-0">✔</span> Health services</li>
                        <li class="flex"><span class="text-blue-600 mr-2.5 mt-0.5 shrink-0">✔</span> Social care support</li>
                        <li class="flex"><span class="text-blue-600 mr-2.5 mt-0.5 shrink-0">✔</span> Activities and services for children and young people</li>
                    </ul>
                    <a href="https://www.sloughfamilyservices.org.uk/kb5/sloughcst/directory/localoffer.page" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-blue-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-blue-700 transition">
                        Visit Your Local Offer
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Slough SEND IASS -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Slough Information, Advice and Support Service</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Slough's SEND IASS provides:</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Free, impartial advice for parents and carers</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Help understanding SEND law and processes</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Support preparing for meetings or appeals</li>
                    </ul>
                    <a href="https://www.sloughsendiass.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit Your SEND IASS
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- The Autism Group -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/the-autism-group-logo-t.webp') }}" alt="The Autism Group logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">The Autism Group</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Based in Maidenhead, they provide:</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-purple-600 mr-2.5 mt-0.5 shrink-0">✔</span> Special interest clubs for autistic children and young people aged 9–25, held on Saturdays in central Maidenhead</li>
                        <li class="flex"><span class="text-purple-600 mr-2.5 mt-0.5 shrink-0">✔</span> Free TAG talks and workshops giving parents insight and simple strategies to support their young person</li>
                        <li class="flex"><span class="text-purple-600 mr-2.5 mt-0.5 shrink-0">✔</span> Parent support through coffee and chat sessions across East Berkshire, plus 1-to-1 help via TAG@Home</li>
                    </ul>
                    <a href="https://www.theautismgroup.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-purple-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-purple-700 transition">
                        Find out more
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Autism Berkshire -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/Autism_logo_web.png') }}" alt="Autism Berkshire logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Autism Berkshire</h3>
                    <p class="text-gray-600 text-sm leading-relaxed flex-grow mb-6">Founded in 1990, Berkshire Autistic Society is the leading autism charity in the area, delivering training, specialist services, and leisure activities.</p>
                    <a href="https://www.autismberkshire.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-pink-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-pink-700 transition">
                        Find out more
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Schools and SENCos -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Schools and SENCos</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">For many families, the first step is:</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-amber-600 mr-2.5 mt-0.5 shrink-0">✔</span> Speaking to the school special educational needs coordinator (SENCo)</li>
                        <li class="flex"><span class="text-amber-600 mr-2.5 mt-0.5 shrink-0">✔</span> Discussing concerns with teachers</li>
                        <li class="flex"><span class="text-amber-600 mr-2.5 mt-0.5 shrink-0">✔</span> Requesting meetings to review support</li>
                    </ul>
                    <div class="bg-amber-50 rounded-xl p-4 text-sm text-gray-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-2.5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Schools should be able to guide parents towards additional services where needed.
                    </div>
                </div>

                <!-- Slough Children First -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/SloughChildrenFirstLogo.png') }}" alt="Slough Children First logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Slough Children First</h3>
                    <p class="text-gray-600 text-sm leading-relaxed flex-grow mb-6">Owned wholly by Slough Borough Council, Slough Children First provides social care and support services to children, young people and families.</p>
                    <a href="https://www.sloughchildrenfirst.co.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-green-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-green-700 transition">
                        Find Support
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>

            <!-- Slough SEND Parent Carer Forum (us!) — full-width feature card -->
            <div class="mt-8 bg-gray-900 rounded-3xl overflow-hidden relative">
                <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl"></div>

                <div class="relative grid md:grid-cols-5 items-center">
                    <!-- Logo banner -->
                    <div class="md:col-span-2 p-8 md:p-10 md:pr-5">
                        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                            <img src="{{ asset('build/assets/images/support_logos/slough_send_parent_carer_forum.png') }}" alt="Slough SEND Parent Carer Forum logo: a diverse family under a rainbow, flanked by a puzzle piece and a heart puzzle, with stars, a speech bubble and a group of figures, and the slogan 'Every Voice Matters'." class="w-full rounded-xl">
                        </div>
                    </div>
                    <!-- Copy -->
                    <div class="md:col-span-3 p-8 md:p-10 md:pl-5">
                        <span class="inline-block bg-orange-500/20 text-orange-400 px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase mb-4">Every Voice Matters</span>
                        <h3 class="text-2xl font-bold text-white mb-4">Slough SEND Parent Carer Forum <span class="text-orange-400">(us!)</span></h3>
                        <p class="text-gray-300 text-sm mb-4">We provide:</p>
                        <ul class="space-y-3 text-sm text-gray-300 mb-8">
                            <li class="flex"><span class="text-orange-400 mr-2.5 mt-0.5 shrink-0">✔</span> Peer support and advice from other parents and carers</li>
                            <li class="flex"><span class="text-orange-400 mr-2.5 mt-0.5 shrink-0">✔</span> A path for you to have your say in Slough and Berkshire's SEND support and governance</li>
                            <li class="flex"><span class="text-orange-400 mr-2.5 mt-0.5 shrink-0">✔</span> Support with navigating key SEND services</li>
                        </ul>
                        <a href="{{ route('about') }}" wire:navigate class="inline-flex items-center bg-orange-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition">
                            About Us
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SECTION: GENERAL SEND SUPPORT SERVICES ===== -->
    <section id="national" class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-orange-600 font-semibold uppercase tracking-wide text-sm">National Organisations</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">General SEND Support Services</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto mt-4">Charities and organisations offering advice and support wherever you are in the UK.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- IPSEA -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/ipsea-e1776260357330.webp') }}" alt="IPSEA logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">IPSEA</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">The Independent Provider of Special Education Advice offers free, independent legal advice on SEND law, EHCPs, appeals and tribunals.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Expert advice on your rights</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Tribunal support</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Template letters & guides</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Helpline & email advice</li>
                    </ul>
                    <a href="https://www.ipsea.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Get Legal Advice
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Contact -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/contactOrgLogo-e1776257798236.jpg') }}" alt="Contact logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Contact</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Contact for families with disabled children is a leading charity for families raising disabled children.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Free helpline & listening ear service</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Parent-to-parent support</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Practical guides & financial help</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Local groups & events</li>
                    </ul>
                    <a href="https://contact.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit Contact
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Scope -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/ScopeLogo.png') }}" alt="Scope logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Scope</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Scope is a disability equality charity offering both practical and emotional support.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Information on rights & benefits</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Education & employment advice</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Campaigning for better inclusion</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Parent support services</li>
                    </ul>
                    <a href="https://www.scope.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit Scope
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- YoungMinds -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/YoungMindsLogo-e1776261399286.png') }}" alt="YoungMinds logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">YoungMinds</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">YoungMinds is a UK charity fighting for children and young people's mental health, with parent guides and a helpline.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Advice on anxiety, school refusal & emotional wellbeing</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Parent guides & helpline</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Crisis support information</li>
                    </ul>
                    <a href="https://www.youngminds.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit YoungMinds
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- SOS!SEN -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/SosisenLogo.png') }}" alt="SOS!SEN logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">SOS!SEN</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">SOS!SEN is an independent national helpline for special education advice.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Free confidential telephone support</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Help securing the right educational provision</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Advice on SEN processes</li>
                    </ul>
                    <a href="https://sossen.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit SOS!SEN
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <!-- Family Fund -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl h-24 flex items-center justify-center px-6 mb-6">
                        <img src="{{ asset('build/assets/images/support_logos/familyfund.svg') }}" alt="Family Fund logo" class="max-h-14 object-contain">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Family Fund</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">Family Fund is a UK charity working with government and various SEND services to support families in various ways.</p>
                    <ul class="space-y-2.5 text-sm text-gray-600 flex-grow mb-6">
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Grants for low income households</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Free virtual workshops online</li>
                        <li class="flex"><span class="text-primary-600 mr-2.5 mt-0.5 shrink-0">✔</span> Advice and information on SEND services and financing guidance</li>
                    </ul>
                    <a href="https://www.familyfund.org.uk/" target="_blank" rel="noopener noreferrer" class="self-start inline-flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 transition">
                        Visit Family Fund
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="py-16 md:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-3xl p-10 md:p-12 text-center relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-primary-500/10 rounded-full blur-3xl"></div>

                <div class="relative">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Can't find what you need?</h2>
                    <p class="text-gray-300 mb-8 leading-relaxed">If you need specific signposting or support, reach out to our steering committee directly and we'll point you in the right direction.</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center bg-orange-500 text-white px-7 py-3 rounded-full font-semibold hover:bg-orange-600 transition">
                        Contact Us
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>