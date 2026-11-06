<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ? "$title — " : '' }}{{ config('app.name') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">

<script>
    // Apply the saved theme before first paint (default: dark) to avoid a flash.
    try { if (localStorage.getItem('theme') !== 'light') document.documentElement.classList.add('dark'); } catch (e) {}
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
