<?php
/**
 * Created by PhpStorm.
 * User: jamesroberson
 * Date: 3/6/18
 * Time: 2:43 PM
 */

require_once __DIR__ . '/vendor/autoload.php';


define('APPLICATION_NAME', 'API Credential Service');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/gmail-php-quickstart.json
define('SCOPES', array(
        Google_Service_Gmail::GMAIL_READONLY)
);

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}


(new Jamesway\GoogleApiCredentialService(APPLICATION_NAME, SCOPES))->getCredentials();