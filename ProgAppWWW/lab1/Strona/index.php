<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

  
    $idp = $_GET['idp'];

    if (empty($idp)) {
        $strona = 'html/glowna.html';
    } 
    else if ($idp == 'podstrona1') {
        $strona = 'html/p2.html';
    } else if ($idp == 'podstrona2') {
        $strona = 'html/p3.html';
    } else if ($idp == 'podstrona3') {
        $strona = 'html/p4.html';
    } else if ($idp == 'podstrona4') {
        $strona = 'html/p5.html';
    } else if ($idp == 'podstrona5') {
        $strona = 'html/p6.html';
    }
    else if ($idp == 'filmy') {
        $strona = 'html/filmy.html';
    }
    else {
        $strona = 'html/glowna.html';
    }

    if (!file_exists($strona)) {
        $strona = 'html/glowna.html';
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Największe budynki świata</title>
    
    <link rel="stylesheet" href="css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
</head>
<body>

    <header>
        <h1>Największe budynki świata</h1>
    </header>

    <nav id="menu-glowne">
        <ul>
            
            <li><a href="index.php">Główna</a></li> 
            
            <li><a href="index.php?idp=podstrona1">Burj Khalifa</a></li>
            <li><a href="index.php?idp=podstrona2">Shanghai Tower</a></li>
            <li><a href="index.php?idp=podstrona3">Abraj Al-Bait</a></li>
            <li><a href="index.php?idp=podstrona4">Ping An Finance Center</a></li>
            <li><a href="index.php?idp=podstrona5">Lotte World Tower</a></li>
            
            <li><a href="index.php?idp=filmy">Filmy</a></li>
        </ul>
    </nav>

    <main>
        <?php

            include($strona);
            include('config.php');
        ?>
    </main>

    <footer>
        <p>Największe budynki świata na stan 2025</p>
        
        <?php
            $nr_indeksu = '175500'; 
            $nrGrupy = '3'; 
            
            
            echo 'Autor: Jakub Sierocki ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
        ?>
    </footer>

    </body>
</html>