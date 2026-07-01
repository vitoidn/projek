<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <div class="bg-white border border-slate-200 px-10 py-12">
                <div class="flex flex-col items-center mb-10">
                    <img src="{{ asset('usui-logo.png') }}" alt="PT USUI" class="h-14 mb-4">
                    <h2 class="text-base font-semibold text-slate-800 tracking-wide">PT USUI</h2>
                    <p class="text-xs text-slate-400 mt-1">Production Operational Record</p>
                </div>

                <x-auth-session-status class="mb-5" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-medium text-slate-500 uppercase tracking-wider">Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            class="block w-full border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none"
                            placeholder="name@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="space-y-1 mt-6">
                        <label for="password" class="block text-xs font-medium text-slate-500 uppercase tracking-wider">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="block w-full border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none"
                            placeholder="password">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="mt-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="border-slate-300 text-slate-700 focus:ring-slate-300">
                            <span class="text-xs text-slate-500">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="mt-8 w-full border border-slate-700 bg-slate-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 active:bg-slate-900 transition-colors">
                        Sign in
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} PT USUI. All rights reserved.
            </p>
        </div>
    </div>
</x-guest-layout>
