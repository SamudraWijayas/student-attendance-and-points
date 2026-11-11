<x-layouts.app headerTitle="Buku Induk Kedisiplinan Siswa" headerSubtitle="Rekam jejak komprehensif perilaku, kedisiplinan, prestasi, dan pembinaan siswa">

    <div class="space-y-6 pt-2">

        <!-- FILTER BAR -->
        <x-card>
            <form method="GET" action="{{ route('disciplinary-book.index') }}" class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 flex-1">
                    <select name="class_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa atau NIS..." class="px-3 py-2 rounded-xl border border-slate-200 text-xs flex-1 max-w-sm">

                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                        Filter
                    </button>
                </div>

                <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5 cursor-pointer">
                    <x-icon name="printer" class="w-4 h-4" />
                    <span>Cetak Buku Induk</span>
                </button>
            </form>
        </x-card>

        <!-- BOOK TABLE -->
        <x-card title="Buku Catatan Induk Karakter Siswa">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Siswa</th>
                            <th class="py-3 px-3">Kelas & NIS</th>
                            <th class="py-3 px-3 text-center">Poin Prestasi</th>
                            <th class="py-3 px-3 text-center">Poin Pelanggaran</th>
                            <th class="py-3 px-3 text-center">Remedial</th>
                            <th class="py-3 px-3 text-center">Net Karakter</th>
                            <th class="py-3 px-3 text-center">SP Aktif</th>
                            <th class="py-3 px-3 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $st)
                            @php
                                $summary = $st->getActivePointsSummary();
                                $latestSp = $st->violationFollowUps->whereNotNull('warning_letter_type')->first();
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3">
                                    <a href="{{ route('students.show', $st->id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                        {{ $st->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-semibold text-slate-800">{{ $st->schoolClass?->name }}</span>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $st->nis }}</p>
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-extrabold text-emerald-700">
                                    +{{ $summary['prestasi'] }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-rose-700">
                                    {{ $summary['pelanggaran'] }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-semibold text-teal-700">
                                    +{{ $summary['remedial'] }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-black text-sm">
                                    <span class="{{ $summary['total_net'] >= 0 ? 'text-teal-800' : 'text-rose-700' }}">
                                        {{ $summary['total_net'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($latestSp)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] uppercase">
                                            {{ $latestSp->warning_letter_type }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-medium">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('students.show', $st->id) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 text-[11px] font-semibold hover:bg-slate-50 inline-block">
                                        Buka Profil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </x-card>

    </div>

</x-layouts.app>

