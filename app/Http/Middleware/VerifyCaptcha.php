<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCaptcha
{
    protected array $routes = [
        'login',
        'register',
        'password.email',
        'password.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (
            $request->isMethod('post') &&
            $routeName &&
            in_array($routeName, $this->routes, true)
        ) {
            $request->validate([
                'captcha' => 'required|captcha',
            ]);
        }

        return $next($request);
    }
}
