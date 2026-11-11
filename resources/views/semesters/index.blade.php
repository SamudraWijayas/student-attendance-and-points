<x-layouts.app headerTitle="Manajemen Semester & Reset Poin" headerSubtitle="Atur semester aktif dan jalankan proses reset semester sesuai aturan resmi sekolah">

    <div class="space-y-6 pt-2">

        <!-- INFO CARD -->
        <x-alert type="warning">
            <strong>Aturan Resmi Reset Semester SMK Tri Sukses Natar:</strong>
            <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                <li>Poin yang di-reset menjadi 0: <strong>Poin Prestasi</strong>, <strong>Pelanggaran Ringan</strong>, dan <strong>Pelanggaran Sedang</strong>.</li>
                <li>Poin yang <strong>TIDAK PERNAH</strong> di-reset: <strong>Pelanggaran Berat (-50 s/d -75)</strong>, <strong>Pelanggaran Fatal (-100)</strong>, serta <strong>Riwayat SP (1, 2, 3)</strong>.</li>
                <li>Sistem otomatis menyimpan snapshot seluruh poin siswa sebelum reset dijalankan.</li>
            </ul>
        </x-alert>

        <div class="flex items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Semester</h3>
            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-semester')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Semester</span>
            </button>
        </div>

        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Tahun Ajaran</th>
                            <th class="py-3 px-3">Semester</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Status Reset</th>
                            <th class="py-3 px-3">Waktu Reset</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($semesters as $s)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3 font-mono font-bold text-slate-900 text-sm">{{ $s->academicYear?->name }}</td>
                                <td class="py-3 px-3 font-bold text-slate-800 text-sm">{{ $s->semester_type->label() }}</td>
                                <td class="py-3 px-3">
                                    @if($s->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            <span>SEMESTER AKTIF</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @if($s->is_reset_completed)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                            Sudah Direset & Diarsip
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-medium">Belum Direset</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-500">
                                    {{ $s->reset_at ? $s->reset_at->translatedFormat('d M Y H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$s->is_active)
                                            <form method="POST" action="{{ route('semesters.set-active', $s->id) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-[11px] font-semibold border border-teal-200 cursor-pointer">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$s->is_reset_completed)
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', 'modal-reset-{{ $s->id }}')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold border border-rose-200 cursor-pointer"
                                            >
                                                Reset Poin Semester
                                            </button>

                                            <!-- MODAL RESET SEMESTER -->
                                            <x-modal id="modal-reset-{{ $s->id }}" title="Konfirmasi Reset Semester: {{ $s->semester_type->label() }} {{ $s->academicYear?->name }}" maxWidth="md">
                                                <form method="POST" action="{{ route('semesters.execute-reset', $s->id) }}" class="space-y-4 text-left">
                                                    @csrf
                                                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 leading-relaxed">
                                                        <strong>Peringatan Penting:</strong> Tindakan ini akan mengarsipkan seluruh poin ke tabel snapshot. Poin prestasi, pelanggaran ringan, dan sedang akan di-reset menjadi 0. <strong>Pelanggaran berat dan fatal TIDAK akan di-reset.</strong>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                            Ketik <strong class="text-rose-600 font-mono">RESET SEMESTER</strong> untuk konfirmasi:
                                                        </label>
                                                        <input type="text" name="confirm_text" required placeholder="RESET SEMESTER" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase">
                                                    </div>

                                                    <div class="flex items-center justify-end gap-2 pt-2">
                                                        <button type="button" x-on:click="$dispatch('close-modal', 'modal-reset-{{ $s->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                                                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">Jalankan Reset Semester</button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- MODAL ADD SEMESTER -->
        <x-modal id="modal-add-semester" title="Tambah Semester Baru" maxWidth="md">
            <form method="POST" action="{{ route('semesters.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Semester <span class="text-rose-500">*</span></label>
                    <select name="semester_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        <option value="ganjil">Semester Ganjil</option>
                        <option value="genap">Semester Genap</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded text-teal-600">
                    <span>Langsung Aktifkan Semester Ini</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-semester')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Semester</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

