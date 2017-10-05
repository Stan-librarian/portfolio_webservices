// ------------------------------------------------------------------------
// Licence : GPL
// ------------------------------------------------------------------------
<?php
	$myIniFile = parse_ini_file ("config.ini", TRUE, INI_SCANNER_RAW);
	$url = $myIniFile["webservices"]["url"] ;
	if (isset($_GET['cb']) && $_GET['cb'] != '') {
		$patron = $_GET['cb'] ;
	}
	else{
		$patron = $myIniFile["webservices"]["default_patron"] ;
	}
	if (isset($_GET['password']) && $_GET['password'] != '') {
		$password = $_GET['password'] ;
	}
	else{
		$password =$myIniFile["webservices"]["default_password"] ;
	}
	$xml_data = '<?xml version="1.0" encoding="utf-8"?> 
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<login_request xmlns="http://bibliomondo.com/ZoneServices/">
				<userId>'.$patron.'</userId>
				<password>'.$password.'</password>
			</login_request>
		</soap:Body>
	</soap:Envelope>' ;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	// SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
	$output = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $output);
	$xml=simplexml_load_string($output) or die("Error: Cannot create object");
	$nom = $xml->soapBody->login_response->patron_name;
	$cb = $xml->soapBody->login_response->patron_id;
	$status = $xml->soapBody->login_response->status;
?>

<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Webservices Portfolio - login_request</title>
		<link rel="stylesheet" type="text/css" href="webservices.css" media="screen" />
	</head>
	<body>
		<h1><a href="index.html">login_request</a></h1>
		<h2>Authentification d'un usager</h2>
		<form id="infos_usager" method="get" action="">
			<label>CB Usager :  </label>&nbsp;<input type="text" name="cb" placeholder="EXXXXXX" />  <label>Mot de Passe :  </label>&nbsp;<input type="text" name="password" placeholder="JJMMAAAA" />  <input type="submit" value="Afficher données">
		</form>
		<hr class="type_3">
		<?php
			echo '<table border=1>' ;
			echo '<thead>' ;
			echo '<tr><th>Champ</th><th>Valeur</th></tr>' ;
			echo '</thead>' ;
			echo '<tr><td>CB</td><td>' . $cb . '</td></tr>' ;
			echo '<tr><td>Nom</td><td>' . $nom . '</td></tr>' ;
			echo '<tr><td>Status</td><td>' . $status . '</td></tr>' ;
			echo '</table>' ;
			echo '<hr class="type_3" />' ;
			echo 'Résultat brut : ' ;
			print_r($output);
		?>
	</body>
</html>
