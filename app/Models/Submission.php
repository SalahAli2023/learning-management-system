<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_url',
        'grade',
        'feedback'
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    public function assignment()
    { 
        return $this->belongsTo(Assignment::class); 
    }

    public function student()
    { 
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isGraded()
    {
        return !is_null($this->grade);
    }

    public function isLate()
    {
        return $this->assignment->due_date && 
                $this->created_at->gt($this->assignment->due_date);
    }

    public function getGradePercentageAttribute()
    {
        if (!$this->grade || !$this->assignment->max_score) {
            return 0;
        }
        return ($this->grade / 100) * 100; // Assuming max_score is 100
    }
}
