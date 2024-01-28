<?php

namespace App\Exceptions;

use Exception;

class InsufficientPlayersException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'error' => 'Insufficient number of players for position: '. $this->message
        ], 400);
    }
}
