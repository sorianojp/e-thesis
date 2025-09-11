<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name'
    ];

    public function theses(): BelongsTo
    {
        return $this->hasMany(Thesis::class);
    }
}
