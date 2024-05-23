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
        "total_o",
        "ce",
        "total_ce",
        "ea",
        "total_ea",
        "an",
        "total_an",
        "n",
        "total_n",
        "r",
        "total_r",
        "i",
        "total_i",
        "a",
        "total_a",
        "s",
        "total_s",
        "e",
        "total_e",
        "c",
        "total_c",
        "math",
        "total_math",
        "visual",
        "total_visual",
        "memory",
        "total_memory",
        "reading",
        "total_reading",
        "induction",
        "total_induction",
        "quantitative_reasoning",
        "total_quantitative_reasoning",

    ];
}