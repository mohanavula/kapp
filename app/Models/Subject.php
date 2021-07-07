<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function curricula() {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subjects');
    }

}