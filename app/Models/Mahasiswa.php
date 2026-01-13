<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nim',
        'prodi',
        'angkatan',
        'email',
        'telepon',
        'alamat',
        'foto',
        'created_at',
        'updated_at',
    ];

    public function yudisiumSidings()
    {
        return $this->hasMany(YudisiumSiding::class);
    }
}