<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'idbuku'; // Sesuaikan SQL
    public $timestamps = false; // SQL tidak punya created_at/updated_at

    protected $fillable = ['kode', 'judul', 'pengarang', 'idkategori'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }
}