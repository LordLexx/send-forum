<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Create Newsletter · Slough SEND Parent Carer Forum' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;600&family=Lora:wght@400;600&family=Merriweather:wght@400;700&family=Inter:wght@400;600&family=Roboto:wght@400;500;700&family=Lato:wght@400;700&family=Nunito+Sans:wght@400;600&display=swap" rel="stylesheet">
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
