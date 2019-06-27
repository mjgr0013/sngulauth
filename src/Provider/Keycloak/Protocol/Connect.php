<?php


namespace Sngular\Auth\Provider\Keycloak\Protocol;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Sngular\Auth\Provider\Keycloak\Exception\EncryptionConfigurationException;
use Sngular\Auth\Provider\Keycloak\ResourceOwner\KeycloakResourceOwner;

/**
 * Class Connect
 * @package Sngular\Auth\Provider\Keycloak\Protocol
 */
class Connect extends AbstractProvider
{
    const AUTHORIZATION_CODE = 'authorization_code';
    const REFRESH_TOKEN = 'refresh_token';

    use BearerAuthorizationTrait;

    /**
     * Keycloak URL, eg. http://localhost:8080/auth.
     *
     * @var string
     */
    public $authServerUrl = null;

    /**
     * Realm name.
     *
     * @var string
     */
    public $realm = null;

    /**
     * Encryption algorithm.
     *
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/rfc7518#section-3
     * for a list of spec-compliant algorithms.
     *
     * @var string
     */
    public $encryptionAlgorithm = null;

    /**
     * Encryption key.
     *
     * @var string
     */
    public $encryptionKey = null;

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->getIdentityProviderBaseUrl() . '/protocol/openid-connect/auth';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getIdentityProviderBaseUrl() . '/protocol/openid-connect/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getIdentityProviderBaseUrl() . '/protocol/openid-connect/userinfo';
    }

    /**
     * Build the identity provider base url.
     *
     * @return string
     */
    protected function getIdentityProviderBaseUrl()
    {
        return $this->authServerUrl . '/realms/' . $this->realm;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['name', 'email'];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $error = $data['error'] . ': ' . $data['error_description'];
            throw new IdentityProviderException($error, 0, $data);
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface|KeycloakResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new KeycloakResourceOwner($response);
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken $token
     * @return KeycloakResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->decryptResponse($token->getToken());

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Attempts to decrypt the given response.
     *
     * @param  string|array|null $response
     *
     * @return string|array|null
     */
    public function decryptResponse($response)
    {
        if (!is_string($response)) {
            return $response;
        }

        if ($this->usesEncryption()) {
            return json_decode(
                json_encode(
                    JWT::decode(
                        $response,
                        $this->buildEncryptionKey(),
                        array($this->encryptionAlgorithm)
                    )
                ),
                true
            );
        }

        throw EncryptionConfigurationException::undeterminedEncryption();
    }

    /**
     * Add the footer/header to the encryption key provided by keycloak
     * @return string
     */
    protected function buildEncryptionKey()
    {
        return "-----BEGIN PUBLIC KEY-----\n{$this->encryptionKey}\n-----END PUBLIC KEY-----";
    }

    /**
     * Checks if provider is configured to use encryption.
     *
     * @return bool
     */
    public function usesEncryption()
    {
        return (bool)$this->encryptionAlgorithm && $this->encryptionKey;
    }

    /**
     * @param string $code
     * @return AccessToken
     */
    public function authByCode(string $code)
    {
        return $this->getAccessToken(self::AUTHORIZATION_CODE, [
            'code' => $code
        ]);
    }

    /**
     * @param string $refreshToken
     * @return AccessToken
     */
    public function authByRefreshToken(string $refreshToken)
    {
        return $this->getAccessToken(self::REFRESH_TOKEN, [
            'refresh_token' => $refreshToken
        ]);
    }

    /**
     * TODO: http://docs.identityserver.io/en/latest/endpoints/introspection.html
     */
    public function introspectCode($token)
    {
        $client = new Client();

        $response = $client->post(
            'http://localhost:8181/auth/realms/master/protocol/openid-connect/token/introspect',
            [
                'headers' => [
                    'Authorization' => $this->generateAuthorizationBasic(),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'token' => $token
                ]
            ]
        );


        dump($response->getBody()->getContents());
        die();

    }

    /**
     * @return string
     */
    private function generateAuthorizationBasic()
    {
        return 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}");
    }
}