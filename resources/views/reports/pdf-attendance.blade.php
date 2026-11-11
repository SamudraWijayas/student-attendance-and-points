<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Siswa - {{ $school->school_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
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
            font-size: 14pt;
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
        .report-date {
            text-align: center;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
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
        <div class="school-info">{{ $school->address }} • Telp: {{ $school->phone }} • Email: {{ $school->email }}</div>
    </div>

    <div class="report-title">
        LAPORAN REKAPITULASI ABSENSI KEDATANGAN SISWA
    </div>
    <div class="report-date">
        Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
        @if($selectedClass) • Kelas: {{ $selectedClass->name }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 60px;">NIS</th>
                <th>Nama Lengkap Siswa</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th style="width: 70px;">Jam Datang</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 70px;">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $att)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $att->student->nis }}</td>
                    <td><strong>{{ $att->student->name }}</strong></td>
                    <td>{{ $att->student->schoolClass?->name }}</td>
                    <td>{{ $att->student->schoolClass?->major?->name }}</td>
                    <td>{{ substr($att->check_in_time, 0, 5) }} WIB</td>
                    <td>{{ $att->status->label() }}</td>
                    <td>{{ $att->method->label() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">Tidak ada catatan kehadiran pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div>Natar, {{ date('d F Y') }}</div>
            <div>Kepala Sekolah / Kesiswaan</div>
            <div style="height: 50px;"></div>
            <div><strong>{{ $school->principal_name ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</strong></div>
            <div style="font-size: 8pt; color: #64748b;">NIP: {{ $school->principal_nip ?? '197505122000031005' }}</div>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>

