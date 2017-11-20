Nifty-Client
=======================================
Nifty Client is the official PHP client for the [nifty API](http://docs.nifty.co.ke/).
It does some heavy lifting to provide a simple and portable client.
If you do find the client missing some features, please make a [pull request](https://github.com/east36/php-nifty-client/pulls)
or [cut an issue](https://github.com/east36/php-nifty-client/issues).

Installation
===============
>This package depends on the [PHP Requests library](https://github.com/rmccue/Requests)

Download the zipped package.

or Run the command

```bash
$ git clone --recursive git@github.com:east36/php-nifty-client.git

```

Quickstart
=============

1. Get your api credentials from the [nifty dashboard](https://www.integ.nifty.co.ke/users/register/),
2. Follow the instructions as specified in the `example.php`

```php
$config = array(
  'access_id' => '<access-id>',
  'secret_key' => '<secret-id>',
  'user_id' => '<user-id>',
  'api_base' => "http://api.integ.nifty.co.ke/api/v1",
);
```
3. Follow the instructions as specified in the [docs](docs.nifty.co.ke)
