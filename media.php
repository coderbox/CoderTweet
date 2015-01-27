<?php
include_once 'config.php';

include_once 'core/CoderTweet.php';

$fp =  fopen('overview6.png', 'rb');
$image = '';
while($line = fgets($fp, 2048)){
  $image.= $line;
}
fclose($fp);

$service = new CoderTweet();
$service->CALLBACK_URL = CALLBACK_URL;
$service->CONSUMER_KEY = CONSUMER_KEY;
$service->CONSUMER_SECRET = CONSUMER_SECRET;
$service->load_secure_tokens();
$credentials = $service->request_media_upload($image);

echo '<pre>';
print_r($credentials);
echo '</pre>';
?>
