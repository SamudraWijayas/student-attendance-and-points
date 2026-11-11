<x-layouts.app headerTitle="Pengaturan Akun Admin" headerSubtitle="Kelola identitas akun dan perbarui kata sandi administrator">

    <div class="max-w-3xl space-y-6 pt-2">

        <!-- Form 1: Biodata Akun -->
        <x-card title="Informasi Akun Administrator">
            <form method="POST" action="{{ route('settings.account.update') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs cursor-pointer">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </x-card>

        <!-- Form 2: Ganti Password -->
        <x-card title="Ganti Password" subtitle="Pastikan password baru Anda kuat dan sulit ditebak">
            <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                    @error('current_password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Password Baru (Min 8 Karakter)</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                        @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs cursor-pointer">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </x-card>

    </div>

</x-layouts.app>

