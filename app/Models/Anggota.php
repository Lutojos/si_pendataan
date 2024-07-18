<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Anggota extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'anggota';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'umur',
        'gender',
        'address',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'desa_id',
        'phone_number',
        'image_path',
        'latitude',
        'longitude',
    ];

    public function scopeToken($query, $token)
    {
        $table  = $this->getTable();
        $column = $this->primaryKey;

        return $query->where(DB::Raw("md5(concat({$table}.{$column}, '-', date_format(curdate(), '%Y%m%d')))"), $token);
    }

    public function images()
    {
        return $this->hasMany(AnggotaImage::class, 'anggota_id', 'id');
    }

    public function getProfil()
    {
        $path = 'storage/' . $this->image_path;
        if (file_exists(public_path($path)) && $this->image_path) {
            return asset($path);
        }
        return asset('img/avatar.png');
    }
}
