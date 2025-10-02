<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    protected $casts = [
        'approved_at' => 'datetime',
        'plagiarism_report' => 'array',
        'plagiarism_checked_at' => 'datetime',
    ];

    protected $fillable = [
        'thesis_title_id',
        'chapter_label',
        'thesis_pdf_path',
        'status',
        'approved_at',
        'approved_by',
        'plagiarism_score',
        'plagiarism_report',
        'plagiarism_checked_at',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function thesisTitle(): BelongsTo
    {
        return $this->belongsTo(ThesisTitle::class);
    }
}
