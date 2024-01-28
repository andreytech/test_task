<?php

namespace App\Http\Requests;

use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use App\Traits\CustomValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * /**
 * @property-read string name
 * @property-read string position
 * @property-read array playerSkills
 */
class StorePlayerRequest extends FormRequest
{
    use CustomValidationTrait;

    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|in:' . implode(',', PlayerPosition::values()),
            'playerSkills.*.skill' => 'required|string|in:' . implode(',', PlayerSkill::values()),
            'playerSkills.*.value' => 'required|integer|between:0,100'
        ];
    }
}
