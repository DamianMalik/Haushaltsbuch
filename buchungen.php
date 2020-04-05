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
	if(isset($_POST['Bank'])) {
		$Bankid = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_POST['Bank']))) ;
	} else {
		$Bankid = "X";
	} // Ende der If-Abfrage



	# Auslesen der Filtervariable 'Zeitraum'
	if(isset($_GET['Zeitraum'])) {
		$Zeitraum = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_GET['Zeitraum']))) ;
	} else {
		$Zeitraum = "XX";
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
	
	
	# Filter für Datenbankabfrage vordefinieren
	if ($Bankid == '')   $Bankid = '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11'; 
	if ($Bankid == 'X')  $Bankid = '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11'; 
	if ($Zeitraum == '')   $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == 'XX') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == '5J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 5 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == '3J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == '2J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 2 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == '1J') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 1 YEAR), '%Y-%m-%d')"; 
	if ($Zeitraum == '6M') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 6 MONTH), '%Y-%m-%d')"; 
	if ($Zeitraum == '3M') $Startdatum = "DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')"; 
	
	
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
				WHERE a.`BankID` = b.`id`
				AND   a.`BankID` IN ( $Bankid )
				AND   a.`Buchungsdatum` >=  " . $Startdatum . "
				AND   ( a.`Verwendungszweck` LIKE '" 
						. $Suchbegriff 
						. "' OR    a.`AuftraggeberEmpfaenger` LIKE '" 
						. $Suchbegriff . "' )
				ORDER BY a.`Buchungsdatum` DESC, 
						a.`Datensatznummer` DESC, 
						a.`Datensatznummer` DESC; ";
	$DBausgabe = $pdo->query($DBabfrage);
	?>
	<?php 
	# **********************************************************
	# ***                     Ausgabe                        ***
	# **********************************************************
	?>
		<div class="container-fluid	">
            <h1 class="pt-3 pb-3">Buchungen</h1>
			<?php 
			# **********************************************************
			# ***               Filter-Buttons                       ***
			# **********************************************************
			?>
      
			<div class="btn-group btn-group-lg pb-3">
			<div class="btn-group btn-group-lg">
			<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">Konto</button>
			<div class="dropdown-menu">
			<a class="dropdown-item" href="#">Alle</a>
			<a class="dropdown-item" href="#">Konto A</a>
			<a class="dropdown-item" href="#">Konto B</a>
			</div>
			</div> 


			<div class="btn-group btn-group-lg">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Zeitraum
			</button>
			<div class="dropdown-menu">
			<a class="dropdown-item" href="#">3 Monate</a>
			<a class="dropdown-item" href="#">6 Monate</a>
			<a class="dropdown-item" href="#">2020</a>
			<a class="dropdown-item" href="#">2019</a>
			<a class="dropdown-item" href="#">2018</a>
			<a class="dropdown-item" href="#">2017</a>
			</div>
			</div>      
			
			
			<input type="text" class="form-control form-control-lg ml-2" placeholder="Suchbegriff" aria-label="Suche" aria-describedby="basic-addon1">
			<div class="input-group-append">
				<span class="btn btn-dark btn-lg" id="basic-addon2">Suche</span>
			</div>

			</div>

    

            <?php 
			# **********************************************************
			# ***               Tabelle ausgeben                     ***
			# **********************************************************
			?>
            <!-- Responsive Tabelle mit Padding 3 -->
            <div class="table-responsive pt-3 h5">
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
								if ($zeile['Betrag'] > 0) $farbe = '#00B233'; // grüne Farbe
								if ($zeile['Betrag'] < 0) $farbe = '#FF0000'; // rote Farbe
								echo "<font color='" . $farbe . "'>" 
									 .number_format($zeile['Betrag'], 2, ",", ".")
									 . "</font>";
							echo '</td>';
                        echo '</tr>';
                    
					}
					?>
                    </tbody>
                    
                </table>
            </div>


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
