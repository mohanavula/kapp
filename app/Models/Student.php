<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function specialization() {
        return $this->belongsTo(Specialization::class);
    }

    public function regulation() {
        return $this->belongsTo(Regulation::class);
    }

    public function aggregates() {
        return $this->hasMany(Aggregate::class);
    }
}
