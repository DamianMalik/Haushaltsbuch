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
 
	<script>
	$(document).ready(function() {
		$(".spinner-border").hide();
	});
	</script>
</head>
<body class="font-Bitter bg-light text-dark">
<?php
# **********************************************************
# ***                                                    ***
# ***                     Definitionen                   ***
# ***                                                    ***
# **********************************************************

# Navigationsleiste einfügen-->
include 'navigation.php';


# **********************************************************
# ***            Verarbeitung Formulareingaben           ***
# ********************************************************** 

# Auslesen der Formulareingabe "Bank" 
# htmlentities --> Aus der Zeichenkette werden HTML-Tags in Code um. 
# strip_tags   --> Aus der Zeichenkette werden HTML- und PHP-Tags entfernt.
# preg_replace --> Aus der Zeichenkette werden bestimmte Zeichen gelöscht.
if(isset($_POST['Bank'])) {
	$strBankID_aus_POST = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_POST['Bank'])));
} else {
	$strBankID_aus_POST = "X";
} 

# Auslesen der Formulareingabe "Zeitraum" 
if(isset($_POST['Zeitraum'])) {
	$strZeitraum_aus_POST = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_POST['Zeitraum'])));
} else {
	$strZeitraum_aus_POST = "X";
} 

# Auslesen der Formulareingabe 'Suchbegriff'
# Damit htmlentities die Umlaute nicht umwandelt, ist die Option ENT_XML1 erforderlich
if(isset($_POST['Suchbegriff'])) {
	$strSuchbegriff_aus_POST = preg_replace('![^0-9a-zA-ZäöüÄÖÜ\ ]!', '', strip_tags(htmlentities($_POST['Suchbegriff'], ENT_XML1 )));
} else {
	$strSuchbegriff_aus_POST = "";
} 
# Für die LIKE-Bedingungen wird der Suchbegriff verfeinert
# $Suchbegriff = "%" . $Suchbegriff . "%";
# $strSuchbegriff_SQL = "%" . $strSuchbegriff_aus_POST . "%";
# Nur Suchbegriffe mit 3 oder mehr Zeichen werden verarbeitet
if (strlen($strSuchbegriff_aus_POST) >= '3') {
	$strSuchbegriff_SQL = "%" . $strSuchbegriff_aus_POST . "%";
} else {
	$strSuchbegriff_aus_POST = "";
	$strSuchbegriff_SQL = "";
}


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
				 if ($strBankID_aus_POST != 'X') {
					 $DBabfrage .= "WHERE `BankID` = '" . $strBankID_aus_POST . "' ";
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
if ($strZeitraum_aus_POST == 'X')  $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 20 YEAR), '%Y-%m-%d')"; 
if ($strZeitraum_aus_POST == '3M') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')"; 
if ($strZeitraum_aus_POST == '6M') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 6 MONTH), '%Y-%m-%d')"; 
if ($strZeitraum_aus_POST == '1J') $sqlAND = "AND   a.`Buchungsdatum` >= DATE_FORMAT(DATE_SUB(SYSDATE(), INTERVAL 1 YEAR), '%Y-%m-%d')"; 
if ($strZeitraum_aus_POST >= '1965') $strJahr = "WHERE YEAR(`Buchungsdatum`) = '" . $strZeitraum_aus_POST . "' ";
if (strlen($strZeitraum_aus_POST) == '4') $sqlAND = "AND YEAR(`Buchungsdatum`) = '" . $strZeitraum_aus_POST . "' ";


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
			if ($strBankID_aus_POST != 'X') {
				$DBabfrage .= "AND   a.`BankID` = '" .  $strBankID_aus_POST . "' ";
			}
$DBabfrage .= $sqlAND; 
if ($strSuchbegriff_SQL != '') {
	$DBabfrage .= "	AND   ( a.`Verwendungszweck` LIKE '" 
							. $strSuchbegriff_SQL 
							. "' OR    a.`AuftraggeberEmpfaenger` LIKE '" 
							. $strSuchbegriff_SQL . "' )";
}
$DBabfrage .= "
			ORDER BY a.`Buchungsdatum` DESC, 
					 a.`Datensatznummer` DESC; ";
$DBausgabe = $pdo->query($DBabfrage);
#LIMIT 30; 


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
		echo '<span class="badge badge-pill badge-info ml-2">';
		if ($strBankID_aus_POST != 'X') {
			echo $arrBankID_aus_DB[$strBankID_aus_POST]; 
		} else {
			echo 'Alle Konten';
		}
		echo '</span>';
		echo '<span class="badge badge-pill badge-warning ml-2">';
			echo $strSuchbegriff_aus_POST;
		echo '</span>';
		?>
		<div class="spinner-border text-info ml-2" role="status">
			<span class="sr-only">Loading...</span>
		</div>
	</div>
	
	
	<?php
	# **********************************************************
	# ***               Button Toolbar                       ***
	# **********************************************************
	?>
	<div class="btn-toolbar pt-3" role="toolbar" aria-label="Toolbar mit Button-Gruppe und Eingeabefeld">
	
		<?php
		# **********************************************************
		# ***               Button Group                         ***
		# **********************************************************
		?>
		<div class="btn-group" role="group" aria-label="Button Gruppe mit verschachteltem Dropdown">
			<?php
			# ***************** Button "Konto" *************************
			?>
			<div class="btn-group" role="group" aria-label="Dropdown Konto">
				<button id="btnGroupDropKonto" 
						type="button" 
						class="btn btn-dark dropdown-toggle btn-lg" 
						data-toggle="dropdown" 
						aria-haspopup="true" 
						aria-expanded="false">Konto
						<?php 
							if ($strBankID_aus_POST  != 'X') { 
								echo '<span class="badge badge-info">'
									. $strBankID_aus_POST
									. '</span>'; 
							}
						?>
				</button>
				<div class="dropdown-menu" aria-labelledby="btnGroupDropKonto">
					<form action="buchungen.php" method="POST">
						<?php
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="' . $strZeitraum_aus_POST . '">'; 
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="X">
								<span class="badge badge-info mr-2">⚪</span>Alle Konten</button>';
						?>
					</form>
					<?php
					foreach($arrBankID_aus_DB as $index2 => $bankname) {
						echo '<form action="buchungen.php" method="POST">';
							echo '<input type="hidden" 
										name="Zeitraum" 
										value="' . $strZeitraum_aus_POST . '">';
							echo '<button type="submit" 
										class="dropdown-item"
										name="Bank"
										value="' . $index2 . '">'
										. '<span class="badge badge-info mr-2">' 
										. $index2 
										. '</span>'
										. $bankname 
										. '</button>';
						echo '</form>';
					}
					?>
				</div>
			</div> 
			
			<?php
			# ***************** Button "Zeitraum" *************************
			?>
			<div class="btn-group" role="group" aria-label="Dropdown Zeitraum">
				<button id="btnGroupDropZeitraum" 
						type="button" 
						class="btn btn-secondary dropdown-toggle btn-lg" 
						data-toggle="dropdown" 
						aria-haspopup="true" 
						aria-expanded="false">Zeitraum
						<?php 
							if ($strZeitraum_aus_POST != 'X') { 
								echo '<span class="badge badge-info">'
									. $strZeitraum_aus_POST
									. '</span>'; 
							}
						?>
				</button>
				<div class="dropdown-menu" aria-labelledby="btnGroupDropZeitraum">
					<form action="buchungen.php" method="POST">
						<?php
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="X">';
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="' . $strBankID_aus_POST . '">Alle Buchungen
						</button>';
						?>
					</form>
					<form action="buchungen.php" method="POST">
						<?php
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="3M">';
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="' . $strBankID_aus_POST . '">3 Monate</button>';
						?>
					</form>
					<form action="buchungen.php" method="POST">
						<?php
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="6M">';
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="' . $strBankID_aus_POST . '">6 Monate</button>';
						?>
					</form>
					<form action="buchungen.php" method="POST">
						<?php
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="1J">';
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="' . $strBankID_aus_POST . '">1 Jahr
						</button>';
						?>
					</form>
					<?php
					foreach($arrKalenderjahre_aus_DB as $Jahr) { 
						echo '<form action="buchungen.php" method="POST">';
						echo '<input type="hidden" 
								name="Zeitraum" 
								value="' . $Jahr . '">';
						echo '<button type="submit" 
								class="dropdown-item"
								name="Bank"
								value="' . $strBankID_aus_POST . '">' . $Jahr . '</button>';
						echo '</form>';
						
					}
					?>
				</div>
			</div>
			<?php
			# ***************** Suchfeld *************************
			?>
			<form action="buchungen.php" method="POST">
				<div class="input-group">
					<input type="hidden" 
						   name="Bank" 
						   value="X">
					<input type="hidden" 
						   name="Zeitraum" 
						   value="X">
					<input type="text" 
						   name="Suchbegriff"
						   class="form-control form-control-lg ml-2" 
						   placeholder="Suchbegriff" 
						   aria-label="Suche">
					<div class="input-group-append">
						<button type="submit" 
								class="btn btn-dark btn-lg">Suche</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php 
	# **********************************************************
	# ***               Tabelle ausgeben                     ***
	# **********************************************************
	?>
	<div class="table-responsive pt-3">
		<table class="table table-hover">
			<thead class="thead-dark">
				<tr>
					<th style="width: 15%">Bank</th>
					<th style="width: 10%">Datum</th>
					<th style="width: 25%">Auftraggeber / Empfänger</th>
					<th style="width: 40%">Verwendungszweck</th>
					<th style="width: 10%" class="text-right">Betrag</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($DBausgabe as $zeile) { 
					echo '<tr>';
						echo '<td>';
							echo $zeile['Bank'];
						echo '</td>';
						echo '<td>'; 
							echo date('d.m.Y', strtotime($zeile['Buchungsdatum'])); 
						echo '</td>';
						echo '<td>';
							echo '<div><span class="badge badge-pill badge-info">'
									. $zeile['Buchungstyp']
									. '</span></div>'; 
							echo $zeile['AuftraggeberEmpfaenger'];
						echo '</td>';
						echo '<td>';
							echo $zeile['Verwendungszweck'];
						echo '</td>';
						echo '<td class="text-right">';
							if ($zeile['Betrag'] > 0) $strFarbe = '#00B233'; // grüne Farbe
							if ($zeile['Betrag'] < 0) $strFarbe = '#FF0000'; // rote Farbe
							echo "<font color='" . $strFarbe . "'>" 
								 . number_format($zeile['Betrag'], 2, ",", ".")
								 . "</font>";
						echo '</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
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
