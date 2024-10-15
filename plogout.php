<?php

setcookie("centraldata", "", time() - 3600);

$centralData = (@isset($_COOKIE['centraldata']) ? $_COOKIE['centraldata'] : '');

// echo "remove centraldata<br />\n";
// echo "centraldata = $centraldata<br />\n";

?>

<html>
	<head>
		<title>Onpays Monitoring</title>
		<link rel=StyleSheet href="style.css" type="text/css"/>
	</head>
	
	<body>
		<meta http-equiv='REFRESH' content='0;url=main.php'>
		<!-- 
		// Berhasil logout, harap menunggu untuk kembali ke halaman login...
		// <br />
		// Atau langsung klik <a href='main.php'>ini</a>
		-->
		<br />
	</body>
</html>
