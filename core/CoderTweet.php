<?php
final class CoderTweetException extends Exception{

}

final class CoderTweet{
  const RESPONSE_NONE = -1;
  const RESPONSE_QUERY = 0;
  const RESPONSE_JSON = 1;

  public $CONSUMER_KEY = '';
  public $CONSUMER_SECRET = '';
  public $CALLBACK_URL = '';

  private $oauth_nonce = '';
  private $oauth_signature = '';
  private $oauth_signature_method = 'HMAC-SHA1';
  private $oauth_timestamp = '';
  private $oauth_token = '';
  private $oauth_token_secret = '';
  private $oauth_verifier = '';
  private $oauth_version = '1.0';

  private $version = 'v0.1';
  private $user_agent = 'CoderTweet Agent';
  private $curl_default_options = array();

  public function  __construct(){
    $this->curl_default_options[CURLOPT_VERBOSE] = TRUE;
    $this->curl_default_options[CURLINFO_HEADER_OUT] = TRUE;
    $this->curl_default_options[CURLOPT_RETURNTRANSFER] = TRUE;
    $this->curl_default_options[CURLOPT_HEADER] = TRUE;
    $this->curl_default_options[CURLOPT_SSL_VERIFYPEER] = FALSE;
    $this->curl_default_options[CURLOPT_SSL_VERIFYHOST] = FALSE;
  }

  public function get_oauth_nonce(){
    return $this->oauth_nonce;
  }

  public function get_oauth_token(){
    return $this->oauth_token;
  }

  public function get_oauth_token_secret(){
    return $this->oauth_token_secret;
  }

  public function get_oauth_timestamp(){
    return $this->oauth_timestamp;
  }

  public function request_oauth_authorize_url(){
    $this->oauth_nonce = md5(time());
    $this->oauth_timestamp = time();

    $request_params = array(
      'oauth_callback' => $this->CALLBACK_URL,
      'oauth_consumer_key' => $this->CONSUMER_KEY,
      'oauth_nonce' => $this->oauth_nonce,
      'oauth_signature_method' => $this->oauth_signature_method,
      'oauth_timestamp' => $this->oauth_timestamp,
      'oauth_version' => $this->oauth_version
    );

    $request_url = 'https://api.twitter.com/oauth/request_token';

    $keys = array($this->CONSUMER_SECRET);

    $this->_generate_oauth_signature('POST', $request_url, $request_params, $keys);

    $headers[] = 'User-Agent: ' . $this->user_agent;

    $headers[] = "Authorization:
                      OAuth oauth_callback=\"" . rawurlencode($this->CALLBACK_URL) . "\",
                            oauth_consumer_key=\"{$this->CONSUMER_KEY}\",
                            oauth_nonce=\"" . $this->oauth_nonce . "\",
                            oauth_signature=\"" . rawurlencode($this->oauth_signature) . "\",
                            oauth_signature_method=\"{$this->oauth_signature_method}\",
                            oauth_timestamp=\"{$this->oauth_timestamp}\",
                            oauth_version=\"{$this->oauth_version}\"";

    $curl_options = $this->curl_default_options;
    $curl_options[CURLOPT_POST] = TRUE;
    $curl_options[CURLOPT_URL] = $request_url;
    $curl_options[CURLOPT_HTTPHEADER] = $headers;

    try{
      $response = $this->_send_request($curl_options, self::RESPONSE_QUERY);
      $this->oauth_token = $response->body['oauth_token'];
      $this->oauth_token_secret = $response->body['oauth_token_secret'];
      $authorize_url = 'https://api.twitter.com/oauth/authorize?oauth_token=' . $this->oauth_token;
      return $authorize_url;
    }catch(Exception $ex){
      $this->_handle_exception($ex);
    }

  }

  public function request_oauth_access_token(){
    $this->oauth_nonce = md5(time());
    $this->oauth_timestamp = time();

    $request_params = array(
      'oauth_callback' => $this->CALLBACK_URL,
      'oauth_consumer_key' => $this->CONSUMER_KEY,
      'oauth_nonce' => $this->oauth_nonce,
      'oauth_signature_method' => $this->oauth_signature_method,
      'oauth_timestamp' => $this->oauth_timestamp,
      'oauth_token' => $this->oauth_token,
      'oauth_verifier' => $this->oauth_verifier,
      'oauth_version' => $this->oauth_version
    );

    $request_url = 'https://api.twitter.com/oauth/access_token';

    $keys = array($this->CONSUMER_SECRET, $this->oauth_token_secret);

    $this->_generate_oauth_signature('POST', $request_url, $request_params, $keys);

    $headers[] = 'User-Agent: ' . $this->user_agent;

    $headers[] = "Authorization:
                      OAuth oauth_callback=\"" . rawurlencode($this->CALLBACK_URL) . "\",
                            oauth_consumer_key=\"{$this->CONSUMER_KEY}\",
                            oauth_nonce=\"" . $this->oauth_nonce . "\",
                            oauth_signature=\"" . rawurlencode($this->oauth_signature) . "\",
                            oauth_signature_method=\"{$this->oauth_signature_method}\",
                            oauth_timestamp=\"{$this->oauth_timestamp}\",
                            oauth_token=\"{$this->oauth_token}\",
                            oauth_version=\"{$this->oauth_version}\"";

    $curl_options = $this->curl_default_options;
    $curl_options[CURLOPT_URL] = $request_url;
    $curl_options[CURLOPT_POST] = TRUE;
    $curl_options[CURLOPT_HTTPHEADER] = $headers;
    $curl_options[CURLOPT_POSTFIELDS] = "oauth_verifier={$this->oauth_verifier}";

    try{
      $response = $this->_send_request($curl_options, self::RESPONSE_QUERY);
      $this->oauth_token = $response->body['oauth_token'];
      $this->oauth_token_secret = $response->body['oauth_token_secret'];
      return TRUE;
    }catch(Exception $ex){
      $this->_handle_exception($ex);
      return FALSE;
    }
  }

  public function request_account_verify_credentials(){
    $this->oauth_nonce = md5(time());
    $this->oauth_timestamp = time();

    $request_params = array(
      'oauth_callback' => $this->CALLBACK_URL,
      'oauth_consumer_key' => $this->CONSUMER_KEY,
      'oauth_nonce' => $this->oauth_nonce,
      'oauth_signature_method' => $this->oauth_signature_method,
      'oauth_timestamp' => $this->oauth_timestamp,
      'oauth_token' => $this->oauth_token,
      'oauth_version' => $this->oauth_version
    );

    $request_url = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    $keys = array($this->CONSUMER_SECRET, $this->oauth_token_secret);

    $this->_generate_oauth_signature('GET', $request_url, $request_params, $keys);

    $headers[] = 'User-Agent: ' . $this->user_agent;

    $headers[] = "Authorization:
                      OAuth oauth_callback=\"" . rawurlencode($this->CALLBACK_URL) . "\",
                            oauth_consumer_key=\"{$this->CONSUMER_KEY}\",
                            oauth_nonce=\"" . $this->oauth_nonce . "\",
                            oauth_signature=\"" . rawurlencode($this->oauth_signature) . "\",
                            oauth_signature_method=\"{$this->oauth_signature_method}\",
                            oauth_timestamp=\"{$this->oauth_timestamp}\",
                            oauth_token=\"{$this->oauth_token}\",
                            oauth_version=\"{$this->oauth_version}\"";

    $curl_options = $this->curl_default_options;
    $curl_options[CURLOPT_URL] = $request_url;
    $curl_options[CURLOPT_HTTPHEADER] = $headers;

    try{
      $response = $this->_send_request($curl_options, self::RESPONSE_JSON);
      return $response->body;
    }catch(Exception $ex){
      $this->_handle_exception($ex);
      return FALSE;
    }
  }

  public function detect_oauth_verifier(){
    if(isset($_GET['oauth_verifier'])){
      $this->oauth_verifier = $_GET['oauth_verifier'];
    }
  }

  public function store_secure_tokens(){
    setcookie('tot', gzdeflate($this->oauth_token),0, '/', $_SERVER['HTTP_HOST'], false, true);
    setcookie('tots', gzdeflate($this->oauth_token_secret),0, '/', $_SERVER['HTTP_HOST'], false, true);
  }

  public function load_secure_tokens(){
    if(isset($_SERVER['HTTP_COOKIE'])){
      $parts = explode('; ' , $_SERVER['HTTP_COOKIE']);
      if(is_array($parts) && count($parts) > 0){
        foreach($parts as $value){

          // find the twitter oauth_token
          if(strpos($value,'tot=')!==FALSE){
            $encoded_tot = urldecode(str_replace('tot=','',$value));
            $this->oauth_token = gzinflate($encoded_tot);
          }

          // find the twitter oauth_token_secret
          if(strpos($value,'tots=')!==FALSE){
            $encoded_tots = urldecode(str_replace('tots=','',$value));
            $this->oauth_token_secret = gzinflate($encoded_tots);
          }
        }
      }
    }
    else if(isset($_COOKIE['tot']) && isset($_COOKIE['tots'])){
      $this->oauth_token = gzinflate($_COOKIE['tot']);
      $this->oauth_token_secret = gzinflate($_COOKIE['tots']);
    }
  }

  private function _generate_oauth_signature($http_method='POST', $request_url='', $params=array(), $keys=array()){
    $request_params = http_build_query($params,'', '&', PHP_QUERY_RFC3986);

    $sign_params = implode('&' , array(
      strtoupper($http_method),
      rawurlencode($request_url),
      rawurlencode($request_params)
    ));

    if(count($keys)>1){
      $encoded_keys = array_map('rawurlencode', $keys);
      $sign_key = implode('&' , $encoded_keys);
    }else{
      $sign_key = rawurlencode($keys[0]).'&';
    }

    $this->oauth_signature = base64_encode(hash_hmac('sha1', $sign_params, $sign_key, TRUE));
    if(false){
      echo '<pre>';
      echo "\n{$sign_params}\n\n{$sign_key}\n";
      echo '</pre>';
    }
  }

  private function _send_request($curl_options, $response_format = self::RESPONSE_NONE){
    $ch = curl_init();
    curl_setopt_array($ch, $curl_options);
    $result = curl_exec($ch);
    if($result===FALSE){
      $error = 'cURL Error(' . curl_errno($ch) . '): ' . curl_error($ch);
      curl_close($ch);
      throw new CoderTweetException($error, 500);
    }else{
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      $header = substr($result, 0, $header_size);
      $body = substr($result, $header_size);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_info = curl_getinfo($ch);
      curl_close($ch);
      if(intval($http_status)!=200){
        echo '<pre>';
        echo $curl_info['request_header'];
        echo '</pre>';
        throw new CoderTweetException($result,$http_status);
      }else{
        $response = new stdClass;
        $response->header = $header;
        switch($response_format){
          case self::RESPONSE_QUERY:
            $response->body = array();
            parse_str(trim($body), $response->body);
            break;
          case self::RESPONSE_JSON:
            $response->body = json_decode(trim($body));
            break;
          default:
            $response->body = $body;
            break;
        }
        return $response;
      }
    }
  }

  private function _handle_exception($ex){
    echo '<pre>';
    echo $ex->getMessage();
    echo '</pre>';
  }

}
?>
