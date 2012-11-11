<?php
require('rmb_conf.php');
$hash = $_POST['hash'];
$code_banque = $_POST['code_banque'];
$code_guichet = $_POST['code_guichet'];
$num_compte = $_POST['num_compte'];
$cle_rib = $_POST['cle_rib'];
// log the transfert request
$stmt = $dbh->prepare("INSERT INTO remboursemoi_transfert_request (code_banque,code_guichet,num_compte,cle_rib) VALUES (?,?,?,?)"); 
try {
    $stmt->execute( array($code_banque,$code_guichet,$num_compte,$cle_rib));
    $tr_id = $dbh->lastInsertId();
} catch(PDOExecption $e) { 
    $dbh->rollback(); 
    die("Error!: " . $e->getMessage() . "</br>");
}
// Get the email adresse form the hash
$stmt = $dbh->prepare("select bnf_email from remboursemoi_transaction where public_balance_hash = ?"); 
try {
    $stmt->execute( array($hash));
    $hash_line = $stmt->fetchAll();
    if(count($hash_line) == 1) {
        // It is a correct hash
        $email_from_hash = $hash_line[0]['bnf_email'];
    } else {
        die();
    }
} catch(PDOExecption $e) { 
    $dbh->rollback(); 
    die("Error!: " . $e->getMessage() . "</br>");
}
// link the transaction to the transfert request
$stmt = $dbh->prepare("UPDATE remboursemoi_transaction SET tr_id = ? WHERE bnf_email = ? AND pp_txn_id IS NOT NULL AND tr_id IS NULL"); 
try {
    $stmt->execute( array($tr_id,$email_from_hash));
} catch(PDOExecption $e) { 
    $dbh->rollback(); 
    die("Error!: " . $e->getMessage() . "</br>");
}
// Send an email to the admin with :
// - link to the ballance page

error_reporting(1);

require('lib/Swift/swift_required.php');

$subject = 'Nouvelle demande de virement pour Rembourse Moi';
// approved domains only!
$from = array(CONTACT_EMAIL =>'Rembourse Moi');
$to = array(TRANSFERT_EMAIL  => 'Benjamin AZAN');


//$text = "ID de la demande : ".$tr_id." Lien vers le compte de la personne :  http://".$_SERVER['HTTP_HOST']."/balance.php?hash=".urlencode($hash);

$text = 'coucou';

$transport = Swift_SmtpTransport::newInstance('in.mailjet.com', 587);
$transport->setUsername(MAILJET_USERNAME);
$transport->setPassword(MAILJET_PASSWORD);
$swift = Swift_Mailer::newInstance($transport);

$message = new Swift_Message($subject);
$message->setFrom($from);
$message->setBody($text, 'text/plain');
$message->setTo($to);

$recipients = $swift->send($message, $failures);

// redirect to the balance page
header('Location: http://'.$_SERVER['HTTP_HOST'].'/balance.php?hash='.urlencode($hash).'&message=transfert_asked');