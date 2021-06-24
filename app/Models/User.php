<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property mixed first_name
 * @property mixed email
 * @property mixed phone
 * @property mixed last_name
 * @property mixed uid
 * @property mixed id
 * @property mixed full_name
 * @property mixed business
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasRoles, Validateable, SoftDeletes, CascadeSoftDeletes;

    /**
     * @var string
     */
    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uid',
        'first_name',
        'last_name',
        'middle_name',
        'birthday',
        'phone',
        'address',
        'email',
        'password',
        'active',
        'chat_id',
    ];

    /**
     * @var array
     */
    protected array $uniqueRules = ['phone', 'email'];

    /**
     * Validation Rules for Validatable trait
     *
     * @var array $rules
     */
    protected array $rules = [
        'register' => [
            'phone' => ['required', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],
        'login' => [
            'phone' => ['required', 'max:50', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
        ],
        'passwordMake' => [
            'phone' => ['required', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],
        'passwordReset' => [
            'phone' => ['required', 'max:50', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],
        'create' => [
            'phone' => ['required', 'max:30', 'unique:users'],
            'business_id' => ['required', 'exists:businesses,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'first_name' => ['sometimes', 'nullable', 'max:250'],
            'last_name' => ['sometimes', 'nullable', 'max:250'],
            'middle_name' => ['sometimes', 'nullable', 'max:250'],
            'birthday' => ['sometimes', 'nullable', 'date'],
            'address' => ['sometimes', 'nullable', 'string'],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'unique:users,email'],
        ],
        'update' => [
            'id' => ['required', 'exists:users'],
            'phone' => 'sometimes|max:30|unique:users,phone,',
            'business_id' => ['sometimes', 'nullable', 'exists:businesses,id'],
            'role_id' => ['sometimes', 'nullable', 'exists:roles,id'],
            'first_name' => ['sometimes', 'nullable', 'max:250'],
            'last_name' => ['sometimes', 'nullable', 'max:250'],
            'middle_name' => ['sometimes', 'nullable', 'max:250'],
            'birthday' => ['sometimes', 'nullable', 'date'],
            'address' => ['sometimes', 'nullable', 'string'],
            'email' => 'sometimes|nullable|string|email|unique:users,email,',
        ]
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = ['password', 'active', 'deleted_at', 'pivot'];

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['business'];

    /**
     * @var array|string[]
     */
    public array $selfRelations = ['business', 'businesses', 'roles'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->hasRole(config('roles.manager.name'));
    }

    /**
     * @return bool
     */
    public function isWaiter(): bool
    {
        return $this->hasRole(config('roles.waiter.name'));
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Set the user's first name.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get all of the post's comments.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get all of the post's comments.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * @return HasMany
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    /**
     * @return BelongsToMany
     */
    public function business(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uid) {
                $model->uid = Str::uuid();
            }
        });
    }
}
