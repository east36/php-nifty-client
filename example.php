<?php
require_once('wallet_client.php');


$config = array(
  'access_id' => '<access-id>',
  'secret_key' => '<secret-id>',
  'user_id' => '<user-id>',
  'api_base' => "https://api.integ.nifty.co.ke/api/v1",
);

// Change this to use your till details. Be careful to use the correct platform details (integ vs prod)
$till_number = array(
  'integ' => 'xxxxxx',
  'production' => 'xxxxxx'
);
$phone_number = '2547xxxxxxxx';


if (!count(debug_backtrace()))
{
    echo "\n<====== Initialise the nifty client ===>\n";
    $nifty_client = new NiftyWalletClient($config);

    //Creating an empty wallet
    echo "\n<====== Create wallet ===>\n";
    $response = $nifty_client->wallet->create_wallet();
    var_dump($response->returned_resultset[0]->balance);

    // Get the balance of a wallet
    echo "\n<====== Get wallet balance ===>\n";
    $response = $nifty_client->wallet->get_wallet();
    var_dump($response->returned_resultset[0]->balance);

    //Consume a token
    echo "\n<====== Claim c2b transactions ===>\n";
    $transaction_id = 'LGJ4M7XVX7';

    $response = $nifty_client->c2b->claim_transaction(
     $transaction_id, $phone_number, $till_number['integ']);
    var_dump($response);

    // Reverse transactions
    echo "\n<====== Reverse transactions ===>\n";
    $payment_id = 'dfc295ba-6c8a-11e7-a87a-063d7358ef43';
    $reversal_reason = 'Made a mistake claiming this transaction.';
    $response = $nifty_client->c2b->reverse_transaction($payment_id, $reversal_reason);
    var_dump($response);

    // List the transactions
    echo "\n<====== List transactions ===>\n";
    $data = array();
    $response = $nifty_client->c2b->list_transactions($data);
    var_dump($response);

    // Get a specific transaction
    echo "\n<====== Get transaction ===>\n";
    $response = $nifty_client->c2b->get_transaction($payment_id);
    var_dump($response);

    // Online checkout transactions
    echo "\n<====== Initiate Online transaction transaction ===>\n";
    $transaction_id = bin2hex(openssl_random_pseudo_bytes(32));
    $transaction_amount = 10;
    $callback_url = 'http://merchant_url.com/call/me/back';
    $service_reference_id = 'tag2';
    $response = $nifty_client->online_checkout->initiate_checkout(
      $transaction_id, $phone_number,
      $transaction_amount, $callback_url,
      $service_reference_id);
    var_dump($response->returned_resultset[0]);

    // List online checkout transactions
    echo "\n<======  List online transactions ===>\n";
    $transaction_id = bin2hex(openssl_random_pseudo_bytes(32));
    $callback_url = 'http://merchant_url.com/call/me/back';
    $service_reference_id = 'tag1';
    $data = array(
      'transaction_id' => '09535e8016e4d511978fb36f38a72ad8ff42a4bd120f528efd88b17361e6f788');
    $response = $nifty_client->online_checkout->list_transactions($data);
    var_dump($response);
}
?>
