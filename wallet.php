<?php
require_once("nifty_client_base.php");


class Wallet extends NiftyClientBase
{
    private function get_wallet_url()
    {
        $url_part = '/user/'. $this->user_id. '/wallet';
        return $this->url_base. $url_part;
    }

    /**
    * Create the wallet if it doesn't exist and return it.
    * Otherwise return the existing wallet
    */
    public function create_wallet()
    {
        $headers = array();
        $data = null;
        return $this->get_json(Requests::post(
          $this->get_wallet_url(),
          $headers,
          $data,
          $this->get_authorisation_header($this->access_id, $this->secret_key, 'POST')));
    }

    /**
    * Fetch the wallet details. If there's no wallet, the response will be an empty list
    * Otherwise return the existing wallet
    */
    public function get_wallet()
    {
        $headers = array();
        return $this->get_json(Requests::get(
          $this->get_wallet_url(),
          $headers,
          $this->get_authorisation_header($this->access_id, $this->secret_key, 'GET')));
    }
}

?>
