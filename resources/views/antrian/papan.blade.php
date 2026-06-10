<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian Real-Time</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at center, #1e1e38 0%, #0f0f1e 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .header-bar {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-bar h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 2px;
            background: linear-gradient(to right, #b66dff, #ac80ff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .live-indicator {
            background: rgba(27, 207, 180, 0.1);
            color: #1bcfb4;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(27, 207, 180, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #1bcfb4;
            box-shadow: 0 0 10px #1bcfb4;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.5; }
        }
        .main-display {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .display-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 60px 40px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            text-align: center;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .nomor-label {
            font-size: 20px;
            font-weight: 600;
            color: rgba(255,255,255,0.4);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .nomor-value {
            font-size: 200px;
            font-weight: 800;
            background: linear-gradient(135deg, #b66dff 0%, #7e3ff2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 0.95;
            margin-bottom: 15px;
            letter-spacing: -2px;
            filter: drop-shadow(0 10px 20px rgba(182, 109, 255, 0.3));
        }
        .nama-value {
            font-size: 42px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .loket-value {
            font-size: 22px;
            color: #1bcfb4;
            font-weight: 500;
            background: rgba(27, 207, 180, 0.05);
            padding: 8px 30px;
            border-radius: 50px;
            display: inline-block;
            border: 1px solid rgba(27, 207, 180, 0.15);
        }
        .footer-bar {
            background: rgba(255, 255, 255, 0.02);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .clock {
            font-size: 22px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
        }
        .copyright {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
        }

        /* Overlay untuk Aktivasi Suara */
        .overlay-activation {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(10, 10, 20, 0.96);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(15px);
            transition: all 0.5s ease;
        }
        .overlay-content {
            text-align: center;
            max-width: 500px;
            padding: 40px;
        }
        .btn-activate {
            background: linear-gradient(135deg, #b66dff 0%, #7e3ff2 100%);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 700;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(182, 109, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-activate:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(182, 109, 255, 0.6);
        }
        .overlay-activation.d-none {
            opacity: 0;
            pointer-events: none;
        }

        /* Gelombang Suara (Audio Wave Animation) */
        .audio-wave {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 30px;
            margin-top: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .audio-wave.active {
            opacity: 1;
        }
        .bar {
            width: 4px;
            height: 10px;
            background-color: #1bcfb4;
            border-radius: 50px;
            animation: bounce 1s ease-in-out infinite;
        }
        .bar:nth-child(2) { animation-delay: 0.1s; height: 18px; }
        .bar:nth-child(3) { animation-delay: 0.2s; height: 26px; }
        .bar:nth-child(4) { animation-delay: 0.3s; height: 16px; }
        .bar:nth-child(5) { animation-delay: 0.4s; height: 8px; }
        @keyframes bounce {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(2); }
        }
    </style>
</head>
<body>

    {{-- Overlay Aktivasi --}}
    <div class="overlay-activation" id="overlay">
        <div class="overlay-content">
            <h2 class="fw-bold mb-3">Selamat Datang di Papan Antrian</h2>
            <p class="text-muted">Klik tombol di bawah untuk mengaktifkan sistem suara notifikasi panggilan real-time (Web Speech API).</p>
            <button class="btn-activate" onclick="aktivasiPapan()">Aktifkan Papan Antrian</button>
        </div>
    </div>

    {{-- Header --}}
    <div class="header-bar">
        <h1>PAPAN ANTRIAN UTAMA</h1>
        <div class="live-indicator">
            <div class="live-dot" id="sseDot"></div>
            <span>LIVE MONITOR</span>
        </div>
    </div>

    {{-- Main Area --}}
    <div class="main-display">
        <div class="display-card" id="displayCard">
            {{-- Tampilan Kosong --}}
            <div id="belumAda">
                <i class="mdi mdi-broadcast text-muted mb-4" style="font-size: 72px; display: block; opacity: 0.2;"></i>
                <h3 class="fw-semibold text-muted">Menunggu Panggilan Antrian...</h3>
                <p class="text-muted mb-0 small">Nomor antrian baru akan muncul otomatis di sini ketika dipanggil oleh petugas.</p>
            </div>

            {{-- Tampilan Panggilan Nomor --}}
            <div id="tampilNomor" style="display: none;">
                <div class="nomor-label">Nomor Antrian</div>
                <div class="nomor-value" id="nomorBesar">-</div>
                <div class="nama-value" id="namaText">-</div>
                <div class="loket-value"><i class="mdi mdi-arrow-right-bold-circle"></i> Silakan Menuju ke Loket Layanan</div>
                <br>
                <div class="audio-wave" id="audioWave">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-bar">
        <div class="clock" id="clock">00:00:00</div>
        <div class="copyright">&copy; {{ date('Y') }} Workshop Real-Time App. All rights reserved.</div>
    </div>

    <script>
        let aktif = false;
        let lastNomor = null;

        // Aktivasi Audio Papan
        function aktivasiPapan() {
            aktif = true;
            document.getElementById('overlay').classList.add('d-none');

            // Trigger silent utterance to unlock speech synthesis on mobile browsers
            const test = new SpeechSynthesisUtterance('');
            test.volume = 0;
            speechSynthesis.speak(test);
        }

        // Digital Clock
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = h + ':' + m + ':' + s;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Text-to-Speech
        function ucapkan(teks) {
            if (!aktif) return;
            if (!('speechSynthesis' in window)) return;

            speechSynthesis.cancel(); // batalkan suara sebelumnya jika menumpuk

            const utterance = new SpeechSynthesisUtterance(teks);
            utterance.lang = 'id-ID';
            utterance.rate = 0.85; // Kecepatan pelafalan lambat agar terdengar ramah & jelas
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

            // Cari voice bahasa Indonesia
            const voices = speechSynthesis.getVoices();
            const voiceId = voices.find(v => v.lang.startsWith('id') || v.lang.includes('id_ID'));
            if (voiceId) utterance.voice = voiceId;

            // Efek visual audio wave aktif saat berbunyi
            utterance.onstart = function() {
                document.getElementById('audioWave').classList.add('active');
            };
            utterance.onend = function() {
                document.getElementById('audioWave').classList.remove('active');
            };

            speechSynthesis.speak(utterance);
        }

        // Force reload voices on browsers that load them asynchronously
        if ('speechSynthesis' in window) {
            speechSynthesis.getVoices();
            speechSynthesis.onvoiceschanged = () => speechSynthesis.getVoices();
        }

        // SSE Connection
        const eventSource = new EventSource('{{ route("sse.antrian") }}');

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            updatePapan(data);
        };

        eventSource.onerror = function() {
            document.getElementById('sseDot').style.background = '#fe7c96';
            document.getElementById('sseDot').style.boxShadow = '0 0 10px #fe7c96';
        };

        eventSource.onopen = function() {
            document.getElementById('sseDot').style.background = '#1bcfb4';
            document.getElementById('sseDot').style.boxShadow = '0 0 10px #1bcfb4';
        };

        // Memperbarui UI Papan secara dinamis
        function updatePapan(data) {
            const dipanggil = data.dipanggil;
            const displayCard = document.getElementById('displayCard');

            if (dipanggil) {
                // Sembunyikan pesan kosong, tampilkan detail panggilan
                document.getElementById('belumAda').style.display = 'none';
                document.getElementById('tampilNomor').style.display = 'block';

                document.getElementById('nomorBesar').textContent = dipanggil.nomor_antrian;
                document.getElementById('namaText').textContent = dipanggil.nama;

                // Mainkan suara hanya jika nomor antrian baru dipanggil
                if (dipanggil.nomor_antrian !== lastNomor) {
                    lastNomor = dipanggil.nomor_antrian;

                    // Efek flash/glowing card saat ada perubahan nomor
                    displayCard.style.boxShadow = '0 0 40px rgba(182, 109, 255, 0.4)';
                    setTimeout(() => {
                        displayCard.style.boxShadow = '0 20px 50px rgba(0,0,0,0.3)';
                    }, 1000);

                    // Beri jeda 0.5 detik sebelum melafalkan teks
                    setTimeout(function() {
                        ucapkan('Nomor antrian ' + dipanggil.nomor_antrian + ', atas nama ' + dipanggil.nama + ', silakan menuju ke loket layanan.');
                    }, 500);
                }
            } else {
                // Tampilkan pesan default menunggu antrean
                document.getElementById('belumAda').style.display = 'block';
                document.getElementById('tampilNomor').style.display = 'none';
                lastNomor = null;
            }
        }
    </script>
</body>
</html>
