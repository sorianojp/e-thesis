<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostgradThesis extends Model
{
    protected $table = 'postgrad_theses';

    protected $fillable = [
        'title',
        'adviser',
        'thesis_pdf_path',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
