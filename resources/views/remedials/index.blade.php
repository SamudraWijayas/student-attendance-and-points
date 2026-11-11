<x-layouts.app headerTitle="Remedial Poin Pelanggaran" headerSubtitle="Pengurangan poin pelanggaran ringan dan sedang melalui kerja bakti sosial atau tugas amal sholeh">

    <div class="space-y-6 pt-2">

        <!-- INFO BANNER -->
        <x-alert type="info">
            <strong>Ketentuan Remedial Poin:</strong> Hanya berlaku untuk pelanggaran kategori <strong>Ringan</strong> dan <strong>Sedang</strong>. Pelanggaran Berat (-50 s/d -75) dan Fatal (-100) <strong>TIDAK DAPAT</strong> diremedial. Poin remedial maksimal sebesar sisa poin pelanggaran kasus tersebut.
        </x-alert>

        <div class="flex items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Riwayat Pelaksanaan Remedial</h3>
            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'modal-add-remedial')"
                class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-2 shadow-xs cursor-pointer"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Input Catatan Remedial</span>
            </button>
        </div>

        <!-- REMEDIAL TABLE -->
        <x-card title="Daftar Remedial Terselesaikan">
            @if($remedials->isEmpty())
                <x-empty-state icon="rotate-ccw" title="Belum Ada Catatan Remedial" description="Belum ada siswa yang menyelesaikan kegiatan tugas remedial." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal Kegiatan</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kasus Pelanggaran Asal</th>
                                <th class="py-3 px-3">Bentuk Kegiatan</th>
                                <th class="py-3 px-3">Catatan Verifikasi</th>
                                <th class="py-3 px-3">Verifikator</th>
                                <th class="py-3 px-3 text-right">Poin Ditebus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($remedials as $rem)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">{{ $rem->activity_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $rem->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $rem->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">Kelas: {{ $rem->student->schoolClass?->name }} • NIS {{ $rem->student->nis }}</div>
                                    </td>
                                    <td class="py-3 px-3 max-w-xs">
                                        <p class="font-bold text-slate-900">{{ $rem->pointTransaction?->category?->action_name }}</p>
                                        <p class="text-[11px] text-slate-500">Poin asal: {{ $rem->pointTransaction?->points }} Poin</p>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-teal-800">{{ $rem->activity_type }}</span>
                                        <p class="text-[11px] text-slate-600 mt-0.5">{{ $rem->description }}</p>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">{{ $rem->verification_notes ?? '-' }}</td>
                                    <td class="py-3 px-3 text-slate-500">{{ $rem->recordedByUser?->name ?? 'Admin' }}</td>
                                    <td class="py-3 px-3 text-right font-mono">
                                        <span class="text-base font-extrabold text-teal-700">+{{ $rem->points_deducted }}</span>
                                        <span class="text-[10px] text-teal-600 font-semibold block">Poin Tebus</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $remedials->links() }}
                </div>
            @endif
        </x-card>

        <!-- MODAL ADD REMEDIAL -->
        <x-modal id="modal-add-remedial" title="Pencatatan Tugas Remedial Poin" maxWidth="lg">
            <form method="POST" action="{{ route('remedials.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Pilih Kasus Pelanggaran yang Diremedial <span class="text-rose-500">*</span>
                    </label>
                    <select name="point_transaction_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold">
                        <option value="">-- Pilih Kasus Pelanggaran (Khusus Ringan & Sedang yang masih bersisa) --</option>
                        @foreach($eligibleViolations as $ev)
                            <option value="{{ $ev->id }}">
                                {{ $ev->student->name }} ({{ $ev->student->schoolClass?->name }}) - {{ $ev->category?->action_name }} [Sisa {{ $ev->remaining_violation_points }} Poin]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Bentuk Kegiatan Remedial <span class="text-rose-500">*</span>
                        </label>
                        <select name="activity_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                            <option value="Kerja Bakti Sosial">Kerja Bakti Sosial</option>
                            <option value="Tugas Amal Sholeh">Tugas Amal Sholeh</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="activity_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Jumlah Poin yang Ditebus / Dikurangi <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="points_deducted" min="1" value="5" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm font-mono font-bold text-teal-800">
                    <p class="text-[11px] text-slate-500 mt-0.5">Nilai poin tidak boleh melebihi sisa minus pada kasus yang dipilih</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Deskripsi Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" rows="2" required placeholder="Contoh: Membersihkan area taman sekolah dan mushola setelah jam pelajaran..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Verifikasi Guru / Pembina
                    </label>
                    <textarea name="verification_notes" rows="2" placeholder="Telah diverifikasi dan dituntaskan dengan penuh tanggung jawab..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-add-remedial')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Remedial</button>
                </div>
            </form>
        </x-modal>

    </div>

</x-layouts.app>

