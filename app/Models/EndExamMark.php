<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndExamMark extends Model
{
    use HasFactory;

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
 }
