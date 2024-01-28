<?php

namespace App\Http\Requests;

use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use App\Rules\PositionUniqueCombination;
use App\Traits\CustomValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamSelectionRequest extends FormRequest
{
    use CustomValidationTrait;

    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        return [
            '*.position' => ['required', 'string', Rule::in(PlayerPosition::values())], new PositionUniqueCombination($this->all()),
            '*.mainSkill' => ['required', 'string', Rule::in(PlayerSkill::values())],
            '*.numberOfPlayers' => ['required', 'integer']
        ];
    }
}
