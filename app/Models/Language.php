<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use Validateable, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'native', 'code', 'regional'];

    /**
     * @var array|string[]
     */
    protected array $cascadeDeletes = ['translations'];

    /**
     *|---------------------------------------------------------------------------
     *| Validation Rules for Validatable trait
     *|---------------------------------------------------------------------------
     *| @var array[] $rules
     */
    protected array $rules = [
        'create' => [
            'name' => ['required', 'max:50'],
            'native' => ['required', 'max:50'],
            'code' => ['required', 'max:10'],
            'regional' => ['required', 'max:10'],
        ],
        'update' => [
            'id' => ['required', 'exists:languages,id'],
            'name' => ['sometimes', 'string', 'max:50'],
            'native' => ['sometimes', 'string', 'max:50'],
            'code' => ['sometimes', 'string', 'max:10'],
            'regional' => ['sometimes', 'string', 'max:10'],
        ],
    ];

    /**
     * @return HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
