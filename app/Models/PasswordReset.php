<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';
    public $incrementing  = false;
    protected $table      = 'password_resets';
    public $timestamps    = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];
}
