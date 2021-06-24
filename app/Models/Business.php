<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed user_id
 * @property array columns
 */
class Business extends Model
{
    use HasFactory, Validateable, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'parent_id', 'business_type_id', 'deleted_at'];

    /**
     * @var array|string[]
     */
    public array $selfRelations = ['type', 'parent', 'branches', 'tables', 'workers', 'user', 'dishes'];

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['branches', 'tables', 'workers'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'phone',
        'address',
        'business_type_id',
        'language_id',
        'country_id',
        'user_id',
        'parent_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     *|---------------------------------------------------------------------------
     *| Validation Rules for Validatable trait
     *|---------------------------------------------------------------------------
     *| TODO  {
     *| TODO   "redis" : "add  __Redis Cache System__ which will work with its own __Validation Rule__",
     *| TODO   "files" : "add  __Validate Custom Class__ to make the model even smaller, to automate the operation"
     *| TODO  }
     *|---------------------------------------------------------------------------
     *| @var array[] $rules
     */
    protected array $rules = [
        'create' => [
            'title' => ['required', 'max:250'],
            'business_type_id' => ['required', 'exists:business_types,id'],
            'phone' => ['nullable', 'max:30'],
            'address' => ['nullable', 'string', 'max:250'],
            'parent_id' => ['nullable', 'exists:businesses,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'selected.workers.*' => ['nullable', 'exists:users,id'],
            'selected.tables.*' => ['nullable', 'exists:tables,id'],
        ],
        'update' => [
            'id' => ['required', 'exists:businesses,id'],
            'title' => ['required', 'max:250'],
            'business_type_id' => ['required', 'exists:business_types,id'],
            'phone' => ['nullable', 'max:30'],
            'address' => ['nullable', 'string', 'max:250'],
            'parent_id' => ['nullable', 'exists:businesses,id'],
            'language_id' => ['nullable', 'exists:languages,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'selected.workers.*' => ['nullable', 'exists:users,id'],
            'selected.tables.*' => ['nullable', 'exists:tables,id'],
        ],
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGiveType($query)
    {
        return $query->with('type', function ($q) {
            $q->select('id', 'type');
        });
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasAccess($id): bool
    {
        return $this->workers()->wherePivot('user_id', '=', $id)->exists();
    }

    /**
     * @param $id
     * @return bool
     */
    public function existsWorker($id): bool
    {
        return $this->workers()->wherePivot('user_id', '=', $id)->exists();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGeneral($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Set the user's first name.
     *
     * @param string $value
     * @return void
     */
    public function setParentIdAttribute(string $value)
    {
        $this->attributes['parent_id'] = $value ?: null;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * @return HasMany
     */
    public function branches(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->giveType();
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id')->giveType();
    }

    /**
     * @return HasMany
     */
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id')->select('id', 'type');
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return BelongsToMany
     */
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @return MorphMany
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class,'reviewable');
    }
}
