<?php

namespace Database\Factories;

use App\Enums\PlayerSkill;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerSkill>
 */
class PlayerSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'skill' => $this->faker->randomElement(PlayerSkill::values()),
            'value' => $this->faker->numberBetween(1, 100),
            'player_id' => Player::factory(),
        ];
    }
}
