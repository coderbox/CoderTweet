<?php
include_once 'config.php';

$nonce = md5(time());
$timestamp = time();
$status = 'Testing now ' . time();
$mids = '559757605065994240';
$request_params_array = array(
  'media_ids' => $mids,
  'oauth_consumer_key' => CONSUMER_KEY,
  'oauth_nonce' => $nonce,
  'oauth_signature_method' => 'HMAC-SHA1',
  'oauth_timestamp' => $timestamp,
  'oauth_token' => '209786645-ynuWn1lk4rBHZFW7UAfmRGrTs0nSIJ97lr6ClQOF',
  'oauth_version' => '1.0',
  'status' => $status
);

$request_params = http_build_query($request_params_array,'', '&', PHP_QUERY_RFC3986);

$sign_params = implode('&' , array(
  'POST',
  rawurlencode('https://api.twitter.com/1.1/statuses/update.json'),
  rawurlencode($request_params)
));
$sign_key = rawurlencode(CONSUMER_SECRET) . '&' . rawurlencode('LsvXPx6MPq8fziZt45pCQycKiSpulCXfP0blSBU5xBlYN');
$oauth_sign = base64_encode(hash_hmac('sha1', $sign_params, $sign_key, TRUE));

$headers[] = 'User-Agent: PHP-Generated Request';
$headers[] = "Authorization:
                    OAuth oauth_consumer_key=\"" . CONSUMER_KEY . "\",
                          oauth_nonce=\"" . $nonce . "\",
                          oauth_signature=\"" . rawurlencode($oauth_sign) . "\",
                          oauth_signature_method=\"HMAC-SHA1\",
                          oauth_timestamp=\"" . $timestamp . "\",
                          oauth_token=\"209786645-ynuWn1lk4rBHZFW7UAfmRGrTs0nSIJ97lr6ClQOF\",
                          oauth_version=\"1.0\"";


$ch = curl_init('https://api.twitter.com/1.1/statuses/update.json');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'status=' . rawurlencode($status) . '&media_ids=' . $mids);
curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

$result = curl_exec($ch);
if($result===false){
  echo 'cURL Error(' . curl_errno($ch) . '): ' . curl_error($ch);
  curl_close($ch);
  exit;
}

echo '<pre>';
  echo '<h2>REQUEST</h2>';
  print_r(curl_getinfo($ch));
  echo '<h2>RESPONSE</h2>';
  echo htmlentities($result);
  //echo "\nhttps://api.twitter.com/oauth/authorize?oauth_token=";
  echo '</pre>';
  curl_close($ch);
?>
