<x-layouts.app headerTitle="Input Transaksi Poin Siswa" headerSubtitle="Catat poin prestasi atau pelanggaran resmi berdasarkan tata tertib SMK Tri Sukses Natar">

    <div x-data="pointForm()" class="max-w-4xl space-y-6 pt-2">

        <x-card title="Formulir Transaksi Poin Karakter">
            <form method="POST" action="{{ route('points.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 1. Pilih Siswa -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Pilih Siswa <span class="text-rose-500">*</span>
                    </label>
                    <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                        <option value="">-- Pilih Siswa Berdasarkan Nama / Kelas / NIS --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ (old('student_id', $selectedStudentId) == $st->id) ? 'selected' : '' }}>
                                {{ $st->name }} (Kelas {{ $st->schoolClass?->name }} - NIS {{ $st->nis }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Jenis Transaksi (Tabs / Radios) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Jenis Transaksi Poin <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label
                            class="p-4 rounded-2xl border-2 flex items-center gap-3 cursor-pointer transition-all"
                            :class="pointType === 'prestasi' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-950 font-bold shadow-xs' : 'border-slate-200 bg-slate-50/40 text-slate-600'"
                        >
                            <input type="radio" name="transaction_type" value="prestasi" x-model="pointType" x-on:change="onTypeChange()" class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <p class="text-sm font-bold flex items-center gap-1.5 text-emerald-700">
                                    <x-icon name="award" class="w-4 h-4" />
                                    <span>Poin Prestasi (+)</span>
                                </p>
                                <p class="text-[11px] font-normal text-slate-500 mt-0.5">Penghargaan akhlakul karimah & prestasi akademik/lomba</p>
                            </div>
                        </label>

                        <label
                            class="p-4 rounded-2xl border-2 flex items-center gap-3 cursor-pointer transition-all"
                            :class="pointType === 'pelanggaran' ? 'border-rose-500 bg-rose-50/70 text-rose-950 font-bold shadow-xs' : 'border-slate-200 bg-slate-50/40 text-slate-600'"
                        >
                            <input type="radio" name="transaction_type" value="pelanggaran" x-model="pointType" x-on:change="onTypeChange()" class="text-rose-600 focus:ring-rose-500">
                            <div>
                                <p class="text-sm font-bold flex items-center gap-1.5 text-rose-700">
                                    <x-icon name="alert-triangle" class="w-4 h-4" />
                                    <span>Poin Pelanggaran (-)</span>
                                </p>
                                <p class="text-[11px] font-normal text-slate-500 mt-0.5">Pelanggaran kedisiplinan, seragam, atau tata tertib</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Kategori Poin -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori & Jenis Tindakan Resmi <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="point_category_id"
                        id="point_category_id"
                        required
                        x-model="selectedCategoryId"
                        x-on:change="onCategoryChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold"
                    >
                        <option value="">-- Pilih Tindakan Berdasarkan Peraturan Resmi --</option>
                        <template x-for="cat in filteredCategories" :key="cat.id">
                            <option :value="cat.id" x-text="formatCategoryOption(cat)"></option>
                        </template>
                    </select>
                </div>

                <!-- Category Info Box -->
                <div x-show="currentCategory !== null" class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200 text-xs text-teal-900 space-y-1" x-cloak>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-teal-950" x-text="currentCategory?.category_name"></span>
                        <span class="font-mono font-extrabold text-teal-800" x-text="'Rentang Ketentuan: ' + currentCategory?.min_points + ' s/d ' + currentCategory?.max_points + ' Poin'"></span>
                    </div>
                    <p class="text-slate-600" x-text="currentCategory?.description || currentCategory?.action_name"></p>
                </div>

                <!-- 4. Nilai Poin & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nilai Poin <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="points"
                            required
                            x-model="pointValue"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-base font-mono font-bold"
                            :class="pointType === 'prestasi' ? 'text-emerald-700' : 'text-rose-700'"
                        >
                        <p class="text-[11px] text-slate-500 mt-1" x-text="pointType === 'prestasi' ? 'Nilai positif (+)' : 'Nilai minus (-) akan otomatis disimpan ke database'"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tanggal Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                    </div>
                </div>

                <!-- 5. Keterangan / Kronologi -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keterangan & Kronologi Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        name="description"
                        rows="3"
                        required
                        placeholder="Tuliskan rincian kronologi, lokasi kejadian, saksi, atau prestasi yang diraih secara jelas..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm leading-relaxed"
                    >{{ old('description') }}</textarea>
                </div>

                <!-- 6. Bukti Foto & Opsi Tindak Lanjut Awal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Upload Bukti Foto / Dokumen (Opsional)
                        </label>
                        <input type="file" name="evidence" accept="image/*" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div x-show="pointType === 'pelanggaran'" x-cloak>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Rencana Tindak Lanjut Awal
                        </label>
                        <select name="follow_up_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                            <option value="Teguran Lisan & Penataan Seragam">Teguran Lisan & Penataan Seragam</option>
                            <option value="Konseling BK & Surat Pernyataan">Konseling BK & Surat Pernyataan</option>
                            <option value="Pemanggilan Orang Tua / Wali Murid">Pemanggilan Orang Tua / Wali Murid</option>
                            <option value="Penerbitan Surat Peringatan (SP 1)">Penerbitan Surat Peringatan (SP 1)</option>
                            <option value="Konferensi Kasus Khusus / Sidang Pleno">Konferensi Kasus Khusus / Sidang Pleno</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="border-t border-slate-100 pt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-700">Batal</a>
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl text-white font-semibold text-xs shadow-sm flex items-center gap-2 cursor-pointer"
                        :class="pointType === 'prestasi' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                    >
                        <x-icon name="check" class="w-4 h-4" />
                        <span x-text="pointType === 'prestasi' ? 'Simpan Poin Prestasi' : 'Simpan Poin Pelanggaran'"></span>
                    </button>
                </div>
            </form>
        </x-card>

    </div>

    <script>
        function pointForm() {
            return {
                pointType: '{{ old('transaction_type', $preselectedType) }}',
                categories: @json($categories),
                selectedCategoryId: '',
                currentCategory: null,
                pointValue: 10,

                get filteredCategories() {
                    return this.categories.filter(c => c.type === this.pointType);
                },

                onTypeChange() {
                    this.selectedCategoryId = '';
                    this.currentCategory = null;
                    this.pointValue = this.pointType === 'prestasi' ? 15 : 5;
                },

                onCategoryChange() {
                    const cat = this.categories.find(c => c.id == this.selectedCategoryId);
                    this.currentCategory = cat || null;
                    if (cat) {
                        this.pointValue = cat.default_points;
                    }
                },

                formatCategoryOption(cat) {
                    const prefix = cat.severity ? `[${cat.severity.toUpperCase()}] ` : '';
                    return `${prefix}${cat.action_name} (${cat.min_points} - ${cat.max_points} Poin)`;
                }
            };
        }
    </script>
</x-layouts.app>

