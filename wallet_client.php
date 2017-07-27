<?php
date_default_timezone_set('Africa/Nairobi');
require_once("wallet.php");
require_once("c2b.php");
require_once("online_checkout.php");


class NiftyWalletClient
{
    public function __construct($config)
    {
        $this->online_checkout = new OnlineCheckout($config);
        $this->wallet = new Wallet($config);
        $this->c2b = new C2B($config);
    }
}

?>
