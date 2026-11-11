<x-layouts.app headerTitle="Riwayat Absensi Kedatangan" headerSubtitle="Arsip data absensi seluruh siswa SMK Tri Sukses Natar dengan filter lengkap">

    <div class="space-y-6 pt-2">

        <!-- FILTER CARD -->
        <x-card>
            <form method="GET" action="{{ route('attendance.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas</label>
                    <select name="class_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                        <x-icon name="filter" class="w-4 h-4" />
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('attendance.history') }}" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 flex items-center justify-center cursor-pointer">
                        <x-icon name="rotate-ccw" class="w-4 h-4" />
                    </a>
                </div>
            </form>
        </x-card>

        <!-- TABLE OF ATTENDANCES -->
        <x-card title="Data Riwayat Absensi ({{ $attendances->total() }} Data Ditemukan)">
            @if($attendances->isEmpty())
                <x-empty-state icon="history" title="Tidak Ditemukan Riwayat" description="Silakan sesuaikan tanggal atau kriteria filter Anda." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kelas</th>
                                <th class="py-3 px-3">Waktu</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Metode</th>
                                <th class="py-3 px-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($attendances as $att)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">
                                        {{ $att->attendance_date->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $att->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $att->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">NIS: {{ $att->student->nis }}</div>
                                    </td>
                                    <td class="py-3 px-3 font-medium text-slate-700">
                                        {{ $att->student->schoolClass?->name }}
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                        {{ substr($att->check_in_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $att->status->badgeClass() }}">
                                            {{ $att->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">
                                        {{ $att->method->label() }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-500">
                                        {{ $att->notes ?? '-' }}
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

    </div>

</x-layouts.app>

