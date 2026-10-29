<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('build/assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>@yield('title', 'Slough SEND Parent Carer Forum') · Slough SEND PCF</title>
    <meta name="description" content="Your private community feed. Connect, share, and support other Slough SEND parent carers.">
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #141f52 0%, #2b3a7a 100%); }
        .post-card:hover .post-title { color: #141f52; }
        [x-cloak] { display: none !important; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <!-- ===== Navbar Partial ===== -->
    @include('layouts.forum-header')

    <!-- ===== Page Content ===== -->
    <div class="pt-24 pb-12 min-h-screen">
        {{ $slot }}
    </div>

    @livewireScripts
    <script>
        // Ensure the DOM is fully loaded and Notyf is initialized
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Success Messages (from standard Laravel redirects)
            @if (session('success'))
                window.notyf.success("{{ session('success') }}");
            @endif

            // 2. Error Messages (from standard Laravel redirects)
            @if (session('error'))
                window.notyf.error("{{ session('error') }}");
            @endif

            // 3. Validation Errors (Loops through all errors on page load)
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    window.notyf.error("{{ $error }}");
                @endforeach
            @endif
        });

        // Global Livewire Hook: Only shows red toasts for NEW validation errors
        document.addEventListener('livewire:initialized', function () {
            // Keep track of errors we've already shown to prevent spam
            let previousErrors = {};

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    const currentErrors = component.snapshot?.memo?.errors ?? {};
                    const currentFlat = Object.values(currentErrors).flat();

                    // Get the previous errors for this specific component
                    const prev = previousErrors[component.id] || [];

                    // Only show errors that are NEW (weren't in the previous snapshot)
                    currentFlat.forEach(msg => {
                        if (!prev.includes(msg)) {
                            window.notyf.error(msg);
                        }
                    });

                    // Update the tracker with the current errors
                    previousErrors[component.id] = currentFlat;
                });
            });
        });

        // Navbar shadow on scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('shadow-md');
            } else {
                navbar.classList.remove('shadow-md');
            }
        });
    </script>
</body>
</html>