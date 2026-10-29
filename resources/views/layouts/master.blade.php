<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('build/assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>Slough SEND Parent Carer Forum</title>
    <meta name="description"
        content="A private, secure community for parent carers of children with Special Educational Needs and Disabilities (SEND) in Slough.">
    <!-- Tailwind CSS for styling -->
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
                            700: '#141f52', // The exact requested main color
                            800: '#101944',
                            900: '#0d153b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #141f52 0%, #2b3a7a 100%);
        }
    </style>
    <!-- CSRF Token for Livewire/Forms -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Required for Livewire, wire:navigate, and the loading bar -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Page Content -->
    <main>
        <!-- Page Header Start-->
        @include('layouts.header')
        <!-- Page Header Ends-->

        {{ $slot }}

        <!-- Page Footer Start-->
        @include('layouts.footer')
        <!-- Page Footer Ends-->
    </main>

    <!-- Required for Livewire, wire:navigate, and the loading bar -->
    @livewireScripts

    <!-- ===== JAVASCRIPT ===== -->
    <script>
        // Mobile Menu Toggle
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Navbar shadow on scroll
        const navbar = document.getElementById('navbar');
        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.classList.add('shadow-lg');
                } else {
                    navbar.classList.remove('shadow-lg');
                }
            });
        }
    </script>
</body>

</html>