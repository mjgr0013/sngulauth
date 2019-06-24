<?php

namespace Sngular\Keycloak\Manager\Manager;


use Stevenmaguire\OAuth2\Client\Provider\Keycloak;

class AuthManager
{
    const SAML_PROTOCOL     = 'saml';
    const OPENID_PROTOCOL   = 'openid';

    /**
     * @var string
     */
    private $protocol;

    public function openIdProtocol()
    {
        $this->protocol = self::OPENID_PROTOCOL;

        return $this->buildAuthObject();
    }

    /**
     * @return Keycloak
     */
    private function buildAuthObject()
    {
        return new Keycloak([
            'authServerUrl'             => 'http://localhost:8181/auth',
            'realm'                     => 'master',
            'clientId'                  => 'test-client-2',
            'clientSecret'              => '9ca97b8a-d563-4ea5-8b25-f5b6dbb322b5',
            'redirectUri'               => 'http://localhost:8080/',
            'encryptionAlgorithm'       => null,
            'encryptionKey'             => null,
            'encryptionKeyPath'         => null
        ]);
    }
}