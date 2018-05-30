<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Database\FirestoreDB;

class User extends Authenticatable
{
    private $conn;

    private $username;
    private $password;

    protected $remember_token_name = 'remember_token';

    public function __construct() {
        $this -> conn = new FirestoreDB();
    }

    public function fetch_user_creds(Array $credentials) {
        $document = 'user_table';
        $creds = ['user', '=', $credentials['username']];
        $user_array = $this -> conn -> get_document($document, $creds);

        if ($user_array) {
            foreach ($user_array as $user) {
                $retrieved_creds = [$user['user'], $user['pass']];
                $this -> username = $retrieved_creds[0];
                $this -> password = $retrieved_creds[1];
            }
        }
        
        else {
            return false;
        }
        
        return $this;  
    }

    public function getAuthIdentifierName() {
        return 'username';
    }

    public function getAuthIdentifier() {
        return $this->username;
    }

    public function getAuthPassword() {
        // should return the hashed password
        return $this->password;
    }

    public function getRememberToken() {
        return $this -> remember_token_name;
    }

    public function setRememberToken($value) {
        $this -> remember_token_name = $value;
    }

    public function getRememberTokenName() {
        
    }
}
