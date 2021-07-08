<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exam() {
        return $this->BelongsTo(Exam::class);
    }
}
