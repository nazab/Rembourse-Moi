<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
require 'rmb_conf.php';
if(!empty($_GET['qte'])) {
		$qte = $_GET['qte'];
	} else {
		$qte = 1;
	}
	$unit_price = ($_GET['amount']+($_GET['cent']/100));
	$price = $unit_price * $qte;
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo htmlentities($_GET['name']);?> te demande de lui rembourser <?php echo htmlentities(number_format($price,2,',',' '));?>&#8364;.</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="view.js"></script>
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
			<h2><a href="http://remboursemoi.fr">Rembourse moi</a></h2>
			<p>Les bons comptes font les bons amis alors restons amis.</p>
		</div>
		</form>
		<div id="pay_description">
		<p id="share">
		Copie/Colle cette adresse à tes amis<br/>
		<img src="arrow-down.png" style="height:50px;margin-top:20px"><br/>
		<form>
		<?php
		$url = 'http://remboursemoi.fr/pay.php?email='.urlencode($_GET['email']).'&name='.urlencode($_GET['name']).'&amount='.urlencode($_GET['amount']).'&cent='.urlencode($_GET['cent']).'&qte='.urlencode($qte);
require('bitly.php');
$bt = new Bitly('nazab','R_17a95912a5e8e7bd464b88c0f8236376');
$results = $bt->shorten($url, 'j.mp');
$short_url = 'http://j.mp/'.$results['hash'];
?>
<input readonly="readonly" id="urltoshare" type="text" value="<?php echo $short_url;?>" style="font-size:20px;width:280px;"/>
</form><br/><br/>pour te faire rembourser.<br/><br/>Merci !
		</p>
		<?php
		$stmt = $dbh->prepare("INSERT INTO `remboursemoi_request` (`rmb_name`,`rmb_email`,`rmb_amount`) values (?,?,?)"); 

    try { 
        $stmt->execute( array($_GET['name'], $_GET['email'],$_GET['amount'].'.'.$_GET['cent']));
    } catch(PDOExecption $e) { 
        $dbh->rollback(); 
        die("Error!: " . $e->getMessage() . "</br>");
    } 
		?>
		</div>
		<div id="footer">
			<a href="http://benjaminazan.fr">Benjamin Azan</a>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
<script type="text/javascript">
$("#urltoshare").click(function() {
	$(this).select();
});
</script>
</body>
</html>