<x-layouts.app headerTitle="Edit Data Siswa" headerSubtitle="Perbarui biodata siswa: {{ $student->name }}">

    <div class="max-w-4xl space-y-6 pt-2">

        <x-card title="Edit Biodata Siswa" subtitle="NIS dan NISN harus unik antar siswa">
            <form method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section: Identitas Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-bold">
                        @error('nis')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            NISN (Nomor Induk Siswa Nasional)
                        </label>
                        <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                        @error('nisn')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Siswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select name="gender" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="birth_place"
                            value="{{ old('birth_place', $student->birth_place) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="birth_date"
                            value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <!-- Section: Penempatan Kelas & Akademik -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-slate-100 pt-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Pilih Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select name="school_class_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold">
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('school_class_id', $student->school_class_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->major?->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tahun Masuk / Angkatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="enrollment_year"
                            value="{{ old('enrollment_year', $student->enrollment_year) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Status Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="active"
                                {{ old('status', $student->status->value) === 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive"
                                {{ old('status', $student->status->value) === 'inactive' ? 'selected' : '' }}>Tidak
                                Aktif</option>
                            <option value="graduated"
                                {{ old('status', $student->status->value) === 'graduated' ? 'selected' : '' }}>Lulus
                            </option>
                            <option value="transferred"
                                {{ old('status', $student->status->value) === 'transferred' ? 'selected' : '' }}>Pindah
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Section: Kontak & Foto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Ganti Foto Siswa (Opsional)
                        </label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Tempat Tinggal
                    </label>
                    <textarea name="address" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="border-t border-slate-100 pt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('students.show', $student->id) }}"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">Batal</a>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-sm flex items-center gap-2 cursor-pointer">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </x-card>

    </div>

</x-layouts.app>
