<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    public function semester() {
        return $this->belongsTo(Semester::class);
    }

    public function specialization() {
        return $this->belongsTo(Specialization::class);
    }

    public function subjects() {
        return $this->belongsToMany(Subject::class);
    }

    public function subject_category() {
        return $this->belongsTo(SubjectCategory::class);
    }

    public function subject_offering_type() {
        return $this->belongsTo(SubjectOfferingType::class);
    }

}
