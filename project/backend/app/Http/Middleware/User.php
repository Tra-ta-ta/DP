<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class User
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->isUser()) {
                return $next($request);
            }
            if (Auth::user()->isPersonal()) {
                return $next($request);
            }
        }
        return response()->json(['status' => 'Вы не авторизованы'], 401);
    }
}
