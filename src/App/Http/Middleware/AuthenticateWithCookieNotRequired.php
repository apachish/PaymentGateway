<?php

namespace Armanbroker\Auth\App\Http\Middleware;

use Armanbroker\Auth\Services\UserService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth as AuthAlias;
use Illuminate\Support\Facades\Log;

class AuthenticateWithCookieNotRequired
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if($request->cookie("armanToken"))
        {
            $request->headers->set('Authorization', "Bearer " . $request->cookie("armanToken"));
            $this->authenticate($request, $guards);
        }
        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationExceptiongit
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (AuthAlias::guard($guard)->check()) {
                return AuthAlias::shouldUse($guard);
            }
        }
        $refresh_token = $request->cookie("armanRefreshToken");
        Cookie()->forget('armanRefreshToken');
        if ($refresh_token) {
            $data = UserService::decrypt_token('refresh', $refresh_token);

            if ($data && $request->ip() == $data['ip'] && $request->header('User-Agent') == $data['user_agent'])
            {
                cache()->put("token_".$data['token_id'],now(),config('auth-arman.ttl'));
                $token = UserService::checkRefreshToken($data);
                $request->headers->set('Authorization', "Bearer " . $token);
                foreach ($guards as $guard) {
                    if (AuthAlias::guard($guard)->check()) {
                        return AuthAlias::shouldUse($guard);
                    }
                }
            }
        }
        $this->unauthenticated($request, $guards);

    }

    /**
     * Handle an unauthenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //
    }
}
