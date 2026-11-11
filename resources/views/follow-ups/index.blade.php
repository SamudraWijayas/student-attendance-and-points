<x-layouts.app headerTitle="Tindak Lanjut & Konseling Disiplin" headerSubtitle="Pengelolaan pemanggilan siswa, konseling BK, surat pernyataan, dan penerbitan SP">

    <div class="space-y-6 pt-2">

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs">
                <span class="text-rose-700 font-semibold uppercase">Belum Ditangani:</span>
                <p class="text-xl font-extrabold text-rose-800 mt-0.5">{{ $stats['belum_ditangani'] }} Kasus</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
                <span class="text-amber-700 font-semibold uppercase">Dalam Proses:</span>
                <p class="text-xl font-extrabold text-amber-800 mt-0.5">{{ $stats['dalam_proses'] }} Kasus</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                <span class="text-emerald-700 font-semibold uppercase">Kasus Selesai:</span>
                <p class="text-xl font-extrabold text-emerald-800 mt-0.5">{{ $stats['selesai'] }} Kasus</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900 text-white text-xs">
                <span class="text-teal-300 font-semibold uppercase">Surat Peringatan (SP):</span>
                <p class="text-xl font-extrabold text-white mt-0.5">{{ $stats['total_sp'] }} SP Terbit</p>
            </div>
        </div>

        <!-- CASES TABLE -->
        <x-card title="Daftar Kasus Tindak Lanjut Kedisiplinan">
            @if($followUps->isEmpty())
                <x-empty-state icon="shield-check" title="Tidak Ada Kasus Kedisiplinan" description="Seluruh kasus kedisiplinan telah selesai ditangani atau belum ada pelanggaran baru." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal Kasus</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Pelanggaran Terkait</th>
                                <th class="py-3 px-3">Tindakan Disiplin</th>
                                <th class="py-3 px-3">Status Kasus</th>
                                <th class="py-3 px-3">SP / Panggilan Ortu</th>
                                <th class="py-3 px-3 text-right">Update Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($followUps as $fu)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">{{ $fu->follow_up_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $fu->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $fu->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">Kelas: {{ $fu->student->schoolClass?->name }} • NIS {{ $fu->student->nis }}</div>
                                    </td>
                                    <td class="py-3 px-3 max-w-xs">
                                        <p class="font-bold text-slate-900">{{ $fu->pointTransaction?->category?->action_name }}</p>
                                        <p class="text-[11px] text-rose-600 font-semibold">{{ $fu->pointTransaction?->points }} Poin • Tingkat {{ $fu->pointTransaction?->severity?->label() }}</p>
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-slate-800">{{ $fu->follow_up_type }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $fu->status->badgeClass() }}">
                                            {{ $fu->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($fu->warning_letter_type)
                                            <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-extrabold uppercase text-[10px]">
                                                {{ $fu->warning_letter_type }}
                                            </span>
                                        @endif
                                        @if($fu->parent_invitation_status)
                                            <p class="text-[10px] text-slate-600 mt-0.5">Ortu: {{ ucfirst($fu->parent_invitation_status) }}</p>
                                        @endif
                                        @if(!$fu->warning_letter_type && !$fu->parent_invitation_status)
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <button
                                            type="button"
                                            x-on:click="$dispatch('open-modal', 'modal-fu-{{ $fu->id }}')"
                                            class="px-3 py-1.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-[11px] font-semibold cursor-pointer shadow-xs"
                                        >
                                            Update
                                        </button>

                                        <!-- MODAL UPDATE FOLLOW UP -->
                                        <x-modal id="modal-fu-{{ $fu->id }}" title="Update Penanganan: {{ $fu->student->name }}" maxWidth="md">
                                            <form method="POST" action="{{ route('follow-ups.update', $fu->id) }}" class="space-y-4 text-left">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Penanganan</label>
                                                    <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                                        <option value="belum_ditangani" {{ $fu->status->value === 'belum_ditangani' ? 'selected' : '' }}>Belum Ditangani</option>
                                                        <option value="dalam_proses" {{ $fu->status->value === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                                                        <option value="selesai" {{ $fu->status->value === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Tindakan Disiplin</label>
                                                    <input type="text" name="follow_up_type" value="{{ $fu->follow_up_type }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Surat Peringatan (SP)</label>
                                                    <select name="warning_letter_type" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                        <option value="">-- Tidak Ada SP --</option>
                                                        <option value="sp1" {{ $fu->warning_letter_type === 'sp1' ? 'selected' : '' }}>SP 1</option>
                                                        <option value="sp2" {{ $fu->warning_letter_type === 'sp2' ? 'selected' : '' }}>SP 2</option>
                                                        <option value="sp3" {{ $fu->warning_letter_type === 'sp3' ? 'selected' : '' }}>SP 3</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pemanggilan Orang Tua</label>
                                                    <select name="parent_invitation_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                                        <option value="">-- Tidak Ada Panggilan Ortu --</option>
                                                        <option value="dikirim" {{ $fu->parent_invitation_status === 'dikirim' ? 'selected' : '' }}>Surat Dikirim</option>
                                                        <option value="hadir" {{ $fu->parent_invitation_status === 'hadir' ? 'selected' : '' }}>Orang Tua Hadir</option>
                                                        <option value="tidak_hadir" {{ $fu->parent_invitation_status === 'tidak_hadir' ? 'selected' : '' }}>Orang Tua Tidak Hadir</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Konseling BK</label>
                                                    <textarea name="counseling_notes" rows="2" placeholder="Catatan hasil pembinaan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $fu->counseling_notes }}</textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Penyelesaian Kasus</label>
                                                    <textarea name="resolution_notes" rows="2" placeholder="Hasil akhir / kesepakatan penanganan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $fu->resolution_notes }}</textarea>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-fu-{{ $fu->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Batal</button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold">Simpan Catatan</button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $followUps->links() }}
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>

