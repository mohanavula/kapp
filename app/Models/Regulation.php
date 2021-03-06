<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory;

    public function program() {
        return $this->belongsTo(Program::class);
    }

    public function semesters() {
        return $this->hasMany(Semester::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
