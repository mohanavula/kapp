<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exam_schedules() {
        return $this->hasMany(ExamSchedule::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }
}
