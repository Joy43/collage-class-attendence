{{-- Authenticated shell: sidebar + top bar + scrolling content area. Usage: <x-layouts.app title="Dashboard"> … </x-layouts.app> --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
    <meta name="notifications-url" content="{{ route('notifications.index') }}">
</head>
<body data-page="{{ request()->route()?->getName() }}" class="h-screen w-screen overflow-hidden transition-colors duration-150">
<div class="flex h-screen w-screen overflow-hidden">

    @include('partials.sidebar')

    <div class="flex h-full min-w-0 grow flex-col overflow-hidden">
        @include('partials.topbar')

        <main id="main-scroll" class="grow overflow-y-auto bg-canvas px-4 py-5 sm:px-8 sm:py-7">
            @if (session('status'))
                <x-alert type="success">{{ session('status') }}</x-alert>
            @endif
            @if (session('warning'))
                <x-alert type="warning">{{ session('warning') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert type="error">{{ $errors->first() }}</x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

<div id="toast-container" class="fixed right-6 bottom-6 z-[99999] flex flex-col gap-2.5"></div>

@stack('scripts')
</body>
</html>
