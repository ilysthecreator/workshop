<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Antrian - Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.1);
        }
        .brand-icon {
            background: linear-gradient(135deg, #b66dff 0%, #7e3ff2 100%);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(182, 109, 255, 0.3);
            color: white;
            font-size: 30px;
        }
        .form-card h2 {
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .form-card p {
            color: #6c757d;
            font-size: 15px;
            margin-bottom: 35px;
        }
        .form-control-custom {
            border-radius: 12px;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            transition: all 0.3s ease;
        }
        .form-control-custom:focus {
            border-color: #b66dff;
            box-shadow: 0 0 0 3px rgba(182, 109, 255, 0.15);
            background: #ffffff;
        }
        .btn-custom {
            background: linear-gradient(135deg, #b66dff 0%, #7e3ff2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            box-shadow: 0 8px 20px rgba(182, 109, 255, 0.25);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(182, 109, 255, 0.35);
            color: white;
        }
        .btn-custom:active {
            transform: translateY(1px);
        }
        .input-group-custom {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }
        .input-group-custom label {
            font-weight: 600;
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="form-card">
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4 0 1 1 1 1 1h4c0-.73.272-1.96.936-3.044L6.936 9.28zM4.887 7.668c.11-.22.23-.43.364-.643A4 4 0 0 1 5 2a4 4 0 0 1 3.998 4c0 .307-.035.606-.102.893C8.428 6.577 7.79 6 7 6c-1.562 0-2.53.802-3.007 1.493a3.5 3.5 0 0 1-.11.175z"/>
            </svg>
        </div>
        <h2>Pendaftaran Antrian</h2>
        <p>Silakan isi nama Anda di bawah untuk mendaftar dan mencetak tiket antrian secara langsung.</p>

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3 text-start small mb-4">
                @foreach ($errors->all() as $error)
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('antrian.store') }}" id="formAntrian">
            @csrf
            <div class="input-group-custom">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control form-control-custom" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda..." required value="{{ old('nama') }}" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-custom">Daftar Antrian</button>
        </form>
    </div>

    <script>
        // Membuka tiket di tab baru untuk pencetakan (sesuai spesifikasi modul)
        document.getElementById('formAntrian').addEventListener('submit', function(e) {
            this.target = '_blank';
            // Bersihkan kolom input setelah jeda singkat
            setTimeout(() => {
                document.getElementById('nama').value = '';
            }, 500);
        });
    </script>
</body>
</html>
