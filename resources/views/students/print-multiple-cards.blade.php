<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Kartu Siswa Kelas {{ $schoolClass->name }} - SMK Tri Sukses Natar</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f1f5f9;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 86mm);
            grid-gap: 6mm 10mm;
            justify-content: center;
            padding: 10mm 0;
        }
        .id-card {
            width: 86mm;
            height: 54mm;
            background: linear-gradient(135deg, #042f2e 0%, #0f766e 55%, #115e59 100%);
            border-radius: 4mm;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            padding: 3mm 4mm;
            box-sizing: border-box;
            color: white;
            position: relative;
            overflow: hidden;
            border: 1px dashed #0d9488;
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 2mm;
            border-bottom: 1px solid rgba(255,255,255,0.25);
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
        }
        .logo-placeholder {
            width: 6mm;
            height: 6mm;
            background: white;
            border-radius: 1.2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            font-weight: 900;
            font-size: 3.5mm;
        }
        .school-title {
            font-size: 2.5mm;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .card-type {
            font-size: 1.6mm;
            color: #5eead4;
            font-weight: 600;
        }
        .body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 35mm;
        }
        .details {
            width: 52mm;
            display: flex;
            flex-direction: column;
            gap: 1.2mm;
        }
        .item-label {
            font-size: 1.5mm;
            text-transform: uppercase;
            color: #99f6e4;
            font-weight: 600;
        }
        .item-val {
            font-size: 2.3mm;
            font-weight: 700;
            word-break: break-word;
        }
        .qr-section {
            width: 25mm;
            height: 25mm;
            background: white;
            border-radius: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5mm;
            box-sizing: border-box;
        }
        .qr-section svg {
            width: 100%;
            height: 100%;
        }
        .footer {
            position: absolute;
            bottom: 1.5mm;
            left: 4mm;
            right: 4mm;
            display: flex;
            justify-content: space-between;
            font-size: 1.5mm;
            color: #ccfbf1;
            border-top: 0.5px solid rgba(255,255,255,0.15);
            padding-top: 0.8mm;
        }
        .no-print {
            text-align: center;
            padding: 15px;
            background: white;
            border-bottom: 1px solid #cbd5e1;
        }
        .print-btn {
            background: #0f766e;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        @media print {
            body { background: none; }
            .no-print { display: none; }
            .grid-container { padding: 0; }
            .id-card { box-shadow: none; border: 0.5px dashed #64748b; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Seluruh Kartu Kelas {{ $schoolClass->name }} ({{ count($cardsData) }} Kartu)</button>
    </div>

    <div class="grid-container">
        @foreach($cardsData as $item)
            <div class="id-card">
                <div class="header">
                    <div class="logo-placeholder">TS</div>
                    <div>
                        <div class="school-title">{{ $school->school_name }}</div>
                        <div class="card-type">KARTU TANDA SISWA & ABSENSI QR</div>
                    </div>
                </div>

                <div class="body">
                    <div class="details">
                        <div>
                            <div class="item-label">Nama Siswa</div>
                            <div class="item-val">{{ $item['student']->name }}</div>
                        </div>
                        <div>
                            <div class="item-label">NIS</div>
                            <div class="item-val">{{ $item['student']->nis }}</div>
                        </div>
                        <div>
                            <div class="item-label">Kelas</div>
                            <div class="item-val">{{ $schoolClass->name }}</div>
                        </div>
                        <div>
                            <div class="item-label">Angkatan</div>
                            <div class="item-val" style="font-size: 2mm;">{{ $item['student']->enrollment_year }}</div>
                        </div>
                    </div>

                    <div class="qr-section">
                        {!! $item['qrSvg'] !!}
                    </div>
                </div>

                <div class="footer">
                    <span>Berlaku s/d Lulus</span>
                    <span>SMK Tri Sukses Natar</span>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>

