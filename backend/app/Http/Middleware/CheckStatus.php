<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*return response()->json([
            'message' => 'Ваш аккаунт заблокирован или неактивен.',
            'user' => $request->user()

        ], 403);*/
        // Проверяем, залогинен ли юзер и активен ли он
        if ($request->user() && $request->user()->status !== 'active') {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            return response()->json(['message' => 'Ваш аккаунт заблокирован или неактивен.'], 403);
        }
        return $next($request);
    }
}
