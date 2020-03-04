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
  # <!-- Navigationsleiste -->
  include 'navigation.php';
  ?>
  
  
  <div class="container-fluid">
	<h1 class="pt-3">Letzte Updates</h1>
	
	<!-- Responsive Tabelle mit Padding 3 -->
	<div class="table-responsive pt-3 h4">
		<table class="table table-hover">
			<thead class="table-dark">
				<tr>
					<th scope="col">Inhaber</th>
					<th scope="col">Bank</th>
					<th scope="col">Konto</th>
					<th scope="col">letztes Update</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>ABC</td>
					<td>Bank A</td>
					<td>Konto 1</td>
					<td>30.11.2019</td>
				</tr>
				<tr>
					<td>ABC</td>
					<td>Bank A</td>
					<td>Konto 2</td>
					<td>31.12.2019</td>
				</tr>
				<tr>
					<td>ABC</td>
					<td>Bank B</td>
					<td>Konto 1</td>
					<td>31.01.2020</td>
				</tr>
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
