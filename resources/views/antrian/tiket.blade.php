<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ $antrian->nomor_antrian }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .ticket-container {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        /* Guntingan tiket di samping kiri-kanan */
        .ticket-container::before, .ticket-container::after {
            content: '';
            position: absolute;
            top: 210px;
            width: 20px;
            height: 20px;
            background: #e5e7eb;
            border-radius: 50%;
            z-index: 10;
        }
        .ticket-container::before { left: -10px; }
        .ticket-container::after { right: -10px; }
        
        .ticket-header {
            background: linear-gradient(135deg, #b66dff 0%, #7e3ff2 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .ticket-header h4 {
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
            font-size: 20px;
        }
        .ticket-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.8;
        }
        .ticket-body {
            padding: 30px;
            text-align: center;
        }
        .label-nomor {
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .nomor-antrian {
            font-size: 84px;
            font-weight: 800;
            color: #7e3ff2;
            line-height: 1;
            margin: 15px 0;
            letter-spacing: -2px;
        }
        .nama-tamu {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }
        .waktu {
            color: #9ca3af;
            font-size: 13px;
            margin-bottom: 25px;
        }
        .divider {
            border-top: 2px dashed #e5e7eb;
            margin: 0 0 25px 0;
            position: relative;
        }
        .badge-status {
            font-size: 14px;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: inline-block;
        }
        .btn-print {
            border: 1.5px solid #7e3ff2;
            color: #7e3ff2;
            background: transparent;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            margin-top: 25px;
            width: 100%;
        }
        .btn-print:hover {
            background: #7e3ff2;
            color: white;
            transform: translateY(-1px);
        }
        
        /* CSS khusus untuk print */
        @media print {
            body { background: white; padding: 0; }
            .ticket-container { box-shadow: none; border: none; max-width: 100%; }
            .btn-print, .ticket-container::before, .ticket-container::after { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h4>TIKET ANTRIAN</h4>
            <p>Sistem Layanan Workshop Real-Time</p>
        </div>
        <div class="ticket-body">
            <div class="label-nomor">Nomor Antrian Anda</div>
            <div class="nomor-antrian" id="ticketNumber">{{ $antrian->nomor_antrian }}</div>
            <div class="nama-tamu" id="ticketName">{{ $antrian->nama }}</div>
            <div class="waktu">{{ $antrian->created_at->format('d M Y, H:i') }} WIB</div>

            <div class="divider"></div>

            <p class="text-muted mb-2 small font-weight-semibold">STATUS SAAT INI</p>
            <span id="statusBadge" class="badge-status {{ $antrian->status == 'menunggu' ? 'bg-warning text-dark' : 'text-white ' . ($antrian->status == 'dipanggil' ? 'bg-success' : ($antrian->status == 'terlambat' ? 'bg-danger' : 'bg-secondary')) }}">
                {{ strtoupper($antrian->status) }}
            </span>

            <button onclick="window.print()" class="btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                </svg> Cetak Tiket
            </button>
        </div>
    </div>

    <script>
        // SSE Connection untuk update status tiket secara real-time
        const antrianId = parseInt("{{ $antrian->id }}");
        const eventSource = new EventSource('{{ route("sse.antrian") }}');

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            const myAntrian = data.daftar.find(item => item.id === antrianId);

            if (myAntrian) {
                const badge = document.getElementById('statusBadge');
                const status = myAntrian.status;

                badge.textContent = status.toUpperCase();
                badge.className = 'badge-status ';

                if (status === 'menunggu') {
                    badge.classList.add('bg-warning', 'text-dark');
                } else if (status === 'dipanggil') {
                    badge.classList.add('bg-success', 'text-white');
                    // Opsi: Mainkan peringatan getar di HP jika didukung
                    if ('vibrate' in navigator) navigator.vibrate([200, 100, 200]);
                } else if (status === 'terlambat') {
                    badge.classList.add('bg-danger', 'text-white');
                } else {
                    badge.classList.add('bg-secondary', 'text-white');
                }
            }
        };
    </script>
</body>
</html>
