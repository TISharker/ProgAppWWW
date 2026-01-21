<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

$login = 'admin';
$pass = 'admin';
$admin_email = 'admin@admin.pl';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

if (!$link) {
    echo '<b>przerwane połączenie: </b> ' . mysqli_connect_error();
    exit();
}
