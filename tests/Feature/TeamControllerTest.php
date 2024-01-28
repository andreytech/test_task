<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Exceptions\InsufficientPlayersException;
use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamControllerTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;
    public function test_sample()
    {
        $requirements =
            [
                'position' => "defender",
                'mainSkill' => "speed",
                'numberOfPlayers' => 1
            ];


        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $this->assertNotNull($res);
    }

    public function test_players_selection_by_skill_match_and_high_score()
    {
        $positionRequested = 'midfielder';
        $positionNotRequested = 'defender';
        $skillRequested = 'defense';
        $skillNotRequested = 'stamina';

        $userPositionNoMatch = Player::factory()->create(['position' => $positionNotRequested]);
        PlayerSkill::factory()->for($userPositionNoMatch)->create(['skill' => $skillRequested, 'value' => 100]);

        $userSkillMatch = Player::factory()->create(['position' => $positionRequested]);
        PlayerSkill::factory()->for($userSkillMatch)->create(['skill' => $skillRequested, 'value' => 100]);
        PlayerSkill::factory()->for($userSkillMatch)->create(['skill' => $skillNotRequested, 'value' => 90]);

        $userSkillNoMatch = Player::factory()->create(['position' => $positionRequested]);
        PlayerSkill::factory()->for($userSkillNoMatch)->create(['skill' => $skillNotRequested, 'value' => 70]);

        $userSkillNoMatch2 = Player::factory()->create(['position' => $positionRequested]);
        PlayerSkill::factory()->for($userSkillNoMatch2)->create(['skill' => $skillNotRequested, 'value' => 80]);

        $response = $this->postJson(route('api.team.process'), [[
                "position" => $positionRequested,
                "mainSkill" => $skillRequested,
                "numberOfPlayers" => 2
            ]]
        );

        $response->assertOk();
        $response->assertExactJson([
            [
                "id" => 2,
                "name" => $userSkillMatch->name,
                "position" => $positionRequested,
                "skills" => [
                    [
                        "id" => 2,
                        "skill" => $skillRequested,
                        "value" => 100,
                        "player_id" => 2
                    ],
                    [
                        "id" => 3,
                        "skill" => $skillNotRequested,
                        "value" => 90,
                        "player_id" => 2
                    ]
                ]
            ],
            [
                "id" => 4,
                "name" => $userSkillNoMatch2->name,
                "position" => $positionRequested,
                "skills" => [
                    [
                        "id" => 5,
                        "skill" => $skillNotRequested,
                        "value" => 80,
                        "player_id" => 4
                    ]
                ]
            ]
        ]);
    }

    public function test_insufficient_number_of_players_for_position()
    {
        $positionRequested = 'midfielder';
        $positionNotRequested = 'defender';
        $skillRequested = 'defense';

        $userPositionNoMatch = Player::factory()->create(['position' => $positionNotRequested]);
        PlayerSkill::factory()->for($userPositionNoMatch)->create(['skill' => $skillRequested, 'value' => 100]);
        PlayerSkill::factory(2)->for($userPositionNoMatch)->create();

        $userPositionMatch = Player::factory()->create(['position' => $positionRequested]);
        PlayerSkill::factory()->for($userPositionMatch)->create(['skill' => $skillRequested, 'value' => 100]);
        PlayerSkill::factory(2)->for($userPositionMatch)->create();

//        $this->expectException(InsufficientPlayersException::class);

        $response = $this->postJson(route('api.team.process'), [[
                "position" => $positionRequested,
                "mainSkill" => $skillRequested,
                "numberOfPlayers" => 2
            ]]
        );

        $response->assertStatus(400);
        $response->assertExactJson(["error" => "Insufficient number of players for position: ".$positionRequested]);
    }

    public function test_checks_position_skill_uniqueness()
    {
        $response = $this->postJson(route('api.team.process'), [
            [
                "position" => "midfielder",
                "mainSkill" => "speed",
                "numberOfPlayers" => 1
            ],
            [
                "position" => "defender",
                "mainSkill" => "strength",
                "numberOfPlayers" => 2
            ],
            [
                "position" => "midfielder",
                "mainSkill" => "speed",
                "numberOfPlayers" => 2
            ]
        ]);

        $response->assertUnprocessable();
        $response->assertExactJson(["message" => "The position and main skill combination must be unique."]);

    }

}
