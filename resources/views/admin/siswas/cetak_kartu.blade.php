<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Ujian</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        .print-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .card {
            width: 8cm;
            height: 5.5cm;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 15px;
            box-sizing: border-box;
            background-color: #fff;
            page-break-inside: avoid;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0 0;
            font-size: 10px;
        }
        .content table {
            width: 100%;
            font-size: 12px;
        }
        .content td {
            padding: 4px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 70px;
        }
        .separator {
            width: 10px;
            text-align: center;
        }
        .value {
            font-family: monospace;
            font-size: 13px;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #555;
            font-style: italic;
        }
        .no-data {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
            width: 100%;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1cm;
                size: A4 portrait;
            }
        }
        
        .controls {
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-print {
            background-color: #107c41;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <div class="controls no-print">
        <a href="{{ route('admin.siswas.index') }}" class="btn-back">Kembali</a>
        <button class="btn-print" onclick="window.print()">Cetak Sekarang</button>
        <p style="margin-top: 10px; color: #666;">
            Menampilkan {{ count($siswas) }} kartu siswa 
            @if($kelas) untuk kelas <strong>{{ $kelas->nama_kelas }}</strong> @endif
        </p>
    </div>

    <div class="print-container">
        @forelse ($siswas as $siswa)
        <div class="card">
            <div class="header">
                <h3>KARTU PESERTA UJIAN</h3>
                <p>CBT Berbasis Web Tahun Ajaran {{ date('Y') }}/{{ date('Y', strtotime('+1 year')) }}</p>
            </div>
            <div class="content">
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>{{ Str::limit($siswa->nama, 20) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Kelas</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Username</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $siswa->nisn }}</td>
                    </tr>
                    <tr>
                        <td class="label">Password</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>{{ $siswa->password_text ?? 'Tidak Tersedia' }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="footer">
                * Jaga kerahasiaan username & password ini
            </div>
        </div>
        @empty
        <div class="no-data">
            Tidak ada data siswa untuk dicetak.
        </div>
        @endforelse
    </div>

    <script>
        // Otomatis memunculkan dialog print saat halaman dimuat (jika ada data)
        @if(count($siswas) > 0)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
        @endif
    </script>
</body>
</html>
