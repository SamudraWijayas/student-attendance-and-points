<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Poin Karakter Siswa - {{ $school->school_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #0f172a;
            margin: 0;
            padding: 10mm;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
        }
        .school-info {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }
        .report-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 4px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .sig-box {
            float: right;
            text-align: center;
            width: 60mm;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ $school->school_name }}</div>
        <div class="school-info">{{ $school->address }} • Telp: {{ $school->phone }}</div>
    </div>

    <div class="report-title">
        LAPORAN REKAPITULASI POIN PRESTASI & PELANGGARAN SISWA
    </div>
    <div style="text-align: center; font-size: 8.5pt; color: #475569; margin-bottom: 12px;">
        Dicetak pada: {{ date('d F Y H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 60px;">Tanggal</th>
                <th style="width: 55px;">NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th style="width: 75px;">Jenis</th>
                <th>Tindakan / Pelanggaran</th>
                <th style="width: 45px; text-align: right;">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $tx)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $tx->incident_date->format('d/m/Y') }}</td>
                    <td>{{ $tx->student->nis }}</td>
                    <td><strong>{{ $tx->student->name }}</strong></td>
                    <td>{{ $tx->student->schoolClass?->name }}</td>
                    <td>{{ strtoupper($tx->type->value) }} {{ $tx->severity ? '(' . $tx->severity->label() . ')' : '' }}</td>
                    <td>{{ $tx->category?->action_name }}</td>
                    <td style="text-align: right; font-weight: bold; color: {{ $tx->type->value === 'prestasi' ? '#059669' : '#e11d48' }};">
                        {{ $tx->type->value === 'prestasi' ? '+' : '' }}{{ $tx->points }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">Tidak ada data transaksi poin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div>Natar, {{ date('d F Y') }}</div>
            <div>Kepala Sekolah / Kesiswaan</div>
            <div style="height: 45px;"></div>
            <div><strong>{{ $school->principal_name ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</strong></div>
            <div style="font-size: 8pt; color: #64748b;">NIP: {{ $school->principal_nip ?? '197505122000031005' }}</div>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>

