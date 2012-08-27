<?php
require('rmb_conf.php');
if(!empty($_GET['hash'])) {
	$hash = $_GET['hash'];
	// Check hash is a real one
	// Fetch the email form the hash
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
	// Fetch all the transaction made fo this email address
	$stmt = $dbh->prepare("select ht_firstname,ht_lastname, tx_unit_price, tx_qte, pp_ipn_blob from remboursemoi_transaction where bnf_email = ? AND pp_txn_id is not null and tr_id is null");
    try {
        $stmt->execute( array($email_from_hash));
        $payment_list = $stmt->fetchAll();
    } catch(PDOExecption $e) { 
        $dbh->rollback(); 
        die("Error!: " . $e->getMessage() . "</br>");
    }

	
} else {
	die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#form_467579").validate(
    {
  		rules: {
	    	code_banque: {
	    	  required: true,
		      digits: true,
		      minlength:5,
		      maxlength:5
	    	},
	    	code_guichet: {
	    	  required: true,
		      digits: true,
		      minlength:5,
		      maxlength:5
	    	},
	    	num_compte: {
	    	  required: true,
		      digits: true,
		      minlength:11,
		      maxlength:11
	    	},
	    	cle_rib: {
	    	  required: true,
		      digits: true,
		      minlength:2,
		      maxlength:2
	    	}
  		},
  		messages: {
	        code_banque: "Attention cela doit être que une série de 5 chiffres et rien d'autre.",
	        code_guichet: "Attention cela doit être que une série de 5 chiffres et rien d'autre.",
	        num_compte: "Attention cela doit être que une série de 11 chiffres et rien d'autre.<br/> Si il t'en manque ajoutes des 0 au début pour faire 11 chiffres.",
	        cle_rib: "Attention cela doit être une série de 2 chiffres et rien d'autre."
	    }
	}
    );
});
</script>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4350990-32']);
  _gaq.push(['_trackPageview']);
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
 })();
</script>
</head>
<body id="main_body" >
	<img id="top" src="top.png" alt="">
	<div id="form_container">
		<h1>Remboursement entre amis</h1>
		<form class="appnitro">
		<div class="form_description">
			<h2><a href="http://www.remboursemoi.fr">Rembourse moi</a></h2>
			<p>Les bons comptes font les bons amis alors restons amis.</p>
		</div>
		</form>
		<div id="pay_description">
		<?php
		if(!empty($_GET['message']) && $_GET['message'] == 'transfert_asked') {
			?>
			<p class="notification">L'argent sera sur votre compte sous 30 jours.<br/>Vous recevrez un email quand le virement sera effectué.</p>
			<?php
		}
		$sum =  0;
		foreach($payment_list as $p) {
			$sum += $p['tx_unit_price'] * $p['tx_qte'];
			}
		
		?>
			<h2>Votre solde est de <?php echo number_format($sum,2,',',' ');?>&#8364;</h2>
			<p style="font-size:14px;">
			<?php foreach($payment_list as $p) {
					echo '- '.htmlentities($p['ht_firstname']).' '.htmlentities($p['ht_lastname']).' '.htmlentities(number_format($p['tx_unit_price'] * $p['tx_qte'],2,',',' ')).'&#8364;<br/>';
				}
			?>
			</p>
			<p>
			Demander un virement sur votre compte.<br/>
			<form id="form_467579" class="appnitro"  method="post" action="request-transfert.php">
				<ul >
			
					<li id="li_1" >
		<label class="description" for="code_banque">Code banque </label>
		<div>
			<input id="code_banque" name="code_banque" class="element text medium" type="text" maxlength="5" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>5 chiffres</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="code_guichet">Code guichet </label>
		<div>
			<input id="code_guichet" name="code_guichet" class="element text medium" type="text" maxlength="5" value=""/> 
		</div><p class="guidelines" id="guide_2"><small>5 chiffres aussi</small></p> 
		</li>		<li id="li_3" >
		<label class="description" for="num_compte">Numéro de compte </label>
		<div>
			<input id="num_compte" name="num_compte" class="element text medium" type="text" maxlength="11" value=""/> 
		</div><p class="guidelines" id="guide_3"><small>11 chiffres</small></p> 
		</li>		<li id="li_4" >
		<label class="description" for="cle_rib">Clé RIB </label>
		<div>
			<input id="cle_rib" name="cle_rib" class="element text medium" type="text" maxlength="2" value=""/> 
		</div><p class="guidelines" id="guide_4"><small>2 chiffres</small></p> 
		</li>
			
					<li class="buttons">
					<input type="hidden" value="<?php echo $hash;?>" name="hash"/>
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Demaner un virement" />
		</li>
			</ul>
				
				</form>	
			</p>
			
			<p>Add pending transfert request list</p>	
		</div>
		<div id="footer">
			<a href="http://benjaminazan.com">Benjamin Azan</a>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
</body>
</html>