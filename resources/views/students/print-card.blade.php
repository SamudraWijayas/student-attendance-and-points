<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Kartu Siswa - {{ $student->name }} - SMK Tri Sukses Natar</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }

        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .id-card {
            width: 86mm;
            height: 54mm;
            background: linear-gradient(135deg, #042f2e 0%, #0f766e 55%, #115e59 100%);
            border-radius: 4mm;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            padding: 3mm 4mm;
            box-sizing: border-box;
            color: white;
            position: relative;
            overflow: hidden;
            border: 1px solid #14b8a6;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 2mm;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .logo-placeholder {
            width: 7mm;
            height: 7mm;
            background: white;
            border-radius: 1.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            font-weight: 900;
            font-size: 4mm;
        }

        .school-title {
            font-size: 2.8mm;
            font-weight: 800;
            letter-spacing: 0.2mm;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .card-type {
            font-size: 1.8mm;
            color: #5eead4;
            font-weight: 600;
            letter-spacing: 0.3mm;
            margin-top: 0.4mm;
        }

        .body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 34mm;
        }

        .details {
            width: 50mm;
            display: flex;
            flex-direction: column;
            gap: 1.2mm;
        }

        .item-label {
            font-size: 1.6mm;
            text-transform: uppercase;
            color: #99f6e4;
            font-weight: 600;
        }

        .item-val {
            font-size: 2.5mm;
            font-weight: 700;
            line-height: 1.2;
            word-break: break-word;
        }

        .qr-section {
            width: 26mm;
            height: 26mm;
            background: white;
            border-radius: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5mm;
            box-sizing: border-box;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .qr-section svg {
            width: 100%;
            height: 100%;
        }

        .footer {
            position: absolute;
            bottom: 1.8mm;
            left: 4mm;
            right: 4mm;
            display: flex;
            justify-content: space-between;
            font-size: 1.6mm;
            color: #ccfbf1;
            border-top: 0.5px solid rgba(255, 255, 255, 0.15);
            padding-top: 1mm;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-btn {
            background: #0f766e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .card-container {
                padding: 0;
            }

            .id-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Kartu Siswa Sekarang</button>
    </div>

    <div class="card-container">
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
                        <div class="item-val">{{ $student->name }}</div>
                    </div>
                    <div>
                        <div class="item-label">NIS / NISN</div>
                        <div class="item-val">{{ $student->nis }} {{ $student->nisn ? '/ ' . $student->nisn : '' }}
                        </div>
                    </div>
                    <div>
                        <div class="item-label">Kelas & Jurusan</div>
                        <div class="item-val">{{ $student->schoolClass?->name }}
                            ({{ $student->schoolClass?->major?->name }})</div>
                    </div>
                    <div class="item-val" style="font-size: 2.1mm;">
                        {{ $student->birth_place ?: '-' }},
                        {{ $student->birth_date?->format('d/m/Y') ?? '-' }}
                    </div>
                </div>

                <div class="qr-section">
                    {!! $qrSvg !!}
                </div>
            </div>

            <div class="footer">
                <span>Berlaku s/d Lulus</span>
                <span>SMK Tri Sukses Natar • Lampung</span>
            </div>
        </div>
    </div>

</body>

</html>
