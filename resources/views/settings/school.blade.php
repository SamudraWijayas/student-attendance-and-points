<x-layouts.app headerTitle="Profil Sekolah" headerSubtitle="Identitas resmi SMK Tri Sukses Natar yang tertera pada kartu dan dokumen laporan">

    <div class="max-w-3xl space-y-6 pt-2">

        <x-card title="Pengaturan Identitas & Kontak Sekolah">
            <form method="POST" action="{{ route('settings.school.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" name="school_name" value="{{ old('school_name', $school->school_name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Alamat Lengkap Sekolah <span class="text-rose-500">*</span></label>
                    <textarea name="address" rows="2" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('address', $school->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nomor Telepon <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $school->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Website Sekolah</label>
                    <input type="text" name="website" value="{{ old('website', $school->website) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Kepala Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="principal_name" value="{{ old('principal_name', $school->principal_name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="principal_nip" value="{{ old('principal_nip', $school->principal_nip) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-sm flex items-center gap-2 cursor-pointer">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Simpan Profil Sekolah</span>
                    </button>
                </div>
            </form>
        </x-card>

    </div>

</x-layouts.app>

