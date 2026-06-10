<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'nama',
        'alamat',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'foto_blob',
        'foto_path',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinsi_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'kota_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'kecamatan_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'kelurahan_id', 'id');
    }
}
