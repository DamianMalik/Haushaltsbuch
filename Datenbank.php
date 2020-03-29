<?php
# Verbindungsaufbau

$pdo = new PDO('mysql:host=localhost;
                dbname=Haushaltsbuch',
               'root', 
               '',
               array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") 
               );



# the first setAttribute() line, which tells PDO to 
# disable emulated prepared statements and 
# use real prepared statements. This makes sure the statement and 
# the values aren't parsed by PHP before sending it to the MySQL server
# (giving a possible attacker no chance to inject malicious SQL).
# Another benefit with using prepared statements is that if you 
# execute the same statement many times in the same session it will 
# only be parsed and compiled once, giving you some speed gains.
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


# the error mode isn't strictly necessary, but it is advised to add it.
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

# um mehr als 65.000 Datenfelder einzufügen, kann der Parameter 
# auf `true` gesetzt werden. Aber meines Erachtens leidet dadurch 
# die Insert-Geschwindigkeit erheblich.
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
# $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);


# echo "Verbindung hergestellt. Willkommen." . "<br>";

