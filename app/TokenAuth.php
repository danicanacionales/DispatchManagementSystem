<?php

namespace App;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Keychain; 
use Lcobucci\JWT\Signer\Rsa\Sha256;

class TokenAuth {

    public function __construct() {

    }

    public function createToken($uid) {
        $signer = new Sha256();

        $keychain = new Keychain();


        $token = (new Builder())->setIssuer('firebase-adminsdk-yodsk@xxxxxxxxxxxx.iam.gserviceaccount.com') // Configures the issuer (iss claim)
                ->setSubject('firebase-adminsdk-yodsk@xxxxxxxxxxxxxx.iam.gserviceaccount.com')
                ->setAudience('https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit') // Configures the audience (aud claim)
                ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                ->setExpiration(time() + (60*60)) // Configures the expiration time of the token (exp claim)
                
                ->set('uid', $uid)
                ->sign($signer,  $keychain->getPrivateKey('file://'.base_path().'/rsaprivate_unenc.pem'))
                ->getToken(); // Retrieves the generated token

        return $token;
    }

    public function validateToken($token, $uid) {
        $data = new ValidationData();
        $data -> setIssuer('firebase-adminsdk-yodsk@xxxxxxxxxxxxxxxxx.iam.gserviceaccount.com');
        $data -> setAudience('https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit');
        $data -> setId($uid);

        $verifysigned = $token->verify($signer, $keychain->getPublicKey('file://'.base_path().'/rsapublic.pem'));

        return $token -> validate($data) && $verifysigned;
    }

    protected function parseToken($token) {

    }
}