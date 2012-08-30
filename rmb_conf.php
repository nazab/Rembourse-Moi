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

try { 
    $dbh = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME, DBUSER, DBPASS);
} catch( PDOExecption $e ) { 
    die("Error!: " . $e->getMessage() . "</br>"); 
}

// Email vers lequel les demande de virements sont notifiés
define('TRANSFERT_EMAIL','benjamin@nazab.com');
// WebSite Email
define('CONTACT_EMAIL','paiement@remboursemoi.fr');
