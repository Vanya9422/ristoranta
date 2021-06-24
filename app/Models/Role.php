<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @method static findOrFail(mixed $role_id)
 */
class Role extends SpatieRole
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'display_name',
        'guard_name',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['pivot'];
}
