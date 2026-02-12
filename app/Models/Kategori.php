<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'idkategori'; // Sesuaikan SQL
    public $timestamps = false; // SQL tidak punya created_at/updated_at
    
    protected $fillable = ['nama_kategori'];

    public function buku()
    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }
}