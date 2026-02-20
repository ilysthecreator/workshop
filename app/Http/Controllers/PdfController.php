<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // a. Sertifikat (Landscape)
    public function generateSertifikat()
{
    $data = [
        'nama' => 'Ilyas',
        'predikat' => 'KELOMPOK TERBAIK',
        'tanggal' => date('d F Y')
    ];

    $pdf = Pdf::loadView('pdf.sertifikat', $data)
              ->setPaper('a4', 'landscape');
    
    return $pdf->stream('Sertifikat_Penghargaan.pdf');
}

    // b. Undangan Fakultas (Portrait dengan Header)
    public function generateUndangan()
{
    $data = [
        'nomor' => '1794/UN3.DPKKA/PK.01.03/2026',
        'acara' => 'Seminar Mindentrepreneur: Airlangga Young Entrepreneur Tahun 2026',
        'tanggal' => 'Sabtu, 21 Februari 2026',
        'pukul' => '07.30 WIB - selesai',
        'tempat' => 'Aula Ternate ASEEC Tower Lt. 1, Kampus B Universitas Airlangga',
        'tanggal_surat' => '20 Februari 2026',
        'nama_pejabat' => 'Prof. Dr. Elly Munadziroh, drg., MS.',
        'nip_pejabat' => '197005221996012001'
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.undangan', $data)
                ->setPaper('a4', 'portrait');

    return $pdf->stream('Surat_Tugas_1794.pdf');
}
}
