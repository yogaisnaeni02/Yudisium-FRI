<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YudisiumResult extends Model
{
    protected $fillable = [
        'student_id',
        'ipk',
        'predikat_kelulusan',
        'status_pembimbing',
        'status_penguji',
        'status_kelulusan',
        'cumlaude',
        'title_cumlaude',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
