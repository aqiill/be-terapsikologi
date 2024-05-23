<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;

    protected $fillable = [
        "school_id",
        "school_status",
        "student_name",
        "final_score",
        "student_email",
        "password",
        "birth_date",
        "gender",
        "province",
        "city",
        "address",
        "contact",
        "payment_status",
        "recommendation_type",
        "role"
    ];
}