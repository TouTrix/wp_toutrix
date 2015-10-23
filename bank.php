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
We only accept BitCoin for the moment.<br/>
<br/>
<center><font size='4'>First deposit is doubled.</font></center>
<br/>
<h3>Bitcoin</h3>
Send any amount to this payment address: <?php echo $bitcoin_addresse; ?><br/><br/>
<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $bitcoin_addresse; ?>'>
<br/><br/>
Deposit are automatic. But it take few minutes.

<h2>Withdrawal</h2>

Configure your account in settings to receive hourly payment. You never have to wait for your money. You can send the money directly to your exchanger or in your mobile wallet.<br/>
We may have to approve your account first.


<h1>Exchange Bitcoins</h1>

<!-- TODO Exchange recommendation -->

If you don't have BitCoin Wallet, you can use an exchanger and they will receive money on your behalf. <br/>
<br/>
We recommend <a href="https://www.coinbase.com/join/52cb9f042fe57d1d9400001f" target="_blank">CoinBase</a>. <br/>
<br/>

<h1>Need a lend?</h1>

<!-- TODO We should ask adserver with keyword: btc lend -->

<a href='https://btcjam.com/?r=688f61e1-439f-4b8a-93a5-e7f57068c158&utm_medium=direct&utm_source=referral_url&utm_campaign=ad_2' target="_blank">BTCjam</a>

<h1>Need a lend?</h1>

<!-- TODO We should ask adserver with keyword: btc lend -->

<a href='https://btcjam.com/?r=a4b60cb9-52a2-4a41-9dc3-7c1bc5e817e4&utm_medium=direct&utm_source=referral_url&utm_campaign=ad-2'>BTCjam</a>

<?php
}
?>
