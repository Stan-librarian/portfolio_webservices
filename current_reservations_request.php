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
			<current_reservations_request xmlns="http://bibliomondo.com/ZoneServices/">
				<subscriber>'.$patron.'</subscriber>
				<count>99</count>
			</current_reservations_request>
		</soap:Body>
	</soap:Envelope>' ;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output1 = curl_exec($ch);
	curl_close($ch);
	// SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
	$output1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $output1);
	$xml=simplexml_load_string($output1) or die("Error: Cannot create object");
	$subscriber = $xml->soapBody->current_reservations_response->subscriber;
	$set = $xml->soapBody->current_reservations_response->set;
	$set_count = $xml->soapBody->current_reservations_response->set_count;
	if($set_count != 0){
		$max = intval($set_count)- 1 ;
		// on a récupéré un numéro de lot et un nombre de prêts, on lance maintenant la requête fetchreservations_request avec ces paramètres :
		$xml_data = '<?xml version="1.0" encoding="utf-8"?> 
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<fetchReservations_request xmlns="http://bibliomondo.com/ZoneServices/">
				<subscriber>'.$subscriber.'</subscriber>
				<set>'.$set.'</set>
				<from>0</from>
				<to>'.$max.'</to>
			</fetchReservations_request>
			</soap:Body>
		</soap:Envelope>' ;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output2 = curl_exec($ch);
		curl_close($ch);
		// SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
		$output2 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $output2);
		$xml=simplexml_load_string($output2) or die("Error: Cannot create object");
		$results = $xml->soapBody->fetchReservations_response->results;
		$prets = array();
		for($i=0; $i<=$max; $i++){
			$var = 'i_'.$i ;
			$prets[$i] = $results->$var->no;
			$resv_date[$i] = $results->$var->resv_date;
			$seq_no[$i] = $results->$var->seq_no;
			$branch_code[$i] = $results->$var->branch->branch_code;
			$branch_desc[$i] = $results->$var->branch->description->fre;
			$document[$i] = $results->$var->document;
			$resv_rank[$i] = $results->$var->resv_rank;
			$issue_caption[$i] = $results->$var->issue_caption;
			$print_status[$i] = $results->$var->print_status;
		}
	}
?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Webservices Portfolio - current_reservations_request</title>
		<link rel="stylesheet" type="text/css" href="webservices.css" media="screen" />
		<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
	</head>
	<body>
		<a id="haut"></a>
		<h1><a href="index.html">current_reservations_request et fetchReservations_request</a></h1>
		<h2>Liste des réservations d'un usager</h2>
		<form id="infos_usager" method="get" action="">
			<label>CB Usager :  </label>&nbsp;<input type="text" name="cb" placeholder="EXXXXXX" />  <input type="submit" value="Afficher données">
		</form>
		<hr class="type_3" />
		<?php
			if($set_count != 0){
				echo '<table border=1>' ;
				echo '<thead>' ;
				echo '<tr><th>seq_no</th><th>no_perio</th><th>État</th><th>Exemplaire assigné</th><th>Date de réservation</th><th>Rang</th><th>Localisation de retrait</th></tr>' ;
				echo '</thead>' ;
				for($i=0; $i<=$max; $i++){
					if($print_status[$i] == 'A') {
						$print_status[$i] = 'exemplaire assigné, pas d\'avis de disponibilité imprimé' ;
					}
					elseif($print_status[$i] == 'A1') {
						$print_status[$i] = ' avis de disponibilité imprimé' ;
					}
					else {$print_status[$i] = 'aucun exemplaire assigné' ;}
					echo '<tr><td>' . $seq_no[$i]  . '</td><td>' . $issue_caption[$i]  . '</td><td>' . $print_status[$i]  . '</td><td>' . $document[$i]  . '</td><td>' . $resv_date[$i]   . '</td><td>' . $resv_rank[$i]  . '</td><td>' . $branch_desc[$i]  . '</td></tr>' ;
				}
				echo '</table>' ;
			}
			else{
				echo 'Pas de réservation en cours pour cet usager' ;
			}
			echo '<hr class="type_3" />' ;
			echo '<b>Résultat brut (current_reservations_response)</b> : ' ;
			print_r($output1);
			echo '<hr class="type_3" />' ;
			echo '<b>Résultat brut (fetchReservations_response)</b> : ' ;
			print_r($output2);
			echo '<br />' ;
		?>
		<a href="#haut"><button type="button" class="right" style="font-size: 2em; font-weight: bold;" title="Retour en haut de page"> <i class="fa fa-arrow-up" aria-hidden="true"></i> </button></a>
		<!--<a href="#haut"><button type="button" class="right" style="font-size: 2em; font-weight: bold;" title="Retour en haut de page">&nbsp;&#8679;&nbsp;</button></a>-->
	</body>
</html>
