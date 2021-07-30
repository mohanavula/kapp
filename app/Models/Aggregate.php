<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aggregate extends Model
{
    use HasFactory;

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }
}
