<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    use HasFactory;
    protected $guarded = [];

    function category()
    {
        return $this->belongsTo(QuestionCategories::class, 'category_id', 'id');
    }
}