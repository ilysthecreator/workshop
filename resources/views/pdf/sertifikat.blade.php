<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Penghargaan</title>
    <style>
        /* Reset default margin */
        @page {
            margin: 0px;
            size: landscape;
        }
        body {
            margin: 0px;
            font-family: 'Helvetica', Arial, sans-serif; /* Font standar mirip desain */
            text-align: center;
            color: #333;
        }
        
        /* Container utama: Gunakan padding, JANGAN height 100% */
        .container {
            padding-top: 40px;
            padding-bottom: 20px;
            padding-left: 50px;
            padding-right: 50px;
        }

        /* Tipografi Judul */
        .header-small {
            font-size: 14px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #555;
            margin-top: 20px;
        }
        .header-large {
            font-size: 48px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
            color: #000;
        }

        /* Bagian Isi */
        .diberikan-kepada {
            font-family: 'Times New Roman', serif; /* Font serif italic */
            font-style: italic;
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #666;
        }
        
        .nama-peserta {
            font-size: 42px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #222;
        }

        .predikat {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            color: #E67E22; /* Warna Oranye sesuai gambar */
            margin-top: 15px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .deskripsi-kegiatan {
            font-size: 12px;
            line-height: 1.5;
            text-transform: uppercase;
            color: #444;
            max-width: 700px;
            margin: 0 auto; /* Center block */
        }

        /* Tanda Tangan */
        .table-ttd {
            width: 100%;
            margin-top: 60px; /* Jarak aman agar tidak kena page break */
            border: none;
        }
        .table-ttd td {
            vertical-align: bottom;
            text-align: center;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .ttd-jabatan {
            font-size: 12px;
            color: #555;
        }
        .tanggal {
            font-size: 12px;
            margin-bottom: 60px; /* Ruang untuk tanda tangan basah */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header-small">SERTIFIKAT</div>
        <div class="header-large">PENGHARGAAN</div>

        <div class="diberikan-kepada">Diberikan Kepada:</div>
        <div class="nama-peserta">{{ $nama }}</div>

        <div class="predikat">SEBAGAI {{ $predikat ?? 'KELOMPOK TERBAIK' }}</div>

        <div class="deskripsi-kegiatan">
            DALAM KEGIATAN<br>
            <strong>MAGANG HIMPUNAN D4 TEKNIK INFORMATIKA</strong><br>
            DEPARTEMEN PENGABDIAN MASYARAKAT 2025
        </div>

        <table class="table-ttd">
            <tr>
                <td width="35%">
                    <div style="height: 80px;"></div> <div class="ttd-name">(Nama Ketua Departemen)</div>
                    <div class="ttd-jabatan">Ka. Dept Pengabdian Masyarakat</div>
                </td>
                
                <td width="30%">
                    </td>

                <td width="35%">
                    <div class="tanggal">Surabaya, {{ date('d F Y') }}</div>
                    
                    <div style="height: 20px;"></div> <div class="ttd-name">(Nama Ketua Himpunan)</div>
                    <div class="ttd-jabatan">Ketua Himpunan - Kabinet Selaras</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>