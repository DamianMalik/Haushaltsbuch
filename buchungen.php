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

    <title>Seitentitel</title>

    <!-- Google Fonts -->
    <!-- Font Type: Patrick Hand -->
    <link href="https://fonts.googleapis.com/css?family=Bitter" 
          rel="stylesheet">

</head>

<body class="font-Bitter bg-light text-dark">

	<?php
	# <!-- Navigationsleiste -->
	include 'navigation.php';
	?>
        <div class="container-fluid	">

            <h1 class="pt-3 pb-3">Buchungen</h1>

            <!-- mb-3 etwas Abstand nach unten 
            <div class="input-group mb-3">
                <a href="#" class="btn btn-dark  active" role="button" aria-pressed="true">Alles</a>
                <a href="#" class="btn btn-dark ml-1  active" role="button" aria-pressed="true">5 Jahre</a>
                <a href="#" class="btn btn-dark ml-1 active" role="button" aria-pressed="true">3 Jahre</a>
                <a href="#" class="btn btn-dark ml-1 active" role="button" aria-pressed="true">2 Jahre</a>
                <a href="#" class="btn btn-dark ml-1 active" role="button" aria-pressed="true">1 Jahr</a>
                <a href="#" class="btn btn-dark ml-1 active" role="button" aria-pressed="true">6 Monate</a>
                <a href="#" class="btn btn-dark ml-1 active" role="button" aria-pressed="true">3 Monate</a>

                <input type="text" class="form-control ml-2" placeholder="Suchbegriff" aria-label="Suche" aria-describedby="basic-addon1">
                <div class="input-group-append">
                    <span class="btn btn-dark" id="basic-addon2">Suche</span>
                </div>
            </div> -->
<!---------------------------------------------------------------------->

      
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


<!----------------------------------------------------------->      

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
                        <tr>
                            <th scope="row">B 1</th>
                            <td>30.11.2019</td>
                            <td>Überw</td>
                            <td>Person XYZ</td>
                            <td>Parkplatz</td>
                            <td>-28,30</td>
                        </tr>
                        <tr>
                            <th scope="row">B 2</th>
                            <td>05.12.2019</td>
                            <td>Auftraggeber</td>
                            <td>Carwash</td>
                            <td>Auftraggeber</td>
                            <td>-13,20</td>
                        </tr>
                        <tr>
                            <th scope="row">b 3</th>
                            <td>07.12.2019</td>
                            <td>Überw</td>
                            <td>Firma XYZ</td>
                            <td>Sitzkissen</td>
                            <td>-150,00</td>
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
