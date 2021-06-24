<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed product
 * @property mixed imageable
 */
class Image extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url','is_primary'];

    /**
     * @var string[]
     */
    protected $hidden = ['imageable_type', 'imageable_id'];

    /**
     * Get the parent commentable model (post or video).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
