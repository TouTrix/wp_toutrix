<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function mt_toutrix_bank_page() {
  global $toutrix_adserver;
  toutrix_get_token();

  $fields = new stdclass();
  $fields->id = $toutrix_adserver->userId;
  $bitcoin_addresse = $toutrix_adserver->get_bitcoin_address($fields);
  $bitcoin_addresse = $bitcoin_addresse->address->bitcoin_address;
?>
<h1>TouTrix Bank<h1>
<h2>Make a deposit</h2>
We only accept BitCoin for the moment. We should add PayPal and Paxum shortly. For Paypal, we will need personal information to verify your account.<br/>
<br/>
<h3>Bitcoin</h3>
Addresse payment: <?php echo $bitcoin_addresse; ?><br/><br/>
<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $bitcoin_addresse; ?>'>
<br/><br/>
Deposit are automatic. But it take a lot of time.

<h2>Ask a withdrawl</h2>

Withdrawal is coming very soon. You will be able to ask automatic hourly payment. Never wait to receive your money. You can send the money directly to your exchanger or in your mobile wallet. We will provide more information.

<h1>Exchange recommendation</h1>

If you don't BitCoin, use an exchanger. We recommend CoinBase. Click the link below.<br/><br/>

See <a href='http://toutrix.com/category/bitcoins/' target='_blank'>articles on TouTrix web site</a><br/>

<?php
}
?>
