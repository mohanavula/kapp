<?php

namespace App\Models;

use App\Http\Livewire\Curriculum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    public function regulation() {
        return $this->belongsTo(Regulation::class);
    }

    public function curricula() {
        return $this->hasMany(Curriculum::class);
    }
}
