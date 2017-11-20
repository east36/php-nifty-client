<?php
require_once("signed_request_auth.php");


class NiftyClientBase
{
  public function __construct($config)
  {
    $this->access_id = $config['access_id'];
    $this->secret_key = $config['secret_key'];
    $this->user_id = $config['user_id'];
    $this->api_base =  $config['api_base'];
  }

  public function get_authorisation_header($access_id, $secret_key, $type)
  {
    return  array(
      'auth' => new SignedRequestAuth($access_id, $secret_key), 'type' => $type);
  }

  public function get_json($response_obj)
  {
    $json_body = json_decode($response_obj->body);
    if (json_last_error() === JSON_ERROR_NONE)
    {
      return $json_body;
    }
    return $response_obj->body;
  }
}

?>
