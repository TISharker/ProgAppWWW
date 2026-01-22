<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include('cfg.php');
include('showpage.php');
include('contact.php');
include('koszyk.php');
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
                echo '<li><a href="index.php?id=' . $row['id'] . '">' . $row['page_title'] . '</a></li>';
            }
            ?>
            <li><a href="index.php?id=shop">Sklep</a></li>
            <li><a href="index.php?id=contact">Kontakt</a></li>
        </ul>
    </nav>

    <main>
        <?php
        if (isset($_GET['id'])) {
            $id_strony = $_GET['id'];

            if ($id_strony === 'contact') {
                echo PokazKontakt();
            } elseif ($id_strony === 'forgot_pass') {
                echo PrzypomnijHaslo();
            } elseif ($id_strony === 'cart') {
                echo PokazZawartoscKoszyka($link);
            } elseif ($id_strony === 'shop') {
                echo PokazSklep($link);
            } else {
                $tresc_strony = PokazPodstrone($id_strony);
                echo $tresc_strony;
            }
        } else {
            $tresc_strony = PokazPodstrone(1);
            echo $tresc_strony;
        }
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
        <a href="index.php?id=forgot_pass" style="color: #ccc; text-decoration: none; font-size: 0.8em;">Przypomnij hasło</a>
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