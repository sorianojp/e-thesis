<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThesisTitle extends Model
{
    public const MAX_MEMBERS = 10;

    protected $fillable = [
        'user_id',
        'course_id',
        'adviser_id',
        'title',
        'abstract_pdf_path',
        'endorsement_pdf_path',
        'verification_token',
        'panel_chairman',
        'panelist_one',
        'panelist_two',
        'defense_date',
    ];

    protected $casts = [
        'defense_date' => 'date',
    ];

    public static function titleDefenseChapters(): array
    {
        return ['Chapter 1', 'Chapter 2', 'Chapter 3'];
    }

    public static function finalDefenseChapters(): array
    {
        return ['Chapter 1', 'Chapter 2', 'Chapter 3', 'Chapter 4', 'Chapter 5'];
    }

    public function requiredChapters(): array
    {
        return self::finalDefenseChapters();
    }

    public function titleDefenseApproved(): bool
    {
        $required = self::titleDefenseChapters();

        $approved = $this->theses
            ->whereIn('chapter_label', $required)
            ->where('status', 'approved')
            ->pluck('chapter_label')
            ->unique()
            ->all();

        sort($required);
        sort($approved);

        return $required === $approved;
    }

    public function chaptersAreApproved(): bool
    {
        $required = $this->requiredChapters();

        if ($required === []) {
            return false;
        }

        $chapters = $this->theses
            ->whereIn('chapter_label', $required)
            ->where('status', 'approved')
            ->pluck('chapter_label')
            ->unique()
            ->all();

        sort($required);
        sort($chapters);

        return $required === $chapters;
    }

    public function approvedChaptersCount(): int
    {
        return $this->theses
            ->where('status', 'approved')
            ->unique('chapter_label')
            ->count();
    }

    public static function approvedChaptersCountForStudent(int $studentId): int
    {
        return static::query()
            ->where('user_id', $studentId)
            ->with(['theses' => fn ($q) => $q->where('status', 'approved')])
            ->get()
            ->flatMap(fn (ThesisTitle $title) => $title->theses)
            ->unique('chapter_label')
            ->count();
    }

    public static function finalDefenseTitleForStudent(int $studentId): ?self
    {
        return static::query()
            ->where('user_id', $studentId)
            ->with(['theses' => fn ($q) => $q->latest('updated_at')])
            ->get()
            ->first(fn (ThesisTitle $title) => $title->chaptersAreApproved());
    }

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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_title_members', 'thesis_title_id', 'student_id')
            ->withTimestamps();
    }

    public function hasMember(int $studentId): bool
    {
        if ($this->relationLoaded('members')) {
            return $this->members->contains(fn (User $member) => (int) $member->id === $studentId);
        }

        return $this->members()->where('student_id', $studentId)->exists();
    }

    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
    }
}
