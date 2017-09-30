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
			<get_patron_summary_request xmlns="http://bibliomondo.com/ZoneServices/">
				<subscriber>'.$patron.'</subscriber>
				<fetchloans>true</fetchloans>
				<fetchSuggestions>true</fetchSuggestions>
				<checkFines>true</checkFines>
				<checkMaxLoans>true</checkMaxLoans>
			</get_patron_summary_request>
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
	$omnidex_id = $xml->soapBody->get_patron_summary_response->omnidex_id;
	$name = $xml->soapBody->get_patron_summary_response->name;
	$sex = $xml->soapBody->get_patron_summary_response->sex;
	$address_1 = $xml->soapBody->get_patron_summary_response->address_1;
	$address_2 = $xml->soapBody->get_patron_summary_response->address_2;
	$address_3 = $xml->soapBody->get_patron_summary_response->address_3;
	$postal_code = $xml->soapBody->get_patron_summary_response->postal_code;
	$quarter_code = $xml->soapBody->get_patron_summary_response->quarter_code;
	$quarter_name = $xml->soapBody->get_patron_summary_response->quarter->description->fre;
	$address_email = $xml->soapBody->get_patron_summary_response->address_email;
	$telephone_1 = $xml->soapBody->get_patron_summary_response->telephone_1;
	$birth_date = $xml->soapBody->get_patron_summary_response->birth_date;
	$branch = $xml->soapBody->get_patron_summary_response->branch;
	$category = $xml->soapBody->get_patron_summary_response->category;
	$subscription_type = $xml->soapBody->get_patron_summary_response->subscription_type;
	$expiry_date = $xml->soapBody->get_patron_summary_response->expiry_date;
	$subscription_date = $xml->soapBody->get_patron_summary_response->subscription_date;
	$renewal_date = $xml->soapBody->get_patron_summary_response->renewal_date;
	$current_checkouts = $xml->soapBody->get_patron_summary_response->current_checkouts;
	$month_checkouts = $xml->soapBody->get_patron_summary_response->month_checkouts;
	$year_checkouts = $xml->soapBody->get_patron_summary_response->year_checkouts;
	$total_checkouts = $xml->soapBody->get_patron_summary_response->total_checkouts;
	$late_doc = $xml->soapBody->get_patron_summary_response->late_doc;
	$disputed_loans = $xml->soapBody->get_patron_summary_response->disputed_loans;
	$overdue_loans = $xml->soapBody->get_patron_summary_response->overdue_loans;
	$resv_cur = $xml->soapBody->get_patron_summary_response->resv_cur;
	$resv_avail = $xml->soapBody->get_patron_summary_response->resv_avail;
	$bool_error = false ;
	if($xml->soapBody->get_patron_summary_response->accountTrapLevel == 'Error' || $xml->soapBody->get_patron_summary_response->accountTrapLevel == 'Warning'){
		$bool_error = true ;
		$problemes = $xml->soapBody->get_patron_summary_response->traps ;
	}
	?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Webservices Portfolio - get_patron_summary_request</title>
		<link rel="stylesheet" type="text/css" href="webservices.css" media="screen" />
	</head>
	<body>
		<h1><a href="index.html">get_patron_summary_request</a></h1>
		<h2>Détail d'un dossier d'abonné</h2>
		<form id="infos_usager" method="get" action="">
			<label>CB Usager :  </label>&nbsp;<input type="text" name="cb" placeholder="EXXXXXX" />  <input type="submit" value="Afficher données">
		</form>
		<hr class="type_3" />
		<?php
			echo '<table border=1>' ;
			echo '<thead>' ;
			echo '<tr><th>Champ</th><th>Valeur</th></tr>' ;
			echo '</thead>' ;
			echo '<tr><td>CB</td><td>' . $patron . '</td></tr>' ;
			echo '<tr><td>omnidex_id</td><td>' . $omnidex_id . '</td></tr>' ;
			echo '<tr><td>Nom</td><td>' . $name . '</td></tr>' ;
			echo '<tr><td>Sexe</td><td>' . $sex . '</td></tr>' ;
			echo '<tr><td>Date de naissance</td><td>' . $birth_date . '</td></tr>' ;
			echo '<tr><td>Adresse</td><td>' . $address_1 . '<br .>' . $address_2 . '<br .>' . $address_3 . '<br .>' . $postal_code . '</td></tr>' ;
			echo '<tr><td>Code quartier</td><td>' . $quarter_code . '</td></tr>' ;
			echo '<tr><td>Nom quartier</td><td>' . $quarter_name . '</td></tr>' ;
			echo '<tr><td>Adresse mail</td><td>' . $address_email . '</td></tr>' ;
			echo '<tr><td>Téléphone</td><td>' . $telephone_1 . '</td></tr>' ;
			echo '<tr><td>Localisation</td><td>' . $branch . '</td></tr>' ;
			echo '<tr><td>Catégorie</td><td>' . $category . '</td></tr>' ;
			echo '<tr><td>Type d\'abonnement</td><td>' . $subscription_type . '</td></tr>' ;
			echo '<tr><td>Date de fin d\'abonnement</td><td>' . $expiry_date . '</td></tr>' ;
			echo '<tr><td>Date d\'abonnement</td><td>' . $subscription_date . '</td></tr>' ;
			echo '<tr><td>Date de renouvellement</td><td>' . $renewal_date . '</td></tr>' ;
			echo '<tr><td>Prêts en cours</td><td>' . $current_checkouts . '</td></tr>' ;
			echo '<tr><td>Prêts en retard</td><td>' . $late_doc . '</td></tr>' ;
			echo '<tr><td>overdue_loans</td><td>' . $overdue_loans . '</td></tr>' ;
			echo '<tr><td>Prêts du mois</td><td>' . $month_checkouts . '</td></tr>' ;
			echo '<tr><td>Prêts de l\'année</td><td>' . $year_checkouts . '</td></tr>' ;
			echo '<tr><td>Prêts totaux</td><td>' . $total_checkouts . '</td></tr>' ;
			echo '<tr><td>Prêts contestés</td><td>' . $disputed_loans . '</td></tr>' ;
			echo '<tr><td>Réservations en cours</td><td>' . $resv_cur . '</td></tr>' ;
			echo '<tr><td>Réservations disponibles</td><td>' . $resv_avail . '</td></tr>' ;
			echo '<tr><td>Erreurs</td><td>' ;
			if($bool_error === true){
				foreach($problemes->trap as $value){
					echo $value->trapLevel . ' : ' . $value->trapID . '<br />' ;
				}
			}
			echo '</td></tr>' ;
			echo '</table>' ;
			echo '<hr class="type_3" />' ;
			echo 'Résultat brut : ' ;
			print_r($output);
			?>
	</body>
</html>
