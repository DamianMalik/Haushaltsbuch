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


	<title>Haushaltsbuch | CSV-Import</title>

	<!-- Google Fonts -->
	<!-- Font Type: Patrick Hand -->
	<link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet"> 

</head>
<body class="font-Bitter bg-light text-dark">

	<?php
	# **********************************************************
	# ***             Navigationsleiste                      ***
	# ********************************************************** 
	include 'navigation.php';

	# **********************************************************
	# ***        Parameter aus dem Feld $_FILES lesen        ***
	# ********************************************************** 

	# $filename  = $_FILES['CSVDatei']; 
	$Dateiname = $_FILES['CSVDatei']['name'];       // Dateiname (ohne Laufwerk/Pfad)
	$type      = $_FILES['CSVDatei']['type'];       // Dateityp, z.B. "image/gif"
	$size      = $_FILES['CSVDatei']['size'];       // Dateigröße in Byte
	$uploaderr = $_FILES['CSVDatei']['error'];      // Fehlernummer (0 = kein Fehler)
	$tmpfile   = $_FILES['CSVDatei']['tmp_name'];   // Name der lokalen, temporären Datei. Ist erforderlich für 
	
	$zeile = 1;
	$flagCSV_Quelle = "unbekannt";
	$strMusterTyp01 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Betrag;Währung;Saldo;Währung";
	$strMusterTyp02 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Saldo;Währung;Betrag;Währung";
	$strMusterTyp03 = '"Datum";Uhrzeit;Zeitzone;Name;Typ;Status;Währung;Brutto;Gebühr;Netto;Absender E-Mail-Adresse;Empfänger E-Mail-Adresse;Transaktionscode;Lieferadresse;Adress-Status;Artikelbezeichnung;Artikelnummer;Versand- und Bearbeitungsgebühr;Versicherungsbetrag;Umsatzsteuer;Option 1 Name;Option 1 Wert;Option 2 Name;Option 2 Wert;Zugehöriger Transaktionscode;Rechnungsnummer;Zollnummer;Anzahl;Empfangsnummer;Guthaben;Adresszeile 1;Adresszusatz;Ort;Bundesland;PLZ;Land;Telefon;Betreff;Hinweis;Ländervorwahl;Auswirkung auf Guthaben';
	$strMusterTyp03 = '\ufeff\"Datum\";Uhrzeit;Zeitzone;Name;Typ;Status;W\u00e4hrung;Brutto;Geb\u00fchr;Netto;Absender E-Mail-Adresse;Empf\u00e4nger E-Mail-Adresse;Transaktionscode;Lieferadresse;Adress-Status;Artikelbezeichnung;Artikelnummer;Versand- und Bearbeitungsgeb\u00fchr;Versicherungsbetrag;Umsatzsteuer;Option 1 Name;Option 1 Wert;Option 2 Name;Option 2 Wert;Zugeh\u00f6riger Transaktionscode;Rechnungsnummer;Zollnummer;Anzahl;Empfangsnummer;Guthaben;Adresszeile 1;Adresszusatz;Ort;Bundesland;PLZ;Land;Telefon;Betreff;Hinweis;L\u00e4ndervorwahl;Auswirkung auf Guthaben';
	
	
	# **********************************************************
	# ***                Prüfung CSV Bank                    ***
	# ********************************************************** 
	# Definiere Array
	$arrZeile = []; 
	$numFeldanzahl = 0;
	
	# Prüfung, ob die CSV von der Bank kommt
	if (($datei = fopen($tmpfile, "r")) !== FALSE) {
		while ( ($arrZeile = fgetcsv($datei, 0, ';')) !== FALSE) {
			# Die folgende Zeile beseitigt UTF-8 Probleme 
			$arrZeile = array_map("utf8_encode", $arrZeile); 
			
			# Zähle Felder in der Zeile
			$numFeldanzahl = count($arrZeile);
			# echo "<b>Feldanzahl:</b> " . $numFeldanzahl . "<br>";
			
			if ($numFeldanzahl == 9 ) {
				# Die Arrays-Elemente werden zu einem String zusammengesetzt
				$strZusammensetzung = implode(";",$arrZeile); 
				
				if ( $strZusammensetzung == $strMusterTyp01) {
					$flagCSV_Quelle = "ING v1";
					# Break beendet nicht das IF, sondern die While-Schleife
					break;
				}
				if ( $strZusammensetzung == $strMusterTyp02) {
					$flagCSV_Quelle = "ING v2";
					# Break beendet nicht das IF, sondern die While-Schleife
					break;
				}
			}
		} // Ende der While-Schleife
		
		# echo "Überprüfung Bank wurde beendet" . "<br><hr>";
	
    fclose($datei); // Datei wird geschlossen
	} // Ende der if-Bedingung

	# **********************************************************
	# ***                Prüfung CSV PayPal                  ***
	# ********************************************************** 
	
	if (($datei = fopen($tmpfile, "r")) !== FALSE) {
		while ( ($arrZeile = fgetcsv($datei, 0, ',', '"')) !== FALSE) {
			# Die folgende Zeile beseitigt UTF-8 Probleme (kommt hier nicht zur Anwendung!)
			# $arrZeile = array_map("utf8_encode", $arrZeile); 
			
			# Zähle Felder in der Zeile
			$numFeldanzahl = count($arrZeile);
			
			# Überprüfung ob die Felder 7=Brutto und 12=Transaktionscode lauten
			if ($numFeldanzahl == 41) {
				if ($arrZeile[7] == 'Brutto' AND $arrZeile[12] == 'Transaktionscode') {
					$flagCSV_Quelle = "PayPal";
					break;
				}
			}
		} // Ende der While-Schleife
		
		# echo "Überprüfung PayPal wurde beendet" . "<br><hr>";
		
    fclose($datei); // Datei wird geschlossen
	} // Ende der if-Bedingung
	
	# echo "<b>Es wurde folgende CSV-Quelle erkannt:</b> " . $flagCSV_Quelle . "<br>";
	# echo "<br><br>";
	
	
	# **********************************************************
	# ***                  Datenbankzugang                   ***
	# **********************************************************
	# Verbindungsaufbau zur Datenbank
	include 'datenbank.php';
	
	
	# **********************************************************
	# ***           Datenbanktabelle festlegen               ***
	# ********************************************************** 
	
	# $dbtabellenname = "Buchungen_PayPal";
	$dbtabellenname = "Buchungen";
	
	
	# **********************************************************
	# ***              Upload Nummer festlegen               ***
	# **********************************************************
	
	$DBabfrage = "SELECT `Uploadnummer`
				  FROM `" . $dbtabellenname . "` 
				  ORDER BY `Uploadnummer` DESC
				  LIMIT 1;";
	$DBausgabe = $pdo->query($DBabfrage);

	foreach($DBausgabe as $zeile) {
			$Uploadnummer_ausDB = $zeile['Uploadnummer'];
		} // Ende der foreach-Schleife 

	# Uploadnummer festlegen
	# Falls die Datenbankabfrage leer ist: 
	if (empty($Uploadnummer_ausDB)) { 
				$Uploadnummer_ausDB = 999; 
				}
	# Die neue Uploadnummer um +1 erhöhen
	$Uploadnummer_neu = $Uploadnummer_ausDB + 1;
				
	unset($DBabfrage); 
	unset($DBausgabe); 
	unset($zeile); 

	
	
	# **********************************************************
	# ***                                                    ***
	# ***                     Ausgabe                        ***
	# ***                                                    ***
	# **********************************************************
	?>
	<!-- <div class="container-fluid"> -->
	<div class="container">
		<!-- <div class="pt-3"> -->
		<h1 class="pt-3 pb-4">Prüfung CSV-Datei</h1>
		<div class="card-deck mb-5 font-Bitter">
		
			<!-- Karte 1 - Uploadinformationen -->
			<div class="card" style="width: 18rem;">
				<div class="card-header h4 text-white bg-dark">Uploadinformationen</div>
				<ul class="list-group list-group-flush bg-light">
				<?php
					echo '<li class="list-group-item">';
						echo '<span class="badge badge-secondary">Dateiname</span>' . '<br>'; 
						echo $Dateiname;
					echo '</li>';
					echo '<li class="list-group-item">';
							echo '<span class="badge badge-secondary">Temporäre Datei</span>' . '<br>'; 
							echo $tmpfile;
						echo '</li>';
					echo '<li class="list-group-item">';
							echo '<span class="badge badge-secondary">Größe</span>' . '<br>'; 
							echo  number_format($size, 0, ',', '.') 
								. " Byte (" 
								. number_format(round($size / 1024, 2), 0, ',', '.')
								. " kB)";
						echo '</li>';
					echo '<li class="list-group-item">';
						echo '<span class="badge badge-secondary">letzte Upload Nummer</span>' . '<br>'; 
						if ($Uploadnummer_ausDB == '999') {
							echo "noch kein Upload durchgeführt." . "<br>";
						} else {
							echo $Uploadnummer_ausDB . "<br>";
						}
					echo '</li>';
					echo '<li class="list-group-item">';
							echo '<span class="badge badge-secondary">neue Upload Nummer</span>' . '<br>'; 
							echo $Uploadnummer_neu . "<br>";
						echo '</li>';
					echo '<li class="list-group-item">';
							if ( $type == "text/csv") {
								echo '<span class="badge badge-success">Dateityp</span>' . '<br>'; 
							} else {
								echo '<span class="badge badge-danger">Dateityp</span>' . '<br>'; 
							}
							echo $type;
						echo '</li>';
					
					echo '<li class="list-group-item">';
							if ( $uploaderr == 0 ) {
								echo '<span class="badge badge-success">Fehler</span>' . '<br>'; 
							} else {
								echo '<span class="badge badge-danger">Fehler</span>' . '<br>'; 
							}
							echo $uploaderr;
						echo '</li>';
				?>
				</ul>
			</div>
			<!-- Karte 2 - Kontoinformationen -->
			<div class="card" style="width: 18rem;">
				<div class="card-header h4 text-white bg-dark">Kontoinformationen</div>
				<ul class="list-group list-group-flush bg-light">
				<?php
					echo '<li class="list-group-item">';
						if ($flagCSV_Quelle == 'unbekannt') {
							echo '<span class="badge badge-danger">CSV-Quelle</span>' . '<br>'; 
							echo $flagCSV_Quelle;
						} else {
							echo '<span class="badge badge-info">CSV-Quelle</span>' . '<br>'; 
							echo $flagCSV_Quelle;
						}
						echo '</li>';
					echo '<li class="list-group-item">';
							# echo "<small><b>Konto ID</b></small><br>";
							echo '<span class="badge badge-danger">Konto</span>' . '<br>'; 
							echo "1";
						echo '</li>';
					echo '<li class="list-group-item">';
							# echo "<small><b>Inhaber</b></small><br>";
							echo '<span class="badge badge-danger">Inhaber</span>' . '<br>'; 
							echo "XYZ";
						echo '</li>';
						
					echo '<li class="list-group-item">';
							# echo "<small><b>Bank</b></small><br>";
							echo '<span class="badge badge-danger">Bank</span>' . '<br>'; 
							echo "xXXX";
						echo '</li>';
						
					
					echo '<li class="list-group-item">';
							# echo "<small><b>IBA</b></small><br>";
							echo '<span class="badge badge-danger">IBA</span>' . '<br>'; 
							echo "XYd484303487464";
						echo '</li>';
					
					echo '<li class="list-group-item">';
							# echo "<small><b>K....Nummer</b></small><br>";
							echo '<span class="badge badge-danger">K...nummer</span>' . '<br>'; 
							echo "433632";
						echo '</li>';
					
					echo '<li class="list-group-item">';
						# echo "<small><b>K...</b></small><br>";
						echo '<span class="badge badge-danger">G...-Nummer</span>' . '<br>'; 
						echo "G...Konto";
					echo '</li>';
					echo '<li class="list-group-item">';
						# echo "<small><b>L...</b></small><br>";
						echo '<span class="badge badge-danger">L-Nummer</span>' . '<br>'; 
						echo "L.....";
					echo '</li>';
				?>
				</ul>
			</div>
			
		</div>
	
	

<?php
########################################################################################
?>




	
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>
