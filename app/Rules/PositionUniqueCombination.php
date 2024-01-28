<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PositionUniqueCombination implements Rule
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function passes($attribute, $value)
    {
        $combinations = [];
        foreach ($this->data as $item) {
            $combinations[] = $item['position'] . '-' . $item['mainSkill'];
        }

        return count($combinations) === count(array_unique($combinations));
    }

    public function message()
    {
        return 'The position and main skill combination must be unique.';
    }
}
