@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-map-marker-multiple"></i>
    </span> Wilayah Indonesia
  </h3>
</div>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Cari Data Wilayah Administrasi Indonesia</h4>
        <p class="card-description">Pilih Provinsi, Kota/Kabupaten, Kecamatan, dan Kelurahan/Desa secara berurutan</p>

        <div class="row">
          {{-- Level 1: Provinsi --}}
          <div class="col-md-6 mb-4">
            <label class="form-label font-weight-bold">Level 1: Provinsi</label>
            <select id="selectProvinsi" class="form-control form-control-lg">
              <option value="0">Pilih Provinsi</option>
              @foreach($provinces as $prov)
                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Level 2: Kota/Kabupaten --}}
          <div class="col-md-6 mb-4">
            <label class="form-label font-weight-bold">Level 2: Kota / Kabupaten</label>
            <select id="selectKota" class="form-control form-control-lg" disabled>
              <option value="0">Pilih Kota</option>
            </select>
          </div>

          {{-- Level 3: Kecamatan --}}
          <div class="col-md-6 mb-4">
            <label class="form-label font-weight-bold">Level 3: Kecamatan</label>
            <select id="selectKecamatan" class="form-control form-control-lg" disabled>
              <option value="0">Pilih Kecamatan</option>
            </select>
          </div>

          {{-- Level 4: Kelurahan/Desa --}}
          <div class="col-md-6 mb-4">
            <label class="form-label font-weight-bold">Level 4: Kelurahan / Desa</label>
            <select id="selectKelurahan" class="form-control form-control-lg" disabled>
              <option value="0">Pilih Kelurahan</option>
            </select>
          </div>
        </div>

        {{-- Result Display --}}
        <div class="mt-3" id="hasilWilayah" style="display:none;">
          <div class="alert alert-success">
            <h5 class="mb-2"><i class="mdi mdi-check-circle"></i> Wilayah Terpilih:</h5>
            <p class="mb-0" id="txtHasil"></p>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectProvinsi   = document.getElementById('selectProvinsi');
    const selectKota       = document.getElementById('selectKota');
    const selectKecamatan  = document.getElementById('selectKecamatan');
    const selectKelurahan  = document.getElementById('selectKelurahan');
    const hasilWilayah     = document.getElementById('hasilWilayah');
    const txtHasil         = document.getElementById('txtHasil');

    // Helper: reset a select to default
    function resetSelect(select, placeholder) {
        select.innerHTML = '<option value="0">' + placeholder + '</option>';
        select.disabled = true;
    }

    // Helper: populate a select with data
    function populateSelect(select, data, placeholder) {
        select.innerHTML = '<option value="0">' + placeholder + '</option>';
        data.forEach(function(item) {
            let opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            select.appendChild(opt);
        });
        select.disabled = false;
    }

    // Helper: update result display
    function updateHasil() {
        let prov = selectProvinsi.options[selectProvinsi.selectedIndex];
        let kota = selectKota.options[selectKota.selectedIndex];
        let kec  = selectKecamatan.options[selectKecamatan.selectedIndex];
        let kel  = selectKelurahan.options[selectKelurahan.selectedIndex];

        if (kel && kel.value !== '0') {
            txtHasil.textContent = prov.text + ' → ' + kota.text + ' → ' + kec.text + ' → ' + kel.text;
            hasilWilayah.style.display = 'block';
        } else {
            hasilWilayah.style.display = 'none';
        }
    }

    // Level 1: Provinsi changed
    selectProvinsi.addEventListener('change', function() {
        let provinceId = this.value;

        // Reset level 2, 3, 4
        resetSelect(selectKota, 'Pilih Kota');
        resetSelect(selectKecamatan, 'Pilih Kecamatan');
        resetSelect(selectKelurahan, 'Pilih Kelurahan');
        hasilWilayah.style.display = 'none';

        if (provinceId !== '0') {
            axios.get('/wilayah/regencies/' + provinceId)
                .then(function(response) {
                    populateSelect(selectKota, response.data, 'Pilih Kota');
                })
                .catch(function(error) {
                    console.error('Error loading kota:', error);
                });
        }
    });

    // Level 2: Kota changed
    selectKota.addEventListener('change', function() {
        let regencyId = this.value;

        // Reset level 3, 4
        resetSelect(selectKecamatan, 'Pilih Kecamatan');
        resetSelect(selectKelurahan, 'Pilih Kelurahan');
        hasilWilayah.style.display = 'none';

        if (regencyId !== '0') {
            axios.get('/wilayah/districts/' + regencyId)
                .then(function(response) {
                    populateSelect(selectKecamatan, response.data, 'Pilih Kecamatan');
                })
                .catch(function(error) {
                    console.error('Error loading kecamatan:', error);
                });
        }
    });

    // Level 3: Kecamatan changed
    selectKecamatan.addEventListener('change', function() {
        let districtId = this.value;

        // Reset level 4
        resetSelect(selectKelurahan, 'Pilih Kelurahan');
        hasilWilayah.style.display = 'none';

        if (districtId !== '0') {
            axios.get('/wilayah/villages/' + districtId)
                .then(function(response) {
                    populateSelect(selectKelurahan, response.data, 'Pilih Kelurahan');
                })
                .catch(function(error) {
                    console.error('Error loading kelurahan:', error);
                });
        }
    });

    // Level 4: Kelurahan changed
    selectKelurahan.addEventListener('change', function() {
        updateHasil();
    });
});
</script>
@endpush
