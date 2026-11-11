<x-layouts.app headerTitle="Data Prestasi Siswa" headerSubtitle="Daftar penghargaan dan poin prestasi akhlak serta akademik siswa">

    <div class="space-y-6 pt-2">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('points.prestasi') }}" class="flex flex-wrap items-center gap-3">
                <select name="class_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS / deskripsi..." class="px-3 py-2 rounded-xl border border-slate-200 text-xs">

                <button type="submit" class="px-3 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold cursor-pointer">
                    Filter
                </button>
            </form>

            <a href="{{ route('points.create', ['type' => 'prestasi']) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-2 shadow-xs shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Input Prestasi</span>
            </a>
        </div>

        <x-card title="Daftar Prestasi Siswa ({{ $transactions->total() }} Data)">
            @if($transactions->isEmpty())
                <x-empty-state icon="award" title="Belum Ada Catatan Prestasi" description="Data prestasi belum dicatatkan untuk kriteria ini." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3">Siswa</th>
                                <th class="py-3 px-3">Kelas</th>
                                <th class="py-3 px-3">Kategori</th>
                                <th class="py-3 px-3">Keterangan</th>
                                <th class="py-3 px-3 text-right">Poin</th>
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
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $tx->category?->category_name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 max-w-sm">{{ $tx->description }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <span class="text-base font-extrabold text-emerald-700 font-mono">+{{ $tx->points }}</span>
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

