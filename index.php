<?php
include_once 'config.php';

include_once 'core/CoderTweet.php';


$service = new CoderTweet();
$service->CALLBACK_URL = CALLBACK_URL;
$service->CONSUMER_KEY = CONSUMER_KEY;
$service->CONSUMER_SECRET = CONSUMER_SECRET;
$auth_url = $service->request_authorize_url();

//store encoded tokens for later use
$service->store_secure_tokens();

echo '<pre>';
echo $auth_url;
echo '</pre>';
?>
