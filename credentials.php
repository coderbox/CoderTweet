<?php
include_once 'config.php';

include_once 'core/CoderTweet.php';

$service = new CoderTweet();
$service->CALLBACK_URL = CALLBACK_URL;
$service->CONSUMER_KEY = CONSUMER_KEY;
$service->CONSUMER_SECRET = CONSUMER_SECRET;
$service->load_secure_tokens();
$service->request_account_verify_credentials();

?>
