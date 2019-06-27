<?php

namespace Sngular\Auth\Auth\Handler;

use League\OAuth2\Client\Token\AccessToken;
use Sngular\Auth\Provider\Keycloak\ResourceOwner\KeycloakResourceOwner;

class SessionHandler
{
    const KEY_NAME = 'sngulauth';

    /**
     * @var AccessToken
     */
    private $token;

    /**
     * @var KeycloakResourceOwner
     */
    private $resourceOwner;

    /**
     * Fetch token and user data from session and build the required objects
     * SessionHandler constructor.
     */
    public function __construct()
    {
        if (!isset($_SESSION[self::KEY_NAME])) {
            return;
        }
        $this->token = new AccessToken(json_decode($_SESSION[self::KEY_NAME]['token'], true));
        $this->resourceOwner = new KeycloakResourceOwner(json_decode($_SESSION[self::KEY_NAME]['userData'], true));
    }

    /**
     * @param AccessToken $accessToken
     * @param array $userData
     */
    public static function persistSessionData(AccessToken $accessToken, array $userData)
    {
        $_SESSION[self::KEY_NAME]['token']      = json_encode($accessToken);
        $_SESSION[self::KEY_NAME]['userData']   = json_encode($userData);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isAuthenticated()
    {
        $authenticated = isset($_SESSION[self::KEY_NAME]) && $this->tokenIsStillValid();

        if (!$authenticated) {
            $this->logout();
        }

        return $authenticated;
    }

    /**
     * @param string $role
     * @return bool
     * @throws \Exception
     */
    public function hasRole(string $role)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return in_array($role, $this->resourceOwner->getRoles());
    }

    /**
     * @return string
     */
    public function tokenValidUntil()
    {
        $dateTime = (new \DateTime('@' . $this->token->getExpires()))->setTimezone(new \DateTimeZone('Europe/Madrid'));

        return $dateTime->format('H:i:s');
    }

    /**
     * Check token is still valid
     * @return bool
     * @throws \Exception
     */
    public function tokenIsStillValid()
    {
        return new \DateTime('@' . $this->token->getExpires()) > new \DateTime('now', new \DateTimeZone('GMT'));
    }

    /**
     * @return array
     */
    public function getUser()
    {
        return $this->resourceOwner->toArray();
    }

    /**
     * Destroy session auth data
     */
    public function logout()
    {
        unset($_SESSION[self::KEY_NAME]);
    }
}