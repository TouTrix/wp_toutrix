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
<?php toutrix_echo_funds_available(); ?>
<h2>Make a deposit</h2>
We only accept BitCoin for the moment. We should add PayPal and Paxum shortly. For Paypal, we will need personal information to verify your account.<br/>
<br/>
<center><font size='4'>First deposit is doubled.</font></center>
<br/>
<h3>Bitcoin</h3>
Send any amount to this payment address: <?php echo $bitcoin_addresse; ?><br/><br/>
<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $bitcoin_addresse; ?>'>
<br/><br/>
Deposit are automatic. But it take few minutes.

<h2>Withdrawal</h2>

Withdrawal are automatic. Just configure your account to receive hourly payment. You never have to wait for your money. You can send the money directly to your exchanger or in your mobile wallet. We may have to approve your account first.

<h1>Exchange recommendation</h1>

<!-- TODO Exchange recommendation -->

If you don't have BitCoin Wallet, you can use an exchanger and they will receive money on your behalf. We recommend CoinBase. Click the link below.<br/><br/>

See <a href='http://toutrix.com/category/bitcoins/' target='_blank'>articles on TouTrix web site</a><br/>

<h1>Need a lend?</h1>

<!-- TODO We should ask adserver with keyword: btc lend -->

<a href='https://btcjam.com/?r=a4b60cb9-52a2-4a41-9dc3-7c1bc5e817e4&utm_medium=direct&utm_source=referral_url&utm_campaign=ad-2'>BTCjam</a>

<?php
}
?>
