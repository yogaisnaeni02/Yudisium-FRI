<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'student_id',
        'status',
        'submitted_at',
        'progress',
    ];

    protected $casts = [
        'progress' => 'json',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the student associated with the submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the documents for the submission.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Calculate progress percentage.
     */
    public function getProgressPercentage(): int
    {
        $documents = $this->documents()->count();
        if ($documents === 0) {
            return 0;
        }

        $approved = $this->documents()->where('status', 'approved')->count();
        return (int) (($approved / $documents) * 100);
    }
}
