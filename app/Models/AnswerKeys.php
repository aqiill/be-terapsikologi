<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerKeys extends Model
{
    use HasFactory;
    protected $guarded = [];

    function question()
    {
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }
}