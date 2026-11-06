{{-- Unauthenticated pages (login). --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#0B1020] bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(37,99,235,0.22),transparent),radial-gradient(ellipse_60%_50%_at_80%_80%,rgba(56,189,248,0.14),transparent)] p-5 text-fg">
    {{ $slot }}
</body>
</html>
