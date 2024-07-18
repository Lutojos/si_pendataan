<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Kecamatan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'kecamatan';
    public $timestamps = true;

    protected $fillable = [
        'provinsi_id',
        'kota_id',
        'kecamatan_name',
    ];

    public function scopeToken($query, $token)
    {
        $table  = $this->getTable();
        $column = $this->primaryKey;

        return $query->where(DB::Raw("md5(concat({$table}.{$column}, '-', date_format(curdate(), '%Y%m%d')))"), $token);
    }
}
