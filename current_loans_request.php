<?php
	$myIniFile = parse_ini_file ("config.ini", TRUE, INI_SCANNER_RAW);
	$url = $myIniFile["webservices"]["url"] ;
	if (isset($_GET['cb']) && $_GET['cb'] != '') {
		$patron = $_GET['cb'] ;
	}
	else{
		$patron = $myIniFile["webservices"]["default_patron"] ;
	}
	
	$xml_data = '<?xml version="1.0" encoding="utf-8"?> 
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<current_loans_request xmlns="http://bibliomondo.com/ZoneServices/">
				<subscriber>'.$patron.'</subscriber>
				<count>99</count>
			</current_loans_request>
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
	$subscriber = $xml->soapBody->current_loans_response->subscriber;
	$set = $xml->soapBody->current_loans_response->set;
	$set_count = $xml->soapBody->current_loans_response->set_count;
	if($set_count != 0){
		$max = intval($set_count)- 1 ;
		// on a récupéré un numéro de lot et un nombre de prêts, on lance maintenant la requête fetchLoans_request avec ces paramètres :
		$xml_data = '<?xml version="1.0" encoding="utf-8"?> 
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<fetchLoans_request xmlns="http://bibliomondo.com/ZoneServices/">
				<subscriber>'.$subscriber.'</subscriber>
				<set>'.$set.'</set>
				<from>0</from>
				<to>'.$max.'</to>
			</fetchLoans_request>
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
		$results = $xml->soapBody->fetchLoans_response->results;
		$prets = array();
		for($i=0; $i<=$max; $i++){
			$var = 'i_'.$i ;
			$prets[$i] = $results->$var->no;
			$loan_date[$i] = $results->$var->loan_date;
			$return_date[$i] = $results->$var->return_date;
			$seq_no[$i] = $results->$var->seq_no;
			$branch_code[$i] = $results->$var->branch->branch_code;
			$branch_desc[$i] = $results->$var->branch->description->fre;
		}
	}
?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Webservices Portfolio - current_loans_request</title>
		<link rel="stylesheet" type="text/css" href="webservices.css" media="screen" />
	</head>
	<body>
		<h1><a href="index.html">current_loans_request et fetchLoans_request</a></h1>
		<h2>Liste des prêts d'un usager</h2>
		<form id="infos_usager" method="get" action="">
			<label>CB Usager :  </label>&nbsp;<input type="text" name="cb" placeholder="EXXXXXX" />  <input type="submit" value="Afficher données">
		</form>
		<hr class="type_3" />
		<?php
			if($set_count != 0){
				echo '<table border=1>' ;
				echo '<thead>' ;
				echo '<tr><th>CB</th><th>seq_no</th><th>loan_date</th><th>return_date</th><th>localisation de prêt</th></tr>' ;
				echo '</thead>' ;
				for($i=0; $i<=$max; $i++){
					echo '<tr><td>'.$prets[$i].'</td><td>' . $seq_no[$i]  . '</td><td>' . $loan_date[$i]   . '</td><td>' . $return_date[$i]  . '</td><td>' . $branch_desc[$i]  . '</td></tr>' ;
				}
				echo '</table>' ;
			}
			else{
				echo 'Pas de prêt en cours pour cet usager' ;
			}
			echo '<hr class="type_3" />' ;
			echo 'Résultat brut : ' ;
			print_r($output);
			?>
	</body>
</html>
