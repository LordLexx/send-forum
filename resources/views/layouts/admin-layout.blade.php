<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('build/assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>Admin Panel · Slough SEND Parent Carer Forum</title>
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        @include('layouts.admin-sidebar')

        <main class="flex-grow md:ml-64">
            @include('layouts.admin-header')

            <!-- Page Content Injects Here -->
            <div class="p-6 space-y-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    <!-- Notyf Notifications -->
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        // Initialize Notyf
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            dismissible: true
        });

        // Listen for events dispatched by Livewire (Only once!)
        window.addEventListener('notify', (event) => {
            const type = event.detail.type;
            const message = event.detail.message;

            if (type === 'success') {
                notyf.success(message);
            } else if (type === 'error') {
                notyf.error(message);
            }
        });
    </script>
        <!-- Notyf JS -->
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <!-- Emoji Button JS -->
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4/dist/index.min.js"></script>

</body>

</html>