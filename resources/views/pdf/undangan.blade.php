<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Universitas Airlangga</title>
    <style>
        /* Pengaturan Halaman A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 2.5cm 2.5cm; /* Margin standar surat resmi */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.15; /* Spasi baris rapat khas surat dinas */
            color: #000;
        }

        /* KOP SURAT */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000; /* Garis ganda tebal tipis */
            padding-bottom: 10px;
            margin-bottom: 25px;
            position: relative;
        }
        .logo-unair {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px; /* Sesuaikan ukuran logo */
            height: auto;
        }
        .kop-text {
            margin-left: 90px; /* Memberi ruang untuk logo di kiri */
        }
        .kop-kementerian {
            font-size: 14pt;
            text-transform: uppercase;
            margin: 0;
            font-weight: normal;
        }
        .kop-univ {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .kop-unit {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .kop-alamat {
            font-size: 10pt;
            margin-top: 5px;
            font-weight: normal;
        }

        /* JUDUL SURAT */
        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-teks {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .nomor-surat {
            font-size: 12pt;
            margin-top: 2px;
        }

        /* ISI SURAT */
        .isi-surat {
            text-align: justify;
            margin-bottom: 10px;
        }
        
        /* TABEL DETAIL (Hari, Tanggal, dll) */
        .tabel-detail {
            margin-left: 40px; /* Indentasi */
            margin-top: 10px;
            margin-bottom: 10px;
            width: 90%;
        }
        .tabel-detail td {
            vertical-align: top;
            padding-bottom: 5px;
        }

        /* TANDA TANGAN */
        .ttd-container {
            margin-top: 40px;
            width: 100%;
            text-align: right; /* Default align right untuk container */
        }
        .ttd-box {
            display: inline-block;
            width: 300px; /* Lebar area tanda tangan */
            text-align: left; /* Teks di dalam box rata kiri (biasanya) atau tengah */
            margin-right: 0;
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px; /* Ruang tanda tangan */
        }

        /* FOOTER / VALIDASI */
        .footer-validasi {
            margin-top: 50px;
            font-size: 9pt;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="kop-surat">
        <div class="kop-text">
            <h3 class="kop-kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h3>
            <h2 class="kop-univ">UNIVERSITAS AIRLANGGA</h2>
            <h3 class="kop-unit">DIREKTORAT PENGEMBANGAN KARIR,<br>INKUBASI KEWIRAUSAHAAN, DAN ALUMNI</h3>
            <p class="kop-alamat">
                Kampus C Mulyorejo Surabaya 60115 Telp/Fax (031) 5915551<br>
                Website: http://ppkk.unair.ac.id | Email: sekretariat@ppkk.unair.ac.id
            </p>
        </div>
    </div>

    <div class="judul-surat">
        <h3 class="judul-teks">SURAT TUGAS</h3>
        <p class="nomor-surat">Nomor: {{ $nomor ?? '1794/UN3.DPKKA/PK.01.03/2026' }}</p>
    </div>

    <div class="isi-surat">
        <p>
            Direktur Direktorat Pengembangan Karir, Inkubasi Kewirausahaan, dan Alumni Universitas Airlangga menugaskan mahasiswa nama terlampir untuk mengikuti kegiatan:
        </p>

        <table class="tabel-detail">
            <tr>
                <td width="25%">Acara</td>
                <td width="3%">:</td>
                <td><strong>{{ $acara ?? 'Seminar Mindentrepreneur: Airlangga Young Entrepreneur Tahun 2026' }}</strong></td>
            </tr>
            <tr>
                <td>Hari, Tanggal</td>
                <td>:</td>
                <td>{{ $tanggal ?? 'Sabtu, 21 Februari 2026' }}</td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td>:</td>
                <td>{{ $pukul ?? '07.30 WIB - selesai' }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>{{ $tempat ?? 'Aula Ternate ASEEC Tower Lt. 1, Kampus B Universitas Airlangga' }}</td>
            </tr>
        </table>

        <p>Demikian surat tugas ini diterbitkan untuk dilaksanakan dengan penuh tanggung jawab.</p>
    </div>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>{{ $tanggal_surat ?? '20 Februari 2026' }}</p>
            <p>Direktur,</p>
            
            <div class="nama-pejabat">{{ $nama_pejabat ?? 'Prof. Dr. Elly Munadziroh, drg., MS.' }}</div>
            <p>NIP. {{ $nip_pejabat ?? '197005221996012001' }}</p>
        </div>
    </div>

    <div class="footer-validasi">
        Scan QR Code untuk validasi surat ini. (Contoh Validasi Dokumen Digital)
    </div>

</body>
</html>