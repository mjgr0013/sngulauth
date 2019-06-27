<?php


namespace Sngular\Auth\Provider\Keycloak\ResourceOwner;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * All those data are fetched from Oauth2 response, see https://tools.ietf.org/html/rfc7662 for some references
 *
 * Class KeycloakResourceOwner
 * @package Sngular\Auth\Provider\Keycloak\ResourceOwner
 */
class KeycloakResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var
     */
    private $subject;

    /**
     *
     * @var string
     */
    private $isEmailVerified;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles;

    /**
     * Creates new resource owner.
     *
     * @param array $response
     * @param array $roles
     */
    public function __construct(array $response, array $roles = [])
    {
        $this->subject         = $response['sub'];
        $this->isEmailVerified = $response['email_verified'];
        $this->fullName        = $response['name'];
        $this->username        = $response['preferred_username'];
        $this->email           = $response['email'];
        $this->name            = $response['given_name'];

        $this->roles = array_merge($response['realm_access']['roles'], $response['resource_access']['account']['roles']);
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function isEmailVerified()
    {
        return $this->isEmailVerified;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->subject;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}