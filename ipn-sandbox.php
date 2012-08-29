<?php
require('rmb_conf.php');
// read the post from PayPal system and add 'cmd'
$req = 'cmd=' . urlencode('_notify-validate');
 
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
 
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.sandbox.paypal.com'));
$res = curl_exec($ch);
curl_close($ch);
 
$_POST = unserialize('a:33:{s:8:"mc_gross";s:2:"24";s:22:"protection_eligibility";s:10:"Ineligible";s:8:"payer_id";s:13:"REET5JJUXAKNG";s:3:"tax";s:4:"0.00";s:12:"payment_date";s:25:"02:15:26 Aug 17, 2012 PDT";s:14:"payment_status";s:9:"Completed";s:7:"charset";s:12:"windows-1252";s:10:"first_name";s:8:"Benjamin";s:6:"mc_fee";s:4:"1.84";s:14:"notify_version";s:3:"3.6";s:6:"custom";s:2:"19";s:12:"payer_status";s:8:"verified";s:8:"business";s:30:"marie_1345148567_biz@gmail.com";s:8:"quantity";s:1:"2";s:11:"verify_sign";s:56:"AqR7tzhb1PTfF1OtmG.iq4JrXcG6AWQ4BGAZBP2UcfXR7mnMZdfglkl6";s:11:"payer_email";s:31:"benjam_1345188359_per@gmail.com";s:6:"txn_id";s:17:"8AE54836862886420";s:12:"payment_type";s:7:"instant";s:9:"last_name";s:4:"Azan";s:14:"receiver_email";s:30:"marie_1345148567_biz@gmail.com";s:11:"payment_fee";s:0:"";s:11:"receiver_id";s:13:"BBQV4ADGLQF74";s:8:"txn_type";s:10:"web_accept";s:9:"item_name";s:22:"Remboursement de Marie";s:11:"mc_currency";s:3:"EUR";s:11:"item_number";s:0:"";s:17:"residence_country";s:2:"FR";s:8:"test_ipn";s:1:"1";s:15:"handling_amount";s:4:"0.00";s:19:"transaction_subject";s:1:"6";s:13:"payment_gross";s:0:"";s:8:"shipping";s:4:"0.00";s:12:"ipn_track_id";s:13:"f6873b904da24";}');
var_dump($_POST);
 
// assign posted variables to local variables
global $item_name,$item_number,$payment_status,$payment_amount,$payment_currency,$txn_id,$receiver_email,$business,$payer_email,$dbh,$first_name,$last_name,$custom,$quantity,$res;
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$business = $_POST['business'];
$payer_email = $_POST['payer_email'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$custom = $_POST['custom'];
$quantity = $_POST['quantity'];

if (strcmp ($res, "VERIFIED") == 0 || true) {
	// check the payment_status is Completed
	if($payment_status != 'Completed') {
		pp_ipn_log_n_die('Payement Status is not "Completed" recieved ('.$payment_status.')');
	}
	// check that txn_id has not been previously processed
	try {
	
		    $stmt = $dbh->prepare("select pp_txn_id from remboursemoi_transaction where pp_txn_id = ?");
	
	    try { 
	        $stmt->execute( array($txn_id));
	        $result = $stmt->fetchAll();
	        foreach($result as $r) {
		        if(!empty($r['pp_txn_id']) && $r['pp_txn_id'] == $txn_id) {
			        pp_ipn_log_n_die('Transaction ('.$txn_id.') already processed');
		        }
	        }
	    } catch(PDOExecption $e) { 
	        $dbh->rollback(); 
	        die("Error!: " . $e->getMessage() . "</br>");
	    } 
	} catch( PDOExecption $e ) { 
	    die("Error!: " . $e->getMessage() . "</br>"); 
	}
	// check that receiver_email is your Primary PayPal email
	if($receiver_email != PAYPAL_BUSINESS_EMAIL && $business != PAYPAL_BUSINESS_EMAIL) {
		pp_ipn_log_n_die('Bad reciever email expcted ('.PAYPAL_BUSINESS_EMAIL.') recieved (receiver_email='.$reciever_email.', business='.$business.')');
	}
	// check that payment_amount/payment_currency are correct
	try {
	        $stmt = $dbh->prepare("select tx_qte,tx_unit_price from remboursemoi_transaction where ID = ?");
	        $stmt->execute( array($custom));
	        $result = $stmt->fetchAll();
	        foreach($result as $r) {
		        if(!empty($r['tx_qte']) && $r['tx_qte'] != $quantity) {
			        pp_ipn_log_n_die('Quantity not valid. Expected ('.$r['tx_qte'].') recieved ('.$quantity.')');
		        }
		        if(!empty($r['tx_unit_price']) && $r['tx_unit_price'] != $payment_amount/$quantity) {
			        pp_ipn_log_n_die('Payment amount not valid. Expected ('.$r['tx_unit_price'].') recieved ('.$payment_amount/$quantity.')');
		        }
	        }
	    } catch(PDOExecption $e) { 
	        $dbh->rollback(); 
	        die("Error!: " . $e->getMessage() . "</br>");
	    }
	// process payment
	try {
			$stmt = $dbh->prepare("update remboursemoi_transaction set ht_firstname=?, ht_lastname=?, ht_email=?,pp_txn_id=?, pp_ipn_blob=? where ID = ?");
	        $stmt->execute( array($first_name,$last_name,$payer_email,$txn_id,serialize($_POST),$custom));
	        pp_ipn_log_n_die('OK');
	    } catch(PDOExecption $e) { 
	        $dbh->rollback(); 
	        die("Error!: " . $e->getMessage() . "</br>");
	    } 

}
else if (strcmp ($res, "INVALID") == 0) {
	// log for manual investigation
	pp_ipn_log_n_die('curl Status id INVALID');
}


function pp_ipn_log_n_die($_err_msg) {
global $item_name,$item_number,$payment_status,$payment_amount,$payment_currency,$txn_id,$receiver_email,$business,$payer_email,$dbh,$first_name,$last_name,$custom,$quantity,$res;
try {
	        $stmt = $dbh->prepare("INSERT INTO  `remboursemoi_pp_ipn_log` (
`firstname` ,
`lastname` ,
`payment_status` ,
`amount` ,
`custom` ,
`blob` ,
`error_message` ,
`curl_status`
)
VALUES (?, ?, ?,  ?, ?, ?, ?, ?)");
	        $v = $stmt->execute(array($first_name,$last_name,$payment_status,$payment_amount,$custom,serialize($_POST),$_err_msg,$res));

	    } catch(Execption $e) { 
	        $dbh->rollback(); 
	        die("Error!: " . $e->getMessage() . "</br>");
	    }
die();
}
?>