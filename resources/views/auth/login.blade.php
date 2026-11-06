<x-layouts.guest title="Faculty Login">
    <div class="flex w-full max-w-[420px] flex-col rounded-2xl border border-white/10 bg-white/[.03] p-10 shadow-2xl backdrop-blur-xl">
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex size-12 items-center justify-center rounded-xl bg-indigo-600 text-2xl font-bold text-white shadow-[0_4px_10px_rgba(79,70,229,.4)]">A</div>
            <h1 class="mb-1.5 text-2xl font-bold tracking-tight">Faculty Portal</h1>
            <p class="text-[.9rem] text-slate-400">Sign in to manage student attendance</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 flex items-center gap-2 rounded-lg border border-red-500/20 bg-red-500/10 px-3.5 py-2.5 text-[.85rem] font-medium text-red-300">
                ⚠️ <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @php
                $input = 'w-full rounded-lg border border-white/10 bg-white/[.02] px-3.5 py-3 text-[.95rem] text-white outline-hidden transition placeholder:text-slate-500 focus:border-indigo-600 focus:bg-white/5 focus:ring-[3px] focus:ring-indigo-600/25';
                $label = 'text-[.8rem] font-semibold tracking-wide text-slate-400 uppercase';
            @endphp

            <div class="mb-5 flex flex-col gap-2">
                <label for="user_name" class="{{ $label }}">Username</label>
                <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" placeholder="Enter username" required autofocus autocomplete="username" class="{{ $input }}">
            </div>

            <div class="mb-7 flex flex-col gap-2">
                <label for="password" class="{{ $label }}">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="current-password" class="{{ $input }}">
            </div>

            <button type="submit" class="w-full rounded-lg bg-indigo-600 p-3 text-[.95rem] font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 active:scale-[.98]">
                Sign In
            </button>
        </form>
    </div>
</x-layouts.guest>
