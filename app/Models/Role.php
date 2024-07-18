<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes;

    public function scopeToken($query, $token)
    {
        $table  = $this->getTable();
        $column = $this->primaryKey;

        return $query->where(DB::Raw("md5(concat({$table}.{$column}, '-', date_format(curdate(), '%Y%m%d')))"), $token);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleted(function (Role $role) {
            $role->update(['deleted_by' => Auth::user()->id]);
        });
    }
}
