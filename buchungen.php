<!doctype html>
<html lang="de">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" 
          content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" 
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" 
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" 
          crossorigin="anonymous">

    <!-- Zusätzliches CSS -->
    <link rel="stylesheet" 
          href="style.css">

    <title>Buchungen</title>

    <!-- Google Fonts -->
    <!-- Font Type: Patrick Hand -->
    <link href="https://fonts.googleapis.com/css?family=Bitter" 
          rel="stylesheet">

 
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" 
	        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" 
	        crossorigin="anonymous"></script>
 
	<script type="text/javascript">
	$(document).ready(function() {
		$(".spinner-border").hide();
	});
	</script>
</head>

<body class="font-Bitter bg-light text-dark">

	<?php
	
	# <!-- Navigationsleiste einfügen-->
	include 'navigation.php';
	
	
	# **********************************************************
	# ***                   Definitionen                     ***
	# ********************************************************** 
	
	# Werte aus der Formulareingabe "Bank" auslesen
	# htmlentities --> Aus der Zeichenkette werden HTML-Tags in Code um. 
	# strip_tags   --> Aus der Zeichenkette werden HTML- und PHP-Tags entfernt.
	# preg_replace --> Aus der Zeichenkette werden bestimmte Zeichen gelöscht.
	if(isset($_GET['Bank'])) {
		$strBankID_aus_GET = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_GET['Bank']))) ;
	} else {
		$strBankID_aus_GET = "X";
	} // Ende der If-Abfrage


	# Auslesen der Filtervariable 'Zeitraum'
	if(isset($_GET['Zeitraum'])) {
		$strZeitraum_aus_GET = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_GET['Zeitraum']))) ;
	} else {
		$strZeitraum_aus_GET = "X";
	} // Ende der If-Abfrage



	# Auslesen der Filtervariable 'Suchbegriff'
	if(isset($_POST['Suchbegriff'])) {
		$Suchbegriff = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_POST['Suchbegriff']))) ;
	} else {
		$Suchbegriff = "";
	} // Ende der If-Abfrage
	# Für die LIKE-Bedingungen wird der Suchbegriff verfeinert
	$Suchbegriff = "%" . $Suchbegriff . "%";


	# **********************************************************
	# ***                  Datenbankzugang                   ***
	# **********************************************************
	# Verbindungsaufbau zur Datenbank
	include 'datenbank.php';
	
	
	
	
	# **********************************************************
	# ***            Liste der Konten aus DB auslesen        ***
	# **********************************************************
	$DBabfrage = 	"SELECT `id`, 
							`Inhaber`, 
							`Bankname`, 
							`Kontonummer`,
							`Kontoname`
					FROM  `Banken` 
					ORDER BY `id` ASC";
	$DBausgabe = $pdo->query($DBabfrage);
	
	# Definition "arrBankID_aus_DB" als Array, das alle Konten enthält. 
	$arrBankID_aus_DB = array();
	# Die id des Arrays entspricht der ID aus der Tabelle `Banken`
	foreach($DBausgabe as $index1 => $zaehler) {
		$arrBankID_aus_DB[$zaehler['id']] =  $zaehler['Inhaber'] 
		             . " | " 
		             . $zaehler['Bankname']
		             . " | " 
		             . $zaehler['Kontoname'];
	}
	
	
	# **********************************************************
	# ***       Liste der Kalenderjahre aus DB auslesen      ***
	# **********************************************************
	
	$DBabfrage = 	"SELECT DISTINCT YEAR(`Buchungsdatum`) AS 'Jahr'
					 FROM `Buchungen` ";
					 if ($strBankID_aus_GET != 'X') {
						 $DBabfrage .= "WHERE `BankID` = '" . $strBankID_aus_GET . "' ";
					 }
					 $DBabfrage .= "ORDER BY `Jahr` DESC";
	$DBausgabe = $pdo->query($DBabfrage);
	
	# Definition "arrBankID_aus_DB" als Array, das alle Konten enthält. 
	$arrKalenderjahre_aus_DB = array();
	# Die id des Arrays entspricht der ID aus der Tabelle `Banken`
	foreach($DBausgabe as $Kalenderjahr) {
		$arrKalenderjahre_aus_DB[] =  $Kalenderjahr['Jahr'];
	}
	
	
	
	
	# Filter für Datenbankabfrage vordefinieren
	# if ($strZeitraum_aus_GET == '')   $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == 'X')  $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
	if ($strZeitraum_aus_GET == 'X')  $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '5J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 5 YEAR), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '3J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 YEAR), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '2J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 2 YEAR), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '3M') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')"; 
	if ($strZeitraum_aus_GET == '3M') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '6M') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 6 MONTH), '%Y-%m-%d')"; 
	if ($strZeitraum_aus_GET == '6M') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 6 MONTH), '%Y-%m-%d')"; 
	# if ($strZeitraum_aus_GET == '1J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 1 YEAR), '%Y-%m-%d')"; 
	if ($strZeitraum_aus_GET == '1J') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 1 YEAR), '%Y-%m-%d')"; 
	if ($strZeitraum_aus_GET >= '1965') $strJahr = "WHERE YEAR(`Buchungsdatum`) = '" . $strZeitraum_aus_GET . "' ";
	if (strlen($strZeitraum_aus_GET) == '4') $sqlAND = "AND YEAR(`Buchungsdatum`) = '" . $strZeitraum_aus_GET . "' ";
	
	/*
	SELECT * 
	FROM `Buchungen` 
	WHERE YEAR(`Buchungsdatum`) = '2015' 
	ORDER BY `Buchungsdatum` ASC
	*/
	
	# **********************************************************
	# ***          Liste der Buchungen aus DB auslesen       ***
	# **********************************************************

	$DBabfrage = "SELECT CONCAT(b.`Bankname`, ' ', b.`Inhaber`, ' ', b.`Kontoname`) AS Bank,
						a.`Buchungsdatum`, 
						a.`Valuta`, 
						a.`Buchungstyp`,
						a.`AuftraggeberEmpfaenger`,
						a.`Verwendungszweck`,
						a.`Betrag`, 
						a.`Waehrung`,
						a.`Saldo`
				FROM `Buchungen` a, 
					`Banken` b
				WHERE a.`BankID` = b.`id`";
				if ($strBankID_aus_GET != 'X') {
					$DBabfrage .= "AND   a.`BankID` = '" .  $strBankID_aus_GET . "' ";
				}
				
				$DBabfrage .= $sqlAND; 
				$DBabfrage .=
				"
				
				AND   ( a.`Verwendungszweck` LIKE '" 
						. $Suchbegriff 
						. "' OR    a.`AuftraggeberEmpfaenger` LIKE '" 
						. $Suchbegriff . "' )
				ORDER BY a.`Buchungsdatum` DESC, 
						a.`Datensatznummer` DESC, 
						a.`Datensatznummer` DESC; ";
	$DBausgabe = $pdo->query($DBabfrage);
	#LIMIT 30; 
	?>
	<?php 
	# **********************************************************
	# ***                                                    ***
	# ***                     Ausgabe                        ***
	# ***                                                    ***
	# **********************************************************
	?>
		<div class="container-fluid	">
            <div class="pt-3">
				<span class="h1">Buchungen</span>
				<?php 
				if ($strBankID_aus_GET != 'X') {
					echo '<span class="badge badge-pill badge-info ml-2">' . $arrBankID_aus_DB[$strBankID_aus_GET] . '</span>';
				} else {
					echo '<span class="badge badge-pill badge-info ml-2">Alle Konten</span>';
				}
				?>
				<div class="spinner-border text-info ml-2" role="status">
					<span class="sr-only">Loading...</span>
				</div>
            </div>
			
			<?php
			# <h1 class="pt-3">Buchungen<span class="badge badge-info mr-2">Hallo</span></h1>
			# echo $DBabfrage . "<br>"; 
			echo "<br>";
			
			
			# **********************************************************
			# ***               Filter-Buttons                       ***
			# **********************************************************
			?>
			
			<div class="btn-group btn-group-lg pt-3 pb-3">
				<div class="btn-group btn-group-lg">
					<button type="submit" 
							class="btn btn-dark dropdown-toggle" 
							data-toggle="dropdown">Konto
						<?php echo '<span class="badge badge-info">';
						if ($strBankID_aus_GET  != 'X') {
							echo $strBankID_aus_GET;
						}
						echo '</span>'; ?>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" 
						   href="/buchungen.php">
						   <span class="badge badge-info mr-2">⚪</span>Alle Konten</a>
						<?php
						foreach($arrBankID_aus_DB as $index2 => $bankname) {
							if ($strZeitraum_aus_GET != 'X') {
								$URL_Ziel = "/buchungen.php?Bank=" . $index2 . "&Zeitraum=" . $strZeitraum_aus_GET;
							} else {
								$URL_Ziel = "/buchungen.php?Bank=" . $index2;
							}
							echo '<a class="dropdown-item" href="' 
								. $URL_Ziel 
								. '">' 
								. '<span class="badge badge-info mr-2">' 
								. $index2 
								. '</span>'
								. $bankname
					            . '</a>';
						}
						?>
						</a>
					</div> 
				</div> 


				<div class="btn-group btn-group-lg">
					<button type="button" 
							class="btn btn-secondary dropdown-toggle" 
							data-toggle="dropdown">Zeitraum
							<?php echo '<span class="badge badge-info">'; 
										if ($strZeitraum_aus_GET != 'X') {
											echo $strZeitraum_aus_GET;
										}
							           echo '</span>'; ?>
					</button>
					<div class="dropdown-menu">
						<?php
						$URL_Ziel = "/buchungen.php?Bank=" . $strBankID_aus_GET . "&Zeitraum=X";
						echo '<a class="dropdown-item" href="' . $URL_Ziel .  '">Alle Buchungen</a>';
						
						$URL_Ziel = "/buchungen.php?Bank=" . $strBankID_aus_GET . "&Zeitraum=3M";
						echo '<a class="dropdown-item" href="' . $URL_Ziel .  '">3 Monate</a>';
						$URL_Ziel = "/buchungen.php?Bank=" . $strBankID_aus_GET . "&Zeitraum=6M";
						echo '<a class="dropdown-item" href="' . $URL_Ziel .  '">6 Monate</a>';
						$URL_Ziel = "/buchungen.php?Bank=" . $strBankID_aus_GET . "&Zeitraum=1J";
						echo '<a class="dropdown-item" href="' . $URL_Ziel .  '">1 Jahr</a>';
						
						foreach($arrKalenderjahre_aus_DB as $Jahr) { 
							$URL_Ziel = "/buchungen.php?Bank=" . $strBankID_aus_GET . "&Zeitraum=" . $Jahr;
							echo '<a class="dropdown-item" href="' . $URL_Ziel .  '">' . $Jahr . '</a>';
						}
						?>
					</div>
				</div>      
				
				<?php
				###### HIER FORMULAR mit <FORM> beginnen ###############
				?>
				
				<input type="text" 
						class="form-control form-control-lg ml-2" 
						placeholder="Suchbegriff" 
						aria-label="Suche" 
						aria-describedby="basic-addon1">
					<div class="input-group-append">
						<span class="btn btn-dark btn-lg" id="basic-addon2">Suche</span>
					</div>
			</div>
			<?php
			###### HIER FORMULAR mit <FORM> beginnen ###############
			?>



            <?php 
			# **********************************************************
			# ***               Tabelle ausgeben                     ***
			# **********************************************************
			?>
            <!-- Responsive Tabelle mit Padding 3 -->
			<!-- <div class="table-responsive pt-3 h6"> -->
            <div>
            <div class="table-responsive-md">
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Bank</th>
                            <th scope="col">Datum</th>
                            <th scope="col">Buchungstyp</th>
                            <th scope="col">Auftraggeber / Empfänger</th>
                            <th scope="col">Verwendungszweck</th>
                            <th scope="col">Betrag</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
					foreach($DBausgabe as $zeile) { 
						echo '<tr>';
							echo '<td scope="row">';
								echo $zeile['Bank'];
							echo '</td>';
                            echo '<td>'; 
								echo date('d.m.Y', strtotime($zeile['Buchungsdatum'])); 
							echo '</td>';
							echo '<td>';
								echo $zeile['Buchungstyp'];
							echo '</td>';
                            echo '<td>';
								echo $zeile['AuftraggeberEmpfaenger'];
							echo '</td>';
                            echo '<td>';
								echo $zeile['Verwendungszweck'];
							echo '</td>';
							echo '<td>';
								if ($zeile['Betrag'] > 0) $strFarbe = '#00B233'; // grüne Farbe
								if ($zeile['Betrag'] < 0) $strFarbe = '#FF0000'; // rote Farbe
								echo "<font color='" . $strFarbe . "'>" 
									 .number_format($zeile['Betrag'], 2, ",", ".")
									 . "</font>";
							echo '</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>
			</div>
	<?php
	# **********************************************************
	# ***                  JavaScript                        ***
	# **********************************************************
	?>
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" 
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" 
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" 
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" 
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" 
            crossorigin="anonymous"></script>
    </div>
</body>

</html>
