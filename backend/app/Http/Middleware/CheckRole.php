<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleString): Response
    {
        if (! $request->user()) {
            return response()->json(['message' => 'Необходима авторизация.'], 401);
        }

        // 2. Безопасно пытаемся преобразовать строку из параметров роута в наш Enum
        $requiredRole = UserRole::tryFrom($roleString);

        // 3. Проверяем: совпадает ли роль пользователя из базы с требуемой ролью
        // Так как в модели User колонка role кастится в Enum, мы можем сравнивать их напрямую через ===
        if ($request->user()->role !== $requiredRole) {
            return response()->json([
                'message' => 'У вас нет прав для выполнения этого действия.'
            ], 403);
        }

        return $next($request);
    }
}
