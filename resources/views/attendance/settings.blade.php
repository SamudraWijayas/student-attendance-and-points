<x-layouts.app headerTitle="Pengaturan Jam Absensi" headerSubtitle="Konfigurasi waktu absensi kedatangan dan toleransi keterlambatan sekolah">

    <div class="max-w-3xl space-y-6 pt-2">

        <x-card title="Konfigurasi Jadwal & Toleransi Absensi" subtitle="Perubahan pengaturan akan langsung diterapkan pada penentuan status absensi QR Code">
            <form method="POST" action="{{ route('attendance.settings.update') }}" class="space-y-6">
                @csrf

                <!-- Jam Operasional Absensi -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jam Mulai Absensi
                        </label>
                        <input
                            type="time"
                            name="check_in_start"
                            value="{{ $settings->check_in_start }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-mono font-bold"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Scanner menolak sebelum jam ini</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 text-teal-700">
                            Batas Tepat Waktu
                        </label>
                        <input
                            type="time"
                            name="on_time_limit"
                            value="{{ $settings->on_time_limit }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border-2 border-teal-500 bg-white dark:bg-slate-800 text-sm font-mono font-bold text-teal-800"
                        >
                        <p class="text-[11px] text-teal-600 mt-1">Setelah jam ini: TERLAMBAT</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jam Akhir Absensi
                        </label>
                        <input
                            type="time"
                            name="check_in_end"
                            value="{{ $settings->check_in_end }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-mono font-bold"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Scanner ditutup setelah jam ini</p>
                    </div>
                </div>

                <!-- Toleransi Menit -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Toleransi Keterlambatan (Menit)
                    </label>
                    <input
                        type="number"
                        name="tolerance_minutes"
                        value="{{ $settings->tolerance_minutes }}"
                        min="0"
                        max="60"
                        class="w-48 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold"
                    >
                    <p class="text-[11px] text-slate-500 mt-1">Jumlah menit dispensasi sebelum siswa resmi ditandai berstatus Terlambat</p>
                </div>

                <!-- Hari Aktif Sekolah -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Hari Aktif Sekolah
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            $activeDays = $settings->active_days ?? [];
                        @endphp
                        @foreach($allDays as $day)
                            <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-2.5 text-xs font-semibold cursor-pointer hover:bg-slate-50 transition-colors">
                                <input
                                    type="checkbox"
                                    name="active_days[]"
                                    value="{{ $day }}"
                                    {{ in_array($day, $activeDays) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500"
                                >
                                <span>{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Action Button -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5 flex items-center justify-end">
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-sm flex items-center gap-2 cursor-pointer"
                    >
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Simpan Perubahan Pengaturan</span>
                    </button>
                </div>
            </form>
        </x-card>

    </div>

</x-layouts.app>

