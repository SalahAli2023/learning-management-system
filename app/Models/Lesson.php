<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'file_url',
        'duration',      // New: lesson duration in minutes
        'is_free',       // New: whether the lesson is free
        'order'
    ];

    protected $casts = [
        'duration' => 'integer',
        'is_free' => 'boolean',
        'order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the course that owns the lesson.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the file URL attribute
     */
    public function getFileUrlAttribute($value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * Scope for free lessons
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for premium lessons
     */
    public function scopePremium($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Scope ordered lessons
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}
