<x-layouts.app headerTitle="Manajemen Kelas" headerSubtitle="Kelola rombel kelas per tingkat dan cetak kartu QR Code kolektif per kelas">

    <div class="space-y-6 pt-2">

        <!-- TOOLBAR & FILTER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('classes.index') }}" class="flex flex-wrap items-center gap-3">
                <select name="grade_level" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Tingkat (10, 11, 12)</option>
                    <option value="10" {{ request('grade_level') == '10' ? 'selected' : '' }}>Tingkat 10</option>
                    <option value="11" {{ request('grade_level') == '11' ? 'selected' : '' }}>Tingkat 11</option>
                    <option value="12" {{ request('grade_level') == '12' ? 'selected' : '' }}>Tingkat 12</option>
                </select>

                <select name="major_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                    Filter
                </button>
            </form>

            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-class')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer shrink-0"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Kelas Baru</span>
            </button>
        </div>

        <!-- CLASSES TABLE -->
        <x-card>
            @if($classes->isEmpty())
                <x-empty-state icon="layers" title="Belum Ada Data Kelas" description="Silakan tambahkan data kelas untuk menampung siswa." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Nama Kelas</th>
                                <th class="py-3 px-3">Tingkat</th>
                                <th class="py-3 px-3">Jurusan</th>
                                <th class="py-3 px-3">Jumlah Siswa</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi & Cetak Kartu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($classes as $c)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-bold text-slate-900 text-sm">{{ $c->name }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono font-bold">Kelas {{ $c->grade_level }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-teal-800">{{ $c->major?->name }} ({{ $c->major?->code }})</td>
                                    <td class="py-3 px-3 font-bold text-slate-800">{{ $c->students_count }} Siswa</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $c->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a
                                                href="{{ route('students.print-class-cards', $c->id) }}"
                                                target="_blank"
                                                class="px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-[11px] font-semibold flex items-center gap-1 border border-teal-200"
                                                title="Cetak Seluruh Kartu QR Kelas Ini"
                                            >
                                                <x-icon name="printer" class="w-3.5 h-3.5" />
                                                <span>Cetak Kartu ({{ $c->students_count }})</span>
                                            </a>

                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', 'modal-edit-class-{{ $c->id }}')"
                                                class="p-1.5 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-slate-100 cursor-pointer"
                                                title="Edit Kelas"
                                            >
                                                <x-icon name="edit" class="w-4 h-4" />
                                            </button>

                                            <form method="POST" action="{{ route('classes.destroy', $c->id) }}" onsubmit="return confirm('Hapus kelas {{ $c->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer">
                                                    <x-icon name="trash-2" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>

                                        <!-- MODAL EDIT CLASS -->
                                        <x-modal id="modal-edit-class-{{ $c->id }}" title="Edit Kelas: {{ $c->name }}" maxWidth="md">
                                            <form method="POST" action="{{ route('classes.update', $c->id) }}" class="space-y-4 text-left">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelas</label>
                                                    <input type="text" name="name" value="{{ $c->name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat</label>
                                                        <select name="grade_level" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                                            <option value="10" {{ $c->grade_level === '10' ? 'selected' : '' }}>10</option>
                                                            <option value="11" {{ $c->grade_level === '11' ? 'selected' : '' }}>11</option>
                                                            <option value="12" {{ $c->grade_level === '12' ? 'selected' : '' }}>12</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan</label>
                                                        <select name="major_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                            @foreach($majors as $m)
                                                                <option value="{{ $m->id }}" {{ $c->major_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                    <input type="checkbox" name="is_active" value="1" {{ $c->is_active ? 'checked' : '' }} class="rounded text-teal-600">
                                                    <span>Status Aktif</span>
                                                </label>
                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-edit-class-{{ $c->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 text-white text-xs font-semibold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

        <!-- MODAL ADD CLASS -->
        <x-modal id="modal-add-class" title="Tambah Kelas Baru" maxWidth="md">
            <form method="POST" action="{{ route('classes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: X TKJ 1, XI TKR 2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Kelas <span class="text-rose-500">*</span></label>
                        <select name="grade_level" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                            <option value="10">Tingkat 10</option>
                            <option value="11">Tingkat 11</option>
                            <option value="12">Tingkat 12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                        <select name="major_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            @foreach($majors as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-teal-600">
                    <span>Kelas Aktif</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-class')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Kelas</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

