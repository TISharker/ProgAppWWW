<?php
session_start();
include('../cfg.php');

function FormularzLogowania()
{
    $wynik = '
    <div class="logowanie">
    <h1 class="heading">Panel CMS:</h1>
    <div class="logowanie">
    <form method="post" name="LoginForm" enctype="multipart/form-data" action="' . $_SERVER['REQUEST_URI'] . '">
    <table class="logowanie">
    <tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
    <tr><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
    <tr><td>&nbsp;</td><td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td></tr>
    </table>
    </form>
    </div>
    </div>
    ';
    return $wynik;
}

function ListaPodstron()
{
    global $link;
    $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_array($result)) {
        echo $row['id'] . ' ' . $row['page_title'] . ' <a href="admin.php?funkcja=usun&id=' . $row['id'] . '">Usuń</a> <a href="admin.php?funkcja=edytuj&id=' . $row['id'] . '">Edytuj</a><br />';
    }
    echo '<br /><a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a>';
    echo '<br /><a href="admin.php?funkcja=kategorie">Zarządzaj Kategoriami</a>';
}

function EdytujPodstrone()
{
    global $link;
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<form method="post" action="">
                Tytuł: <input type="text" name="page_title" value="' . $row['page_title'] . '"><br />
                Treść: <textarea name="page_content">' . $row['page_content'] . '</textarea><br />
                Aktywna: <input type="checkbox" name="status" ' . ($row['status'] == 1 ? 'checked' : '') . '><br />
                <input type="submit" name="edytuj_submit" value="Zapisz">
              </form>';
    }

    if (isset($_POST['edytuj_submit'])) {
        $id = htmlspecialchars($_GET['id']);
        $title = mysqli_real_escape_string($link, $_POST['page_title']);
        $content = mysqli_real_escape_string($link, $_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "UPDATE page_list SET page_title='$title', page_content='$content', status=$status WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

function DodajNowaPodstrone()
{
    global $link;
    echo '<form method="post" action="">
            Tytuł: <input type="text" name="page_title"><br />
            Treść: <textarea name="page_content"></textarea><br />
            Aktywna: <input type="checkbox" name="status" checked><br />
            <input type="submit" name="dodaj_submit" value="Dodaj">
          </form>';

    if (isset($_POST['dodaj_submit'])) {
        $title = mysqli_real_escape_string($link, $_POST['page_title']);
        $content = mysqli_real_escape_string($link, $_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', $status)";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

function UsunPodstrone()
{
    global $link;
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $query = "DELETE FROM page_list WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

// --- KATEGORIE ---

function PokazDrzewoKategorii($matka_id = 0, $prefix = '')
{
    global $link;
    $query = "SELECT * FROM categories WHERE matka='$matka_id'";
    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_array($result)) {
        echo '<div>' . $prefix . $row['nazwa'] . ' (ID: ' . $row['id'] . ') <a href="admin.php?funkcja=edytuj_kat&id=' . $row['id'] . '">Edytuj</a> <a href="admin.php?funkcja=usun_kat&id=' . $row['id'] . '">Usuń</a></div>';
        PokazDrzewoKategorii($row['id'], $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
    }
}

function PokazKategorie()
{
    echo '<h3>Zarządzanie Kategoriami</h3>';
    echo '<a href="admin.php?funkcja=dodaj_kat">Dodaj nową kategorię</a><br /><br />';
    PokazDrzewoKategorii();
}

function DodajKategorie()
{
    global $link;
    echo '<h3>Dodaj Kategorię</h3>';
    echo '<form method="post" action="">
            Nazwa: <input type="text" name="nazwa"><br />
            Matka (ID): <input type="text" name="matka" value="0"><br />
            <input type="submit" name="dodaj_kat_submit" value="Dodaj">
          </form>';

    if (isset($_POST['dodaj_kat_submit'])) {
        $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
        $matka = intval($_POST['matka']);

        $query = "INSERT INTO categories (matka, nazwa) VALUES ('$matka', '$nazwa')";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=kategorie";</script>';
    }
}

function EdytujKategorie()
{
    global $link;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $query = "SELECT * FROM categories WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        echo '<h3>Edytuj Kategorię</h3>';
        echo '<form method="post" action="">
                Nazwa: <input type="text" name="nazwa" value="' . $row['nazwa'] . '"><br />
                Matka (ID): <input type="text" name="matka" value="' . $row['matka'] . '"><br />
                <input type="submit" name="edytuj_kat_submit" value="Zapisz">
              </form>';
    }

    if (isset($_POST['edytuj_kat_submit'])) {
        $id = intval($_GET['id']);
        $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
        $matka = intval($_POST['matka']);

        $query = "UPDATE categories SET nazwa='$nazwa', matka='$matka' WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=kategorie";</script>';
    }
}

function UsunKategorie()
{
    global $link;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        // Usunięcie kategorii
        $query = "DELETE FROM categories WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=kategorie";</script>';
    }
}

// --- KONIEC KATEGORIE ---

if (isset($_POST['x1_submit'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['loggedin'] = true;
    } else {
        echo "Błąd logowania.<br />";
        echo FormularzLogowania();
        exit();
    }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    if (isset($_GET['funkcja'])) {
        if ($_GET['funkcja'] == 'usun') {
            UsunPodstrone();
        } elseif ($_GET['funkcja'] == 'edytuj') {
            EdytujPodstrone();
        } elseif ($_GET['funkcja'] == 'dodaj') {
            DodajNowaPodstrone();
        } elseif ($_GET['funkcja'] == 'kategorie') {
            PokazKategorie();
        } elseif ($_GET['funkcja'] == 'dodaj_kat') {
            DodajKategorie();
        } elseif ($_GET['funkcja'] == 'edytuj_kat') {
            EdytujKategorie();
        } elseif ($_GET['funkcja'] == 'usun_kat') {
            UsunKategorie();
        } else {
            ListaPodstron();
        }
    } else {
        ListaPodstron();
    }
} else {
    echo FormularzLogowania();
}

echo '<br><br>';
echo '<a href="../contact.php?akcja=przypomnij" style="color: red; font-weight: bold;">[Przypomnij hasło]</a>';
