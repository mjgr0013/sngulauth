# This is the ultimate package for manage Keycloak authentication with Openid Connect

### Before start, you need some parameters
- **authServerUrl**: keycloak auth url
- **realm**: realm name that has been set up for the project
- **clientId**: client id name that has been set up for the project
- **clientSecret**: client secret name that has been set up for the project
- **redirectUri**: the redirect url to be redirected after successful credentials prompt (this url must be valid on keycloak client configuration)
- **encryptionAlgorithm**: algorithm to decode the JWT information, default is RS256
- **encryptionKey**: the public key content (without begin header and end footer, this is being added automatically by the package) to decrypt the JWT.



### The auth process
The src/Provider/Keycloak/Protocol/Connect class needs an array to be instantiated with those parameters:
```
$auth = new Connect($config);
```

Then you can build the auth url to redirect user or display a link:
```
$authUrl = $auth->getAuthorizationUrl();
```

After user insert his credentials on Keycloak login page, it will be redirected to **redirectUri** parameter, with a code.
Now you can fetch a token (League\OAuth2\Client\Token\AccessToken) against keycloak with those code:
```ruby
$token = $auth->getAccessToken('authorization_code', [
    code' => $_GET['code']
]);
```

Now you can get the resource owner (the user data) against keycloak
```
$user = $auth->getResourceOwner($token);
```

And decrypt the token to get the token payload:
```
$userData = $auth->decryptResponse($token->getToken());
```
