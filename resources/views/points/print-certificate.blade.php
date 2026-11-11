<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Piagam Juara Karakter - {{ $student->name }} - SMK Tri Sukses Natar</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .certificate-sheet {
            width: 297mm;
            height: 210mm;
            background: #fff;
            padding: 12mm;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .outer-border {
            width: 100%;
            height: 100%;
            border: 4px double #0f766e;
            box-sizing: border-box;
            padding: 4mm;
            position: relative;
        }
        .inner-border {
            width: 100%;
            height: 100%;
            border: 1px solid #14b8a6;
            box-sizing: border-box;
            padding: 8mm 15mm;
            text-align: center;
            position: relative;
        }
        .school-header {
            font-family: 'Arial', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0f766e;
            font-size: 16pt;
            font-weight: 800;
            margin-bottom: 2px;
        }
        .school-sub {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 12px;
        }
        .gold-line {
            width: 120mm;
            height: 2px;
            background: linear-gradient(to right, transparent, #d97706, transparent);
            margin: 0 auto 15px auto;
        }
        .cert-title {
            font-size: 28pt;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 2px;
            margin: 8px 0;
            text-transform: uppercase;
        }
        .cert-subtitle {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #d97706;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .presented-to {
            font-style: italic;
            font-size: 13pt;
            color: #64748b;
            margin-bottom: 10px;
        }
        .student-name {
            font-size: 26pt;
            font-weight: bold;
            color: #0f766e;
            border-bottom: 2px solid #0f766e;
            display: inline-block;
            padding: 0 25px 4px 25px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .student-meta {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #334155;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .cert-body {
            font-size: 12pt;
            line-height: 1.6;
            color: #334155;
            max-width: 190mm;
            margin: 0 auto 30px auto;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 25mm;
            font-family: 'Arial', sans-serif;
        }
        .sig-block {
            text-align: center;
            width: 60mm;
        }
        .sig-date {
            font-size: 10pt;
            color: #475569;
            margin-bottom: 20mm;
        }
        .sig-name {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-role {
            font-size: 9.5pt;
            color: #64748b;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 100;
        }
        .print-btn {
            background: #0f766e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        @media print {
            body { background: none; }
            .no-print { display: none; }
            .certificate-sheet { box-shadow: none; width: 100%; height: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Piagam Penghargaan</button>
    </div>

    <div class="certificate-sheet">
        <div class="outer-border">
            <div class="inner-border">
                
                <div class="school-header">{{ $school->school_name }}</div>
                <div class="school-sub">{{ $school->address }} • Telp: {{ $school->phone }}</div>
                <div class="gold-line"></div>

                <div class="cert-title">PIAGAM PENGHARGAAN</div>
                <div class="cert-subtitle">PREDIKAT JUARA KARAKTER & AKHLAK MULIA</div>

                <div class="presented-to">Diberikan Kepada Siswa Teladan:</div>
                <div class="student-name">{{ $student->name }}</div>
                
                <div class="student-meta">
                    NIS: {{ $student->nis }} • Kelas: {{ $student->schoolClass?->name }} ({{ $student->schoolClass?->major?->name }})
                </div>

                <div class="cert-body">
                    Atas konsistensi budi pekerti luhur, kedisiplinan luar biasa, dan kepatuhan penuh pada tata tertib sekolah dengan perolehan <strong>+{{ $points['prestasi'] }} Poin Prestasi</strong> serta mempertahankan <strong>Nol (0) Poin Pelanggaran</strong> kategori Sedang, Berat, maupun Fatal selama Semester {{ $semester?->semester_type->label() ?? 'Berjalan' }} Tahun Ajaran {{ $semester?->academicYear?->name ?? '2024/2025' }}.
                </div>

                <div class="signatures">
                    <div class="sig-block">
                        <div class="sig-date">Mengetahui,</div>
                        <div class="sig-name">Wali Kelas / BK</div>
                        <div class="sig-role">SMK Tri Sukses Natar</div>
                    </div>

                    <div class="sig-block">
                        <div class="sig-date">Natar, {{ date('d F Y') }}</div>
                        <div class="sig-name">{{ $school->principal_name ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
                        <div class="sig-role">Kepala Sekolah (NIP: {{ $school->principal_nip ?? '197505122000031005' }})</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

