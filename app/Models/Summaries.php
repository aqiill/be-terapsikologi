<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summaries extends Model
{
    use HasFactory;

    protected $table = 'summaries';
    protected $fillable = [
        "student_id",
        "school_id",
        "o",
        "ce",
        "ea",
        "an",
        "n",
        "r",
        "i",
        "a",
        "s",
        "e",
        "c",
        "math",
        "visual",
        "memory",
        "reading",
        "induction",
        "quantitative_reasoning",

    ];
}