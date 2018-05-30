<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;



class FirestoreAuthServiceProvider implements UserProvider
{

    private $model;

    public function __construct(\App\User $user_model) {
        $this -> model = $user_model;
    }
    

    

    public function retrieveByCredentials(array $credentials) {
        if (empty($credentials)) {
            return;
        }

        $user = $this -> model -> fetch_user_creds($credentials);

        return $user;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        $username_check = ($credentials['username']) == ($user -> getAuthIdentifier());
        $password_check = Hash::check($credentials['password'], $user -> getAuthPassword());
        return $username_check && $password_check;
    }

    public function retrieveById($identifier) {}
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

}
