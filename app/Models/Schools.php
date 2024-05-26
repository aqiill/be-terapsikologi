<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schools extends Model
{
    use HasFactory;

    protected $table = 'schools';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'school_name',
        'npsn',
        'school_email',
        'password',
        'province',
        'city',
        'address',
        'operator_name',
        'contact',
        'role',
        'payment_status',
        'created_at',
        'updated_at',
    ];
}