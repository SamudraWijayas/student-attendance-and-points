<x-layouts.app headerTitle="Import Data Siswa"
    headerSubtitle="Import data siswa SMK Tri Sukses Natar menggunakan file Excel">

    <div class="max-w-5xl space-y-6 pt-2">

        {{-- Hasil Import --}}
        @if (session('import_result'))
            @php($result = session('import_result'))

            <x-card title="Hasil Import" subtitle="Ringkasan proses import data siswa">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Berhasil</p>
                        <p class="text-3xl font-bold text-emerald-700 mt-2">
                            {{ $result['success'] ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-amber-50 border border-amber-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Dilewati</p>
                        <p class="text-3xl font-bold text-amber-700 mt-2">
                            {{ $result['skipped'] ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-rose-50 border border-rose-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-700">Gagal</p>
                        <p class="text-3xl font-bold text-rose-700 mt-2">
                            {{ $result['failed'] ?? 0 }}
                        </p>
                    </div>
                </div>

                @if (!empty($result['errors']))
                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-bold text-slate-700 mb-3">Detail Kesalahan</h3>

                        <div class="max-h-64 overflow-y-auto space-y-2">
                            @foreach ($result['errors'] as $error)
                                <div class="rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    <span class="font-bold">Baris {{ $error['row'] ?? '-' }}:</span>
                                    {{ $error['message'] ?? 'Terjadi kesalahan.' }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-card>
        @endif

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <i class="fa-solid fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Petunjuk Import --}}
        <x-card title="Petunjuk Import Siswa" subtitle="Ikuti langkah berikut sebelum mengunggah data siswa">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div
                        class="w-9 h-9 shrink-0 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        1
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Download Template</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Unduh template Excel yang sudah disediakan.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div
                        class="w-9 h-9 shrink-0 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        2
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Isi Data Siswa</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Isi NIS, nama lengkap, dan nama kelas.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div
                        class="w-9 h-9 shrink-0 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        3
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Periksa Data</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Pastikan nama kelas sesuai dengan data kelas di sistem.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div
                        class="w-9 h-9 shrink-0 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        4
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Upload Excel</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Unggah file untuk memulai proses import.
                        </p>
                    </div>
                </div>

            </div>

            <div
                class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl border border-teal-100 bg-teal-50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white text-teal-700 flex items-center justify-center">
                        <i class="fa-solid fa-file-excel text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Template Import Siswa</h3>
                        <p class="text-xs text-slate-500">Format Excel (.xlsx)</p>
                    </div>
                </div>

                <a href="{{ route('students.import.template') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold transition">
                    <i class="fa-solid fa-download"></i>
                    Download Template
                </a>
            </div>
        </x-card>

        {{-- Form Upload --}}
        <x-card title="Upload File Excel" subtitle="Pilih file Excel yang sudah diisi sesuai template">

            <form method="POST" action="{{ route('students.import.store') }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Pilih File Excel <span class="text-rose-500">*</span>
                    </label>

                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-white file:mr-3 file:border-0 file:rounded-lg file:bg-teal-50 file:text-teal-700 file:px-4 file:py-2 file:font-semibold file:text-xs">

                    @error('file')
                        <p class="text-xs text-rose-500 mt-2">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-slate-400 mt-2">
                        Format XLSX, XLS, atau CSV. Maksimal ukuran file 10 MB.
                    </p>
                </div>

                {{-- Informasi --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-circle-info text-blue-600 mt-0.5"></i>

                        <div>
                            <h3 class="text-sm font-bold text-blue-800">Informasi Import</h3>

                            <ul class="mt-2 space-y-1 text-xs text-blue-700 list-disc list-inside">
                                <li>NIS, nama, dan kelas wajib diisi.</li>
                                <li>NIS yang sudah terdaftar akan dilewati.</li>
                                <li>Biodata lainnya akan dibiarkan kosong.</li>
                                <li>Status siswa otomatis aktif.</li>
                                <li>QR Code dibuat otomatis setelah siswa berhasil ditambahkan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div
                    class="border-t border-slate-100 pt-5 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('students.index') }}"
                        class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                        Batal
                    </a>

                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-file-import"></i>
                        Import Data Siswa
                    </button>
                </div>

            </form>
        </x-card>

    </div>

</x-layouts.app>
