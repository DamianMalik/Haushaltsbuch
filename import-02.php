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
	<link href="https://fonts.googleapis.com/css?family=Bitter" 
	      rel="stylesheet"> 

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
	
	# Überprüfung, ob eine Datei hochgeladen wurde und Ermittlung
	# der Dateiparameter. Andernfalls Fehlerausgabe. 
	if (isset($_FILES['CSVDatei']) && $_FILES['CSVDatei']['tmp_name'] != ''){
		if(is_uploaded_file($_FILES['CSVDatei']['tmp_name'])){  
			$Dateiname   = $_FILES['CSVDatei']['name'];     // Dateiname (ohne Laufwerk/Pfad)
			$strDateityp = $_FILES['CSVDatei']['type'];     // Dateityp, z.B. "image/gif"
			$size        = $_FILES['CSVDatei']['size'];     // Dateigröße in Byte
			$uploaderr   = $_FILES['CSVDatei']['error'];    // Fehlernummer (0 = kein Fehler)
			$tmpfile     = $_FILES['CSVDatei']['tmp_name']; // Name der lokalen, temporären Datei. Ist erforderlich für 
		}
	} else {
		echo '<div class="container-fluid">'; 
			echo '<span class="badge badge-warning">Fehler</span> '; 
			echo 'Es wurde keine Datei hochgeladen';
		echo '</div>'; 
		exit;
	}
	
	# **********************************************************
	# ***              Definition Variablen                  ***
	# ********************************************************** 
	
	$zeile = 1;
	$strCSV_Quelle = 'unbekannt';
	$strBank_ausCSV = 'unbekannt';
	$strInhaber_ausCSV = 'unbekannt';
	$strKontonummer_ausCSV = 'unbekannt'; 
	$strKontotyp_ausCSV = 'unbekannt';
	$intID_ausDB = -8432;
	$arrCSVPruefung = array(); // Definition Array // 1=OK; 9=Fehler
	$arrCSVPruefung[2][2] = 9; // Wird auf Fehler gesetzt
	$strMusterTyp01 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Betrag;Währung;Saldo;Währung";
	$strMusterTyp02 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Verwendungszweck;Saldo;Währung;Betrag;Währung";
	$strMusterTyp03 = "Buchung;Valuta;Auftraggeber/Empfänger;Buchungstext;Kategorie;Verwendungszweck;Saldo;Währung;Betrag;Währung";
	
	
	# Definiere Array
	$arrZeile =  array();
	$arrCSVDaten = array();
	$numFeldanzahl = 0;
	
	# **********************************************************
	# ***                Prüfung CSV Bank                    ***
	# ********************************************************** 
	
	# Prüfung, ob die CSV von der Bank kommt
	if (($datei = fopen($tmpfile, "r")) !== FALSE) {
		while ( ($arrZeile = fgetcsv($datei, 0, ';')) !== FALSE) {
			# Die folgende Zeile beseitigt UTF-8 Probleme 
			$arrZeile = array_map("utf8_encode", $arrZeile); 
			
			# Zähle Felder in der Zeile
			$numFeldanzahl = count($arrZeile);
			
			
			if ($numFeldanzahl == 2 ) {
				if ($arrZeile[0] == 'Konto') {
					# Text 'Girokonto: ' entfernen
					$strKontonummer_ausCSV = str_replace('Girokonto: ', '', $arrZeile[1]);
					$strKontonummer_ausCSV = str_replace('Extra-Konto: ', '', $strKontonummer_ausCSV);
					
					# Ermittlung Kontotyp
					$strKontotyp_ausCSV = preg_replace('/[0-9: ]+/', '', $arrZeile[1]);
					if ($strKontotyp_ausCSV == 'DE' ) $strKontotyp_ausCSV = 'unbekannt'; 
					
					# Festlegung CSV-Quelle
					$strCSV_Quelle = 'ING v1';
					$strBank_ausCSV = 'ING-DiBa'; 
				}
				if ($arrZeile[0] == 'IBAN') {
					# Ersetze Leerzeichen
					$strKontonummer_ausCSV = str_replace(' ', '', $arrZeile[1]);
					
					# Festlegung CSV-Quelle
					$strCSV_Quelle = "ING v2";
					$strBank_ausCSV = 'ING-DiBa'; 
				}
				if ($arrZeile[0] == 'Kunde') $strInhaber_ausCSV = $arrZeile[1];
				if ($arrZeile[0] == 'Kontoname') $strKontotyp_ausCSV = $arrZeile[1];
			}
			
			if ($numFeldanzahl == 9 || $numFeldanzahl == 10 ) {
				# Die Arrays-Elemente werden zu einem String zusammengesetzt
				$strZusammensetzung = implode(";",$arrZeile); 
				
				if ( $strZusammensetzung == $strMusterTyp01) {
					$strCSV_Quelle = "ING v1";
					$arrCSVPruefung[2][2] = 1; // CSV wird als Valide deklariert
					# Break beendet nicht das IF, sondern die While-Schleife
					# break;
				}
				if ( $strZusammensetzung == $strMusterTyp02) {
					$strCSV_Quelle = "ING v2";
					$arrCSVPruefung[2][2] = 1; // CSV wird als Valide deklariert
					# Break beendet nicht das IF, sondern die While-Schleife
					# break;
				}
				
				if ( $strZusammensetzung == $strMusterTyp03) {
					$strCSV_Quelle = "ING v3";
					$arrCSVPruefung[2][2] = 1; // CSV wird als Valide deklariert
					# Break beendet nicht das IF, sondern die While-Schleife
					# break;
				}
				
				# Datenzeilen werden in das Array `arrCSVDaten` übernommen.
				# Ausnahme: Überschrift-Zeilen werden übersprungen 
				if ( $strZusammensetzung != $strMusterTyp01 
				     AND $strZusammensetzung != $strMusterTyp02 
				     AND $strZusammensetzung != $strMusterTyp03) {
					$arrCSVDaten[] = $arrZeile;
				}
			}
		} // Ende der While-Schleife
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
			# Falls ja, dann handelt es sich um eine CSV von PayPal 
			if ($numFeldanzahl == 41) {
				if ($arrZeile[7] == 'Brutto' AND $arrZeile[12] == 'Transaktionscode') {
					$strCSV_Quelle = 'PayPal';
					$strBank_ausCSV = 'PayPal'; 
					# break;
				}
				# Ermittlung von Konto und Inhaber ("Kunde")
				if ( intval($arrZeile[7]) < '0' ) {
						$strKontonummer_ausCSV = $arrZeile[10];
						# $strBank_ausCSV = 'PayPal';
						$strInhaber_ausCSV = $arrZeile[10]; 
						$strKontotyp_ausCSV = 'Privat'; 
				}
				# Überprüfung des Inhalts: ist die CSV inhaltlich valide?
				if ( $arrZeile[40] == 'Soll' || $arrZeile[40] == 'Haben') {
					# $intImportStatus = 1;
					$arrCSVPruefung[2][2] = 1; // CSV wird als Valide deklariert
				} /* else {
					# $intImportStatus = 9;
					# $strImportStatus = 'CSV Daten nicht valide oder CSV-Datei enthält keine Daten';
				}
				*/
			}
		} // Ende der While-Schleife
		
		# echo "Überprüfung PayPal wurde beendet" . "<br><hr>";
	fclose($datei); // Datei wird geschlossen
	} // Ende der if-Bedingung
	
	
	# **********************************************************
	# ***                  Datenbankzugang                   ***
	# **********************************************************
	# Verbindungsaufbau zur Datenbank
	include 'datenbank.php';
	
	
	# **********************************************************
	# ***          Kontoinformatonen aus DB ermitteln        ***
	# ********************************************************** 
	
	$DBabfrage = "SELECT `id`, `Inhaber`, `Bankname`, 
	                     `Kontonummer`, `Kontoname`  
	              FROM `Banken` 
	              WHERE `Kontonummer` LIKE '%" . $strKontonummer_ausCSV . "%' ;";
	$DBausgabe = $pdo->query($DBabfrage);
	
	
	foreach($DBausgabe as $datensatz) {
			$intID_ausDB          = $datensatz['id'];
			$strInhaber_ausDB     = $datensatz['Inhaber'];
			$strBank_ausDB        = $datensatz['Bankname'];
			$strKontonummer_ausDB = $datensatz['Kontonummer'];
			$strKontoname_ausDB   = $datensatz['Kontoname'];
		} // Ende der foreach-Schleife 
	
	# Falls die Datenbankabfrage leer ist: 
	if (empty($strKontonummer_ausDB)) { 
				$intID_ausDB = '-1';
	}
	
	unset($DBabfrage); 
	unset($DBausgabe); 
	unset($zeile); 
	
	
	
	# **********************************************************
	# ***              Upload Nummer festlegen               ***
	# **********************************************************
	
	$DBabfrage = "SELECT `Uploadnummer`
				  FROM   `Buchungen` 
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
		
			<!-- Karte 1 - CSV Technische Prüfung -->
			<div class="card" style="width: 18rem;">
				<div class="card-header h4 text-white bg-dark">CSV Technische Prüfung</div>
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
								. ' Byte (' 
								. number_format(round($size / 1024, 2), 0, ',', '.')
								. ' kB)';
						echo '</li>';
					echo '<li class="list-group-item">';
							if ( $strDateityp == 'text/csv') {
								echo '<span class="badge badge-success">Dateityp</span>' . '<br>'; 
							} else {
								echo '<span class="badge badge-danger">Dateityp</span>' . '<br>'; 
							}
							echo $strDateityp;
						echo '</li>';
					echo '<li class="list-group-item">';
							if ( $uploaderr == 0 ) {
								echo '<span class="badge badge-success">Fehler</span>' . '<br>'; 
							} else {
								echo '<span class="badge badge-danger">Fehler</span>' . '<br>'; 
							}
							echo $uploaderr;
						echo '</li>';
					echo '<li class="list-group-item">';
							if ( $strDateityp == 'text/csv' AND $uploaderr == 0 ) {
								echo '<div class="alert alert-success" role="alert">Technische Prüfung in Ordnung</div>';
								$arrCSVPruefung[1][0] = 1; // 1=OK; 9=Fehler
							} else {
								echo '<div class="alert alert-danger" role="alert">Technische Prüfung fehlerhaft</div>';
								$arrCSVPruefung[1][0] = 9; // 1=OK; 9=Fehler
							}
						echo '</li>';
				?>
				</ul>
			</div>
			<!-- Karte 2 - CSV Inhaltliche Prüfung -->
			<div class="card" style="width: 18rem;">
				<div class="card-header h4 text-white bg-dark">CSV Inhaltliche Prüfung</div>
				<ul class="list-group list-group-flush bg-light">
					<?php
					echo '<li class="list-group-item">';
						if ($strCSV_Quelle == 'unbekannt')  {
							echo '<span class="badge badge-danger">CSV-Quelle</span>' . '<br>'; 
							echo $strCSV_Quelle . "<br>"; 
							$arrCSVPruefung[2][1] = 9; // 1=OK; 9=Fehler
						} else {
							echo '<span class="badge badge-success">CSV-Quelle</span>' 
								.'<br>'; 
							echo $strBank_ausCSV . ' (' . $strCSV_Quelle . ")" . "<br>";
							$arrCSVPruefung[2][1] = 1; // 1=OK; 9=Fehler
						}
						echo '</li>';
					echo '<li class="list-group-item">';
						if ($arrCSVPruefung[2][2] != 9)  {
							echo '<span class="badge badge-success">CSV-Inhalt</span>' . '<br>'; 
							echo "CSV Daten valide" . "<br>";
						} else {
							echo '<span class="badge badge-danger">CSV-Inhalt</span>' .'<br>'; 
							echo "CSV Daten nicht valide oder keine Daten in CSV vorhanden" . "<br>";
						}
						echo '</li>';
					echo '<li class="list-group-item">';
						if ( $strInhaber_ausCSV != 'unbekannt') {
							echo '<span class="badge badge-success">Kontoinhaber</span>' . '<br>'; 
							$arrCSVPruefung[2][3] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-danger">Kontoinhaber</span>' . '<br>'; 
							$arrCSVPruefung[2][3] = 9; // 1=OK; 9=Fehler
						}
							echo $strInhaber_ausCSV . '<br>';
						echo '</li>';
					echo '<li class="list-group-item">';
						if ( $strKontonummer_ausCSV != 'unbekannt' ) {
							echo '<span class="badge badge-success">Kontonummer</span>' . '<br>'; 
							$arrCSVPruefung[2][4] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-danger">Kontonummer</span>' . '<br>'; 
							$arrCSVPruefung[2][4] = 9; // 1=OK; 9=Fehler
						}
						echo $strKontonummer_ausCSV . '<br>';
					echo '</li>';
					echo '<li class="list-group-item">';
						if ( $strKontotyp_ausCSV != 'unbekannt' ) {
							echo '<span class="badge badge-success">Kontotyp</span>' . '<br>'; 
							$arrCSVPruefung[2][5] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-warning">Kontotyp</span>' . '<br>'; 
							$arrCSVPruefung[2][5] = 2; // 1=OK; 2=Tolerabel
						}
						echo $strKontotyp_ausCSV . '<br>';
						echo '</li>';
					
					
					echo '<li class="list-group-item">';
							if (   $arrCSVPruefung[2][1] == 9 
								OR $arrCSVPruefung[2][2] == 9 
								OR $arrCSVPruefung[2][3] == 9 
								OR $arrCSVPruefung[2][4] == 9 
								OR $arrCSVPruefung[2][5] == 9 ) {
								echo '<div class="alert alert-danger" role="alert">Inhaltliche Prüfung fehlerhaft</div>';
								$arrCSVPruefung[2][0] = 9; // 1=OK; 9=Fehler
							} else {
								echo '<div class="alert alert-success" role="alert">Inhaltliche Prüfung in Ordnung</div>';
								$arrCSVPruefung[2][0] = 1; // 1=OK; 9=Fehler
							}
						echo '</li>';
				?>
				</ul>
			</div> 
			<!-- Karte 3 - Datenbank-Prüfung -->
			<div class="card" style="width: 18rem;">
				<div class="card-header h4 text-white bg-dark">Prüfung Datenbank</div>
				<ul class="list-group list-group-flush bg-light">
				<?php
					echo '<li class="list-group-item">';
						if ( $intID_ausDB != -1 ) {
							echo '<span class="badge badge-success">Bank-Name aus DB</span>' . '<br>'; 
							echo $strBank_ausDB . '<br>';
							$arrCSVPruefung[3][1] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-warning">Bank-Name</span>' . '<br>'; 
							echo 'Bank wird neu angelegt.';
							$arrCSVPruefung[3][1] = 2; // 1=OK; 2=Tolerabel
						}
						echo '</li>';
					
					echo '<li class="list-group-item">';
						if ( $intID_ausDB != -1 ) {
							echo '<span class="badge badge-success">Bank-ID</span>' . '<br>'; 
							echo $intID_ausDB . '<br>';
							$arrCSVPruefung[3][2] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-warning">Datenbank-ID</span>' . '<br>'; 
							echo 'ID wird neu angelegt.';
							$arrCSVPruefung[3][2] = 2; // 1=OK; 2=Tolerabel
						}
						echo '</li>';
					
					echo '<li class="list-group-item">';
						if ( $intID_ausDB != -1 ) {
							echo '<span class="badge badge-success">Kontoinhaber aus DB</span>' . '<br>'; 
							echo $strInhaber_ausDB . '<br>';
							$arrCSVPruefung[3][3] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-danger">Kontoinhaber</span>' . '<br>'; 
							echo 'Kontoinhaber wird neu angelegt.';
							$arrCSVPruefung[3][3] = 9; // 1=OK; 9=Fehler
						}
						echo '</li>';
					echo '<li class="list-group-item">';
						if ( $intID_ausDB != -1 ) {
							echo '<span class="badge badge-success">Kontonummer aus DB</span>' . '<br>'; 
							echo $strKontonummer_ausDB . '<br>';
							$arrCSVPruefung[3][4] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-danger">Kontonummer</span>' . '<br>'; 
							echo 'Kontonummer wird neu angelegt.';
							$arrCSVPruefung[3][4] = 9; // 1=OK; 9=Fehler
						}
						echo '</li>';
					
					echo '<li class="list-group-item">';
						if ( $intID_ausDB != -1 ) {
							echo '<span class="badge badge-success">Kontoname aus DB</span>' . '<br>'; 
							echo $strKontoname_ausDB . '<br>';
							$arrCSVPruefung[3][5] = 1; // 1=OK; 2=Tolerabel
						} else {
							echo '<span class="badge badge-warning">Kontoname</span>' . '<br>'; 
							echo 'Kontoname wird neu angelegt.';
							$arrCSVPruefung[3][5] = 2; // 1=OK; 2=Tolerabel
						}
						echo '</li>';
					
					echo '<li class="list-group-item">';
							echo '<span class="badge badge-secondary">Upload Nummer</span>' . '<br>'; 
							if ($Uploadnummer_ausDB == '999') {
								echo '1000' . '<br>';
							} else {
								echo $Uploadnummer_neu;
							}
						echo '</li>';
					
					
					echo '<li class="list-group-item">';
							if (   $arrCSVPruefung[3][1] == 9 OR $arrCSVPruefung[3][2] == 9 OR $arrCSVPruefung[3][3] == 9 
							    OR $arrCSVPruefung[3][4] == 9 OR $arrCSVPruefung[3][5] == 9) {
								echo '<div class="alert alert-danger" role="alert">Konto wird nicht in Datenbank angelegt.</div>';
								$arrCSVPruefung[3][0] = 9; // 1=OK; 9=Fehler
							} elseif (   $arrCSVPruefung[3][1] == 2 OR $arrCSVPruefung[3][2] == 2 OR $arrCSVPruefung[3][3] == 2 
							    OR $arrCSVPruefung[3][4] == 2 OR $arrCSVPruefung[3][5] == 2) {
								echo '<div class="alert alert-warning" role="alert">Konto wird neu in Datenbank angelegt.</div>';
								$arrCSVPruefung[3][0] = 2; // 1=OK; 2=Tolerabel
							} else {
								echo '<div class="alert alert-success" role="alert">Prüfung Datenbank in Ordnung</div>';
								$arrCSVPruefung[3][0] = 1; // 1=OK; 2=Tolerabel
							}
						echo '</li>';
						
				?>
				</ul>
			</div> 
			
			
		</div>
		
		<?php
		# Array ggf. sortieren
		# sort($arrCSVPruefung, SORT_ASC);
		# echo "<pre>\n"; var_dump($arrCSVPruefung); echo "</pre>\n";
		
		
		# Prüfung auf Freigabe Import
		if ( $arrCSVPruefung[1][0] == 1 AND $arrCSVPruefung[2][0] == 1 AND $arrCSVPruefung[3][0] == 1 ) {
			echo '<div class="alert alert-success" role="alert">';
			echo '<h4 class="alert-heading">Datenbank-Import</h4>';
			echo '<p>CSV-Datei in Datenbank importiert.</p>';
			echo '</div>';
		} else { 
			echo '<div class="alert alert-danger" role="alert">';
			echo '<h4 class="alert-heading">Keine Import-Freigabe</h4>';
			echo '<p>Die CSV-Prüfung hat Fehler ergeben. Ein Datenimport ist nicht möglich.</p>';
			echo '</div>';
		}
		
		# **********************************************************
		# ***                                                    ***
		# ***                  Datenbereinigung                  ***
		# ***                                                    ***
		# **********************************************************
		
		
		# **********************************************************
		# ***              Sortierung anpassen                  ***
		# ********************************************************** 
		# die Datensätzte von ING-DiBa liegen in 
		# absteigender Reihenfolge vor. Sortierung wird umgekehrt. 
		/*
		$arrCSVDaten_DESC = array(); 
		if ( $strBank_ausCSV == "ING-DiBa" ) {
			$arrCSVDaten_DESC = array_reverse($arrCSVDaten);
		}
		*/
		
		# Die Nachbearbeitung wird nun direkt beim DatenbankInsert vorgenommen
		/*
		if ( $strBank_ausCSV == "ING-DiBa" ) {
			echo "<h1>arrCSVDaten</h1>";
			# echo "<pre>\n"; var_dump($arrCSVDaten); echo "</pre>\n";
			# NACHBEARBEITUNG aller ELEMENTE
			
			
			foreach ( array_reverse($arrCSVDaten) as $arrCSVZeile ) {
			# foreach ( $arrCSVDaten as $arrCSVZeile ) {
				# **********************************************************
				# ***              Datumfelder anpassen                  ***
				# ********************************************************** 
				# Bereinigungen für den Datenbankimport 
				# Die beiden Datums-Felder 'Buchungsdatum' und 'Valuta' 
				# müssen Datenbankkonform sein. Hiermit werden sie 
				# in das Datenbank-Format geändert
				$arrCSVZeile[0] = date('Y-m-d', strtotime($arrCSVZeile[0])); // Buchungsdatum 
				$arrCSVZeile[1] = date('Y-m-d', strtotime($arrCSVZeile[1])); // Valuta
				
				
				# **********************************************************
				# ***         Doppelte Leerzeichen entfernen             ***
				# ********************************************************** 
				# Gelegentlich kommt es vor, dass ein und dieselbe 
				# Buchung in zwei verschiedenen CSV-Dateien unterschiedlich 
				# ist, weil in einer der beiden Dateien (aus unbekannten 
				# Gründen) zusätzliche Leerzeichen enthalten sind. Dadurch 
				# wird diese Buchung als zwei verschiedene Buchungen 
				# interpretiert und fälschlicherweise in die Datenbank 
				# importiert. Die folgenden Zeilen filtern unnötige 
				# Leerzeichen heraus. Dadurch werden diese Buchungen 
				# korrigiert.  
				// Feld 'Auftraggeber/Empfänger' bereinigen
				$arrCSVZeile[2] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[2]) ); 
				// Feld 'Buchungstyp' bereinigen
				$arrCSVZeile[3] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[3]) );
				// Feld 'Verwendungszweck' bereinigen
				$arrCSVZeile[4] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[4]) ); 
				
				# Überflüssige Leerzeichen am Zeilenanfang und 
				# Zeilenende entfernen
				$arrCSVZeile[2] = trim($arrCSVZeile[2]);
				$arrCSVZeile[4] = trim($arrCSVZeile[4]);
				
				
				# **********************************************************
				# ***         Komma gegen Punkt austauschen              ***
				# ********************************************************** 
				# Die Zahlenfelder: Komma gegen Punkt austauschen und 
				# den 1000-er Punkt rausnehmen. Felder `Betrag` und `Saldo`
				$arrCSVZeile[5] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[5]));
				$arrCSVZeile[7] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[7]));
				
				foreach ($arrCSVZeile as $abc) {
					echo "<b>Zelle</b>: " . $abc . "<br>";
				}
				echo "<hr>";
			}
		}
		*/
		
		# **********************************************************
		# ***                                                    ***
		# ***                  Datenbankinsert                   ***
		# ***                                                    ***
		# **********************************************************
		
		$Datensatznummer = 1000;
		
		if ( $strBank_ausCSV == "ING-DiBa" ) {
			foreach ( array_reverse($arrCSVDaten) as $arrCSVZeile ) {
				$Datenbankinsert  = "INSERT INTO `Buchungen` "
							   ."(`Uploadnummer`, `Datensatznummer`, 
								  `BankID`, `Buchungsdatum`, `Valuta`, 
								  `AuftraggeberEmpfaenger`, `Buchungstyp`, 
								  `Verwendungszweck`, `Betrag`, 
								  `Waehrung`, `Saldo`, `CSV_Quelle`) "
							   ."VALUES "
							   ."(" 
							   .":Uploadnummer, :Datensatznummer, :BankID, "
							   .":Buchungsdatum, :Valuta, "
							   .":AuftraggeberEmpfaenger, "
							   .":Buchungstyp, :Verwendungszweck, :Betrag, "
							   .":Waehrung, :Saldo, :CSV_Quelle "
							   .") "
							   ."ON DUPLICATE KEY UPDATE "
							   ."Uploadnummer           = VALUES(Uploadnummer), "
							   ."Datensatznummer        = VALUES(Datensatznummer), "
							   ."BankID                 = VALUES(BankID), "
							   ."Buchungsdatum          = VALUES(Buchungsdatum), "
							   ."Valuta                 = VALUES(Valuta), "
							   ."AuftraggeberEmpfaenger = VALUES(AuftraggeberEmpfaenger), "
							   ."Verwendungszweck       = VALUES(Verwendungszweck), "
							   ."Betrag                 = VALUES(Betrag), "
							   ."Saldo                  = VALUES(Saldo), "
							   ."CSV_Quelle             = VALUES(CSV_Quelle); ";
				
				
				# **********************************************************
				# ***              Datumfelder anpassen                  ***
				# ********************************************************** 
				# Bereinigungen für den Datenbankimport 
				# Die beiden Datums-Felder 'Buchungsdatum' und 'Valuta' 
				# müssen Datenbankkonform sein. Hiermit werden sie 
				# in das Datenbank-Format geändert
				$arrCSVZeile[0] = date('Y-m-d', strtotime($arrCSVZeile[0])); // Buchungsdatum 
				$arrCSVZeile[1] = date('Y-m-d', strtotime($arrCSVZeile[1])); // Valuta
				
				
				# **********************************************************
				# ***         CSV-Unterschiede abfangen                  ***
				# ********************************************************** 
				# Die ING-Diba hat 2016 oder 2017 das CSV-Format 
				# geändert: es wurden zwei Spalten miteinander vertauscht
				# Dies wird hiermit abgefangen. 
				
				# echo "<b>CSV-Version:</b> " . $CSV_Version . "<br>";
				/*
				if ($strCSV_Quelle == "ING v2" ) {
					$Hilfsvariable = $arrCSVZeile[5]; 
					$arrCSVZeile[5] = $arrCSVZeile[7];
					$arrCSVZeile[7] = $Hilfsvariable;
				}
				if ($strCSV_Quelle == "ING v3" ) {
					$Hilfsvariable = $arrCSVZeile[6]; 
					$arrCSVZeile[6] = $arrCSVZeile[8];
					$arrCSVZeile[8] = $Hilfsvariable;
				}
				*/
				
				# **********************************************************
				# ***         Doppelte Leerzeichen entfernen             ***
				# ********************************************************** 
				# Gelegentlich kommt es vor, dass ein und dieselbe 
				# Buchung in zwei verschiedenen CSV-Dateien unterschiedlich 
				# ist, weil in einer der beiden Dateien (aus unbekannten 
				# Gründen) zusätzliche Leerzeichen enthalten sind. Dadurch 
				# wird diese Buchung als zwei verschiedene Buchungen 
				# interpretiert und fälschlicherweise in die Datenbank 
				# importiert. Die folgenden Zeilen filtern unnötige 
				# Leerzeichen heraus. Dadurch werden diese Buchungen 
				# korrigiert.  
				
				// Feld 'Auftraggeber/Empfänger' bereinigen
				$arrCSVZeile[2] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[2]) ); 
				// Feld 'Buchungstyp' bereinigen
				$arrCSVZeile[3] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[3]) );
				
				// Feld 'Verwendungszweck' bereinigen
				if ($strCSV_Quelle == "ING v1" || $strCSV_Quelle == "ING v2") {
					$arrCSVZeile[4] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[4]) ); 
				}
				if ($strCSV_Quelle == "ING v3" ) {
					$arrCSVZeile[5] = trim( preg_replace('/\s+/', ' ', $arrCSVZeile[5]) ); 
				}
				
				
				# **********************************************************
				# ***          1000-er Punkt entfernen und               ***
				# ***         Komma gegen Punkt austauschen              ***
				# ********************************************************** 
				# Die Zahlenfelder: Komma gegen Punkt austauschen und 
				# den 1000-er Punkt rausnehmen. Felder `Betrag` und `Saldo`
				if ($strCSV_Quelle == "ING v1" || $strCSV_Quelle == "ING v2") {
					$arrCSVZeile[5] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[5]));
					$arrCSVZeile[7] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[7]));
				}
				if ($strCSV_Quelle == "ING v3") {
					$arrCSVZeile[6] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[6]));
					$arrCSVZeile[8] = str_replace(',', '.', str_replace('.', '', $arrCSVZeile[8]));
				}
				
				
				$insCmd = $pdo->prepare($Datenbankinsert); 
				if ($strCSV_Quelle == "ING v1") {
					$insCmd->bindParam( ':Uploadnummer', $Uploadnummer_neu, PDO::PARAM_INT );
					$insCmd->bindParam( ':Datensatznummer', $Datensatznummer, PDO::PARAM_INT );
					$insCmd->bindParam( ':BankID', $intID_ausDB, PDO::PARAM_INT );
					$insCmd->bindParam( ':Buchungsdatum', $arrCSVZeile[0], PDO::PARAM_STR );
					$insCmd->bindParam( ':Valuta', $arrCSVZeile[1], PDO::PARAM_STR );
					$insCmd->bindParam( ':AuftraggeberEmpfaenger', $arrCSVZeile[2] );
					$insCmd->bindParam( ':Buchungstyp', $arrCSVZeile[3], PDO::PARAM_STR );
					$insCmd->bindParam( ':Verwendungszweck', $arrCSVZeile[4], PDO::PARAM_STR );
					$insCmd->bindParam( ':Betrag', $arrCSVZeile[5], PDO::PARAM_INT );
					$insCmd->bindParam( ':Waehrung', $arrCSVZeile[6], PDO::PARAM_STR );
					$insCmd->bindParam( ':Saldo', $arrCSVZeile[7], PDO::PARAM_INT );
					$insCmd->bindParam( ':CSV_Quelle', $strBank_ausCSV, PDO::PARAM_STR);
				}
				if ($strCSV_Quelle == "ING v2") {
					$insCmd->bindParam( ':Uploadnummer', $Uploadnummer_neu, PDO::PARAM_INT );
					$insCmd->bindParam( ':Datensatznummer', $Datensatznummer, PDO::PARAM_INT );
					$insCmd->bindParam( ':BankID', $intID_ausDB, PDO::PARAM_INT );
					$insCmd->bindParam( ':Buchungsdatum', $arrCSVZeile[0], PDO::PARAM_STR );
					$insCmd->bindParam( ':Valuta', $arrCSVZeile[1], PDO::PARAM_STR );
					$insCmd->bindParam( ':AuftraggeberEmpfaenger', $arrCSVZeile[2] );
					$insCmd->bindParam( ':Buchungstyp', $arrCSVZeile[3], PDO::PARAM_STR );
					$insCmd->bindParam( ':Verwendungszweck', $arrCSVZeile[4], PDO::PARAM_STR );
					$insCmd->bindParam( ':Betrag', $arrCSVZeile[7], PDO::PARAM_INT );
					$insCmd->bindParam( ':Waehrung', $arrCSVZeile[6], PDO::PARAM_STR );
					$insCmd->bindParam( ':Saldo', $arrCSVZeile[5], PDO::PARAM_INT );
					$insCmd->bindParam( ':CSV_Quelle', $strBank_ausCSV, PDO::PARAM_STR);
				}
				if ($strCSV_Quelle == "ING v3") {
					$insCmd->bindParam( ':Uploadnummer', $Uploadnummer_neu, PDO::PARAM_INT );
					$insCmd->bindParam( ':Datensatznummer', $Datensatznummer, PDO::PARAM_INT );
					$insCmd->bindParam( ':BankID', $intID_ausDB, PDO::PARAM_INT );
					$insCmd->bindParam( ':Buchungsdatum', $arrCSVZeile[0], PDO::PARAM_STR );
					$insCmd->bindParam( ':Valuta', $arrCSVZeile[1], PDO::PARAM_STR );
					$insCmd->bindParam( ':AuftraggeberEmpfaenger', $arrCSVZeile[2] );
					$insCmd->bindParam( ':Buchungstyp', $arrCSVZeile[3], PDO::PARAM_STR );
					$insCmd->bindParam( ':Verwendungszweck', $arrCSVZeile[5], PDO::PARAM_STR );
					$insCmd->bindParam( ':Betrag', $arrCSVZeile[8], PDO::PARAM_INT );
					$insCmd->bindParam( ':Waehrung', $arrCSVZeile[7], PDO::PARAM_STR );
					$insCmd->bindParam( ':Saldo', $arrCSVZeile[6], PDO::PARAM_INT );
					$insCmd->bindParam( ':CSV_Quelle', $strBank_ausCSV, PDO::PARAM_STR);
				}
				$insCmd->execute();
				# Datensatznummer increment um eins erhöhen
				$Datensatznummer++;
			} // Ende der foreach-Schleife
		} // Ende der if-Bedingung
		
		?>
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

</body>
</html>
