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
  ?>

        <!-- <div class="container-fluid"> -->
        <div class="container">

            <h3 class="pt-3">Dateiupload</h3>

            <!-- mb-3 etwas Abstand nach unten -->

            <p class="font-weight-bold">Kontoinformationen:</p>

            <div class="table-responsive">

                <table class="table table-striped">

                    <tbody>
                        <tr>
                            <td>letzte Upload Nummer:</td>
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

            <p class="font-weight-bold">Uploadinformationen:</p>

            <div class="table-responsive">
                <table class="table table-striped">

                    <tbody>
                        <tr>
                            <td>Dateiname:</td>
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
