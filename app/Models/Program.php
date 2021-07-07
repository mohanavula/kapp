<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Program extends Model
{
    use HasFactory;

    public function program_level() {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function specializations() {
        return $this->hasMany(Specialization::class);
    }
}
