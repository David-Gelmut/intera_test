<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;

class ResetPasswordView
{
    /**
     * Перенаправляет случайные GET-запросы к бэкенду на страницу Vue.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $token = $request->route('token');
        $email = $request->query('email');

        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/') . '/reset-password';

        $finalUrl = $frontendUrl . '/' . $token . '?email=' . call_user_func('urlencode', $email);

        return redirect()->away($finalUrl);
    }
}
