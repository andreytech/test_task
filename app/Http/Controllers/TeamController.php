<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientPlayersException;
use App\Http\Requests\TeamSelectionRequest;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    /**
     * @throws InsufficientPlayersException
     */
    public function process(TeamSelectionRequest $request): JsonResponse
    {
        $selectedPlayerIds = [];

        foreach ($request->all() as $playerRequest) {

            $playerSkillMatchIds = Player::query()
                ->join('player_skills', 'player_skills.player_id', '=', 'players.id')
                ->where('players.position', $playerRequest['position'])
                ->where('player_skills.skill', $playerRequest['mainSkill'])
                ->whereNotIn('players.id', $selectedPlayerIds)
                ->orderBy('player_skills.value', 'desc')
                ->limit($playerRequest['numberOfPlayers'])
                ->distinct()
                ->pluck('players.id')
                ->toArray();

            $selectedPlayerIds = array_merge($selectedPlayerIds, $playerSkillMatchIds);

            if(count($playerSkillMatchIds) >= $playerRequest['numberOfPlayers']) {
                continue;
            }

            $limit = $playerRequest['numberOfPlayers'] - count($playerSkillMatchIds);

            $playerMaxSkillIds = Player::query()
                ->join('player_skills', 'player_skills.player_id', '=', 'players.id')
                ->where('players.position', $playerRequest['position'])
                ->whereNotIn('players.id', $selectedPlayerIds)
                ->orderBy('player_skills.value', 'desc')
                ->limit($limit)
                ->distinct()
                ->pluck('players.id')
                ->toArray();

            $selectedPlayerIds = array_merge($selectedPlayerIds, $playerMaxSkillIds);

            if(count($selectedPlayerIds) < $playerRequest['numberOfPlayers']) {
                throw new InsufficientPlayersException($playerRequest['position']);
            }
        }

        $players = Player::findMany($selectedPlayerIds);

        return response()->json($players);
    }
}
