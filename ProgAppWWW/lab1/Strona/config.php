<?
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'moja_strona_175500';
$link = mysql_connect($dbhost, $dbuser, $dbpass);
if (!$link) echo 'błąd przy połączeniu';
if (!mysql_select_db($baza)) echo 'nie wybrano bazy';
?>