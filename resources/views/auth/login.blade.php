<x-layouts.guest title="Masuk Sistem">
    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-2xl">
        
        <!-- School Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white items-center justify-center shadow-lg shadow-teal-600/30 mb-4">
                <x-icon name="graduation-cap" class="w-9 h-9" />
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">SMK TRI SUKSES NATAR</h2>
            <p class="text-xs font-semibold text-teal-600 dark:text-teal-400 mt-1 uppercase tracking-wider">Sistem Absensi QR & Poin Karakter</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Masuk menggunakan akun Administrator sekolah</p>
        </div>

        @if(session('info'))
            <x-alert type="info" class="mb-5">
                {{ session('info') }}
            </x-alert>
        @endif

        @if($errors->any())
            <x-alert type="error" class="mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Username or Email -->
            <div>
                <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Username atau Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="user" class="w-5 h-5" />
                    </div>
                    <input
                        type="text"
                        name="login"
                        id="login"
                        value="{{ old('login') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Contoh: admin"
                        class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-transparent transition-all"
                    >
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="lock" class="w-5 h-5" />
                    </div>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-transparent transition-all"
                    >
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300 dark:border-slate-700"
                    >
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <span class="text-slate-400 text-[11px]">Portal Admin Resmi</span>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-sm shadow-md shadow-teal-700/25 transition-all flex items-center justify-center gap-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600"
            >
                <x-icon name="log-in" class="w-4.5 h-4.5" />
                <span>Masuk ke Dashboard</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80 text-center">
            <p class="text-[11px] text-slate-400">
                &copy; {{ date('Y') }} SMK Tri Sukses Natar • Hak Cipta Dilindungi
            </p>
        </div>
    </div>
</x-layouts.guest>

