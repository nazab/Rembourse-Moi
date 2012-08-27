<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
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
			<h2><a href="http://www.remboursemoi.fr">Rembourse moi</a></h2>
			<p>Les bons comptes font les bons amis alors restons amis.</p>
		</div>
		</form>
		<div id="pay_description">
					<p><?php echo htmlentities($_GET['name']);?> te demande de lui rembourser <?php echo htmlentities(number_format($price,2,',',' ')	);?>&#8364;.</p>
					<p style="font-size:10px;">Je dois <?php if ($qte > 1 ) { ?><a href="http://remboursemoi.fr/pay.php?email=<?php echo $_GET['email'];?>&name=<?php echo $_GET['name'];?>&amount=<?php echo $_GET['amount']?>&cent=<?php echo $_GET['cent']?>&qte=<?php echo $qte-1;?>">une part de moins ( <?php echo number_format($price - $unit_price,2,',',' ');?>&#8364; )</a> ou <?php } ?><a href="http://remboursemoi.fr/pay.php?email=<?php echo $_GET['email'];?>&name=<?php echo $_GET['name'];?>&amount=<?php echo $_GET['amount']?>&cent=<?php echo $_GET['cent']?>&qte=<?php echo $qte+1;?>">une part de plus ( <?php echo number_format($price + $unit_price,2,',',' ');?>&#8364; )</a>.</p>
			<p>
				<form id="pay" action="https://www.paypal.com/cgi-bin/webscr" method="post" onclick>
					<input type="hidden" name="cmd" value="_xclick"/>
					<input type="hidden" name="business" value="<?php echo$_GET['email'];?>"/>
					<input type="hidden" name="item_name" value="Remboursement de <?php echo $_GET['name'];?>"/>
					<input type="hidden" name="amount" value="<?php echo number_format($price,2,'.','');?>"/>
					<input type="hidden" name="currency_code" value="EUR"/>
					<input type="hidden" name="no_note" value="1"/>
					<input type="hidden" name="no_shipping" value="1"/>
					<input type="hidden" name="lc" value="FR"/>
					<input type="hidden" name="return" value="http://remboursemoi.fr/thankyou.html"/>
					<input type="hidden" name="cancel_return" value="http://remboursemoi.fr/cancel.html"/>
					<input type="submit" value="Ok je rembourse <?php echo $_GET['name'];?>" />
				</form>
			</p>
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