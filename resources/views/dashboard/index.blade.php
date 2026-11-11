<x-layouts.app headerTitle="Dashboard Monitoring Sekolah" headerSubtitle="Pusat data real-time absensi kedatangan dan poin karakter siswa SMK Tri Sukses Natar">

    <div class="space-y-6 pt-2">

        <!-- ROW 1: PRIMARY STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4.5">
            <!-- Total Siswa Aktif -->
            <x-stat-card
                title="Total Siswa Aktif"
                :value="$totalActiveStudents . ' Siswa'"
                icon="users"
                iconBg="bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300"
                :trend="$totalClasses . ' Kelas • ' . $totalMajors . ' Jurusan'"
            />

            <!-- Hadir Tepat Waktu -->
            <x-stat-card
                title="Hadir Tepat Waktu"
                :value="$attendanceStats['hadir'] . ' Siswa'"
                icon="check-circle-2"
                iconBg="bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"
                badgeText="Tepat Waktu"
                badgeClass="bg-emerald-100 text-emerald-800"
            />

            <!-- Terlambat Hari Ini -->
            <x-stat-card
                title="Terlambat Hari Ini"
                :value="$attendanceStats['terlambat'] . ' Siswa'"
                icon="clock-alert"
                iconBg="bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300"
                badgeText="Terlambat"
                badgeClass="bg-amber-100 text-amber-800"
            />

            <!-- Persentase Kehadiran -->
            <x-stat-card
                title="Tingkat Kehadiran"
                :value="$attendanceStats['percentage'] . '%'"
                icon="percent"
                iconBg="bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300"
                :trend="$attendanceStats['belum_hadir'] . ' Siswa belum hadir'"
            />
        </div>

        <!-- ROW 2: CHARACTER POINTS SUMMARY -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4.5">
            <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Total Poin Prestasi</p>
                    <h3 class="text-3xl font-extrabold mt-1 tracking-tight">+{{ number_format($totalPrestasiPoints) }}</h3>
                    <p class="text-xs text-emerald-100/90 mt-1">Semester {{ $activeSemester?->semester_type->label() ?? 'Aktif' }}</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center shrink-0">
                    <x-icon name="award" class="w-7 h-7 text-white" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-gradient-to-br from-rose-600 to-red-700 text-white shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-rose-100">Total Poin Pelanggaran</p>
                    <h3 class="text-3xl font-extrabold mt-1 tracking-tight">-{{ number_format($totalPelanggaranPoints) }}</h3>
                    <p class="text-xs text-rose-100/90 mt-1">Akumulasi Kedisiplinan</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center shrink-0">
                    <x-icon name="shield-alert" class="w-7 h-7 text-white" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 text-white shadow-sm flex items-center justify-between sm:col-span-2 lg:col-span-1">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-300">Calon Juara Karakter</p>
                    @if($topStudents->isNotEmpty() && $topStudents->first()['is_eligible'])
                        <h3 class="text-lg font-bold mt-1 text-teal-300 truncate max-w-50">{{ $topStudents->first()['student']->name }}</h3>
                        <p class="text-xs text-slate-300 mt-0.5">{{ $topStudents->first()['student']->schoolClass?->name }} • +{{ $topStudents->first()['total_prestasi'] }} Poin</p>
                    @else
                        <h3 class="text-sm font-semibold mt-2 text-slate-300">Belum ada kandidat</h3>
                        <p class="text-xs text-slate-400">Syarat: 0 poin sedang/berat/fatal</p>
                    @endif
                </div>
                <div class="w-13 h-13 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                    <x-icon name="trophy" class="w-7 h-7 text-amber-300" />
                </div>
            </div>
        </div>

        <!-- ROW 3: CHARTS SECTION (Chart.js) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Tren Kehadiran 7 Hari Terakhir (Line Chart) -->
            <x-card class="lg:col-span-2" title="Tren Kehadiran 7 Hari Terakhir" subtitle="Perbandingan siswa hadir tepat waktu vs terlambat">
                <div class="h-64 sm:h-72">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </x-card>

            <!-- Komposisi Poin Karakter (Donut Chart) -->
            <x-card title="Poin Karakter Sekolah" subtitle="Perbandingan poin prestasi vs pelanggaran">
                <div class="h-56 sm:h-64 flex items-center justify-center">
                    <canvas id="pointsDoughnutChart"></canvas>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-around text-xs">
                    <div class="flex items-center gap-1.5 font-medium text-emerald-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <span>Prestasi (+{{ $totalPrestasiPoints }})</span>
                    </div>
                    <div class="flex items-center gap-1.5 font-medium text-rose-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <span>Pelanggaran (-{{ $totalPelanggaranPoints }})</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- ROW 4: RECENT SCAN ACTIVITY & TOP 5 RANKING -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Absensi Terbaru Hari Ini -->
            <x-card class="lg:col-span-2" title="Absensi Terbaru Hari Ini" subtitle="5 siswa terakhir yang memindai QR Code hari ini">
                <x-slot:action>
                    <a href="{{ route('attendance.monitoring') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-800 flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </x-slot:action>

                @if($recentAttendances->isEmpty())
                    <x-empty-state icon="qr-code" title="Belum Ada Absensi Hari Ini" description="Silakan arahkan siswa untuk memindai kartu QR Code di halaman scanner." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-500 uppercase bg-slate-50/70 border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-3">Siswa</th>
                                    <th class="py-2.5 px-3">Kelas</th>
                                    <th class="py-2.5 px-3">Waktu</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3 text-right">Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($recentAttendances as $att)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-semibold text-slate-900">{{ $att->student->name }}</div>
                                            <div class="text-[11px] text-slate-500">NIS: {{ $att->student->nis }}</div>
                                        </td>
                                        <td class="py-3 px-3 font-medium text-slate-700">
                                            {{ $att->student->schoolClass?->name }}
                                        </td>
                                        <td class="py-3 px-3 font-mono font-medium text-slate-900">
                                            {{ substr($att->check_in_time, 0, 5) }} WIB
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $att->status->badgeClass() }}">
                                                {{ $att->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right text-slate-500">
                                            <span class="inline-flex items-center gap-1">
                                                <x-icon :name="$att->method->value === 'qr_code' ? 'qr-code' : 'edit-3'" class="w-3.5 h-3.5 text-slate-400" />
                                                <span>{{ $att->method->label() }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

            <!-- Top 5 Juara Karakter Leaderboard -->
            <x-card title="Top 5 Ranking Karakter" subtitle="Kandidat peraih predikat Juara Karakter">
                <x-slot:action>
                    <a href="{{ route('juara-karakter.index') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-800 flex items-center gap-1">
                        <span>Peringkat</span>
                        <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </x-slot:action>

                @if($topStudents->isEmpty())
                    <x-empty-state icon="trophy" title="Belum Ada Data Ranking" description="Data poin prestasi belum tercatat pada semester ini." />
                @else
                    <div class="space-y-3">
                        @foreach($topStudents as $rankItem)
                            <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/40 flex items-center justify-between gap-3 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-lg {{ $loop->first ? 'bg-amber-400 text-amber-950 font-extrabold' : 'bg-slate-200 text-slate-700 font-bold' }} flex items-center justify-center text-xs shrink-0">
                                        #{{ $rankItem['rank'] }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $rankItem['student']->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $rankItem['student']->schoolClass?->name }} • NIS {{ $rankItem['student']->nis }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-block px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-extrabold">
                                        +{{ $rankItem['total_prestasi'] }} P
                                    </span>
                                    @if($rankItem['is_eligible'])
                                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5 flex items-center justify-end gap-0.5">
                                            <x-icon name="check" class="w-3 h-3" />
                                            <span>Memenuhi Syarat</span>
                                        </div>
                                    @else
                                        <div class="text-[10px] text-rose-600 font-semibold mt-0.5" title="{{ implode(', ', $rankItem['disqualification_reasons']) }}">
                                            Tidak Lolos Syarat
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <!-- ROW 5: SISWA TERLAMBAT HARI INI & TRANSAKSI POIN TERKINI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Siswa Terlambat Hari Ini -->
            <x-card title="Daftar Siswa Terlambat Hari Ini" subtitle="Monitoring kedatangan lewat dari batas waktu">
                @if($todayLateStudents->isEmpty())
                    <x-empty-state icon="smile" title="Tidak Ada Siswa Terlambat" description="Seluruh siswa yang hadir hari ini datang tepat waktu." />
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($todayLateStudents as $late)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $late->student->name }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $late->student->schoolClass?->name }} • NIS: {{ $late->student->nis }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-mono font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                        {{ substr($late->check_in_time, 0, 5) }} WIB
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <!-- Prestasi & Pelanggaran Terkini -->
            <x-card title="Catatan Karakter Terkini" subtitle="Aktivitas prestasi dan pelanggaran siswa terkini">
                <div class="space-y-3">
                    @forelse($recentPrestasi->take(2) as $p)
                        <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <x-icon name="award" class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-emerald-950">{{ $p->student->name }} <span class="text-emerald-700 font-normal">({{ $p->student->schoolClass?->name }})</span></p>
                                    <p class="text-[11px] text-emerald-800 mt-0.5 line-clamp-1">{{ $p->description }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-700 shrink-0">+{{ $p->points }}</span>
                        </div>
                    @empty
                    @endforelse

                    @forelse($recentPelanggaran->take(2) as $v)
                        <div class="p-3 rounded-xl bg-rose-50/50 border border-rose-100 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <x-icon name="alert-triangle" class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-rose-950">{{ $v->student->name }} <span class="text-rose-700 font-normal">({{ $v->student->schoolClass?->name }})</span></p>
                                    <p class="text-[11px] text-rose-800 mt-0.5 line-clamp-1">{{ $v->description }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-rose-700 shrink-0">{{ $v->points }}</span>
                        </div>
                    @empty
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

    <!-- Chart.js Scripts Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Attendance Trend Line Chart
            const ctxTrend = document.getElementById('attendanceTrendChart');
            if (ctxTrend && window.Chart) {
                new window.Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: @json($sevenDaysLabels),
                        datasets: [
                            {
                                label: 'Hadir Tepat Waktu',
                                data: @json($sevenDaysHadir),
                                borderColor: '#0f766e',
                                backgroundColor: 'rgba(15, 118, 110, 0.1)',
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#0f766e',
                                pointRadius: 4,
                            },
                            {
                                label: 'Terlambat',
                                data: @json($sevenDaysTerlambat),
                                borderColor: '#d97706',
                                backgroundColor: 'rgba(217, 119, 6, 0.08)',
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#d97706',
                                pointRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans', size: 10 } },
                                grid: { color: 'rgba(226, 232, 240, 0.6)' }
                            },
                            x: {
                                ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Points Doughnut Chart
            const ctxPoints = document.getElementById('pointsDoughnutChart');
            if (ctxPoints && window.Chart) {
                new window.Chart(ctxPoints, {
                    type: 'doughnut',
                    data: {
                        labels: ['Poin Prestasi', 'Poin Pelanggaran'],
                        datasets: [{
                            data: [{{ $totalPrestasiPoints }}, {{ $totalPelanggaranPoints }}],
                            backgroundColor: ['#059669', '#e11d48'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>

