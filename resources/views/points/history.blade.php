<x-layouts.app headerTitle="Riwayat Transaksi Poin" headerSubtitle="Arsip seluruh transaksi poin dengan fitur pembatalan transaksi berjejak audit">

    <div class="space-y-6 pt-2">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('points.history') }}" class="flex flex-wrap items-center gap-3">
                <select name="type" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Jenis (Prestasi & Pelanggaran)</option>
                    <option value="prestasi" {{ request('type') === 'prestasi' ? 'selected' : '' }}>Khusus Prestasi (+)</option>
                    <option value="pelanggaran" {{ request('type') === 'pelanggaran' ? 'selected' : '' }}>Khusus Pelanggaran (-)</option>
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS..." class="px-3 py-2 rounded-xl border border-slate-200 text-xs">

                <button type="submit" class="px-3 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <x-card title="Daftar Log Transaksi Poin ({{ $transactions->total() }} Data)">
            @if($transactions->isEmpty())
                <x-empty-state icon="list-checks" title="Belum Ada Riwayat Transaksi" description="Tidak ditemukan catatan transaksi poin yang sesuai." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Tindakan / Kategori</th>
                                <th class="py-3 px-3">Poin</th>
                                <th class="py-3 px-3">Pencatat</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-50/50 {{ $tx->is_cancelled ? 'opacity-60 bg-slate-50/80' : '' }}">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">{{ $tx->incident_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $tx->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $tx->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">NIS: {{ $tx->student->nis }} • {{ $tx->student->schoolClass?->name }}</div>
                                    </td>
                                    <td class="py-3 px-3 max-w-sm">
                                        <span class="font-bold text-slate-900">{{ $tx->category?->action_name }}</span>
                                        <p class="text-[11px] text-slate-600 truncate">{{ $tx->description }}</p>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-extrabold text-sm">
                                        <span class="{{ $tx->type->value === 'prestasi' ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $tx->type->value === 'prestasi' ? '+' : '' }}{{ $tx->points }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-500">{{ $tx->recordedByUser?->name ?? 'Admin' }}</td>
                                    <td class="py-3 px-3">
                                        @if($tx->is_cancelled)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">
                                                Dibatalkan
                                            </span>
                                            <p class="text-[10px] text-rose-600 mt-0.5" title="{{ $tx->cancellation_reason }}">Alasan: {{ $tx->cancellation_reason }}</p>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Valid / Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        @if(!$tx->is_cancelled)
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', 'modal-cancel-{{ $tx->id }}')"
                                                class="px-2.5 py-1 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-semibold cursor-pointer"
                                            >
                                                Batalkan
                                            </button>

                                            <!-- MODAL CANCEL TRANSACTION -->
                                            <x-modal id="modal-cancel-{{ $tx->id }}" title="Pembatalan Transaksi Poin: {{ $tx->student->name }}" maxWidth="md">
                                                <form method="POST" action="{{ route('points.cancel', $tx->id) }}" class="space-y-4 text-left">
                                                    @csrf
                                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                                                        Data transaksi asli tidak akan dihapus, melainkan ditandai dibatalkan dengan pencatatan audit identitas admin dan alasan pembatalan.
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                            Alasan Pembatalan <span class="text-rose-500">*</span>
                                                        </label>
                                                        <textarea name="cancellation_reason" rows="3" required placeholder="Contoh: Salah input nama siswa / verifikasi ulang guru BK bahwa laporan tidak terbukti..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs leading-relaxed"></textarea>
                                                    </div>

                                                    <div class="flex items-center justify-end gap-2 pt-2">
                                                        <button type="button" x-on:click="$dispatch('close-modal', 'modal-cancel-{{ $tx->id }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold">Tutup</button>
                                                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Konfirmasi Batalkan Transaksi</button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>

