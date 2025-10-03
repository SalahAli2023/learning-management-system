<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function course()
    { 
        return $this->belongsTo(Course::class); 
    }

    public function submissions()
    { 
        return $this->hasMany(Submission::class); 
    }

    public function studentSubmission($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function isSubmittedBy($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->exists();
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function getSubmissionCountAttribute()
    {
        return $this->submissions()->count();
    }

    public function getGradedCountAttribute()
    {
        return $this->submissions()->whereNotNull('grade')->count();
    }
}
