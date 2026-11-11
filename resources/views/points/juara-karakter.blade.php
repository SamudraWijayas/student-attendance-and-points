<x-layouts.app headerTitle="Ranking Juara Karakter" headerSubtitle="Peringkat siswa berprestasi terbaik dengan syarat mutlak 0 poin pelanggaran Sedang, Berat, dan Fatal">

    <div class="space-y-6 pt-2">

        <!-- KRITERIA JUARA KARAKTER ALERT -->
        <x-alert type="warning">
            <strong>Ketentuan Penentuan Juara Karakter SMK Tri Sukses Natar:</strong>
            <p class="text-xs mt-1 leading-relaxed">
                1. Memiliki akumulasi poin prestasi tertinggi pada semester penilaian.<br>
                2. <strong>Wajib nol (0) pelanggaran</strong> pada kategori <strong>Sedang (-10 s/d -20)</strong>, <strong>Berat (-50 s/d -75)</strong>, maupun <strong>Fatal (-100)</strong>.<br>
                3. Siswa yang memenuhi syarat berhak menerima <strong>Piagam Penghargaan Resmi Juara Karakter</strong> dari sekolah.
            </p>
        </x-alert>

        <!-- FILTERS -->
        <x-card>
            <form method="GET" action="{{ route('juara-karakter.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Semester</label>
                    <select name="semester_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ $selectedSemester == $s->id ? 'selected' : '' }}>
                                {{ $s->academicYear?->name }} - {{ $s->semester_type->label() }} {{ $s->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas</label>
                    <select name="class_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan</label>
                    <select name="major_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="">Semua Jurusan</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}" {{ $selectedMajor == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Angkatan</label>
                    <select name="enrollment_year" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="">Semua Angkatan</option>
                        @foreach($enrollmentYears as $ey)
                            <option value="{{ $ey }}" {{ $selectedYear == $ey ? 'selected' : '' }}>Angkatan {{ $ey }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                        <x-icon name="filter" class="w-4 h-4" />
                        <span>Filter Ranking</span>
                    </button>
                </div>
            </form>
        </x-card>

        <!-- PODIUM JUARA (TOP 3) -->
        @php
            $eligibleList = $rankings->where('is_eligible', true);
            $top1 = $eligibleList->first();
            $top2 = $eligibleList->skip(1)->first();
            $top3 = $eligibleList->skip(2)->first();
        @endphp

        @if($top1)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <!-- Juara 2 -->
                @if($top2)
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 text-center shadow-sm order-2 md:order-1">
                        <div class="w-12 h-12 rounded-2xl bg-slate-200 text-slate-800 font-extrabold text-lg flex items-center justify-center mx-auto mb-2 shadow-xs">
                            #2
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ $top2['student']->name }}</h4>
                        <p class="text-xs text-slate-500">{{ $top2['student']->schoolClass?->name }} • NIS {{ $top2['student']->nis }}</p>
                        <div class="mt-3 inline-block px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-mono font-extrabold text-sm">
                            +{{ $top2['total_prestasi'] }} Poin
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('juara-karakter.certificate', $top2['student']->id) }}" target="_blank" class="text-xs font-semibold text-teal-700 hover:underline">
                                Cetak Piagam
                            </a>
                        </div>
                    </div>
                @else
                    <div class="hidden md:block"></div>
                @endif

                <!-- Juara 1 (Champion) -->
                <div class="p-6 rounded-3xl bg-gradient-to-b from-amber-400/20 via-white to-white dark:from-amber-950/40 dark:via-slate-900 dark:to-slate-900 border-2 border-amber-400 text-center shadow-lg order-1 md:order-2">
                    <div class="w-16 h-16 rounded-3xl bg-amber-400 text-amber-950 font-black text-2xl flex items-center justify-center mx-auto mb-3 shadow-md">
                        👑 #1
                    </div>
                    <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-amber-950">
                        JUARA KARAKTER UTAMA
                    </span>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-2 truncate">{{ $top1['student']->name }}</h3>
                    <p class="text-xs text-slate-500">{{ $top1['student']->schoolClass?->name }} • NIS {{ $top1['student']->nis }}</p>
                    <div class="mt-4 inline-block px-4 py-1.5 rounded-xl bg-emerald-600 text-white font-mono font-black text-base shadow-sm">
                        +{{ $top1['total_prestasi'] }} Poin Prestasi
                    </div>
                    <div class="mt-4">
                        <a
                            href="{{ route('juara-karakter.certificate', $top1['student']->id) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold shadow-sm"
                        >
                            <x-icon name="printer" class="w-4 h-4" />
                            <span>Cetak Piagam Juara Karakter</span>
                        </a>
                    </div>
                </div>

                <!-- Juara 3 -->
                @if($top3)
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 text-center shadow-sm order-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-700/20 text-amber-900 font-extrabold text-lg flex items-center justify-center mx-auto mb-2 shadow-xs">
                            #3
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ $top3['student']->name }}</h4>
                        <p class="text-xs text-slate-500">{{ $top3['student']->schoolClass?->name }} • NIS {{ $top3['student']->nis }}</p>
                        <div class="mt-3 inline-block px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-mono font-extrabold text-sm">
                            +{{ $top3['total_prestasi'] }} Poin
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('juara-karakter.certificate', $top3['student']->id) }}" target="_blank" class="text-xs font-semibold text-teal-700 hover:underline">
                                Cetak Piagam
                            </a>
                        </div>
                    </div>
                @else
                    <div class="hidden md:block"></div>
                @endif
            </div>
        @endif

        <!-- RANKING TABLE -->
        <x-card title="Tabel Klasemen Peringkat Karakter">
            @if($rankings->isEmpty())
                <x-empty-state icon="trophy" title="Tidak Ditemukan Data" description="Belum ada transaksi poin prestasi pada semester dan filter ini." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Rank</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kelas & Jurusan</th>
                                <th class="py-3 px-3">Poin Prestasi</th>
                                <th class="py-3 px-3">Poin Pelanggaran</th>
                                <th class="py-3 px-3">Kelayakan Juara Karakter</th>
                                <th class="py-3 px-3 text-right">Piagam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rankings as $row)
                                <tr class="hover:bg-slate-50/50 {{ $row['is_eligible'] ? '' : 'opacity-70 bg-slate-50/50' }}">
                                    <td class="py-3 px-3 font-mono font-bold text-sm">
                                        @if($row['rank'] <= 3 && $row['is_eligible'])
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-amber-400 text-amber-950 font-black text-xs">
                                                {{ $row['rank'] }}
                                            </span>
                                        @else
                                            #{{ $row['rank'] }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $row['student']->id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $row['student']->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">NIS: {{ $row['student']->nis }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-semibold text-slate-800">{{ $row['student']->schoolClass?->name }}</span>
                                        <p class="text-[11px] text-slate-500">{{ $row['student']->schoolClass?->major?->name }}</p>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-extrabold text-sm text-emerald-700">
                                        +{{ $row['total_prestasi'] }}
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold {{ $row['total_pelanggaran'] < 0 ? 'text-rose-700' : 'text-slate-400' }}">
                                        {{ $row['total_pelanggaran'] }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($row['is_eligible'])
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                                <span>MEMENUHI SYARAT</span>
                                            </span>
                                        @else
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                    <x-icon name="x-circle" class="w-3 h-3" />
                                                    <span>TIDAK MEMENUHI SYARAT</span>
                                                </span>
                                                @if(!empty($row['disqualification_reasons']))
                                                    <p class="text-[10px] text-rose-700 mt-1 font-medium">
                                                        {{ implode(', ', $row['disqualification_reasons']) }}
                                                    </p>
                                                @elseif($row['total_prestasi'] <= 0)
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Belum memiliki poin prestasi</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        @if($row['is_eligible'])
                                            <a
                                                href="{{ route('juara-karakter.certificate', $row['student']->id) }}"
                                                target="_blank"
                                                class="px-2.5 py-1 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-[11px] font-semibold inline-flex items-center gap-1 shadow-xs"
                                            >
                                                <x-icon name="award" class="w-3.5 h-3.5" />
                                                <span>Piagam</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
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

