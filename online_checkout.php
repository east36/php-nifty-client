<?php
require_once("nifty_client_base.php");


class OnlineCheckout extends NiftyClientBase
{
    private function get_online_checkout_url()
    {
        $url_part = '/user/'. $this->user_id. '/mpesa/online-checkout';
        return $this->api_base. $url_part;
    }

    /**
    *
    * Iniate an online checkout transaction.
    * Required params:
    * @transaction_amount  - Amount to request.
    * @phone_number        - Phone number of subscriber (Format: E.164).
    *
    * Optional:
    * @transaction_id      - A unique transaction id
    * @service_reference_id - A means for a merchant to group related transactions
    * @callback_url         -  URL on merchant side to post transaction status
    */
    public function initiate_checkout(
      $transaction_id, $phone_number,
      $transaction_amount, $callback_url,
      $service_reference_id)
    {
        $headers = array();
        $data = array(
          'transaction_id' => $transaction_id,
          'phone_number' => $phone_number,
          'transaction_amount' => $transaction_amount,
          'callback_url' => $callback_url,
          'service_reference_id' => $service_reference_id);
        return $this->get_json(Requests::post(
          $this->get_online_checkout_url(),
          $headers,
          json_encode($data),
          $this->get_authorisation_header($this->access_id, $this->secret_key, 'POST')));
    }

    /**
    *  List or search Online Checkout Transactions
    *  @payment_id     - Payment id
    *  @phone_number   - Phone number to look up (Format: E.164).
    *  @service_reference_id - Service reference to look up
    *   Example:
    *  $data = array('payment_id'=>'871559E4-BED1-4E0C-A4B0-FBD2A5833E00');
    *  list_transactions($data);
    */
    public function list_transactions($data)
    {
        $headers = array();
        $url_part = '?'. http_build_query($data);
        return $this->get_json(Requests::get(
          $this->get_online_checkout_url(). $url_part,
          $headers,
          $this->get_authorisation_header($this->access_id, $this->secret_key,'GET')));
    }
}

?>
