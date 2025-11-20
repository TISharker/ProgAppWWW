<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    include('cfg.php');
    include('showpage.php');
    
    // Mapowanie 'idp' (linków z menu) na ID w bazie danych
    // Krok 1 w SQL ustawił ID tak: 1=Główna, 2=Burj, 3=Shanghai, 4=Abraj, 5=PingAn, 6=Lotte, 7=Filmy
    
    $idp = $_GET['idp'] ?? ''; // operator ?? zabezpiecza przed brakiem zmiennej

    if ($idp == '' || $idp == 'glowna') $stronaId = 1;
    elseif ($idp == 'podstrona1') $stronaId = 2;
    elseif ($idp == 'podstrona2') $stronaId = 3;
    elseif ($idp == 'podstrona3') $stronaId = 4;
    elseif ($idp == 'podstrona4') $stronaId = 5;
    elseif ($idp == 'podstrona5') $stronaId = 6;
    elseif ($idp == 'filmy') $stronaId = 7;
    else $stronaId = 1;
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
            // Wywołanie treści z bazy danych
            echo PokazPodstrone($stronaId);
        ?>
    </main>

    <footer>
        <p>Największe budynki świata na stan 2025</p>
        <?php
            $nr_indeksu = '175500'; 
            $nrGrupy = '3'; 
            echo 'Autor: Jakub Sierocki ' . $nr_indeksu . ' grupa ' . $nrGrupy;
        ?>
        <br>
        <a href="admin.php" style="color: #ccc; text-decoration: none; font-size: 0.8em;">Panel Admina</a>
    </footer>

    <script>
        $("#animacjaTestowa1").on("click",function(){
            $(this).animate({width: "500px", opacity: 0.4, fontSize: "3em", borderWidth: "10px"}, 1500);
        });
        $("#animacjaTestowa2").on({
            "mouseover" : function() { $(this).animate({width: 300},800); },
            "mouseout" : function() { $(this).animate({width: 200},800); }
        })
        $(".photoInGalery").on({
            "mouseover" : function() { $(this).animate({width: 300, height:500},800); },
            "mouseout" : function() { $(this).animate({width: 100, height: 200},800); }
        })
    </script>
</body>
</html>