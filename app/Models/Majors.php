<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Majors extends Model
{
    use HasFactory;

    protected $table = 'majors';
    protected $fillable = [
        'classification',
        'field',
        'major',
        'major_description',
        'o',
        'ce',
        'ea',
        'an',
        'n',
        'r',
        'i',
        'a',
        's',
        'e',
        'c',
        'math',
        'visual',
        'memory',
        'reading',
        'induction',
        'quantitative_reasoning',
    ];
}