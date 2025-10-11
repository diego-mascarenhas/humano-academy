<?php

namespace Idoneo\HumanoAcademy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academy_chapters';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the course that owns the chapter.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lessons for the chapter.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get total duration of all lessons in this chapter.
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->sum('duration_minutes');
    }

    /**
     * Get lessons count.
     */
    public function getLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }
}
