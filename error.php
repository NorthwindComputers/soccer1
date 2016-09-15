<!doctype html>
<head>
	<title>ERROR | SoccerOne</title>
</head>
<body>
<h1>FORBIDDEN error!</h1>
<p>If you feel that this is in error, please visit us again at <a href="https://soccerone.com">SoccerOne.com</a>!</p>
<?php
	$to = 'richard@northwind.us,kensel@northwind.us';
	$sub = 'SOCCER ONE - Post Error reported on website!';
	$rip = $_SERVER["REMOTE_ADDR"];
	$rhost = $_SERVER["REMOTE_HOST"];
	$uagent = $_SERVER["HTTP_USER_AGENT"];
	$method = $_SERVER["REQUEST_METHOD"];
	$prot = $_SERVER["SERVER_PROTOCOL"];
	$docroot = $_SERVER["DOCUMENT_ROOT"];
	$qstring = $_SERVER["QUERY_STRING"];
	$ruri = $_SERVER["REQUEST_URI"];
	$pvars = file_get_contents('php://input');

	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$msg = '<b>IP:</b> ' . $rip . "<br/>";
	$msg .= '<b>HOST:</b> ' . $rhost . "<br/>";
	$msg .= '<b>User Agent:</b> ' . $uagent . "<br/>";;
	$msg .= '<b>Method:</b> ' . $method . "<br/>";
	$msg .= '<b>Protocol:</b> ' . $prot . "<br/>";
	$msg .= '<b>POST Vars:</b> ' . $pvars . "<br/>";
	$msg .= '<b>Document Root:</b> ' . $docroot . "<br/>";
	$msg .= '<b>Request URI:</b> ' . $ruri . "<br/>";
	$msg .= '<b>Query String:</b> ' . $qstring . "<br/>";

	if ($method == "POST") {
		mail ($to, $sub, $msg, $headers);
	}
?>
</body>