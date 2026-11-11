<x-layouts.app headerTitle="Manajemen Jurusan" headerSubtitle="Kelola program keahlian dan jurusan di SMK Tri Sukses Natar">

    <div class="space-y-6 pt-2">

        <!-- TOOLBAR -->
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Jurusan Sekolah</h3>
            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-major')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Jurusan</span>
            </button>
        </div>

        <!-- MAJORS TABLE -->
        <x-card>
            @if($majors->isEmpty())
                <x-empty-state icon="book-open" title="Belum Ada Jurusan" description="Silakan tambahkan data jurusan awal sekolah." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Kode</th>
                                <th class="py-3 px-3">Nama Jurusan</th>
                                <th class="py-3 px-3">Deskripsi</th>
                                <th class="py-3 px-3">Jumlah Kelas</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($majors as $m)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-mono font-bold text-teal-800">{{ $m->code }}</td>
                                    <td class="py-3 px-3 font-bold text-slate-900">{{ $m->name }}</td>
                                    <td class="py-3 px-3 text-slate-600 max-w-xs truncate">{{ $m->description ?? '-' }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-800">{{ $m->classes_count }} Kelas</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $m->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', 'modal-edit-major-{{ $m->id }}')"
                                                class="p-1.5 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-slate-100 cursor-pointer"
                                                title="Edit Jurusan"
                                            >
                                                <x-icon name="edit" class="w-4 h-4" />
                                            </button>
                                            <form method="POST" action="{{ route('majors.destroy', $m->id) }}" onsubmit="return confirm('Hapus jurusan {{ $m->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer">
                                                    <x-icon name="trash-2" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>

                                        <!-- MODAL EDIT MAJOR -->
                                        <x-modal id="modal-edit-major-{{ $m->id }}" title="Edit Jurusan: {{ $m->name }}" maxWidth="md">
                                            <form method="POST" action="{{ route('majors.update', $m->id) }}" class="space-y-4 text-left">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Jurusan</label>
                                                    <input type="text" name="code" value="{{ $m->code }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Jurusan</label>
                                                    <input type="text" name="name" value="{{ $m->name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi</label>
                                                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $m->description }}</textarea>
                                                </div>
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                    <input type="checkbox" name="is_active" value="1" {{ $m->is_active ? 'checked' : '' }} class="rounded text-teal-600">
                                                    <span>Status Aktif</span>
                                                </label>
                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-edit-major-{{ $m->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
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

        <!-- MODAL ADD MAJOR -->
        <x-modal id="modal-add-major" title="Tambah Jurusan Baru" maxWidth="md">
            <form method="POST" action="{{ route('majors.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: TKJ, TKR, AK" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Teknik Komputer dan Jaringan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Keterangan singkat fokus keahlian..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-teal-600">
                    <span>Langsung Aktifkan Jurusan</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-major')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Jurusan</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

