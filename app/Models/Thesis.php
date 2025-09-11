<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    protected $casts = [
        'approved_at' => 'datetime',
    ];
    
    protected $fillable = [
        'user_id',
        'course_id',
        'version',
        'title',
        'adviser',
        'abstract',
        'thesis_pdf_path',
        'endorsement_pdf_path',
        'status',
        'admin_remarks',
        'approved_at',
        'approved_by'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
