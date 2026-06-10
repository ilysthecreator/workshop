<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';
    public $timestamps = false;

    protected $fillable = [
        'nama_vendor',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }
}
