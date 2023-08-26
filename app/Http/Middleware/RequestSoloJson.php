<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestSoloJson
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isJson()) {
            return response()->json(['message' => 'Solo requests JSON'], 406);
        }

        return $next($request);
    }
}
