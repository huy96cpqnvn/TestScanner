<?php

namespace App\Providers;

use App\Events\CheckUserTokenEvent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\UserToken;
use App\Models\User;
use App\Models\UserAccount;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy('App\Models\User', 'App\Policies\UserPolicy');
        Gate::policy('App\Models\AccountCommission', 'App\Policies\AccountCommissionPolicy');
        Gate::policy('App\Models\UserGroup', 'App\Policies\UserGroupPolicy');
        Gate::policy('App\Models\SettingCommission', 'App\Policies\SettingCommissionPolicy');
        Gate::policy('App\Models\FileImport', 'App\Policies\FileImportPolicy');
        Gate::policy('App\Models\MailTemplate', 'App\Policies\MailTemplatePolicy');
        Gate::policy('App\Models\MailTemplateLocale', 'App\Policies\MailTemplateLocalePolicy');

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {            
            if ($request->input('token') || $request->header('token')) {
                $token = $request->input('token') ? $request->input('token') : $request->header('token');
                try {  
                    // check token exist
                    if (UserToken::checkToken($token, $user_token)) {                               
                        event(new CheckUserTokenEvent($user_token));
                        return $this->_getUser($user_token->user_id, $user_token->user_group_id, $user_token);
                    }
                } catch (\Exception $e) {
                    // An error while decoding token
                    return null;
                }
            }
            return null;
        });
    }

    private function _getUser($user_id, $user_group_id, $user_token)
    {
        $user = User::where('id', $user_id)
            ->where('status', User::STATUS_ACTIVE)
            ->first();
        if ($user) {
            $user->setCurrentUserGroup($user_group_id);
            $user->setUserToken($user_token);
            return $user;
        }        
        return null;
    }
}
