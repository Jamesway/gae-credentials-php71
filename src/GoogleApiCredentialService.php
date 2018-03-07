<?php
/**
 * Created by PhpStorm.
 * User: jamesroberson
 * Date: 3/6/18
 * Time: 12:53 PM
 */

namespace Jamesway;

use Google_Client;

/* generates a access and refresh token for a service */
class GoogleApiCredentialService
{

    const CREDENTIALS_PATH = __DIR__ . '/../secret/credentials.json';

    const CLIENT_SECRET_PATH = __DIR__ . '/../secret/secret.json';

    private $client;


    public function __construct($application_name = null, array $scopes = null, $secret_path = self::CLIENT_SECRET_PATH) // scope: [Google_Service_Gmail::GMAIL_READONLY]
    {

        if (!$application_name) {

            printf("Application name is required\n");
            exit;
        }

        if (!is_array($scopes) || empty($scopes)) {

            printf("\nAn array of \"Scopes\" is required\n");
            exit;
        }

        if (!file_exists($secret_path)) {

            printf("\nSecret file %s doesn't exist\n", $secret_path);
            exit;
        }

        $this->client = new Google_Client();
        $this->client->setApplicationName($application_name);
        $this->client->setScopes(implode(' ', $scopes));
        $this->client->setAuthConfig($secret_path);
        $this->client->setAccessType('offline');

    }


    private function getAccessTokenFromFile($credentials_path)
    {

        if (file_exists($credentials_path)) {

            return json_decode(file_get_contents($credentials_path), true);
        }

        return null;
    }


    private function getAccessTokenFromUser()
    {

        // Request authorization from the user.
        printf("Open the following link in your browser:\n%s\n", $this->client->createAuthUrl());
        print 'Enter verification code: ';

        $auth_code = trim(fgets(STDIN));

        return $this->client->fetchAccessTokenWithAuthCode($auth_code);
    }


    private function writeCredentials($access_token, $credentials_path)
    {

        if (!file_exists(dirname($credentials_path))) {
            mkdir(dirname($credentials_path), 0700, true);
        }
        file_put_contents($credentials_path, json_encode($access_token));
        printf("Credentials saved to %s\n", $credentials_path);
    }


    private function refreshAccessToken($access_token) {

        //need to set it before we can check it
        $this->client->setAccessToken($access_token);

        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {

            return $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        }

        return null;
    }

    public function getCredentials($credentials_path = self::CREDENTIALS_PATH) {

        //from file
        if ($access_token = $this->getAccessTokenFromFile($credentials_path)) {

            //refresh if necessary
            if ($new_access_token = $this->refreshAccessToken($access_token)) {

                $this->writeCredentials($new_access_token, $credentials_path);

            //still valid
            } else {

                printf("\nCredentials in %s are still valid\n", $credentials_path);
            }

        //from user
        } else {

            $access_token = $this->getAccessTokenFromUser();

            $this->writeCredentials($access_token, $credentials_path);
        }

    }


}