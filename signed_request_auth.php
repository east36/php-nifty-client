<?php
/**
*
* Draft doc: https://tools.ietf.org/html/draft-cavage-http-signatures-07
* As per draft appendix C, section 2
*/
require_once("Requests/library/Requests.php");
Requests::register_autoloader();


class SignedRequestAuth implements Requests_Auth
{

  protected $access_id, $secret_key;
  protected $required_headers = array(
      "get" => array("(request-target)", "host", "date"),
      "head" => array("(request-target)", "host", "date"),
      "delete" => array("(request-target)", "host", "date"),
      "put" => array("(request-target)", "host", "date", "content-length", "content-type", "digest"),
      "post" => array("(request-target)", "host", "date", "content-length", "content-type", "digest"),
    );

  public function __construct($access_id, $secret_key)
  {
    $this->access_id = $access_id;
    $this->secret_key = $secret_key;
  }

  private function make_headers(&$headers, $verb, $data, $url)
  {
    $headers['date'] = array_key_exists('date', $headers)? $headers['date'] : date(DATE_RFC1123);
    $headers['content-type'] = array_key_exists('content-type', $headers)? $headers['content-type'] : 'application/json';
    $headers['accept'] = array_key_exists('accept', $headers)? $headers['accept'] : '*/*';
    $headers['host'] = array_key_exists('host', $headers)? $headers['host'] : parse_url($url, PHP_URL_HOST);
    $signing_body_headers = array("put", "post");
    if (in_array($verb, $signing_body_headers))
    {
      if (is_array($data))
      {
        if (empty($data))
        {
          $request_body = "";
        }
        else
        {
          $request_body = json_encode($data);
        }
      }
      else
      {
        $request_body = $data;
      }

      $headers['content-length'] = strlen($request_body);
      $headers['digest'] = "SHA-256=".base64_encode(hash('sha256', $request_body, true));
    }
  }

  protected static function make_path($url, $data, $verb)
  {
    $url_parts = parse_url($url);

    if (in_array($verb, array('post', 'put')))
    {
      $path = '/';
      if (isset($url_parts['path']))
      {
        $path = parse_url($url)['path'];
      }
      return $path;
    }

    if (!empty($data))
    {
      if (empty($url_parts['query']))
      {
        $url_parts['query'] = '';
      }
      $url_parts['query'] .= '&' . http_build_query($data, null, '&');
      $url_parts['query'] = trim($url_parts['query'], '&');
    }

    if (isset($url_parts['path']))
    {
      if (isset($url_parts['query']))
      {
        $path = $url_parts['path'] . '?' . $url_parts['query'];
      }
      else
      {
        $path = $url_parts['path'];
      }
    }
    else
    {
      $path = '/';
    }
    return $path;
  }

  private function make_sign_data($signing_header_options, &$headers, $verb, $data, $url)
  {
    $path =  $this->make_path($url, $data, $verb);
    $sign = array();
    foreach ($signing_header_options as $header)
    {
      if ( $header === '(request-target)')
      {
        array_push($sign, sprintf("%s: %s %s", $header, $verb, $path));
      }
      else
      {
        array_push($sign, sprintf("%s: %s", $header, $headers[$header]));
      }
    }
    $sign_str = join("\n", $sign);
    return $sign_str;
  }

  /**
   * Sign the requests header
   *
   * @param string $url URL to request
   * @param array $headers Associative array of request headers
   * @param string|array $data Data to send either as the POST|GET|DELETE|PUT
   * @param array $options More options to associate with the request
   * @return array Signed headers
   */
  public function sign($url, $headers, $data, $type, $options)
  {
    $verb = strtolower($type);
    // Generate the headers
    $this->make_headers($headers, $verb, $data, $url);

    // Generate the signature
    $signing_header_options = $this->required_headers[$verb];
    $data_ = $this->make_sign_data(
      $signing_header_options, $headers, $verb, $data, $url);
    $signature = hash_hmac('sha256', $data_, $this->secret_key, true);

    // Update the headers with the signed data.
    $headers['authorization'] = sprintf(
		'Signature keyId="%s",algorithm="hmac-sha256",headers="%s",signature="%s"',
		$this->access_id, join(" ", $signing_header_options), base64_encode($signature));
    return $headers;
  }

  public function register(Requests_Hooks &$hooks)
  {
    $hooks->register('requests.before_request', array(&$this, 'before_request'));
  }

  public function before_request(&$url, &$headers, &$data, &$type, &$options)
  {
		$headers = $this->sign($url, $headers, $data, $type, $options);
	}
}
?>
