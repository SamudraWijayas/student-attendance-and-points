<x-layouts.app headerTitle="Manajemen Data Siswa"
    headerSubtitle="Kelola biodata siswa, filter per kelas/jurusan, dan generate kartu absensi QR Code">

    <div class="space-y-6 pt-2">

        <!-- FILTER & ACTION BAR -->
        <x-card>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <form method="GET" action="{{ route('students.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 flex-1">
                    <!-- Filter Kelas -->
                    <select name="class_id"
                        class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Jurusan -->
                    <select name="major_id"
                        class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium">
                        <option value="">Semua Jurusan</option>
                        @foreach ($majors as $m)
                            <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} ({{ $m->code }})</option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select name="status"
                        class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                        <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Lulus
                        </option>
                        <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Pindah
                        </option>
                    </select>

                    <!-- Search Input -->
                    <div class="flex items-center gap-1.5">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari Nama / NIS / NISN..."
                            class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <button type="submit"
                            class="p-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white cursor-pointer shadow-xs">
                            <x-icon name="search" class="w-4 h-4" />
                        </button>
                    </div>
                </form>

                <!-- Action Button: Add Student -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('students.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-sm shadow-teal-700/20">
                        <x-icon name="user-plus" class="w-4 h-4" />
                        <span>Tambah Siswa Baru</span>
                    </a>
                    <a href="{{ route('students.import.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-teal-700 px-4 py-2.5 text-sm font-semibold text-teal-700 hover:bg-teal-50">
                        <i class="fa-solid fa-file-import"></i>
                        Import Siswa
                    </a>
                </div>
            </div>
        </x-card>

        <!-- TABLE OF STUDENTS -->
        <x-card title="Daftar Siswa ({{ $students->total() }} Data)">
            @if ($students->isEmpty())
                <x-empty-state icon="users" title="Tidak Ditemukan Data Siswa"
                    description="Belum ada data siswa yang cocok dengan filter atau pencarian Anda.">
                    <x-slot:action>
                        <a href="{{ route('students.create') }}"
                            class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">
                            Tambah Siswa Sekarang
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">NIS / NISN</th>
                                <th class="py-3 px-3">Kelas & Jurusan</th>
                                {{-- <th class="py-3 px-3">L/P</th>
                                <th class="py-3 px-3">Angkatan</th> --}}
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">QR Code</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($students as $st)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 text-slate-700 font-bold flex items-center justify-center shrink-0 overflow-hidden">
                                                @if ($st->photo)
                                                    <img src="{{ asset('storage/' . $st->photo) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($st->name, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('students.show', $st->id) }}"
                                                    class="font-bold text-slate-900 hover:text-teal-700 transition-colors">
                                                    {{ $st->name }}
                                                </a>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ $st->phone ?? 'Tidak ada telp' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-mono">
                                        <div class="font-bold text-slate-800">{{ $st->nis }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $st->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-semibold text-slate-800">{{ $st->schoolClass?->name }}</span>
                                        <p class="text-[11px] text-slate-500">{{ $st->schoolClass?->major?->name }}</p>
                                    </td>
                                    {{-- <td class="py-3 px-3 font-semibold text-slate-700">
                                        {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                    <td class="py-3 px-3 font-mono font-medium text-slate-700">
                                        {{ $st->enrollment_year }}
                                    </td> --}}
                                    <td class="py-3 px-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $st->status->badgeClass() }}">
                                            {{ $st->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if ($st->qrCode && $st->qrCode->is_active)
                                            <span
                                                class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-[11px]">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                                <span>Aktif</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">Belum Ada</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('students.show', $st->id) }}"
                                                class="p-1.5 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-slate-100 transition-colors"
                                                title="Lihat Detail Profil & Tab">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('students.print-card', $st->id) }}" target="_blank"
                                                class="p-1.5 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-slate-100 transition-colors"
                                                title="Cetak Kartu Siswa QR">
                                                <x-icon name="id-card" class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('students.edit', $st->id) }}"
                                                class="p-1.5 rounded-lg text-slate-600 hover:text-blue-700 hover:bg-slate-100 transition-colors"
                                                title="Edit Biodata">
                                                <x-icon name="edit" class="w-4 h-4" />
                                            </a>
                                            <form method="POST" action="{{ route('students.destroy', $st->id) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $st->name }}?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus / Arsipkan Siswa">
                                                    <x-icon name="trash-2" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>
