<form name="uspsRate" action="uspsRateForm.php" method="post" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="a" value="step1" />
	<div class="formline">Shipping from Zip: <input type="text" name="fromzip" value="<?php echo $fromzip; ?>" /></div>
	<div class="formline">Shipping to Zip: <input type="text" name="tozip" value="<?php echo $tozip; ?>" /></div>
	<div class="formline">Weight (LBS): <input type="text" name="weight" class="short" value="<?php echo $weight; ?>" /></div>
	<div class="formline"><input type="submit" /></div>
</form>

<?php
if (isset($_REQUEST['a']) && $_REQUEST['a'] == "step1") {
	$errmessage = "";
	require("uspsRate.php");
	$myRate = new uspsRate;
	echo "<h1>USPS Rate</h1>\n";
	$rate = $myRate->uspsRate($_POST['weight'], $_POST['tozip'], $_POST['fromzip']);
	echo "MyRate: uspsRate(" . $_POST['weight'] .", " . $_POST['tozip'] . ", " . $_POST['fromzip'] . ")<br/>"; 
	echo "Rate: \$$rate<br/>\n";
}
?>