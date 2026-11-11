<x-layouts.app headerTitle="Master Kategori Poin Karakter" headerSubtitle="Daftar resmi poin prestasi dan pelanggaran tata tertib SMK Tri Sukses Natar">

    <div x-data="{ activeTab: 'prestasi' }" class="space-y-6 pt-2">

        <!-- HEADER & ADD BUTTON -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 border-b border-slate-200">
                <button
                    type="button"
                    x-on:click="activeTab = 'prestasi'"
                    :class="activeTab === 'prestasi' ? 'border-b-2 border-emerald-600 text-emerald-700 font-bold' : 'text-slate-500'"
                    class="px-4 py-2 text-xs cursor-pointer transition-colors"
                >
                    Kategori Prestasi (+)
                </button>
                <button
                    type="button"
                    x-on:click="activeTab = 'pelanggaran'"
                    :class="activeTab === 'pelanggaran' ? 'border-b-2 border-rose-600 text-rose-700 font-bold' : 'text-slate-500'"
                    class="px-4 py-2 text-xs cursor-pointer transition-colors"
                >
                    Kategori Pelanggaran (-)
                </button>
            </div>

            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-category')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer shrink-0"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Kategori Baru</span>
            </button>
        </div>

        <!-- PRESTASI TABLE -->
        <div x-show="activeTab === 'prestasi'">
            <x-card title="Kategori Poin Prestasi Resmi">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Kategori</th>
                                <th class="py-3 px-3">Jenis Tindakan / Perilaku</th>
                                <th class="py-3 px-3">Rentang Nilai</th>
                                <th class="py-3 px-3">Nilai Default</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($prestasiCategories as $cat)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-bold text-emerald-800">{{ $cat->category_name }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-800 max-w-sm">{{ $cat->action_name }}</td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-700">+{{ $cat->min_points }} s/d +{{ $cat->max_points }}</td>
                                    <td class="py-3 px-3 font-mono font-extrabold text-emerald-700">+{{ $cat->default_points }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <form method="POST" action="{{ route('point-categories.toggle', $cat->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-200 text-[11px] font-semibold hover:bg-slate-50">
                                                {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- PELANGGARAN TABLE -->
        <div x-show="activeTab === 'pelanggaran'" x-cloak>
            <x-card title="Kategori Poin Pelanggaran & Tata Tertib Resmi">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tingkat</th>
                                <th class="py-3 px-3">Kategori</th>
                                <th class="py-3 px-3">Bentuk Pelanggaran</th>
                                <th class="py-3 px-3">Rentang Minus</th>
                                <th class="py-3 px-3">Remedial</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pelanggaranCategories as $cat)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->severity?->badgeClass() }}">
                                            {{ $cat->severity?->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-bold text-slate-900">{{ $cat->category_name }}</td>
                                    <td class="py-3 px-3 text-slate-800 max-w-sm">{{ $cat->action_name }}</td>
                                    <td class="py-3 px-3 font-mono font-extrabold text-rose-700">
                                        -{{ $cat->min_points }} {{ $cat->min_points !== $cat->max_points ? 's/d -' . $cat->max_points : '' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($cat->severity?->isRemediable())
                                            <span class="text-teal-700 font-semibold text-[11px]">Bisa Remedial</span>
                                        @else
                                            <span class="text-rose-600 font-semibold text-[11px]">Tidak Bisa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <form method="POST" action="{{ route('point-categories.toggle', $cat->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-200 text-[11px] font-semibold hover:bg-slate-50">
                                                {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- MODAL ADD CATEGORY -->
        <x-modal id="modal-add-category" title="Tambah Kategori Poin Baru" maxWidth="md">
            <form method="POST" action="{{ route('point-categories.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Poin <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        <option value="prestasi">Prestasi (+)</option>
                        <option value="pelanggaran">Pelanggaran (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Keparahan (Jika Pelanggaran)</label>
                    <select name="severity" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="">-- Khusus Prestasi / Kosongkan --</option>
                        <option value="ringan">Ringan (-5 Poin)</option>
                        <option value="sedang">Sedang (-10 s/d -20 Poin)</option>
                        <option value="berat">Berat (-50 s/d -75 Poin)</option>
                        <option value="fatal">Fatal (-100 Poin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelompok Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="category_name" required placeholder="Contoh: Jujur & Beradab, Kedisiplinan Umum" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Tindakan Spesifik <span class="text-rose-500">*</span></label>
                    <input type="text" name="action_name" required placeholder="Contoh: Juara 1 Lomba Kabupaten / Membawa HP" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Min Poin</label>
                        <input type="number" name="min_points" value="10" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Max Poin</label>
                        <input type="number" name="max_points" value="20" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Default</label>
                        <input type="number" name="default_points" value="15" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-category')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Kategori</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

