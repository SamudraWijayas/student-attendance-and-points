<x-layouts.app headerTitle="Tahun Ajaran" headerSubtitle="Kelola kalender tahun ajaran sekolah">

    <div class="space-y-6 pt-2">

        <div class="flex items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Tahun Ajaran</h3>
            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-academic-year')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Tahun Ajaran</span>
            </button>
        </div>

        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Tahun Ajaran</th>
                            <th class="py-3 px-3">Periode Mulai</th>
                            <th class="py-3 px-3">Periode Selesai</th>
                            <th class="py-3 px-3">Jumlah Semester</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($years as $y)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3 font-mono font-bold text-slate-900 text-sm">{{ $y->name }}</td>
                                <td class="py-3 px-3 font-medium">{{ $y->start_date->translatedFormat('d F Y') }}</td>
                                <td class="py-3 px-3 font-medium">{{ $y->end_date->translatedFormat('d F Y') }}</td>
                                <td class="py-3 px-3 font-semibold">{{ $y->semesters_count }} Semester</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $y->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $y->is_active ? 'Sedang Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <button
                                        type="button"
                                        x-on:click="$dispatch('open-modal', 'modal-edit-year-{{ $y->id }}')"
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-slate-100 cursor-pointer"
                                        title="Edit Tahun Ajaran"
                                    >
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </button>

                                    <!-- MODAL EDIT -->
                                    <x-modal id="modal-edit-year-{{ $y->id }}" title="Edit Tahun Ajaran: {{ $y->name }}" maxWidth="md">
                                        <form method="POST" action="{{ route('academic-years.update', $y->id) }}" class="space-y-4 text-left">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tahun Ajaran</label>
                                                <input type="text" name="name" value="{{ $y->name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai</label>
                                                    <input type="date" name="start_date" value="{{ $y->start_date->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai</label>
                                                    <input type="date" name="end_date" value="{{ $y->end_date->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                </div>
                                            </div>
                                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                <input type="checkbox" name="is_active" value="1" {{ $y->is_active ? 'checked' : '' }} class="rounded text-teal-600">
                                                <span>Jadikan Tahun Ajaran Aktif</span>
                                            </label>
                                            <div class="flex items-center justify-end gap-2 pt-2">
                                                <button type="button" x-on:click="$dispatch('close-modal', 'modal-edit-year-{{ $y->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 text-white text-xs font-semibold">Simpan</button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- MODAL ADD ACADEMIC YEAR -->
        <x-modal id="modal-add-academic-year" title="Tambah Tahun Ajaran Baru" maxWidth="md">
            <form method="POST" action="{{ route('academic-years.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tahun Ajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: 2025/2026" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded text-teal-600">
                    <span>Jadikan Tahun Ajaran Aktif</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-academic-year')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Tahun Ajaran</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

