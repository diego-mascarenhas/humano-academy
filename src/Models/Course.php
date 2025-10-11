<?php

namespace Idoneo\HumanoAcademy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academy_courses';

    protected $fillable = [
        'team_id',
        'title',
        'description',
        'long_description',
        'instructor_name',
        'instructor_title',
        'category_id',
        'skill_level',
        'students_count',
        'language',
        'has_captions',
        'thumbnail',
        'status',
        'order',
    ];

    protected $casts = [
        'has_captions' => 'boolean',
        'students_count' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the team that owns the course.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    /**
     * Get the category that the course belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Get the language of the course.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Language::class, 'language', 'code');
    }

    /**
     * Get the chapters for the course.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    /**
     * Get all lessons through chapters.
     */
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    /**
     * Get total lectures count.
     */
    public function getLecturesCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Get total duration in minutes.
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->sum('duration_minutes');
    }

    /**
     * Scope published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope by team.
     */
    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
