<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'district_id', 'name'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
