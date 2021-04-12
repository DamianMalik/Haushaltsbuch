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
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

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
	?>

    <div class="container">
        <h1 class="pt-3 pb-4">Umsatzanzeige importieren</h1>

        <form method="POST" 
              action="import-02.php" 
              enctype="multipart/form-data">
            <!--<div class="custom-file w-50"> -->
            <div class="custom-file h4">
                <input type="file" 
                       class="custom-file-input" 
                       name="CSVDatei" 
                       id="Dateiupload" 
                       accept=".csv" required>

                <label class="custom-file-label" 
                       for="Dateiupload">CSV-Datei...</label>
                <div class="invalid-feedback">Datei auswählen!</div>
            </div>
            <!-- <div class="pt-3 w-50"> -->
            <div class="pt-3">
                <button class="btn-lg btn-primary float-right" 
                        type="submit">Hochladen</button>
            </div>
        </form>

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

    <!-- JS für den Upload Button -->
    <script>
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>

</body>

</html>
