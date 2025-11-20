<?
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

$link = mysql_connect($dbhost, $dbuser, $dbpass);
if (!$link) echo 'błąd przy połączeniu';
if (!mysql_select_db($baza)) echo 'nie wybrano bazy';
?>