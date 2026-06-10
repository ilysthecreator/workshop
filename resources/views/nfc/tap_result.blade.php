<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="theme-color" content="#120e1a">
  <title>Hasil Absensi KTM NFC</title>
  
  <!-- Font Premium & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --bg-gradient: radial-gradient(circle at top right, #241c33 0%, #0f0c16 100%);
      --glass-bg: rgba(255, 255, 255, 0.04);
      --glass-border: rgba(255, 255, 255, 0.08);
      --text-main: #f3f0f7;
      --text-muted: #9f96b0;
      
      /* State Colors */
      --success-primary: #00f2fe;
      --success-secondary: #4facfe;
      --success-glow: rgba(79, 172, 254, 0.4);
      
      --warning-primary: #fbc2eb;
      --warning-secondary: #a6c1ee;
      --warning-glow: rgba(166, 193, 238, 0.4);

      --error-primary: #ff0844;
      --error-secondary: #ffb199;
      --error-glow: rgba(255, 8, 68, 0.4);
    }

    body {
      background: var(--bg-gradient);
      color: var(--text-main);
      font-family: 'Outfit', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      margin: 0;
      padding: 20px;
      overflow-x: hidden;
    }

    /* Background Neon Glow */
    .glowing-orb {
      position: absolute;
      width: 300px;
      height: 300px;
      border-radius: 50%;
      z-index: -1;
      filter: blur(80px);
      opacity: 0.15;
      pointer-events: none;
    }

    .orb-left {
      top: 10%;
      left: -100px;
      background: #b96bf1;
    }

    .orb-right {
      bottom: 10%;
      right: -100px;
      background: #4facfe;
    }

    /* Main Card Container */
    .result-container {
      width: 100%;
      max-width: 420px;
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 30px;
      padding: 30px 24px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
      text-align: center;
      animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      position: relative;
      overflow: hidden;
    }

    @keyframes cardAppear {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Radar Pulse Animation */
    .radar-wrapper {
      position: relative;
      width: 130px;
      height: 130px;
      margin: 10px auto 25px auto;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .radar-ring {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      opacity: 0;
      box-sizing: border-box;
      pointer-events: none;
    }

    .radar-core {
      position: relative;
      width: 90px;
      height: 90px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 2;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }

    .radar-core i {
      font-size: 46px;
      color: #fff;
      animation: iconPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s both;
    }

    @keyframes iconPop {
      from { transform: scale(0); }
      to { transform: scale(1); }
    }

    /* State Configurations */
    /* 1. SUCCESS */
    .state-success .radar-core {
      background: linear-gradient(135deg, var(--success-primary) 0%, var(--success-secondary) 100%);
      box-shadow: 0 0 30px var(--success-glow);
    }
    .state-success .radar-ring {
      border: 2px solid var(--success-primary);
      animation: radarPulseAnim 2.5s infinite linear;
    }

    /* 2. WARNING */
    .state-warning .radar-core {
      background: linear-gradient(135deg, var(--warning-primary) 0%, var(--warning-secondary) 100%);
      box-shadow: 0 0 30px var(--warning-glow);
    }
    .state-warning .radar-ring {
      border: 2px solid var(--warning-primary);
      animation: radarPulseAnim 2.5s infinite linear;
    }

    /* 3. ERROR */
    .state-error .radar-core {
      background: linear-gradient(135deg, var(--error-primary) 0%, var(--error-secondary) 100%);
      box-shadow: 0 0 30px var(--error-glow);
    }
    .state-error .radar-ring {
      border: 2px solid var(--error-primary);
      animation: radarPulseAnim 2.5s infinite linear;
    }

    .radar-ring.ring-2 {
      animation-delay: 0.8s;
    }
    .radar-ring.ring-3 {
      animation-delay: 1.6s;
    }

    @keyframes radarPulseAnim {
      0% {
        transform: scale(0.6);
        opacity: 0.8;
      }
      100% {
        transform: scale(1.6);
        opacity: 0;
      }
    }

    /* Text & Status Styles */
    .status-title {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .state-success .status-title {
      background: linear-gradient(135deg, #ffffff 0%, var(--success-primary) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .state-warning .status-title {
      background: linear-gradient(135deg, #ffffff 0%, var(--warning-primary) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .state-error .status-title {
      background: linear-gradient(135deg, #ffffff 0%, #ff7676 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .status-message {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 25px;
      padding: 0 15px;
      line-height: 1.5;
    }

    /* KTM Digital Card Style Layout */
    .ktm-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
      border: 1px solid rgba(255, 255, 255, 0.07);
      border-radius: 20px;
      padding: 20px;
      text-align: left;
      position: relative;
      overflow: hidden;
      margin-bottom: 25px;
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
    }

    .ktm-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -30%;
      width: 250px;
      height: 250px;
      background: linear-gradient(135deg, rgba(185, 107, 241, 0.1) 0%, rgba(79, 172, 254, 0.02) 100%);
      border-radius: 50%;
      pointer-events: none;
    }

    .ktm-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      padding-bottom: 12px;
      margin-bottom: 15px;
    }

    .ktm-chip {
      width: 32px;
      height: 24px;
      background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
      border-radius: 4px;
      box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.4);
      position: relative;
    }

    .ktm-logo-text {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      color: rgba(255, 255, 255, 0.4);
      text-transform: uppercase;
    }

    .ktm-label {
      font-size: 10px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 2px;
    }

    .ktm-value {
      font-size: 17px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 12px;
    }

    .ktm-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .ktm-class-badge {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 4px 10px;
      border-radius: 10px;
      font-size: 11px;
      font-weight: 600;
      color: var(--success-primary);
    }

    .state-warning .ktm-class-badge {
      color: #ffb84d;
    }

    /* Action Buttons */
    .btn-action {
      background: linear-gradient(135deg, #b96bf1 0%, #7e3eb3 100%);
      color: #fff;
      border: none;
      font-weight: 600;
      font-size: 15px;
      padding: 14px 28px;
      border-radius: 20px;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(185, 107, 241, 0.3);
      text-decoration: none;
      display: inline-block;
    }

    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(185, 107, 241, 0.5);
      color: #fff;
    }

    .btn-action:active {
      transform: translateY(1px);
    }

    /* Small Close Link */
    .close-link {
      font-size: 13px;
      color: var(--text-muted);
      text-decoration: none;
      margin-top: 15px;
      display: inline-block;
      transition: color 0.2s ease;
    }

    .close-link:hover {
      color: var(--text-main);
    }
  </style>
</head>
<body class="state-{{ $status }}">

  <div class="glowing-orb orb-left"></div>
  <div class="glowing-orb orb-right"></div>

  <div class="result-container">
    
    <!-- Radar Status Icon -->
    <div class="radar-wrapper">
      <div class="radar-ring ring-1"></div>
      <div class="radar-ring ring-2"></div>
      <div class="radar-ring ring-3"></div>
      <div class="radar-core">
        @if($status === 'success')
          <i class="mdi mdi-check-circle-outline"></i>
        @elseif($status === 'warning')
          <i class="mdi mdi-alert-circle-outline"></i>
        @else
          <i class="mdi mdi-close-circle-outline"></i>
        @endif
      </div>
    </div>

    <!-- Status Text -->
    <h2 class="status-title">
      @if($status === 'success')
        Absen Sukses!
      @elseif($status === 'warning')
        Sudah Absen
      @else
        Absen Gagal
      @endif
    </h2>
    <p class="status-message">{{ $message }}</p>

    <!-- KTM Digital Card -->
    <div class="ktm-card">
      <div class="ktm-header">
        <div class="ktm-chip"></div>
        <span class="ktm-logo-text">KTM DIGITAL</span>
      </div>
      
      @if($student)
        <div class="ktm-label">Nama Mahasiswa</div>
        <div class="ktm-value">{{ $student->name }}</div>
        
        <div class="ktm-label">NIM</div>
        <div class="ktm-value" style="font-family: monospace; letter-spacing: 1px;">{{ $student->nim }}</div>
        
        <div class="ktm-footer">
          <div>
            <div class="ktm-label">Program Studi / Kelas</div>
            <span class="ktm-class-badge">{{ $student->class }}</span>
          </div>
          <div class="text-end">
            <div class="ktm-label">Waktu Tap</div>
            <span class="small font-weight-bold" style="color: var(--text-main); font-size: 12px;">
              <i class="mdi mdi-clock-outline"></i> {{ $tapped_at }}
            </span>
          </div>
        </div>
      @else
        <!-- Case Error: Kartu Belum Terdaftar -->
        <div class="ktm-label">Nama Mahasiswa</div>
        <div class="ktm-value" style="color: #ff7676;">BELUM TERDAFTAR</div>
        
        <div class="ktm-label">UID SERIAL KARTU</div>
        <div class="ktm-value" style="font-family: monospace; letter-spacing: 1px; color: var(--text-muted); font-size: 15px;">
          {{ $nfc_serial }}
        </div>
        
        <div class="ktm-footer">
          <div>
            <div class="ktm-label">Status</div>
            <span class="badge bg-danger text-white px-2 py-1" style="font-size: 10px;">Unknown Device</span>
          </div>
          <div class="text-end">
            <div class="ktm-label">Waktu Tap</div>
            <span class="small" style="color: var(--text-muted); font-size: 12px;">
              <i class="mdi mdi-clock-outline"></i> {{ $tapped_at }}
            </span>
          </div>
        </div>
      @endif
    </div>

    <!-- Action Button -->
    <a href="{{ route('nfc.history.index') }}" class="btn-action">
      <i class="mdi mdi-history"></i> Lihat Riwayat Absen
    </a>
    
    <a href="javascript:void(0);" onclick="window.close();" class="close-link">
      Tutup Halaman
    </a>

  </div>

  <!-- Audio & Haptic Feedback Logic -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // 1. Getar Haptic Feedback
      const status = "{{ $status }}";
      triggerHaptics(status);

      // 2. Bunyi Beep Audio Feedback
      triggerAudioFeedback(status);
    });

    function triggerHaptics(status) {
      if (!('vibrate' in navigator)) return;

      if (status === 'success') {
        navigator.vibrate([100]); // Getar pendek 100ms
      } else if (status === 'warning') {
        navigator.vibrate([100, 50, 100]); // Getar ganda
      } else {
        navigator.vibrate([300]); // Getar lama 300ms untuk error
      }
    }

    function triggerAudioFeedback(status) {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        
        if (status === 'success') {
          // Beep Nyaring & Riang (Double Beep Ringkas)
          playTone(audioCtx, 1200, 0.1);
          setTimeout(() => {
            playTone(audioCtx, 1500, 0.12);
          }, 120);
        } else if (status === 'warning') {
          // Warning: 2 Bunyi Sedang
          playTone(audioCtx, 800, 0.08);
          setTimeout(() => {
            playTone(audioCtx, 800, 0.08);
          }, 150);
        } else {
          // Error: Bunyi Rendah / Buzz Panjang
          playTone(audioCtx, 280, 0.4);
        }
      } catch (e) {
        console.log("AudioContext tidak didukung di browser ini: " + e.message);
      }
    }

    function playTone(ctx, frequency, duration) {
      const oscillator = ctx.createOscillator();
      const gainNode = ctx.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(ctx.destination);

      oscillator.type = 'sine';
      oscillator.frequency.setValueAtTime(frequency, ctx.currentTime);
      gainNode.gain.setValueAtTime(0.08, ctx.currentTime); // volume nyaman

      oscillator.start();
      oscillator.stop(ctx.currentTime + duration);
    }
  </script>
</body>
</html>
