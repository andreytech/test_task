<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(): JsonResponse
    {
        $players = Player::get();

        return response()->json($players);
    }

    public function show(Player $player): JsonResponse
    {
        return response()->json($player);
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        $player = Player::create($request->only('name', 'position'));

        foreach ($request->playerSkills as $skillData) {
            $player->skills()->create($skillData);
        }

        $player->load('skills');

        return response()->json($player);
    }

    public function update(StorePlayerRequest $request, Player $player): JsonResponse
    {
        // Update player details
        $player->name = $request->name;
        $player->position = $request->position;
        $player->save();

        $player->skills()->delete();

        foreach ($request->playerSkills as $skillData) {
            $skill = new PlayerSkill();
            $skill->skill = $skillData['skill'];
            $skill->value = $skillData['value'];
            $skill->player_id = $player->getKey();
            $skill->save();
        }

        $player->load('skills');

        return response()->json($player);
    }
    public function destroy(Player $player): JsonResponse
    {
        $player->delete();

        return response()->json(['message' => 'Player and their skills have been deleted successfully']);
    }
}
