<?php

namespace Apachish\Auth\App\Providers;

use App\Models\User;
use Armanbroker\Auth\Services\UserService;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
use App\Providers\AuthServiceProvider;
use Armanbroker\Auth\Models\Token;
use Illuminate\Support\Facades\Log;


class AuthApachishServiceProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Auth::viaRequest('arman-token', function ($request) {
            $token = $request->bearerToken();
            if(!$token && $request->api_token) {
                $token = cache()->get($request->api_token);
            }
            $data = UserService::decrypt_token('token', $token);
            if (!$data) return null;
            cache()->put("token_" . $data['token_id'], now(), config('auth-arman.ttl'));
// && $request->ip() == $data['ip']
            //&& $request->header('User-Agent') == $data['user_agent'] todo remove for check front
            if (!empty($data)) {
                $last_request = cache()->get("token_" . $data['token_id']);
                $token_device = Token::find(data_get($data,"token_id"));
                if($token_device == null){
                    $this->removeToken();
                }
                elseif ($last_request &&
                    Carbon::parse($last_request)->diffInRealSeconds(now()) < config('auth-arman.ttl') &&
                    Carbon::parse($data['created_at'])->diffInRealSeconds(now()) < config('auth-arman.max_validation_time')
                ) {
                    cache()->put("token_" . $data['token_id'], now(), config('auth-arman.ttl'));
                    $user = User::find($data['user_id']);
                    if($user) {
                        $user_api_token = cache()->get("token_" . $data['token_id'] . "_" . $user->id);
                        if ($user_api_token)
                            cache()->put("token_" . $data['token_id'] . "_" . $user->id, $user_api_token, config('auth-arman.ttl'));
                        return $user;
                    }else{
                        $this->removeToken();
                        return  null;
                    }

                }
            }else{
                $this->removeToken();
            }
//            return User::where('token', $request->token)->first();
        });
    }

    public function removeToken()
    {
        $cookies = [
            'expires_in' => NULL,
            'token' => NULL,
            'refresh_token' => NULL,
            'refreshToken' => NULL,
            'shahriar' => 1365,
        ];
        foreach ($cookies as $key => $cookie) {
            Cookie::forget($key);
            setcookie($key, "1", time() + (86400 * 30), "/", env("SESSION_DOMAIN")); // 86400 = 1 day
            setcookie($key, $cookie, time() + (86400 * 30), "/", env("SESSION_DOMAIN")); // 86400 = 1 day
        }
    }
}
