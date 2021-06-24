<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Dish
 * @property mixed business
 * @property mixed image
 * @package App\Models
 */
class Dish extends Model
{
    use Validateable, SoftDeletes, CascadeSoftDeletes;

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['tags', 'block', 'image'];

    /**
     * @var array|string[]
     */
    public array $selfRelations = ['image', 'tags', 'category', 'business'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'weight',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'business_id',
        'category_id',
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
            'description' => ['required', 'max:1000', 'string'],
            'price' => ['required', 'max:10'],
            'business_id' => ['required', 'exists:businesses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'file' => ['required', 'mimes:jpg,png,jpeg,svg,pdf', 'max:5120', 'file'],
            'weight' => ['nullable', 'max:10'],
            'calories' => ['nullable', 'max:10'],
            'proteins' => ['nullable', 'max:10'],
            'fats' => ['nullable', 'max:10'],
            'selected*' => ['nullable', 'array', 'exists:tags,id'],
            'carbohydrates' => ['nullable', 'max:10'],
        ],
        'update' => [
            'id' => ['required', 'exists:dishes'],
            'name' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'max:1000', 'string'],
            'price' => ['nullable', 'max:10'],
            'weight' => ['nullable', 'max:10'],
            'calories' => ['nullable', 'max:10'],
            'proteins' => ['nullable', 'max:10'],
            'fats' => ['nullable', 'max:10'],
            'carbohydrates' => ['nullable', 'max:10'],
            'business_id' => ['nullable', 'exists:businesses,id'],
            'category_id' => ['nullable', 'exists:businesses,id'],
            'selected*' => ['nullable', 'array', 'exists:tags,id'],
            'file' => ['sometimes', 'mimes:jpg,png,jpeg,svg,pdf', 'max:5120', 'file'],
        ],
        'block' => [
            'dish_id' => ['required', 'exists:dishes,id'],
            'business_id' => ['required', 'exists:businesses,id'],
            'action' => ['required', 'string', 'max:7', 'min:5'],
        ]
    ];

    /**
     * Get all of the post's comments.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->select('id','url','imageable_type','imageable_id');
    }

    /**
     * @return BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'dish_tags');
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->belongsTo(BlockDish::class)->exists();
    }

    /**
     * @return HasOne
     */
    public function block(): HasOne
    {
        return $this->hasOne(BlockDish::class);
    }
}
