<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThesisTitle extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'adviser_id',
        'title',
        'abstract_pdf_path',
        'endorsement_pdf_path',
        'grade',
        'verification_token',
        'panel_chairman',
        'panelist_one',
        'panelist_two',
        'defense_date',
    ];

    protected $casts = [
        'defense_date' => 'date',
        'grade' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function adviserUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
    }
}
