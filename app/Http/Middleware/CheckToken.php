<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');
        $bearerToken = str_replace('Bearer ', '', $authorizationHeader);

        if ($bearerToken === config('app.api_token')) {
            return $next($request);
        }

        abort(403, 'Invalid token');
    }
}
