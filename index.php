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
    
    
    <title>Haushaltsbuch | Übersicht</title>
    
    <!-- Google Fonts -->
    <!-- Font Type: Patrick Hand -->
    <link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet"> 
    
  </head>
  <body class="font-Bitter bg-light text-dark">
  
  <?php
  # Verbindungsaufbau zur Datenbank
  include 'Datenbank.php';
  
  # <!-- Navigationsleiste -->
  include 'navigation.php';
  
  
  
  
# **********************************************************
# ***            Liste der letzten Updates               ***
# **********************************************************
$DBabfrage = "SELECT ba.`id`, 
                     ba.`Inhaber`, 
                     ba.`Bankname`, 
                     ba.`Kontoname`,
                        (SELECT  MAX(`Timestamp`) 
                        FROM `Buchungen` bu
                        WHERE bu.`BankID` = ba.`id` ) AS `Timestamp`,
                        
                        (SELECT  DATEDIFF(CURDATE(), MAX(`Timestamp`)) 
                        FROM `Buchungen` bu
                        WHERE bu.`BankID` = ba.`id` ) AS `Tage_seit_Update`
                        
                     FROM `Banken` ba";
$DBausgabe = $pdo->query($DBabfrage);
  
  
  ?>
  
  
  <div class="container-fluid">
	<h1 class="pt-3">Letzte Updates</h1>
	
	<!-- Responsive Tabelle mit Padding 3 -->
	<div class="table-responsive pt-3 h5">
		<table class="table table-hover">
			<thead class="thead-dark">
				<tr>
					<th scope="col">Bank</th>
					<th scope="col">Inhaber</th>
					<th scope="col">Konto</th>
					<th scope="col">letztes Update</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($DBausgabe as $zeile) { 
					?>
				<tr>
					
					<?php 
						echo "<td>" . $zeile['Bankname'] . "</td>";
						echo "<td>" . $zeile['Inhaber'] . "</td>";
						echo "<td>" . $zeile['Kontoname'] . "</td>";
						if (!is_null($zeile['Timestamp'])) {
							# Ausgabe des Datums
							$farbe = '#799134'; // grüne Farbe
							if ($zeile['Tage_seit_Update'] > 20) $farbe = '#BAB638'; // hellgrüne Farbe
							if ($zeile['Tage_seit_Update'] > 40) $farbe = '#FAC337'; // gelbe Farbe
							if ($zeile['Tage_seit_Update'] > 60) $farbe = '#E6922C'; // orangene Farbe
							if ($zeile['Tage_seit_Update'] > 80) $farbe = '#9E3129'; // rote Farbe
							echo "<td>" 
								. date('d.m.Y', strtotime($zeile['Timestamp'])) 
								. " (vor "
								. "<font color='" . $farbe . "'>" 
								. $zeile['Tage_seit_Update']
								. "</font>"
								. " Tagen)"
								. "</td>";
							} else {
							# Ausgabe leeres Feld
							echo "<td>" . "" . "</td>";
						}
					?>
				</tr>
				<?php 
				} // Ende der foreach-Schleife 
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
