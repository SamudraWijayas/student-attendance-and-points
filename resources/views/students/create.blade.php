<x-layouts.app headerTitle="Tambah Data Siswa Baru" headerSubtitle="Lengkapi formulir biodata siswa SMK Tri Sukses Natar">

    <div class="max-w-4xl space-y-6 pt-2">

        <x-card title="Formulir Pendaftaran Siswa"
            subtitle="Token QR Code akan otomatis dibuatkan secara unik setelah data siswa berhasil disimpan">

            <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Identitas Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nis" value="{{ old('nis') }}" required maxlength="30"
                            placeholder="Contoh: 240101"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-bold focus:ring-2 focus:ring-teal-600 focus:outline-none">
                        @error('nis')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            NISN
                        </label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="30"
                            placeholder="Contoh: 0071234567"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:ring-2 focus:ring-teal-600 focus:outline-none">
                        @error('nisn')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Siswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="150"
                        placeholder="Masukkan nama lengkap siswa..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:ring-2 focus:ring-teal-600 focus:outline-none">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biodata -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Jenis Kelamin
                        </label>
                        <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tempat Lahir
                        </label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" maxlength="100"
                            placeholder="Contoh: Natar"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                        @error('birth_place')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                        @error('birth_date')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Penempatan Kelas & Akademik -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-slate-100 pt-5">

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Pilih Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select name="school_class_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('school_class_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->major?->name ?? 'Tanpa Jurusan' }})
                                </option>
                            @endforeach
                        </select>
                        @error('school_class_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tahun Masuk / Angkatan
                        </label>
                        <input type="number" name="enrollment_year" value="{{ old('enrollment_year') }}"
                            min="2000" max="2099" placeholder="Contoh: 2026"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                        @error('enrollment_year')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Status Siswa
                        </label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                            <option value="graduated" {{ old('status') === 'graduated' ? 'selected' : '' }}>Lulus
                            </option>
                            <option value="transferred" {{ old('status') === 'transferred' ? 'selected' : '' }}>Pindah
                            </option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Kontak & Alamat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-5">

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" maxlength="25"
                            placeholder="Contoh: 081234567890"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                        @error('phone')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Foto Siswa (Maksimal 2MB)
                        </label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        @error('photo')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Tempat Tinggal
                    </label>
                    <textarea name="address" rows="2" placeholder="Masukkan alamat lengkap siswa..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Button -->
                <div class="border-t border-slate-100 pt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('students.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-sm flex items-center gap-2 cursor-pointer">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Simpan Data & Buat QR Code</span>
                    </button>
                </div>

            </form>
        </x-card>

    </div>

</x-layouts.app>
