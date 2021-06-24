<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property mixed business
 * @property mixed user_id
 * @property mixed qrcode
 */
class Table extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, Validateable, SoftDeletes, CascadeSoftDeletes, Validateable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'number',
        'status',
        'seats',
        'business_id',
        'waiter_id',
        'manager_id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @var array|string[]
     */
    public array $selfRelations = ['business.type', 'qrcode'];

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['qrcode'];

    /**
     * @var string[]
     */
    protected $hidden = ['deleted_at'];

    /**
     * Validation Rules for Validatable trait
     *
     * @var array $rules
     */
    protected array $rules = [
        'create' => [
            'business_id' => ['required', 'exists:businesses,id'],
            'title' => ['sometimes', 'nullable', 'max:250'],
            'number' => ['sometimes', 'nullable', 'numeric'],
            'seats' => ['sometimes', 'nullable', 'numeric'],
            'status' => ['sometimes', 'nullable', 'string'],
        ],
        'update' => [
            'id' => ['required', 'exists:tables,id'],
            'business_id' => ['sometimes', 'nullable', 'exists:businesses,id'],
            'title' => ['sometimes', 'nullable', 'max:250'],
            'number' => ['sometimes', 'nullable', 'numeric'],
            'seats' => ['sometimes', 'nullable', 'numeric'],
            'status' => ['sometimes', 'nullable', 'string'],
        ],
        'tableAuth' => [
            'table_id' => 'required', 'numeric', ['exists:table,id']
        ],
        'guest' => [
            'table_id' => 'required', 'numeric', ['exists:table,id'],
            'chat_id' => 'required', ['exists:users,chat_id'],
        ],
        'addWorkers' => [
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'selected.*' => ['required', 'exists:tables,id'],
        ],
        'review' => [
            'table_id' => 'required', 'numeric', ['exists:table,id'],
            'review' => ['required', 'max:500'],
        ]
    ];

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
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @return BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->select('id', 'title', 'user_id', 'business_type_id');
    }

    /**
     * Get all of the post's comments.
     */
    public function qrcode(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * @return BelongsTo
     */
    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id')->select('id', 'first_name', 'last_name', 'chat_id');
    }

    /**
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id')->select('id', 'first_name', 'last_name');
    }
}
