<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'ipk',
        'total_sks',
        'status_kelulusan',
        'mata_kuliah',
    ];

    protected $casts = [
        'mata_kuliah' => 'json',
    ];

    /**
     * Get the user associated with the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the submissions for the student.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the yudisium results for the student.
     */
    public function yudisiumResults(): HasMany
    {
        return $this->hasMany(YudisiumResult::class);
    }

    /**
     * Get the yudisium sidings for the student.
     */
    public function yudisiumSidings(): HasMany
    {
        return $this->hasMany(YudisiumSiding::class);
    }
}
