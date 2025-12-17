<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/*
     * Projekt: Strona WWW - Wersja v1.8
     * Autor: Jakub Sierocki 175500
     */
include('cfg.php');
include('showpage.php');


$idp = $_GET['idp'] ?? '';

// Obsługa dynamicznego ID strony
if ($idp == '' || $idp == 'glowna') {
    $stronaId = 1;
} elseif (is_numeric($idp)) {
    $stronaId = $idp;
} else {
    // Mapowanie statycznych nazw na ID
    if ($idp == 'podstrona1') $stronaId = 2;
    elseif ($idp == 'podstrona2') $stronaId = 3;
    elseif ($idp == 'podstrona3') $stronaId = 4;
    elseif ($idp == 'podstrona4') $stronaId = 5;
    elseif ($idp == 'podstrona5') $stronaId = 6;
    elseif ($idp == 'filmy') $stronaId = 7;
    else $stronaId = 1;
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
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
</head>

<body onload="startclock()">

    <header>
        <h1>Największe budynki świata</h1>
    </header>

    <nav id="menu-glowne">
        <ul>
            <?php
            // Menu dynamiczne
            $query = "SELECT * FROM page_list WHERE status=1 ORDER BY id ASC LIMIT 100";
            $result = mysqli_query($link, $query);

            while ($row = mysqli_fetch_array($result)) {
                echo '<li><a href="index.php?idp=' . $row['id'] . '">' . $row['page_title'] . '</a></li>';
            }
            ?>
        </ul>
    </nav>

    <main>
        <?php
        echo PokazPodstrone($stronaId);
        ?>
    </main>

    <footer>
        <div id="zegarek"></div>
        <div id="data"></div>
        <p>Największe budynki świata na stan 2025</p>
        <?php
        $nr_indeksu = '175500';
        $nrGrupy = '3';
        echo 'Autor: Jakub Sierocki ' . $nr_indeksu . ' grupa ' . $nrGrupy;
        ?>
        <br>
        <a href="admin/admin.php" style="color: #ccc; text-decoration: none; font-size: 0.8em;">Panel Admina</a>
        <a href="contact.php" style="color: #ccc; text-decoration: none; font-size: 0.8em;">Kontakt</a>
    </footer>

    <script>
        $("#animacjaTestowa1").on("click", function() {
            $(this).animate({
                width: "500px",
                opacity: 0.4,
                fontSize: "3em",
                borderWidth: "10px"
            }, 1500);
        });
        $("#animacjaTestowa2").on({
            "mouseover": function() {
                $(this).animate({
                    width: 300
                }, 800);
            },
            "mouseout": function() {
                $(this).animate({
                    width: 200
                }, 800);
            }
        })
        $(".photoInGalery").on({
            "mouseover": function() {
                $(this).animate({
                    width: 300,
                    height: 500
                }, 800);
            },
            "mouseout": function() {
                $(this).animate({
                    width: 100,
                    height: 200
                }, 800);
            }
        })
    </script>
</body>

</html>