<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('build/assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>Slough SEND Parent Carer Forum</title>
    <meta name="description" content="A private, secure community for parent carers.">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Config: Defines the new brand colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f2f3f8',
                            100: '#e6e8f1',
                            200: '#c5cce6',
                            300: '#9aa6d1',
                            400: '#6a78b5',
                            500: '#3f4d99',
                            600: '#2b3a7a',
                            700: '#141f52',
                            800: '#101944',
                            900: '#0d153b',
                        }
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #141f52 0%, #2b3a7a 100%); }
        .radio-card { transition: all 0.2s ease; cursor: pointer; }
        .radio-card:hover { border-color: #2b3a7a; background-color: #f2f3f8; }
        input[type="radio"]:checked + label { border-color: #2b3a7a; background-color: #f2f3f8; box-shadow: 0 0 0 1px #2b3a7a; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- ===== LEFT PANEL (Dynamic based on route) ===== -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop');"></div>

            <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full overflow-y-auto">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" wire:navigate class="flex items-center space-x-2 w-fit">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl border border-white/30">
                        <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-lg leading-none">Slough SEND</span>
                        <span class="text-xs text-primary-100 leading-none mt-1">Parent Carer Forum</span>
                    </div>
                </a>

                @if(Route::currentRouteName() == 'register')
                    <!-- Register Left Panel -->
                    <div class="max-w-md my-12">
                        <h2 class="text-4xl font-bold leading-tight mb-4">Join the Forum</h2>
                        <p class="text-lg text-primary-100 leading-relaxed mb-8">Help make a difference for families in Slough with Slough’s official SEND parent carer forum.</p>
                        <div class="space-y-4 bg-white/10 backdrop-blur-sm p-6 rounded-2xl border border-white/20">
                            <h3 class="font-semibold text-lg mb-2">Why you should join:</h3>
                            <div class="flex items-start"><svg class="h-6 w-6 mr-3 text-primary-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Share your experiences and views</span></div>
                            <div class="flex items-start"><svg class="h-6 w-6 mr-3 text-primary-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Receive SEND-related news, events and surveys</span></div>
                            <div class="flex items-start"><svg class="h-6 w-6 mr-3 text-primary-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Contribute to shaping local SEND policy and services</span></div>
                        </div>
                    </div>
                    <div class="max-w-md text-sm text-primary-100/80 border-t border-white/20 pt-6">
                        <p class="font-semibold text-white mb-1">Who can join?</p>
                        <p>Membership is open to parents, carers and family members of children and young people aged 0–25 with special educational needs or disabilities who live in or access services in Slough.</p>
                    </div>
                @else
                    <!-- Login Left Panel -->
                    <div class="max-w-md">
                        <h2 class="text-4xl font-bold leading-tight mb-4">Welcome back to your safe space.</h2>
                        <p class="text-lg text-primary-100 leading-relaxed">You are not alone. Log in to connect with other parent carers, share experiences, and access your support resources.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center text-primary-100"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg><span class="font-medium text-sm">100% Private & Secure · Admin Approved Accounts</span></div>
                        <div class="flex items-center text-primary-100"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg><span class="font-medium text-sm">Run by parent carers, for parent carers</span></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ===== RIGHT PANEL (Form Area) ===== -->
        <div class="lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 bg-white">
            <div class="lg:hidden mb-10 flex items-center space-x-2">
                <a href="{{ route('welcome') }}" wire:navigate class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white font-bold text-xl">S</div>
                    <div class="flex flex-col">
                        <span class="font-bold text-primary-700 text-lg leading-none">Slough SEND</span>
                        <span class="text-xs text-gray-500 leading-none mt-1">Parent Carer Forum</span>
                    </div>
                </a>
            </div>

            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
