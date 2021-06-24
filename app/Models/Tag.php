<?php

namespace App\Models;

use App\Traits\Validateable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Tag
 * @package App\Models
 */
class Tag extends Model
{
    use Validateable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'color',
        'business_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['pivot'];

    /**
     * Validation Rules for Validatable trait
     *
     * @var array $rules
     */
    protected array $rules = [
        'create' => [
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'business_id' => ['required', 'exists:businesses,id'],
        ],
        'update' => [
            'id' => ['required', 'exists:tags'],
            'name' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'business_id' => ['nullable', 'exists:businesses,id'],
        ]
    ];

    /**
     * @return BelongsToMany
     */
    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class);
    }
}
