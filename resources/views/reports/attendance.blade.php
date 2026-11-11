<x-layouts.app headerTitle="Laporan Rekap Absensi" headerSubtitle="Laporan kehadiran siswa harian dan bulanan dengan fitur cetak, export PDF, dan Excel">

    <div class="space-y-6 pt-2">

        <!-- FILTER & EXPORT BAR -->
        <x-card>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <form method="GET" action="{{ route('reports.attendance') }}" class="flex flex-wrap items-center gap-3">
                    <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">

                    <select name="class_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                        Tampilkan
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 cursor-pointer">
                        <x-icon name="printer" class="w-4 h-4" />
                        <span>Print</span>
                    </button>
                    <a
                        href="{{ route('reports.attendance.pdf', ['date' => $date, 'class_id' => $classId]) }}"
                        target="_blank"
                        class="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs"
                    >
                        <x-icon name="file-down" class="w-4 h-4" />
                        <span>Export PDF</span>
                    </a>
                    <a
                        href="{{ route('reports.attendance.excel', ['start_date' => $date, 'end_date' => $date, 'class_id' => $classId]) }}"
                        class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs"
                    >
                        <x-icon name="file-spreadsheet" class="w-4 h-4" />
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </x-card>

        <!-- TABLE OF ATTENDANCES -->
        <x-card title="Rekap Kehadiran Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }} ({{ count($attendances) }} Hadir)">
            @if($attendances->isEmpty())
                <x-empty-state icon="calendar-x" title="Tidak Ada Data Kehadiran" description="Tidak ada siswa yang tercatat melakukan absensi pada tanggal ini." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">No</th>
                                <th class="py-2.5 px-3">NIS</th>
                                <th class="py-2.5 px-3">Nama Siswa</th>
                                <th class="py-2.5 px-3">Kelas & Jurusan</th>
                                <th class="py-2.5 px-3">Waktu Kedatangan</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($attendances as $idx => $att)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 px-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $att->student->nis }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $att->student->name }}</td>
                                    <td class="py-2.5 px-3">{{ $att->student->schoolClass?->name }} ({{ $att->student->schoolClass?->major?->name }})</td>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ substr($att->check_in_time, 0, 5) }} WIB</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $att->status->badgeClass() }}">
                                            {{ $att->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $att->method->label() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>

