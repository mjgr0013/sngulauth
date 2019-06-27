<?php

namespace Auth\Handler;


use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Sngular\Auth\Auth\Handler\SessionHandler;

if ( !isset( $_SESSION ) ) $_SESSION = array(  );

class SessionHandlerTest extends TestCase
{
    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * SessionHandlerTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setSessionData();
        $this->sessionHandler = new SessionHandler();
    }

    /**
     *
     */
    private function setSessionData()
    {
        $tokenData = [
            'access_token' => 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJoY0pndWo2bXQ3TEp3Y2Q0NlVGVlcwXzJIc0ZxUDhLVmRYZ1d1Z0tsSTF3In0.eyJqdGkiOiJhMzFhNzljMS03YzU3LTQ4M2EtYWI2ZC1jMTI4OGIwOTRjYTUiLCJleHAiOjE1NjE2MzEzNTgsIm5iZiI6MCwiaWF0IjoxNTYxNjMxMDU4LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgxODEvYXV0aC9yZWFsbXMvbWFzdGVyIiwiYXVkIjoiYWNjb3VudCIsInN1YiI6ImZkZDkzOWY0LWJjZDEtNGUyNi1hYTk3LWQyMzZjZDJhZGE4ZSIsInR5cCI6IkJlYXJlciIsImF6cCI6InRlc3QtY2xpZW50LTIiLCJhdXRoX3RpbWUiOjE1NjE2MjQ3NjEsInNlc3Npb25fc3RhdGUiOiIzMzE4NDViYS02NTAwLTQ1MmUtYjEzMC02MDFiY2YzNjQ4OTgiLCJhY3IiOiIwIiwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iLCJ1c2VyIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJwcm9maWxlIGVtYWlsIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsIm5hbWUiOiJUZXN0IEZpcnN0IE5hbWUgVGVzdCBMYXN0IE5hbWUiLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJ1c2VyLnRlc3QiLCJnaXZlbl9uYW1lIjoiVGVzdCBGaXJzdCBOYW1lIiwiZmFtaWx5X25hbWUiOiJUZXN0IExhc3QgTmFtZSIsImVtYWlsIjoidXNlci50ZXN0QHNuZ3VsYXIudGVhbSJ9.NXbX43D6ma60l9BO9-OfaaJOUp_G_rgC8xGHb0slCzSQ8zQiElH8aq9Ha7H9QIVRK0BmMh38sc7_6--eW595OMW7GVEbwI51TdZbL1DQENDfdILn-6G5iu8k2eHfRa-3ClykWlGQ0wGWSRmsSa5O1xz4crXNgqlQ1qDrs0MrTeKTd3Ia96jmXivCZhjEX6uBOOCsO_SMDVlbDkdt46Wy4Zvz2pq22C6lB6zOvuB-Xzcv5sYwIgL47uZemXS3ISR0PqakViVgE7qjSDFG58NorrlasTmZ5w8LT9DOeflNaNzMClaSlmtZyxYiPXvl7Y3JpXnZRN59yoTXpgZBDzexAQ',
            'expires_in' => strtotime("+5 minutes")

        ];

        $_SESSION[SessionHandler::KEY_NAME]['token']      = json_encode($tokenData);
        $_SESSION[SessionHandler::KEY_NAME]['userData']      = '{"jti":"e1a1e624-3a62-41f2-9d7c-2a13895b1b2d","exp":1561631792,"nbf":0,"iat":1561631492,"iss":"http:\/\/localhost:8181\/auth\/realms\/master","aud":"account","sub":"fdd939f4-bcd1-4e26-aa97-d236cd2ada8e","typ":"Bearer","azp":"test-client-2","auth_time":1561624761,"session_state":"331845ba-6500-452e-b130-601bcf364898","acr":"0","realm_access":{"roles":["offline_access","uma_authorization","user"]},"resource_access":{"account":{"roles":["manage-account","manage-account-links","view-profile"]}},"scope":"profile email","email_verified":true,"name":"Test First Name Test Last Name","preferred_username":"user.test","given_name":"Test First Name","family_name":"Test Last Name","email":"user.test@sngular.team"}';
    }

    public function testTue()
    {
        $this->assertTrue(
            $this->sessionHandler->tokenIsStillValid()
        );

        $this->assertTrue(
            $this->sessionHandler->hasRole('offline_access')
        );

        $this->assertFalse(
            $this->sessionHandler->hasRole('non_present_role')
        );
    }
}