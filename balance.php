<?php
require('rmb_conf.php');
function relative_time( $iTime )
{
	$iTimeDifference = time() - $iTime ;
	
	if( $iTimeDifference<0 ) { return; }
	
	$iSeconds = $iTimeDifference ;
	$iMinutes = round( $iTimeDifference/60 );
	$iHours = round( $iTimeDifference/3600 );
	$iDays = round( $iTimeDifference/86400 );
	$iWeeks = round( $iTimeDifference/604800 );
	$iMonths = round( $iTimeDifference/2419200 );
	$iYears = round( $iTimeDifference/29030400 );
	
	if( $iSeconds<60 )
	return "Il y a moins d'une minute";
	elseif( $iMinutes<60 )
	return 'Il y a ' . $iMinutes . ' minute' . ( $iMinutes>1 ? 's' : '');
	elseif( $iHours<24 )
	return 'Il y a ' . $iHours . ' heure' . ( $iHours>1 ? 's' :  '' );
	elseif( $iDays<7 )
	return 'Il y a ' . $iDays . ' jour' . ( $iDays>1 ? 's' :  '' );
	elseif( $iWeeks <4 )
	return 'Il y a ' . $iWeeks . ' semaine' . ( $iWeeks>1 ? 's' :  '' );
	elseif( $iMonths<12 )
	return 'Il y a ' . $iMonths . ' mois';
	else
	return 'Il y a ' . $iYears . ' an' . ( $iYears>1 ? 's' :  '' );
}

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
    // Fetch all the transaction pending or not for a transfert made for this email address
	$stmt = $dbh->prepare("SELECT tx_fee, ht_firstname, ht_lastname, tx_unit_price, tx_qte, pp_ipn_blob, tr_status, tr_complete_date, tx.created_at as tx_date
FROM remboursemoi_transaction tx
LEFT JOIN remboursemoi_transfert_request tr ON tx.tr_id = tr.ID
WHERE bnf_email =  ?
AND pp_txn_id IS NOT NULL 
ORDER BY tx.created_at DESC");
    try {
        $stmt->execute( array($email_from_hash));
        $transfert_list = $stmt->fetchAll();
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
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="main_body" >
	<img id="top" src="top.png" alt="">
	<div id="form_container">
		<h1>Remboursement entre amis</h1>
		<form class="appnitro">
		<div class="form_description">
			<h2><a href="http://<?php echo $_SERVER['HTTP_HOST'];?>">Rembourse moi</a></h2>
			<p>Les bons comptes font les bons amis alors restons amis.</p>
		</div>
		</form>
		<div id="pay_description">
		<?php
		if(!empty($_GET['message']) && $_GET['message'] == 'transfert_asked') {
			?>
			
			<div class="alert alert-info">
L'argent sera sur <strong>votre compte sous 30 jours</strong>.<br/>Vous recevrez un email quand le virement sera effectué.
</div>
			<?php
		}
		$sum =  0;
		foreach($transfert_list as $p) {
			if($p['tr_status'] == null) {
				$sum += ($p['tx_unit_price'] * $p['tx_qte'])-$p['tx_fee'];
			}
		}
		
		?>
			<h2>Votre solde est de <?php echo number_format($sum,2,',',' ');?> &#8364;</h2>
			<p style="font-size:14px;">
			<table class="table table-bordered table-hover table-condesed" style="font-size:13px;">
				<thead>
					<tr>
						<th>Nom</th>
						<th>Date</th>
						<th>Avant commission</th>
						<th>Frais</th>
						<th>Montant net</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($transfert_list as $p) { 
				switch($p['tr_status']) {
					case 'PENDING':
						echo '<tr class="info">';
					break;
					case 'COMPLETED':
						echo '<tr class="success">';
					break;
					default:
						echo '<tr>';
					break;
				}
				
				
				?>
						<td><?php echo htmlentities($p['ht_firstname']).' '.htmlentities($p['ht_lastname']); ?></td>
						<td><?php echo htmlentities(relative_time(strtotime($p['tx_date']))); ?></td>
						<td><?php echo htmlentities(number_format($p['tx_unit_price'] * $p['tx_qte'],2,',',' ')).' &#8364'; ?></td>
						<td>-<?php echo  number_format($p['tx_fee'],2,',',' ');?> &#8364</td>
						<td><?php echo number_format(($p['tx_unit_price'] * $p['tx_qte'])-$p['tx_fee'],2,',',' ');?> &#8364</td>
						<td>
							<?php
								switch($p['tr_status']) {
									case 'PENDING':
										echo htmlentities('Le virement est en attente');
									break;
									case 'COMPLETED':
										echo htmlentities('Virement efectué');
									break;
									default:
										echo htmlentities('Virement à demander',ENT_COMPAT,'UTF-8');
									break;
								}
							?>
						</td>
					</tr>
				<?php  } ?>
				</tbody>
			</table>
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
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Demander un virement" />
		</li>
			</ul>
				
				</form>	
			</p>
		</div>
		<div id="footer">
			<a href="http://benjaminazan.com">Benjamin Azan</a>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
	 <script src="js/bootstrap.min.js"></script>
</body>
</html>