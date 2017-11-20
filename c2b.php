<?php
require_once("nifty_client_base.php");


class C2B extends NiftyClientBase
{
      private function c2b_url()
      {
          $url_part = '/user/'. $this->user_id. '/mpesa/c2b-confirmation';
          return $this->api_base. $url_part;
      }

      /**
      * List or search C2B Transactions
      * Args (All optional):
      * @transaction_id - MPESA transaction id to look up.
      * @phone_number   - Phone number to look up (Format: E.164).
      * @till_number    - Short code to look up.
      */
      public function list_transactions($data)
      {
          $headers = array();
          $url_part = '?'. http_build_query($data);
          return $this->get_json(Requests::get(
              $this->c2b_url(). $url_part,
              $headers,
              $this->get_authorisation_header($this->access_id, $this->secret_key, 'GET')));
      }

      /**
      * Get a transaction using a payment id
      * Args (Required):
      *  @payment_id     - Transaction payment ID
      */
      public function get_transaction($payment_id)
      {
          $headers = array();
          $url_part = "/". $payment_id;
          return $this->get_json(Requests::get(
              $this->c2b_url(). $url_part,
              $headers,
              $this->get_authorisation_header($this->access_id, $this->secret_key, 'GET')));
      }

      /**
      * Claim a transaction using a token and credit wallet with token amount.
      *
      * Args (All required):
      * @transaction_id - MPESA transaction id sent to the subscriber.
      * @phone_number   - Phone number of the subscriber (Format: E.164).
      * @till_number    - Short code that transaction was sent to.
      */
      public function claim_transaction($transaction_id, $phone_number, $till_number)
      {
          $headers = array();
          $data = array(
            'transaction_id' => $transaction_id,
            'phone_number' => $phone_number,
            'till_number' => $till_number
          );
          return $this->get_json(Requests::post(
              $this->c2b_url(),
              $headers,
              json_encode($data),
              $this->get_authorisation_header($this->access_id, $this->secret_key, 'POST')));
      }


      /**
      * Reverse a transaction and debit wallet with token amount.
      *
      *  This will initiate an MPESA reversal process.
      * @payment_id     - Payment ID of the transaction to reverse
      * @reversal_reason - A short description of why this transaction was reversed.
      */
      public function reverse_transaction($payment_id, $reversal_reason)
      {
          $headers = array();
          $data = array('reversal_reason' => $reversal_reason);
          $url_part = "/". $payment_id;
          return $this->get_json(Requests::put(
              $this->c2b_url(). $url_part,
              $headers,
              json_encode($data),
              $this->get_authorisation_header($this->access_id, $this->secret_key, 'PUT')));
      }

}
 ?>
