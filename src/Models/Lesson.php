<?php

namespace Idoneo\HumanoAcademy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academy_lessons';

    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'video_url',
        'video_path',
        'video_filename',
        'video_poster',
        'duration_minutes',
        'order',
        'status',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the chapter that owns the lesson.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the user progress records for this lesson.
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Check if a specific user has completed this lesson.
     */
    public function isCompletedByUser($userId): bool
    {
        return $this->userProgress()
            ->where('user_id', $userId)
            ->where('completed', true)
            ->exists();
    }

    /**
     * Get the full video URL (handles both external URLs and local paths).
     */
    public function getFullVideoUrlAttribute(): ?string
    {
        // Si tiene una URL externa, usarla directamente
        if ($this->video_url)
        {
            return $this->video_url;
        }

        // Si tiene video_path (solo nombre de archivo), generar URL completa con hash del team
        if ($this->video_path && $this->chapter)
        {
            $course = $this->chapter->course;

            if ($course && $course->team_id)
            {
                $teamHash = \App\Models\Team::generateTeamHash($course->team_id);

                return asset("storage/academy/{$teamHash}/videos/{$this->video_path}");
            }
        }

        return null;
    }

    /**
     * Scope published lessons.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
