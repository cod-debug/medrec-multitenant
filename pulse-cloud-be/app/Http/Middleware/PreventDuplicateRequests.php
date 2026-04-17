<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class PreventDuplicateRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fingerprint = md5($request->method() . 
            $request->ip() . 
            $request->url() . 
            json_encode($request->all()));

            $key = "dedupe:{$fingerprint}";
        
        if(!Cache::add($key, true, now()->addMinutes(0.1))){
            return response()->json(['message' => 'Duplicate request detected. Please wait before trying again.'], 429);
        }
        return $next($request);
    }
}
