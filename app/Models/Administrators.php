<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrators extends Model
{
    use HasFactory;

    protected $table = 'administrators';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'admin_name',
        'admin_email',
        'password',
        'role',
        'created_at',
        'updated_at',
    ];
}