<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $fillable = [
        'nama_dosen',
        'kode_dosen',
        'prodi',
        'nip',
    ];
}
