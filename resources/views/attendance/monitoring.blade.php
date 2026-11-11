<x-layouts.app headerTitle="Monitoring Absensi Harian" headerSubtitle="Pantau kehadiran siswa hari ini, deteksi keterlambatan, dan daftar siswa belum absen">

    <div class="space-y-6 pt-2">

        <!-- FILTER & ACTION TOOLBAR -->
        <x-card>
            <form method="GET" action="{{ route('attendance.monitoring') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                    <input
                        type="date"
                        name="date"
                        value="{{ $currentDate }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                    >
                </div>

                <!-- Filter Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                    <select name="class_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ $selectedStatus === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                        <option value="terlambat" {{ $selectedStatus === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cari Nama / NIS</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Ketik nama atau NIS..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                    >
                </div>

                <!-- Submit & Reset Button -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 py-2 px-3 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <x-icon name="filter" class="w-4 h-4" />
                        <span>Filter</span>
                    </button>
                    <a
                        href="{{ route('attendance.monitoring') }}"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-100 flex items-center justify-center cursor-pointer"
                        title="Reset Filter"
                    >
                        <x-icon name="rotate-ccw" class="w-4 h-4" />
                    </a>
                </div>
            </form>
        </x-card>

        <!-- STATS PILLS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs">
                <span class="text-slate-500 font-medium">Total Siswa Aktif:</span>
                <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5">{{ $todayStats['total_active'] }} Siswa</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs">
                <span class="text-emerald-700 font-medium">Hadir Tepat:</span>
                <p class="text-base font-bold text-emerald-800 mt-0.5">{{ $todayStats['hadir'] }} Siswa</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs">
                <span class="text-amber-700 font-medium">Terlambat:</span>
                <p class="text-base font-bold text-amber-800 mt-0.5">{{ $todayStats['terlambat'] }} Siswa</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200 text-xs">
                <span class="text-rose-700 font-medium">Belum Absen:</span>
                <p class="text-base font-bold text-rose-800 mt-0.5">{{ count($absentStudents) }} Siswa</p>
            </div>
        </div>

        <!-- MAIN TABLE OF ATTENDANCES -->
        <x-card title="Daftar Siswa Sudah Absen ({{ $attendances->total() }} Data)">
            <x-slot:action>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 cursor-pointer"
                    >
                        <x-icon name="printer" class="w-4 h-4" />
                        <span>Print</span>
                    </button>
                    <a
                        href="{{ route('reports.attendance.pdf', ['date' => $currentDate, 'class_id' => $selectedClass]) }}"
                        target="_blank"
                        class="px-3 py-1.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs"
                    >
                        <x-icon name="file-down" class="w-4 h-4" />
                        <span>Export PDF</span>
                    </a>
                </div>
            </x-slot:action>

            @if($attendances->isEmpty())
                <x-empty-state icon="clipboard-list" title="Tidak Ada Data Absensi" description="Tidak ditemukan catatan kehadiran yang sesuai dengan filter tanggal atau kriteria ini." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">No</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kelas / Jurusan</th>
                                <th class="py-3 px-3">Jam Datang</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Metode</th>
                                <th class="py-3 px-3">Pencatat / Catatan</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($attendances as $idx => $att)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3 text-slate-400 font-mono">{{ $attendances->firstItem() + $idx }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $att->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $att->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">NIS: {{ $att->student->nis }} • NISN: {{ $att->student->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-semibold text-slate-800">{{ $att->student->schoolClass?->name }}</span>
                                        <div class="text-[11px] text-slate-500">{{ $att->student->schoolClass?->major?->name }}</div>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                        {{ substr($att->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $att->status->badgeClass() }}">
                                            {{ $att->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">
                                        <span class="inline-flex items-center gap-1 font-medium">
                                            <x-icon :name="$att->method->value === 'qr_code' ? 'qr-code' : 'edit-3'" class="w-3.5 h-3.5 text-slate-400" />
                                            <span>{{ $att->method->label() }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-500">
                                        <div>{{ $att->recordedByUser?->name ?? 'Sistem Scanner' }}</div>
                                        @if($att->notes)
                                            <div class="text-[11px] text-amber-700 italic max-w-xs truncate" title="{{ $att->notes }}">{{ $att->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <button
                                            type="button"
                                            x-on:click="$dispatch('open-modal', 'modal-correct-{{ $att->id }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-teal-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                            title="Koreksi Status"
                                        >
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </button>

                                        <!-- MODAL KOREKSI ABSENSI -->
                                        <x-modal id="modal-correct-{{ $att->id }}" title="Koreksi Absensi: {{ $att->student->name }}" maxWidth="md">
                                            <form method="POST" action="{{ route('attendance.correct', $att->id) }}" class="space-y-4 text-left">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Baru</label>
                                                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                        <option value="hadir" {{ $att->status->value === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                                                        <option value="terlambat" {{ $att->status->value === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Koreksi</label>
                                                    <textarea name="reason" rows="3" required placeholder="Jelaskan alasan perubahan status..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-correct-{{ $att->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Koreksi</button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            @endif
        </x-card>

        <!-- ACCORDION / LIST: SISWA BELUM ABSEN HARI INI -->
        <div x-data="{ open: false }" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
            <button
                type="button"
                x-on:click="open = !open"
                class="w-full p-4.5 flex items-center justify-between text-left hover:bg-slate-50/60 transition-colors cursor-pointer"
            >
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center">
                        <x-icon name="user-x" class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Siswa Belum Hadir / Belum Absen ({{ count($absentStudents) }} Siswa)</h4>
                        <p class="text-xs text-slate-500">Klik untuk melihat rincian siswa yang belum melakukan pemindaian hari ini</p>
                    </div>
                </div>
                <x-icon name="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-200" ::class="open ? 'rotate-180' : ''" />
            </button>

            <div x-show="open" class="p-6 border-t border-slate-100 dark:border-slate-800" x-cloak>
                @if($absentStudents->isEmpty())
                    <p class="text-xs text-emerald-700 font-semibold text-center py-4">Luar biasa! Seluruh siswa aktif telah melakukan absensi.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($absentStudents as $absent)
                            <div class="p-3 rounded-xl border border-rose-100 bg-rose-50/30 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('students.show', $absent->id) }}" class="text-xs font-bold text-slate-900 hover:text-teal-700 truncate">
                                        {{ $absent->name }}
                                    </a>
                                    <p class="text-[11px] text-slate-500">{{ $absent->schoolClass?->name }} • NIS {{ $absent->nis }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                                    Belum Absen
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</x-layouts.app>

