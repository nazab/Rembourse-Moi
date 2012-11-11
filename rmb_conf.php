<?php
$host = $_SERVER['HTTP_HOST'];
switch($host) {
	case 'remboursemoi.fr':
		define('DBNAME','brackeimmyblog');
		define('DBHOST','mysql51-37.perso');
		define('DBUSER','brackeimmyblog');
		define('DBPASS','tdfftxWN');
	break;
	
	case 'dev.remboursemoi':
		define('DBNAME','rmbdev');
		define('DBHOST','localhost');
		define('DBUSER','root');
		define('DBPASS','root');
	break;

}
define('HASH_SALT','A3DdFG906');
//@TODO change to paiement@remboursemoi.fr
define('PAYPAL_BUSINESS_EMAIL','marie_1345148567_biz@gmail.com');
define('MAILJET_USERNAME','f7a8d93d3037126d9180606d9aa16c49');
define('MAILJET_PASSWORD','768c9243405675b728ba94d1d4f3572f');

try { 
    $dbh = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME, DBUSER, DBPASS);
} catch( PDOExecption $e ) { 
    die("Error!: " . $e->getMessage() . "</br>"); 
}

// Email vers lequel les demande de virements sont notifiés
define('TRANSFERT_EMAIL','benjamin.Azan@gmail.com');
// WebSite Email
define('CONTACT_EMAIL','paiement@remboursemoi.fr');
//define('CONTACT_EMAIL','benjamin@nazab.com');

function get_fee($_amount) {
	return 0.05 * $_amount;
}
