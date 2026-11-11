<x-layouts.app headerTitle="Scan Absensi QR Code" headerSubtitle="Arahkan kartu QR Code siswa ke kamera untuk mencatat absensi kedatangan">

    <div x-data="qrScanner()" x-init="initScanner()" class="space-y-6 pt-2">

        <!-- TOP BAR: LIVE STATS & SERVER CLOCK -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center shrink-0">
                    <x-icon name="users" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Aktif</p>
                    <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="stats.total_active">{{ $todayStats['total_active'] }}</p>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                    <x-icon name="check-circle-2" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-emerald-700 uppercase">Hadir Tepat</p>
                    <p class="text-lg font-bold text-emerald-700" x-text="stats.hadir">{{ $todayStats['hadir'] }}</p>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                    <x-icon name="clock-alert" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-amber-700 uppercase">Terlambat</p>
                    <p class="text-lg font-bold text-amber-700" x-text="stats.terlambat">{{ $todayStats['terlambat'] }}</p>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                    <x-icon name="percent" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-blue-700 uppercase">Kehadiran</p>
                    <p class="text-lg font-bold text-blue-700" x-text="stats.percentage + '%'">{{ $todayStats['percentage'] }}%</p>
                </div>
            </div>
        </div>

        <!-- MAIN SCANNER AREA -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT: CAMERA VIEWPORT (7 COLS) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-slate-950 rounded-3xl border border-slate-800 p-4 sm:p-6 shadow-xl relative overflow-hidden">
                    
                    <!-- Top Status Bar in Viewport -->
                    <div class="flex items-center justify-between gap-4 mb-4 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="font-bold text-white tracking-wide" x-text="cameraStatus">Kamera Siap</span>
                        </div>
                        <div class="text-slate-400 font-mono flex items-center gap-1.5">
                            <x-icon name="clock" class="w-3.5 h-3.5 text-teal-400" />
                            <span>Batas Tepat Waktu: <strong class="text-teal-300">{{ substr($settings->on_time_limit, 0, 5) }} WIB</strong></span>
                        </div>
                    </div>

                    <!-- CAMERA VIEWER ELEMENT -->
                    <div class="relative w-full aspect-4/3 sm:aspect-16/10 rounded-2xl bg-slate-900 overflow-hidden flex items-center justify-center border border-slate-800">
                        <div id="reader" class="w-full h-full"></div>

                        <!-- Scanner Crosshair Overlay -->
                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <div class="w-60 h-60 sm:w-72 sm:h-72 border-2 border-teal-400/80 rounded-2xl relative shadow-[0_0_0_9999px_rgba(15,23,42,0.45)]">
                                <!-- Corner Accents -->
                                <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-teal-300 rounded-tl-lg"></div>
                                <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-teal-300 rounded-tr-lg"></div>
                                <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-teal-300 rounded-bl-lg"></div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-teal-300 rounded-br-lg"></div>
                                
                                <!-- Scanning Laser Line -->
                                <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-teal-400 to-transparent shadow-[0_0_8px_#2dd4bf] animate-bounce mt-28"></div>
                            </div>
                        </div>

                        <!-- Camera Offline Fallback / Loading -->
                        <div x-show="!isCameraRunning" class="absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center p-6 text-center text-white z-20" x-cloak>
                            <x-icon name="video-off" class="w-12 h-12 text-slate-500 mb-3" />
                            <h4 class="text-sm font-semibold">Kamera Belum Aktif</h4>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">Izinkan akses kamera di browser Anda untuk memulai scanning QR Code.</p>
                            <button
                                type="button"
                                x-on:click="startCamera()"
                                class="mt-4 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs shadow-md transition-all cursor-pointer"
                            >
                                Aktifkan Kamera
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Camera Controls -->
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                x-on:click="toggleSound()"
                                class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white flex items-center gap-1.5 transition-colors cursor-pointer"
                            >
                                <template x-if="soundEnabled">
                                    <x-icon name="volume-2" class="w-4 h-4 text-teal-400" />
                                </template>

                                <template x-if="!soundEnabled">
                                    <x-icon name="volume-x" class="w-4 h-4 text-teal-400" />
                                </template>

                                <span x-text="soundEnabled ? 'Suara: Aktif' : 'Suara: Hening'"></span>
                            </button>
                            <button
                                type="button"
                                x-on:click="switchCamera()"
                                class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white flex items-center gap-1.5 transition-colors cursor-pointer"
                            >
                                <x-icon name="switch-camera" class="w-4 h-4 text-teal-400" />
                                <span>Ganti Kamera</span>
                            </button>
                        </div>
                        <div>
                            <button
                                type="button"
                                x-on:click="$dispatch('open-modal', 'modal-manual-attendance')"
                                class="px-3 py-1.5 rounded-lg bg-teal-900/60 hover:bg-teal-900 text-teal-200 border border-teal-700/50 flex items-center gap-1.5 transition-colors cursor-pointer"
                            >
                                <x-icon name="edit-3" class="w-4 h-4" />
                                <span>Input Manual</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: SCAN RESULT CARD & FEED (5 COLS) -->
            <div class="lg:col-span-5 space-y-4">

                <!-- Dynamic Real-Time Scan Result Alert Banner -->
                <div
                    x-show="latestResult !== null"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    :class="{
                        'bg-emerald-50 border-emerald-300 text-emerald-900 dark:bg-emerald-950/40 dark:border-emerald-800': latestResult && latestResult.status === 'hadir',
                        'bg-amber-50 border-amber-300 text-amber-900 dark:bg-amber-950/40 dark:border-amber-800': latestResult && latestResult.status === 'terlambat',
                        'bg-rose-50 border-rose-300 text-rose-900 dark:bg-rose-950/40 dark:border-rose-800': latestResult && !latestResult.success
                    }"
                    class="p-5 rounded-3xl border-2 shadow-lg transition-all"
                    x-cloak
                >
                    <template x-if="latestResult && latestResult.success">
                        <div>
                            <div class="flex items-center justify-between gap-2 border-b border-black/10 pb-3 mb-3">
                                <span
                                    :class="latestResult.status === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white'"
                                    class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5"
                                >
                                    <template x-if="latestResult.status === 'hadir'">
                                        <x-icon name="check-circle-2" class="w-3.5 h-3.5" />
                                    </template>

                                    <template x-if="latestResult.status !== 'hadir'">
                                        <x-icon name="clock-alert" class="w-3.5 h-3.5" />
                                    </template>
                                    <span x-text="latestResult.status_label"></span>
                                </span>
                                <span class="font-mono text-xs font-bold" x-text="latestResult.time + ' WIB'"></span>
                            </div>

                            <div class="flex items-center gap-3.5">
                                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-black/10 flex items-center justify-center font-bold text-xl text-teal-800 shrink-0 overflow-hidden shadow-xs">
                                    <template x-if="latestResult.student.photo">
                                        <img :src="'/storage/' + latestResult.student.photo" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!latestResult.student.photo">
                                        <span x-text="latestResult.student.name.charAt(0)"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-base font-extrabold truncate" x-text="latestResult.student.name"></h4>
                                    <p class="text-xs font-medium opacity-80" x-text="'NIS: ' + latestResult.student.nis + ' • ' + latestResult.student.class"></p>
                                    <p class="text-[11px] opacity-70 truncate" x-text="latestResult.student.major"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="latestResult && !latestResult.success">
                        <div class="flex items-start gap-3">
                            <x-icon name="alert-octagon" class="w-6 h-6 text-rose-600 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-bold text-rose-900 dark:text-rose-200">Absensi Gagal Diterima</h4>
                                <p class="text-xs text-rose-700 dark:text-rose-300 mt-1" x-text="latestResult.message"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Recent Scans Stream -->
                <x-card title="Aktivitas Terkini Hari Ini" subtitle="Riwayat pemindaian barcode/QR code terkini">
                    <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                        <template x-for="(item, index) in liveRecentScans" :key="index">
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white truncate" x-text="item.name"></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="item.class + ' • ' + item.nis"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span
                                        :class="item.status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        x-text="item.status === 'hadir' ? 'Hadir' : 'Terlambat'"
                                    ></span>
                                    <p class="font-mono text-[10px] text-slate-500 mt-0.5" x-text="item.time + ' WIB'"></p>
                                </div>
                            </div>
                        </template>

                        @foreach($recentScans as $recent)
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between gap-3 text-xs" x-show="liveRecentScans.length === 0">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ $recent->student->name }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $recent->student->schoolClass?->name }} • NIS {{ $recent->student->nis }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $recent->status->badgeClass() }}">
                                        {{ $recent->status->label() }}
                                    </span>
                                    <p class="font-mono text-[10px] text-slate-500 mt-0.5">{{ substr($recent->check_in_time, 0, 5) }} WIB</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>

            </div>
        </div>

        <!-- MODAL MANUAL ATTENDANCE -->
        <x-modal id="modal-manual-attendance" title="Pencatatan Absensi Manual" maxWidth="md">
            <form method="POST" action="{{ route('attendance.manual') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa</label>
                    <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach(\App\Models\Student::where('status', \App\Enums\StudentStatus::ACTIVE)->with('schoolClass')->orderBy('name')->get() as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->schoolClass?->name }} - {{ $st->nis }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                        <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Kedatangan</label>
                        <input type="time" name="check_in_time" value="{{ date('H:i') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                        <option value="hadir">Hadir Tepat Waktu</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Manual (Audit Log)</label>
                    <textarea name="notes" rows="2" required placeholder="Contoh: Kartu QR tertinggal di rumah / kamera ponsel siswa rusak" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-manual-attendance')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Absensi</button>
                </div>
            </form>
        </x-modal>

    </div>

    <!-- Scanner Script Logic -->
    <script>
        function qrScanner() {
            return {
                html5QrCode: null,
                isCameraRunning: false,
                cameraStatus: 'Menginisialisasi...',
                soundEnabled: true,
                isProcessing: false,
                latestResult: null,
                liveRecentScans: [],
                stats: {
                    total_active: {{ $todayStats['total_active'] }},
                    hadir: {{ $todayStats['hadir'] }},
                    terlambat: {{ $todayStats['terlambat'] }},
                    percentage: {{ $todayStats['percentage'] }}
                },

                // Web Audio API Synthesizer
                audioCtx: null,
                getAudioContext() {
                    if (!this.audioCtx) {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        this.audioCtx = new AudioContext();
                    }
                    if (this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                    return this.audioCtx;
                },

                playTone(type) {
                    if (!this.soundEnabled) return;
                    try {
                        const ctx = this.getAudioContext();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        const now = ctx.currentTime;

                        if (type === 'hadir') {
                            // Pleasant ascending chime
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(587.33, now); // D5
                            osc.frequency.exponentialRampToValueAtTime(880, now + 0.15); // A5
                            gain.gain.setValueAtTime(0.25, now);
                            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                            osc.start(now);
                            osc.stop(now + 0.35);
                        } else if (type === 'terlambat') {
                            // Double tone for late
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(440, now);
                            osc.frequency.setValueAtTime(349.23, now + 0.12);
                            gain.gain.setValueAtTime(0.28, now);
                            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
                            osc.start(now);
                            osc.stop(now + 0.4);
                        } else {
                            // Error buzzer
                            osc.type = 'sawtooth';
                            osc.frequency.setValueAtTime(180, now);
                            gain.gain.setValueAtTime(0.25, now);
                            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                            osc.start(now);
                            osc.stop(now + 0.35);
                        }
                    } catch (e) {
                        console.error('Audio play error:', e);
                    }
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                },

                initScanner() {
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                startCamera() {
                    const readerElement = document.getElementById('reader');
                    if (!readerElement || !window.Html5Qrcode) {
                        this.cameraStatus = 'Gagal memuat library kamera';
                        return;
                    }

                    this.html5QrCode = new window.Html5Qrcode('reader');

                    const config = {
                        fps: 15,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0,
                    };

                    this.html5QrCode.start(
                        { facingMode: 'environment' },
                        config,
                        (decodedText) => this.onScanSuccess(decodedText),
                        (error) => {}
                    ).then(() => {
                        this.isCameraRunning = true;
                        this.cameraStatus = 'Kamera Aktif & Memindai';
                    }).catch(err => {
                        console.warn('Camera start error:', err);
                        this.isCameraRunning = false;
                        this.cameraStatus = 'Izin kamera ditolak atau kamera tidak terdeteksi';
                    });
                },

                switchCamera() {
                    if (this.html5QrCode && this.isCameraRunning) {
                        this.html5QrCode.stop().then(() => {
                            this.isCameraRunning = false;
                            this.startCamera();
                        });
                    }
                },

                onScanSuccess(decodedText) {
                    if (this.isProcessing) return;
                    this.isProcessing = true;

                    // Send token to server
                    fetch('{{ route('attendance.process-scan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: decodedText })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        if (res.status === 200 && res.body.success) {
                            // Scan SUCCESS
                            this.latestResult = {
                                success: true,
                                status: res.body.status,
                                status_label: res.body.status_label,
                                time: res.body.time,
                                student: res.body.student,
                                message: res.body.message
                            };

                            this.playTone(res.body.status);

                            // Update stats
                            if (res.body.stats) {
                                this.stats = res.body.stats;
                            }

                            // Prepend to live activity
                            this.liveRecentScans.unshift({
                                name: res.body.student.name,
                                class: res.body.student.class,
                                nis: res.body.student.nis,
                                status: res.body.status,
                                time: res.body.time
                            });
                            if (this.liveRecentScans.length > 8) {
                                this.liveRecentScans.pop();
                            }
                        } else {
                            // Scan FAILED
                            this.latestResult = {
                                success: false,
                                message: res.body.message || 'QR Code tidak terdaftar.'
                            };
                            this.playTone('error');
                        }
                    })
                    .catch(err => {
                        this.latestResult = {
                            success: false,
                            message: 'Gagal menghubungi server absensi.'
                        };
                        this.playTone('error');
                    })
                    .finally(() => {
                        // Allow next student scan after 1.2s delay
                        setTimeout(() => {
                            this.isProcessing = false;
                        }, 1200);
                    });
                }
            };
        }
    </script>
</x-layouts.app>

