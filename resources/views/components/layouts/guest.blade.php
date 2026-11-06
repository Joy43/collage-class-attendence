{{-- Unauthenticated pages (login). --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="flex min-h-screen items-center justify-center bg-linear-to-br from-slate-900 to-indigo-950 p-5 text-slate-50">
    {{ $slot }}
</body>
</html>
