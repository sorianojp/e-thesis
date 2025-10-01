<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    protected $casts = [
        'approved_at' => 'datetime',
        'defense_date' => 'date',
        'grade' => 'decimal:2',
        'plagiarism_report' => 'array',
        'plagiarism_checked_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'course_id',
        'adviser_id',
        'title',
        'adviser',
        'abstract',
        'abstract_pdf_path',
        'thesis_pdf_path',
        'endorsement_pdf_path',
        'status',
        'grade',
        'verification_token',
        'adviser_remarks',
        'approved_at',
        'approved_by',
        'panel_chairman',
        'panelist_one',
        'panelist_two',
        'defense_date',
        'plagiarism_score',
        'plagiarism_report',
        'plagiarism_checked_at',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adviserUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }
}
