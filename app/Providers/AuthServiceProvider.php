<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Auth\FirestoreAuthGuard;
use App\Database\FirestoreDB;
use App\User;
use App\Providers\FirestoreAuthServiceProvider;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        $this->app->bind('App\Database\FirestoreDB', function ($app) {
            return new FirestoreDB();
        });
        
        $this->app->bind('App\User', function ($app) {
            return new User();
        });
        // add custom guard provider
        Auth::provider('firestore', function ($app, array $config) {
            return new FirestoreAuthServiceProvider($app->make('App\User'));
        });
    
        // add custom guard
        Auth::extend('json', function ($app, $name, array $config) {
            return new FirestoreAuthGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
