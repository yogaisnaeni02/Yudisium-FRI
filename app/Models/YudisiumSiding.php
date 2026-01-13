<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YudisiumSiding extends Model
{
    protected $fillable = ['periode_id', 'mahasiswa_id', 'tanggal_sidang', 'predikat'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
