<x-layouts.app headerTitle="Data Pelanggaran Siswa" headerSubtitle="Daftar pelanggaran tata tertib, tingkat keparahan, dan status tindak lanjut">

    <div class="space-y-6 pt-2">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('points.pelanggaran') }}" class="flex flex-wrap items-center gap-3">
                <select name="severity" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Tingkat (Ringan, Sedang, Berat, Fatal)</option>
                    <option value="ringan" {{ request('severity') === 'ringan' ? 'selected' : '' }}>Ringan (-5)</option>
                    <option value="sedang" {{ request('severity') === 'sedang' ? 'selected' : '' }}>Sedang (-10 s/d -20)</option>
                    <option value="berat" {{ request('severity') === 'berat' ? 'selected' : '' }}>Berat (-50 s/d -75)</option>
                    <option value="fatal" {{ request('severity') === 'fatal' ? 'selected' : '' }}>Fatal (-100)</option>
                </select>

                <select name="class_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS..." class="px-3 py-2 rounded-xl border border-slate-200 text-xs">

                <button type="submit" class="px-3 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                    Filter
                </button>
            </form>

            <a href="{{ route('points.create', ['type' => 'pelanggaran']) }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-2 shadow-xs shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Catat Pelanggaran</span>
            </a>
        </div>

        <x-card title="Daftar Kasus Pelanggaran ({{ $transactions->total() }} Data)">
            @if($transactions->isEmpty())
                <x-empty-state icon="shield-check" title="Tidak Ditemukan Kasus Pelanggaran" description="Tidak ada data pelanggaran sesuai filter yang dipilih." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kelas</th>
                                <th class="py-3 px-3">Tingkat</th>
                                <th class="py-3 px-3">Tindakan / Pelanggaran</th>
                                <th class="py-3 px-3">Tindak Lanjut</th>
                                <th class="py-3 px-3 text-right">Poin Minus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">{{ $tx->incident_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('students.show', $tx->student_id) }}" class="font-bold text-slate-900 hover:text-teal-700">
                                            {{ $tx->student->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500">NIS: {{ $tx->student->nis }}</div>
                                    </td>
                                    <td class="py-3 px-3 font-semibold">{{ $tx->student->schoolClass?->name }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->severity?->badgeClass() }}">
                                            {{ $tx->severity?->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 max-w-sm">
                                        <p class="font-bold text-slate-900">{{ $tx->category?->action_name }}</p>
                                        <p class="text-[11px] text-slate-600 mt-0.5">{{ $tx->description }}</p>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($tx->followUp)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->followUp->status->badgeClass() }}">
                                                {{ $tx->followUp->status->label() }}
                                            </span>
                                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $tx->followUp->follow_up_type }}</p>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono">
                                        <span class="text-base font-extrabold text-rose-700">{{ $tx->points }}</span>
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

