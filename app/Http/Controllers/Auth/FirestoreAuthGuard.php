<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\TokenAuth;


class FirestoreAuthGuard implements Guard
{

    protected $request;
    protected $provider;
    protected $user;
    protected $token;
    protected $tokenauth;


    public function __construct(UserProvider $provider, Request $request) {
        
        $this->request = $request;
        $this->provider = $provider;
        $this->user = NULL;
        $this->tokenauth = new TokenAuth();
    }
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check() {
        return ! is_null($this -> user);
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest() {
        return !($this -> check());
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user() {
        if (! is_null($this->user)) {
            return $this->user;
          }
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id() {
        if ($user = $this->user()) {
            return $this->user()->getAuthIdentifier();
          }
    }

    /**
     * Gets the credentials from input
     *
     * @return array|null
     */
    public function getParams() {

        if (empty($jsondata = file_get_contents('php://input'))){
            $params =array('username' => $this->request->query('user'),
             'password' => $this->request->query('pass'));

             $jsondata = json_encode($params); // lmao what the fuck am i doing
        }

 
        return (!empty($jsondata) ? json_decode($jsondata, TRUE) : NULL);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = []) {
        $credentials;
        // credential input validation
        if (empty($credentials['username']) || empty($credentials['password'])) {
            if (!($credentials = $this -> getParams())) {
                return false;
            }  
        }
        
        $user = $this -> provider -> retrieveByCredentials($credentials);
        
        if (!is_null($user) && $this -> provider -> validateCredentials($user, $credentials)) {
            $this -> setUser($user);
            return true;
        }
        else {
            return false;
        }
    }
    

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user) {
        $this->user = $user;
    
    }

    public function authenticate() {}

    public function setToken() {}

    /**
     * Creates a JWT Token using username as uid
     *
     * @return Token
     */
    public function getToken() {
        if ($this -> check()) {
            
            $this -> token = $this -> tokenauth -> createToken($this -> user -> getAuthIdentifier());
            return $this -> token;
        }
        elseif ($this -> check() && !is_null($this -> token)) {
            return $this -> token;
        }

    }
}
