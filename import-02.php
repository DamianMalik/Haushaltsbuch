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
# <!-- Navigationsleiste -->
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
$flagCSV_Quelle = "undefiniert";
$strMusterTyp01 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Betrag;Währung;Saldo;Währung";
$strMusterTyp02 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Saldo;Währung;Betrag;Währung";
$strMusterTyp03 = '"Datum";Uhrzeit;Zeitzone;Name;Typ;Status;Währung;Brutto;Gebühr;Netto;Absender E-Mail-Adresse;Empfänger E-Mail-Adresse;Transaktionscode;Lieferadresse;Adress-Status;Artikelbezeichnung;Artikelnummer;Versand- und Bearbeitungsgebühr;Versicherungsbetrag;Umsatzsteuer;Option 1 Name;Option 1 Wert;Option 2 Name;Option 2 Wert;Zugehöriger Transaktionscode;Rechnungsnummer;Zollnummer;Anzahl;Empfangsnummer;Guthaben;Adresszeile 1;Adresszusatz;Ort;Bundesland;PLZ;Land;Telefon;Betreff;Hinweis;Ländervorwahl;Auswirkung auf Guthaben';
$strMusterTyp03 = '\ufeff\"Datum\";Uhrzeit;Zeitzone;Name;Typ;Status;W\u00e4hrung;Brutto;Geb\u00fchr;Netto;Absender E-Mail-Adresse;Empf\u00e4nger E-Mail-Adresse;Transaktionscode;Lieferadresse;Adress-Status;Artikelbezeichnung;Artikelnummer;Versand- und Bearbeitungsgeb\u00fchr;Versicherungsbetrag;Umsatzsteuer;Option 1 Name;Option 1 Wert;Option 2 Name;Option 2 Wert;Zugeh\u00f6riger Transaktionscode;Rechnungsnummer;Zollnummer;Anzahl;Empfangsnummer;Guthaben;Adresszeile 1;Adresszusatz;Ort;Bundesland;PLZ;Land;Telefon;Betreff;Hinweis;L\u00e4ndervorwahl;Auswirkung auf Guthaben';
?>

<!-- <div class="container-fluid"> -->
<div class="container">
	<h3 class="h1 pt-3 pb-3">Dateiupload</h3>
	<p class="h3 font-weight-bold">Upload Informationen:</p>
	<?php
	# **********************************************************
	# ***           Dateieigenschaften anzeigen              ***
	# ********************************************************** 
	
	echo "Dateiname: " . $Dateiname . "<br>";
	echo "Dateityp.: " . $type . "<br>";
	echo "Größe....: " . $size . "Byte (" . round($size / 1024, 2) . " kB)<br>";
	echo "Fehler...: " . $uploaderr . "<br>";
	echo "Temp.Dat.: " . $tmpfile . "<br>";
	echo "<br><hr><br>";

	# Definiere Array
	$arrZeile = []; 
	$arrZeile2 = []; 
	$numFeldanzahl = 0;

	echo "<h2>CSV Erkennung</h2>";
	
	# **********************************************************
	# ***                Prüfung CSV Bank                    ***
	# ********************************************************** 
	# Prüfung, ob die CSV von der Bank kommt
	if (($datei = fopen($tmpfile, "r")) !== FALSE) {
		echo "Überprüfung auf CSV von der Bank" . "<br>";
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
		
		echo "Überprüfung Bank wurde beendet" . "<br>";
		echo "<hr>"; 
    fclose($datei); // Datei wird geschlossen
	} // Ende der if-Bedingung

	# **********************************************************
	# ***                      PayPal                        ***
	# ********************************************************** 
	// BOM as a string for comparison.
	$bom = "\xef\xbb\xbf";
	
	if (($datei = fopen($tmpfile, "r")) !== FALSE) {
		
		echo "Überprüfung auf CSV von PayPal" . "<br>";
		while ( ($arrZeile = fgetcsv($datei, 0, ',', '"')) !== FALSE) {
			# Die folgende Zeile beseitigt UTF-8 Probleme (kommt hier nicht zur Anwendung!)
			# $arrZeile = array_map("utf8_encode", $arrZeile); 
			
			$numFeldanzahl = count($arrZeile);
			
			if ($numFeldanzahl == 41 ) {
				$flagCSV_Quelle = "PayPal";
				break;
			}
		} // Ende der While-Schleife
		
		echo "Überprüfung PayPal wurde beendet" . "<br>";
		echo "<hr>"; 
    fclose($datei); // Datei wird geschlossen
	} // Ende der if-Bedingung
	
	echo "<b>Es wurde folgende CSV-Quelle erkannt:</b> " . $flagCSV_Quelle . "<br>";
	echo "<br><br>";
	
	
	######################################################
		
	
	
	
	
	
	
	
	



	?>





	<p class="h3 font-weight-bold">Kontoinformationen:</p>
	<div class="table-responsive">

		<table class="table table-striped">

			<tbody>
				<tr>
					<td style="width: 50%">letzte Upload Nummer:</td>
					<td>1102</td>
				</tr>
				<tr>
					<td>neue Upload Nummer:</td>
					<td>1103</td>
				</tr>
				<tr>
					<td>Konto id:</td>
					<td>1</td>
				</tr>
				<tr>
					<td>Inhaber:</td>
					<td>XYZ</td>
				</tr>
				<tr>
					<td>Bank:</td>
					<td>xXXX</td>
				</tr>
				<tr>
					<td>IBA</td>
					<td>XYd484303487464</td>
				</tr>
				<tr>
					<td>K....Nummer</td>
					<td>433632</td>
				</tr>
				<tr>
					<td>K....</td>
					<td>G....kto</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="border-top my-3"></div>

	<p class="h3 font-weight-bold">Uploadinformationen:</p>

	<div class="table-responsive">
		<table class="table table-striped">

			<tbody>
				<tr>
					<td style="width: 50%">Dateiname:</td>
					<td>skuxjxue.csv</td>
				</tr>
				<tr>
					<td>Dateityp:</td>
					<td>text/csv</td>
				</tr>
				<tr>
					<td>Größe:</td>
					<td>7623Byte (7,44 kB)</td>
				</tr>
				<tr>
					<td>Fehler:</td>
					<td>0</td>
				</tr>
				<tr>
					<td>Temporäre Datei:</td>
					<td>/tmp/phpB5qnQb</td>
				</tr>

			</tbody>
		</table>

	</div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>
