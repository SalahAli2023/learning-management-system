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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
