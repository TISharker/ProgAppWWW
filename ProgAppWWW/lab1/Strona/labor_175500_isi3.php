<?php
$nr_indeksu = '175500'; 
$nrGrupy = '3';

echo 'Jakub Sierocki: ' . $nr_indeksu . ' grupa: ' . $nrGrupy . '<br />';
echo 'Zastosowanie metody include() <br />';

include('test.php');  
require_once('test.php');

$number = 10;
if ($number > 5) {
    echo "wiekszy niz 5";
} elseif ($number == 5) {
    echo "rowny 5";
} else {
    echo "mniejszy niz 5";
}

switch ($number) {
    case 5:
        echo "piec";
        break;
    case 10:
        echo "dziesiec";
        break;
    default:
        echo "inny";
        break;
}


$i = 0;
while ($i < 5) {
    echo "numer: $i <br />";
    $i++;
}


for ($i = 0; $i < 5; $i++) {
    echo "numer: $i <br />";
}


if (isset($_GET['name'])) {
    echo "Hello, " . $_GET['name'];
}

$_POST['age'] = '20'
echo "Your age is: " . $_POST['age'];


session_start();
$_SESSION['username'] = 'Jan Kowalski';
echo $_SESSION['username'];




?>
