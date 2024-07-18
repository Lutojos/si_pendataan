<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function getAvatar()
    {
        $path = 'storage/uploads/avatars/' . $this->avatar;
        if (file_exists(public_path($path)) && $this->avatar) {
            return asset($path);
        }

        return asset('img/avatar.png');
    }

    public function getKtp()
    {
        $path = 'storage/uploads/ktp/' . $this->image_path;
        if (file_exists(public_path($path)) && $this->image_path) {
            return asset($path);
        }

        return asset('img/ktp.png');
    }

    //property
    public function properties()
    {
        return $this->hasMany(Property::class, 'id', 'property_id');
    }

    //global scope
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public function scopeToken($query, $token)
    {
        $table  = $this->getTable();
        $column = $this->primaryKey;

        return $query->where(DB::Raw("md5(concat({$table}.{$column}, '-', date_format(curdate(), '%Y%m%d')))"), $token);
    }

    protected static function booted()
    {
        //deleted
        static::deleted(function ($model) {
            $model->email = $model->email . '-' . time();
            $model->save();
        });
    }
}
