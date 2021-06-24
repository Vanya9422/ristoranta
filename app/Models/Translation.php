<?php

namespace App\Models;

use App\Traits\Validateable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use Validateable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'translations', 'language_id', 'section_id'];

    /**
     * @var array
     */
    protected $casts = [
        'translations' => 'array'
    ];

    /**
     * @var array|string[]
     */
    public array $selfRelations = ['language', 'section'];

    /**
     *|---------------------------------------------------------------------------
     *| Validation Rules for Validatable trait
     *|---------------------------------------------------------------------------
     *| @var array[] $rules
     */
    protected array $rules = [
        'create' => [
            'translations' => 'required|array',
            'language_id' => 'required|exists:languages,id',
            'section_id' => 'required|exists:sections,id',
        ],
        'update' => [
            'id' => ['required', 'exists:translations,id'],
            'translations' => 'sometimes|array',
            'language_id' => 'sometimes|exists:languages,id',
            'section_id' => 'sometimes|exists:sections,id',
        ],
    ];

    /**
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
