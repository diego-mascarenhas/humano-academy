<?php

namespace Idoneo\HumanoAcademy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'academy_user_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'completed_at',
        'watch_time_seconds',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'watch_time_seconds' => 'integer',
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the lesson for this progress.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark the lesson as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Update watch time.
     */
    public function updateWatchTime(int $seconds): void
    {
        $this->increment('watch_time_seconds', $seconds);
    }
}
