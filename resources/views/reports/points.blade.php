<x-layouts.app headerTitle="Laporan Poin Karakter Siswa" headerSubtitle="Laporan perolehan prestasi dan pelanggaran siswa dengan filter kategori dan periode">

    <div class="space-y-6 pt-2">

        <!-- FILTER & EXPORT BAR -->
        <x-card>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <form method="GET" action="{{ route('reports.points') }}" class="flex flex-wrap items-center gap-3">
                    <select name="type" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        <option value="">Semua Jenis (Prestasi & Pelanggaran)</option>
                        <option value="prestasi" {{ $type === 'prestasi' ? 'selected' : '' }}>Prestasi Saja (+)</option>
                        <option value="pelanggaran" {{ $type === 'pelanggaran' ? 'selected' : '' }}>Pelanggaran Saja (-)</option>
                    </select>

                    <select name="class_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <span class="text-xs text-slate-400">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">

                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                        Filter
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 cursor-pointer">
                        <x-icon name="printer" class="w-4 h-4" />
                        <span>Print</span>
                    </button>
                    <a
                        href="{{ route('reports.points.pdf', ['type' => $type, 'class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        target="_blank"
                        class="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs"
                    >
                        <x-icon name="file-down" class="w-4 h-4" />
                        <span>Export PDF</span>
                    </a>
                    <a
                        href="{{ route('reports.points.excel', ['type' => $type, 'class_id' => $classId]) }}"
                        class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs"
                    >
                        <x-icon name="file-spreadsheet" class="w-4 h-4" />
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </x-card>

        <!-- TABLE OF POINT TRANSACTIONS -->
        <x-card title="Laporan Transaksi Karakter ({{ count($transactions) }} Data)">
            @if($transactions->isEmpty())
                <x-empty-state icon="list-checks" title="Tidak Ditemukan Data" description="Tidak ada transaksi poin karakter sesuai kriteria filter." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">No</th>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">NIS</th>
                                <th class="py-2.5 px-3">Nama Siswa</th>
                                <th class="py-2.5 px-3">Kelas</th>
                                <th class="py-2.5 px-3">Jenis / Tingkat</th>
                                <th class="py-2.5 px-3">Tindakan / Pelanggaran</th>
                                <th class="py-2.5 px-3 text-right">Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $idx => $tx)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 px-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">{{ $tx->incident_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $tx->student->nis }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $tx->student->name }}</td>
                                    <td class="py-2.5 px-3 font-semibold">{{ $tx->student->schoolClass?->name }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->type->badgeClass() }}">
                                            {{ strtoupper($tx->type->value) }}
                                        </span>
                                        @if($tx->severity)
                                            <span class="text-[10px] text-slate-500 font-semibold ml-1">({{ $tx->severity->label() }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 max-w-sm">
                                        <p class="font-bold text-slate-900">{{ $tx->category?->action_name }}</p>
                                        <p class="text-[11px] text-slate-600 truncate">{{ $tx->description }}</p>
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-sm">
                                        <span class="{{ $tx->type->value === 'prestasi' ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $tx->type->value === 'prestasi' ? '+' : '' }}{{ $tx->points }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>

