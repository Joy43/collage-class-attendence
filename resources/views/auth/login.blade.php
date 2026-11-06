<x-layouts.guest title="Faculty Login">
    <div class="flex w-full max-w-[430px] flex-col rounded-[22px] border border-line bg-surface p-8 sm:p-10 shadow-[0_20px_60px_rgba(0,0,0,0.6)] backdrop-blur-2xl">
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex size-14 items-center justify-center rounded-2xl bg-linear-to-tr from-primary to-secondary text-2xl font-black text-white shadow-[0_0_24px_rgba(37,99,235,0.5)]">
                A
            </div>
            <h1 class="mb-2 text-2xl font-extrabold tracking-tight text-fg">Faculty Portal</h1>
            <p class="text-sm font-medium text-muted">Sign in to manage student attendance</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-300">
                <span>⚠️</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @php
                $input = 'w-full rounded-xl border border-line bg-white/[0.04] px-4 py-3 text-[.95rem] text-fg outline-hidden backdrop-blur-md transition-all duration-200 placeholder:text-muted/60 focus:border-secondary focus:bg-white/[0.08] focus:ring-[3px] focus:ring-secondary/20';
                $label = 'text-[.78rem] font-bold tracking-wider text-muted uppercase';
            @endphp

            <div class="mb-5 flex flex-col gap-2">
                <label for="user_name" class="{{ $label }}">Username</label>
                <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" placeholder="Enter username" required autofocus autocomplete="username" class="{{ $input }}">
            </div>

            <div class="mb-7 flex flex-col gap-2">
                <label for="password" class="{{ $label }}">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="current-password" class="{{ $input }}">
            </div>

            <button type="submit" class="w-full rounded-xl bg-linear-to-r from-primary to-secondary p-3.5 text-base font-bold text-white shadow-[0_4px_18px_rgba(37,99,235,0.4)] transition-all duration-200 hover:-translate-y-px hover:shadow-[0_6px_25px_rgba(56,189,248,0.5)] active:scale-[.98]">
                Sign In
            </button>
        </form>
    </div>
</x-layouts.guest>
