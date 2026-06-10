<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'reg_provinces';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'name'];

    public function regencies()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}
