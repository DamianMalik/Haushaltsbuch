<nav class="navbar navbar-expand-lg navbar-dark bg-dark h3">
    <button class="navbar-toggler" 
            type="button" 
            data-toggle="collapse" 
            data-target="#navbarTogglerDemo01" 
            aria-controls="navbarTogglerDemo01" 
            aria-expanded="false" 
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" 
         id="navbarTogglerDemo01">
        <ul class="navbar-nav  mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" 
                   href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                   href="import-01.php">CSV-Import</a>
            </li>
            <?php
            /* Der Menüpunkt "Filter" wird herausgenommen, siehe #13
            <li class="nav-item">
                <!-- <a class="nav-link disabled" href="#">Filter</a> -->
                <a class="nav-link disabled" 
                   href="#" 
                   tabindex="-1" 
                   aria-disabled="true">Filter</a>
            </li>
            */
            ?>
            <li class="nav-item">
                <a class="nav-link" 
                   href="buchungen.php">Buchungen</a>
            </li>
            <?php
            /*
            <li class="nav-item">
                <a class="nav-link disabled" 
                   href="#">Auswertung</a>
            </li>
            */ 
            ?>
        </ul>
    </div>
</nav>
