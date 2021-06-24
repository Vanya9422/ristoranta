<?php

namespace App\Services;

use Kreait\Firebase\Auth;

/**
 * Class FirebaseService
 * @package App\Services
 */
class FirebaseService
{

    /**
     * Firebase Auth User Object
     *
     * @var $claims
     */
    private $claims;

    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * UserService constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function verifyToken(string $token): void
    {
        $this->claims = $this->auth->verifyIdToken($token)->claims();
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->claims->get('sub');
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->claims->get('phone_number');
    }
}
