<x-layouts.app headerTitle="Detail Profil Siswa" :headerSubtitle="$student->name . ' (' . $student->schoolClass?->name . ' - NIS: ' . $student->nis . ')'">

    <div x-data="{ activeTab: 'profil' }" class="space-y-6 pt-2">

        <!-- HEADER BANNER & STUDENT HIGHLIGHT -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-teal-800 text-teal-100 flex items-center justify-center font-extrabold text-2xl sm:text-3xl shrink-0 border-2 border-teal-600/40 overflow-hidden shadow-md">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $student->name }}</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $student->status->badgeClass() }}">
                            {{ $student->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">
                        NIS: <strong class="text-slate-700 font-mono">{{ $student->nis }}</strong>
                        @if($student->nisn) • NISN: <span class="font-mono">{{ $student->nisn }}</span> @endif
                        • Kelas: <strong class="text-teal-700">{{ $student->schoolClass?->name }}</strong>
                        ({{ $student->schoolClass?->major?->name }})
                    </p>
                </div>
            </div>

            <!-- Quick Points Badge in Header -->
            <div class="flex items-center gap-3">
                <div class="px-3.5 py-2 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
                    <p class="text-[10px] font-bold text-emerald-700 uppercase">Poin Prestasi</p>
                    <p class="text-lg font-extrabold text-emerald-700">+{{ $pointsSummary['prestasi'] }}</p>
                </div>
                <div class="px-3.5 py-2 rounded-2xl bg-rose-50 border border-rose-200 text-center">
                    <p class="text-[10px] font-bold text-rose-700 uppercase">Poin Pelanggaran</p>
                    <p class="text-lg font-extrabold text-rose-700">{{ $pointsSummary['pelanggaran'] }}</p>
                </div>
                <div class="px-3.5 py-2 rounded-2xl bg-slate-900 text-white text-center">
                    <p class="text-[10px] font-bold text-teal-300 uppercase">Net Karakter</p>
                    <p class="text-lg font-extrabold">{{ $pointsSummary['total_net'] }}</p>
                </div>
            </div>
        </div>

        <!-- 7 TABS NAVIGATION -->
        <div class="border-b border-slate-200 dark:border-slate-800 flex items-center gap-1 overflow-x-auto scrollbar-none text-xs font-semibold">
            <button
                type="button"
                x-on:click="activeTab = 'profil'"
                :class="activeTab === 'profil' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="user" class="w-4 h-4" />
                <span>1. Profil</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'qrcode'"
                :class="activeTab === 'qrcode' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="qr-code" class="w-4 h-4" />
                <span>2. QR Code & Kartu</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'absensi'"
                :class="activeTab === 'absensi' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="calendar-check" class="w-4 h-4" />
                <span>3. Absensi</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'prestasi'"
                :class="activeTab === 'prestasi' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="award" class="w-4 h-4" />
                <span>4. Prestasi (+{{ $pointsSummary['prestasi'] }})</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'pelanggaran'"
                :class="activeTab === 'pelanggaran' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="alert-triangle" class="w-4 h-4" />
                <span>5. Pelanggaran ({{ $pointsSummary['pelanggaran'] }})</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'pembinaan'"
                :class="activeTab === 'pembinaan' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="shield-check" class="w-4 h-4" />
                <span>6. Pembinaan & SP</span>
            </button>
            <button
                type="button"
                x-on:click="activeTab = 'rekam-jejak'"
                :class="activeTab === 'rekam-jejak' ? 'border-teal-700 text-teal-700 border-b-2 bg-teal-50/50' : 'text-slate-500 hover:text-slate-900'"
                class="px-4 py-3 rounded-t-xl transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <x-icon name="archive" class="w-4 h-4" />
                <span>7. Rekam Jejak Semester</span>
            </button>
        </div>

        <!-- TAB 1: PROFIL -->
        <div x-show="activeTab === 'profil'" class="space-y-6">
            <x-card title="Biodata Lengkap Siswa">
                <x-slot:action>
                    <a href="{{ route('students.edit', $student->id) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit Biodata</span>
                    </a>
                </x-slot:action>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-xs">
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Nama Lengkap:</span>
                        <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $student->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">NIS / NISN:</span>
                        <p class="text-sm font-mono font-bold text-slate-900 mt-0.5">{{ $student->nis }} / {{ $student->nisn ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Jenis Kelamin:</span>
                        <p class="text-sm font-medium text-slate-800 mt-0.5">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Tempat, Tanggal Lahir:</span>
                        <p class="text-sm font-medium text-slate-800 mt-0.5">{{ $student->birth_place }}, {{ $student->birth_date->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Kelas & Jurusan:</span>
                        <p class="text-sm font-bold text-teal-800 mt-0.5">{{ $student->schoolClass?->name }} ({{ $student->schoolClass?->major?->name }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Angkatan (Tahun Masuk):</span>
                        <p class="text-sm font-mono font-medium text-slate-800 mt-0.5">{{ $student->enrollment_year }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Nomor Telepon:</span>
                        <p class="text-sm font-medium text-slate-800 mt-0.5">{{ $student->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-semibold">Status Siswa:</span>
                        <p class="text-sm font-bold mt-0.5 text-teal-700">{{ $student->status->label() }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-slate-400 uppercase font-semibold">Alamat Tempat Tinggal:</span>
                        <p class="text-sm text-slate-700 mt-0.5 leading-relaxed">{{ $student->address ?? '-' }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- TAB 2: QR CODE & KARTU SISWA -->
        <div x-show="activeTab === 'qrcode'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- QR Code Viewer Card (5 Cols) -->
                <x-card class="md:col-span-5 text-center" title="Identitas QR Code Siswa">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center my-2">
                        @if($qrSvg)
                            <div class="w-48 h-48">{!! $qrSvg !!}</div>
                        @else
                            <p class="text-xs text-rose-500 font-semibold">QR Code belum dibuat.</p>
                        @endif
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 mt-2 truncate" title="{{ $student->qrCode?->token }}">
                        Token: {{ $student->qrCode?->token ?? '-' }}
                    </p>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-center gap-2">
                        <a
                            href="{{ route('students.print-card', $student->id) }}"
                            target="_blank"
                            class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm"
                        >
                            <x-icon name="printer" class="w-4 h-4" />
                            <span>Cetak Kartu Siswa</span>
                        </a>

                        <form method="POST" action="{{ route('students.regenerate-qr', $student->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin meregenerasi QR Code ini? Kode kartu lama akan dinonaktifkan.');">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold flex items-center gap-1.5 cursor-pointer"
                            >
                                <x-icon name="rotate-ccw" class="w-4 h-4" />
                                <span>Regenerasi QR Baru</span>
                            </button>
                        </form>
                    </div>
                </x-card>

                <!-- Student ID Card Preview Card (7 Cols) -->
                <x-card class="md:col-span-7" title="Pratinjau Kartu Pelajar">
                    <div class="w-full max-w-sm mx-auto bg-gradient-to-br from-teal-900 via-teal-800 to-slate-900 text-white rounded-3xl p-5 shadow-xl border border-teal-700/50 relative overflow-hidden">
                        <!-- Top School Badge -->
                        <div class="flex items-center gap-3 border-b border-teal-700/60 pb-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-white shrink-0">
                                <x-icon name="graduation-cap" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold tracking-tight uppercase leading-tight">{{ $school->school_name }}</h3>
                                <p class="text-[10px] text-teal-300 font-semibold tracking-wider">KARTU ABSENSI & IDENTITAS SISWA</p>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="flex items-center justify-between gap-4">
                            <div class="space-y-1 text-xs min-w-0">
                                <p class="text-[10px] uppercase text-teal-300 font-semibold">Nama Siswa</p>
                                <h4 class="text-sm font-bold truncate">{{ $student->name }}</h4>
                                
                                <p class="text-[10px] uppercase text-teal-300 font-semibold pt-1">NIS / Kelas</p>
                                <p class="font-mono font-bold">{{ $student->nis }} • {{ $student->schoolClass?->name }}</p>

                                <p class="text-[10px] uppercase text-teal-300 font-semibold pt-1">Jurusan</p>
                                <p class="text-[11px] truncate text-slate-200">{{ $student->schoolClass?->major?->name }}</p>
                            </div>

                            <div class="bg-white p-2 rounded-xl shrink-0 shadow-md">
                                @if($qrSvg)
                                    <div class="w-24 h-24">{!! $qrSvg !!}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Bottom Card Footer -->
                        <div class="mt-4 pt-2.5 border-t border-teal-700/50 flex items-center justify-between text-[9px] text-teal-300">
                            <span>Berlaku s/d Lulus</span>
                            <span>SMK Tri Sukses Natar</span>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <!-- TAB 3: ABSENSI -->
        <div x-show="activeTab === 'absensi'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 text-xs">
                    <span class="text-slate-500 font-medium">Total Absensi:</span>
                    <p class="text-base font-bold text-slate-900 mt-0.5">{{ $totalAttendance }} Hari</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                    <span class="text-emerald-700 font-medium">Hadir Tepat:</span>
                    <p class="text-base font-bold text-emerald-800 mt-0.5">{{ $hadirCount }} Kali</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
                    <span class="text-amber-700 font-medium">Terlambat:</span>
                    <p class="text-base font-bold text-amber-800 mt-0.5">{{ $lateCount }} Kali</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-teal-50 border border-teal-200 text-xs">
                    <span class="text-teal-700 font-medium">Tingkat Tepat Waktu:</span>
                    <p class="text-base font-bold text-teal-800 mt-0.5">{{ $attendancePercentage }}%</p>
                </div>
            </div>

            <x-card title="20 Catatan Absensi Terakhir">
                @if($student->attendances->isEmpty())
                    <x-empty-state icon="calendar-x" title="Belum Ada Catatan Absensi" description="Siswa belum pernah melakukan pemindaian kartu absensi." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3">Jam Datang</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3">Metode</th>
                                    <th class="py-2.5 px-3">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($student->attendances as $att)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3 font-semibold">{{ $att->attendance_date->translatedFormat('d M Y') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold">{{ substr($att->check_in_time, 0, 5) }} WIB</td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $att->status->badgeClass() }}">
                                                {{ $att->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-500">{{ $att->method->label() }}</td>
                                        <td class="py-2.5 px-3 text-slate-500">{{ $att->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- TAB 4: PRESTASI -->
        <div x-show="activeTab === 'prestasi'" class="space-y-6" x-cloak>
            <x-card title="Riwayat Poin Prestasi (+{{ $pointsSummary['prestasi'] }} Poin)">
                <x-slot:action>
                    <a href="{{ route('points.create', ['student_id' => $student->id, 'type' => 'prestasi']) }}" class="px-3 py-1.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-1.5">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Input Prestasi Baru</span>
                    </a>
                </x-slot:action>

                @php
                    $prestasiList = $student->pointTransactions->where('type', \App\Enums\PointType::PRESTASI);
                @endphp

                @if($prestasiList->isEmpty())
                    <x-empty-state icon="award" title="Belum Ada Catatan Prestasi" description="Siswa belum memiliki perolehan poin prestasi pada semester ini." />
                @else
                    <div class="space-y-3">
                        @foreach($prestasiList as $tx)
                            <div class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 flex items-start justify-between gap-4">
                                <div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $tx->category?->category_name }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $tx->category?->action_name }}</h4>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $tx->description }}</p>
                                    <p class="text-[11px] text-slate-400 mt-2">
                                        Tanggal: {{ $tx->incident_date->translatedFormat('d M Y') }} • Dicatat oleh: {{ $tx->recordedByUser?->name ?? 'Admin' }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xl font-extrabold text-emerald-700 font-mono">+{{ $tx->points }}</span>
                                    <p class="text-[10px] text-emerald-600 font-semibold">POIN</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <!-- TAB 5: PELANGGARAN -->
        <div x-show="activeTab === 'pelanggaran'" class="space-y-6" x-cloak>
            <x-card title="Riwayat Poin Pelanggaran ({{ $pointsSummary['pelanggaran'] }} Poin)">
                <x-slot:action>
                    <a href="{{ route('points.create', ['student_id' => $student->id, 'type' => 'pelanggaran']) }}" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Catat Pelanggaran</span>
                    </a>
                </x-slot:action>

                @php
                    $pelanggaranList = $student->pointTransactions->where('type', \App\Enums\PointType::PELANGGARAN);
                @endphp

                @if($pelanggaranList->isEmpty())
                    <x-empty-state icon="smile" title="Bersih dari Pelanggaran" description="Siswa tidak memiliki catatan pelanggaran tata tertib." />
                @else
                    <div class="space-y-3">
                        @foreach($pelanggaranList as $tx)
                            <div class="p-4 rounded-2xl border border-rose-100 bg-rose-50/30 flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->severity?->badgeClass() }}">
                                            Tingkat {{ $tx->severity?->label() }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-600">{{ $tx->category?->category_name }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $tx->category?->action_name }}</h4>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $tx->description }}</p>
                                    <p class="text-[11px] text-slate-400 mt-2">
                                        Tanggal: {{ $tx->incident_date->translatedFormat('d M Y') }} • Dicatat oleh: {{ $tx->recordedByUser?->name ?? 'Admin' }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xl font-extrabold text-rose-700 font-mono">{{ $tx->points }}</span>
                                    <p class="text-[10px] text-rose-600 font-semibold">POIN</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <!-- TAB 6: PEMBINAAN & REMEDIAL -->
        <div x-show="activeTab === 'pembinaan'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Tindak Lanjut & Konseling -->
                <x-card title="Tindak Lanjut & Konseling BK">
                    @if($student->violationFollowUps->isEmpty())
                        <x-empty-state icon="shield-check" title="Tidak Ada Kasus Disiplin" description="Siswa belum pernah mendapatkan pemanggilan konseling atau surat peringatan." />
                    @else
                        <div class="space-y-3">
                            @foreach($student->violationFollowUps as $fu)
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-white">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-xs text-slate-900">{{ $fu->follow_up_type }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fu->status->badgeClass() }}">
                                            {{ $fu->status->label() }}
                                        </span>
                                    </div>
                                    @if($fu->warning_letter_type)
                                        <p class="text-xs font-bold text-rose-600 mt-1 uppercase">Peringatan: {{ $fu->warning_letter_type }}</p>
                                    @endif
                                    @if($fu->counseling_notes)
                                        <p class="text-xs text-slate-600 mt-1 italic">{{ $fu->counseling_notes }}</p>
                                    @endif
                                    <p class="text-[10px] text-slate-400 mt-2">Tanggal: {{ $fu->follow_up_date->translatedFormat('d M Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <!-- Remedial Poin -->
                <x-card title="Riwayat Remedial Poin (Pemutihan)">
                    @if($student->remedialPoints->isEmpty())
                        <x-empty-state icon="rotate-ccw" title="Belum Ada Remedial" description="Siswa belum pernah mencatatkan kegiatan tugas amal sholeh atau kerja bakti sosial." />
                    @else
                        <div class="space-y-3">
                            @foreach($student->remedialPoints as $rem)
                                <div class="p-3.5 rounded-xl border border-teal-200 bg-teal-50/40">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-xs text-teal-950">{{ $rem->activity_type }}</span>
                                        <span class="text-xs font-extrabold text-teal-700 font-mono">+{{ $rem->points_deducted }} Poin Ditebus</span>
                                    </div>
                                    <p class="text-xs text-slate-700 mt-1">{{ $rem->description }}</p>
                                    @if($rem->verification_notes)
                                        <p class="text-[11px] text-teal-800 mt-1 font-medium">Verifikasi: {{ $rem->verification_notes }}</p>
                                    @endif
                                    <p class="text-[10px] text-slate-400 mt-2">Tanggal: {{ $rem->activity_date->translatedFormat('d M Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        </div>

        <!-- TAB 7: REKAM JEJAK SEMESTER -->
        <div x-show="activeTab === 'rekam-jejak'" class="space-y-6" x-cloak>
            <x-card title="Arsip Snapshot Poin & Rekam Jejak Antar Semester" subtitle="Sesuai peraturan sekolah, pelanggaran berat, fatal, dan SP tetap tersimpan permanen sampai siswa lulus">
                @if($student->snapshots->isEmpty())
                    <x-empty-state icon="archive" title="Belum Ada Snapshot Semester" description="Snapshot semester akan terbuat otomatis saat admin menjalankan evaluasi dan reset semester." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-3">Tahun Ajaran / Semester</th>
                                    <th class="py-2.5 px-3">Prestasi</th>
                                    <th class="py-2.5 px-3">Ringan</th>
                                    <th class="py-2.5 px-3">Sedang</th>
                                    <th class="py-2.5 px-3">Berat (Tetap)</th>
                                    <th class="py-2.5 px-3">Fatal (Tetap)</th>
                                    <th class="py-2.5 px-3">SP Terakhir</th>
                                    <th class="py-2.5 px-3">Tanggal Arsip</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($student->snapshots as $snap)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3 px-3 font-bold text-slate-900">
                                            {{ $snap->academicYear?->name }} - {{ $snap->semester?->semester_type->label() }}
                                        </td>
                                        <td class="py-3 px-3 text-emerald-700 font-extrabold">+{{ $snap->total_achievement_points }}</td>
                                        <td class="py-3 px-3 text-slate-600">-{{ $snap->total_minor_violations }}</td>
                                        <td class="py-3 px-3 text-orange-600 font-bold">-{{ $snap->total_moderate_violations }}</td>
                                        <td class="py-3 px-3 text-rose-700 font-extrabold">-{{ $snap->total_heavy_violations }}</td>
                                        <td class="py-3 px-3 text-red-900 font-extrabold">-{{ $snap->total_fatal_violations }}</td>
                                        <td class="py-3 px-3">
                                            @if($snap->active_sp_level)
                                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold uppercase text-[10px]">
                                                    {{ $snap->active_sp_level }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-slate-500">{{ $snap->snapshot_date->translatedFormat('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

    </div>

</x-layouts.app>

