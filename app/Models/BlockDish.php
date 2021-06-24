<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockDish extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['dish_id', 'business_id'];

    /**
     * @var bool
     */
    public $incrementing = false;
}
