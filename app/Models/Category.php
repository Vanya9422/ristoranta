<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 * @property mixed business
 * @package App\Models
 */
class Category extends Model
{
    use Validateable, SoftDeletes, CascadeSoftDeletes;

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['subCategory', 'dishes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'business_id',
        'parent_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Validation Rules for Validatable trait
     *
     * @var array $rules
     */
    protected array $rules = [
        'create' => [
            'name' => ['required', 'string', 'max:50'],
            'business_id' => ['required', 'exists:businesses,id'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ],
        'update' => [
            'id' => ['required', 'exists:categories'],
            'name' => ['nullable', 'string', 'max:50'],
            'business_id' => ['nullable', 'exists:businesses,id'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]
    ];

    /**
     * @return HasMany
     */
    public function subCategory(): HasMany
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    /**
     * @return HasMany
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * @return BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
