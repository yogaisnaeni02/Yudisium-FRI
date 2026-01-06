<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'file_path',
        'version_number',
        'notes',
    ];

    /**
     * Get the document associated with the version.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
