@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Customer 1 (Kamera -> DB BLOB)</h4>
                <p class="card-description">Menyimpan gambar hasil scan kamera sebagai LongBlob</p>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="forms-sample" method="POST" action="{{ route('customer.store1') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Customer" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat Lengkap" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="provinsi_id">Provinsi</label>
                        <select class="form-control form-control-sm" id="provinsi_id" name="provinsi_id" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kota_id">Kota</label>
                        <select class="form-control form-control-sm" id="kota_id" name="kota_id" required disabled>
                            <option value="">-- Pilih Kota --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kecamatan_id">Kecamatan</label>
                        <select class="form-control form-control-sm" id="kecamatan_id" name="kecamatan_id" required disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kelurahan_id">Kodepos - Kelurahan</label>
                        <select class="form-control form-control-sm" id="kelurahan_id" name="kelurahan_id" required disabled>
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="d-block">Foto</label>
                        <div class="border p-2 mb-2 text-center" style="width: 200px; min-height: 200px; background: #f8f9fa;">
                            <img id="formPreviewImg" style="max-width: 100%; display: none;" />
                            <span id="formPreviewText" class="text-muted d-block mt-4 pt-3">Belum ada foto</span>
                        </div>
                        <input type="hidden" name="webcam_image" id="webcam_image_input" required>
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#cameraModal">
                            <i class="mdi mdi-camera"></i> Ambil Foto
                        </button>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Data</button>
                    <a href="{{ route('customer.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cameraModalLabel">Modal Ambil Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModalTop"></button>
      </div>
      <div class="modal-body">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Pilihan Kamera:</label>
                <select id="cameraSelect" class="form-control form-control-sm"></select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" id="btnSwitchCamera" class="btn btn-sm btn-info w-100">Ganti Kamera</button>
            </div>
        </div>

        <div class="row text-center">
            <div class="col-md-6">
                <h6>Video</h6>
                <div class="border" style="background:#000;">
                    <video id="videoElement" autoplay playsinline style="width:100%; max-height:240px;"></video>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Snapshot</h6>
                <div class="border text-center" style="background:#eee; min-height: 240px;">
                    <canvas id="canvasElement" style="display:none; max-width:100%;"></canvas>
                    <img id="snapshotPreviewImg" style="display:none; max-width:100%; max-height:240px;" />
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-primary" id="btnSnap">Ambil Foto</button>
        <button type="button" class="btn btn-success" id="btnSavePhoto" disabled>Simpan Foto</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseModalBtn">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    // == Wilayah Cascading Dropdown ==
    $(document).ready(function() {
        $('#provinsi_id').change(function() {
            var province_id = $(this).val();
            $('#kota_id').prop('disabled', true).html('<option value="">Loading...</option>');
            $('#kecamatan_id').prop('disabled', true).html('<option value="">-- Pilih Kecamatan --</option>');
            $('#kelurahan_id').prop('disabled', true).html('<option value="">-- Pilih Kelurahan --</option>');
            
            if(province_id) {
                $.get("{{ url('wilayah/regencies') }}/" + province_id, function(data) {
                    var options = '<option value="">-- Pilih Kota --</option>';
                    $.each(data, function(key, val) { options += '<option value="'+val.id+'">'+val.name+'</option>'; });
                    $('#kota_id').html(options).prop('disabled', false);
                });
            }
        });

        $('#kota_id').change(function() {
            var regency_id = $(this).val();
            $('#kecamatan_id').prop('disabled', true).html('<option value="">Loading...</option>');
            $('#kelurahan_id').prop('disabled', true).html('<option value="">-- Pilih Kelurahan --</option>');
            
            if(regency_id) {
                $.get("{{ url('wilayah/districts') }}/" + regency_id, function(data) {
                    var options = '<option value="">-- Pilih Kecamatan --</option>';
                    $.each(data, function(key, val) { options += '<option value="'+val.id+'">'+val.name+'</option>'; });
                    $('#kecamatan_id').html(options).prop('disabled', false);
                });
            }
        });

        $('#kecamatan_id').change(function() {
            var district_id = $(this).val();
            $('#kelurahan_id').prop('disabled', true).html('<option value="">Loading...</option>');
            
            if(district_id) {
                $.get("{{ url('wilayah/villages') }}/" + district_id, function(data) {
                    var options = '<option value="">-- Pilih Kelurahan --</option>';
                    $.each(data, function(key, val) { options += '<option value="'+val.id+'">'+val.name+'</option>'; });
                    $('#kelurahan_id').html(options).prop('disabled', false);
                });
            }
        });
    });

    // == Webcam Logic ==
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('canvasElement');
    const ctx = canvas.getContext('2d');
    const cameraSelect = document.getElementById('cameraSelect');
    
    const btnSnap = document.getElementById('btnSnap');
    const btnSave = document.getElementById('btnSavePhoto');
    const btnSwitch = document.getElementById('btnSwitchCamera');
    const snapshotPreview = document.getElementById('snapshotPreviewImg');
    
    let currentStream = null;
    let base64Image = null;

    function stopMediaTracks(stream) {
        if(stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    async function getCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            
            cameraSelect.innerHTML = '';
            videoDevices.forEach((camera, index) => {
                const opt = document.createElement('option');
                opt.value = camera.deviceId;
                opt.text = camera.label || `Kamera ${index + 1}`;
                cameraSelect.appendChild(opt);
            });
        } catch (e) {
            console.error('Error getting cameras:', e);
        }
    }

    async function startCamera(deviceId) {
        stopMediaTracks(currentStream);
        const constraints = {
            video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        };
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            currentStream = stream;
            video.srcObject = stream;
        } catch (e) {
            console.error('Error starting camera:', e);
            alert("Tidak bisa mengakses kamera: " + e.message);
        }
    }

    document.getElementById('cameraModal').addEventListener('show.bs.modal', async function () {
        await getCameras();
        if(cameraSelect.options.length > 0) {
            startCamera(cameraSelect.value);
        } else {
            startCamera();
        }
    });

    document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function () {
        stopMediaTracks(currentStream);
    });

    btnSwitch.addEventListener('click', () => {
        if(cameraSelect.value) {
            startCamera(cameraSelect.value);
        }
    });

    btnSnap.addEventListener('click', () => {
        if(video.videoWidth) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            base64Image = canvas.toDataURL('image/jpeg');
            
            snapshotPreview.src = base64Image;
            snapshotPreview.style.display = 'block';
            canvas.style.display = 'none';
            
            btnSave.disabled = false;
        }
    });

    btnSave.addEventListener('click', () => {
        if(base64Image) {
            document.getElementById('webcam_image_input').value = base64Image;
            document.getElementById('formPreviewImg').src = base64Image;
            document.getElementById('formPreviewImg').style.display = 'block';
            document.getElementById('formPreviewText').style.display = 'none';
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
            modal.hide();
        }
    });
</script>
@endpush
