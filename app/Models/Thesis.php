<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    protected $casts = [
        'approved_at' => 'datetime',
        'defense_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'course_id',
        'adviser_id',
        'version',
        'title',
        'adviser',
        'abstract',
        'abstract_pdf_path',
        'thesis_pdf_path',
        'endorsement_pdf_path',
        'status',
        'verification_token',
        'admin_remarks',
        'approved_at',
        'approved_by',
        'panel_chairman',
        'panelist_one',
        'panelist_two',
        'defense_date',
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
