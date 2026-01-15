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
    echo '<br /><a href="admin.php?funkcja=produkty" style="font-weight:bold;">Zarządzaj Produktami</a>';
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

// --- PRODUKTY ---

function SprawdzDostepnosc($produkt)
{
    // Sprawdza dostępność produktu na podstawie trzech warunków
    if ($produkt['status_dostepnosci'] != 1) {
        return '<span style="color: red;">Niedostępny (wyłączony)</span>';
    }
    if ($produkt['ilosc_dostepnych_sztuk_w_magazynie'] <= 0) {
        return '<span style="color: orange;">Brak w magazynie</span>';
    }
    if (!empty($produkt['data_wygasniecia']) && strtotime($produkt['data_wygasniecia']) < time()) {
        return '<span style="color: gray;">Wygasł</span>';
    }
    return '<span style="color: green;">Dostępny (' . $produkt['ilosc_dostepnych_sztuk_w_magazynie'] . ' szt.)</span>';
}

function PokazProdukty()
{
    global $link;
    echo '<h3>Zarządzanie Produktami</h3>';
    echo '<a href="admin.php?funkcja=dodaj_produkt">Dodaj nowy produkt</a><br /><br />';

    $query = "SELECT * FROM products ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    echo '<tr style="background: #eee;"><th>ID</th><th>Tytuł</th><th>Cena netto</th><th>VAT</th><th>Kategoria</th><th>Status</th><th>Akcje</th></tr>';

    while ($row = mysqli_fetch_array($result)) {
        $cena_brutto = $row['cena_netto'] * (1 + $row['podatek_vat'] / 100);
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
        echo '<td>' . number_format($row['cena_netto'], 2) . ' zł</td>';
        echo '<td>' . $row['podatek_vat'] . '%</td>';
        echo '<td>' . $row['kategoria'] . '</td>';
        echo '<td>' . SprawdzDostepnosc($row) . '</td>';
        echo '<td><a href="admin.php?funkcja=edytuj_produkt&id=' . $row['id'] . '">Edytuj</a> | ';
        echo '<a href="admin.php?funkcja=usun_produkt&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno usunąć?\')">Usuń</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

function DodajProdukt()
{
    global $link;
    echo '<h3>Dodaj Produkt</h3>';
    echo '<a href="admin.php?funkcja=produkty">&laquo; Powrót do listy</a><br /><br />';

    if (isset($_POST['dodaj_produkt_submit'])) {
        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $opis = mysqli_real_escape_string($link, $_POST['opis']);
        $cena_netto = floatval($_POST['cena_netto']);
        $podatek_vat = floatval($_POST['podatek_vat']);
        $ilosc = intval($_POST['ilosc']);
        $status = isset($_POST['status_dostepnosci']) ? 1 : 0;
        $kategoria = intval($_POST['kategoria']);
        $gabaryty = mysqli_real_escape_string($link, $_POST['gabaryty']);
        $data_wygasniecia = !empty($_POST['data_wygasniecia']) ? "'" . mysqli_real_escape_string($link, $_POST['data_wygasniecia']) . "'" : "NULL";

        $query = "INSERT INTO products (tytul, opis, data_utworzenia, data_modyfikacji, data_wygasniecia, cena_netto, podatek_vat, ilosc_dostepnych_sztuk_w_magazynie, status_dostepnosci, kategoria, gabaryty_produktu) 
                  VALUES ('$tytul', '$opis', NOW(), NOW(), $data_wygasniecia, $cena_netto, $podatek_vat, $ilosc, $status, $kategoria, '$gabaryty')";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=produkty";</script>';
        return;
    }

    echo '<form method="post" action="">
        <table>
        <tr><td>Tytuł:</td><td><input type="text" name="tytul" required style="width:300px;"></td></tr>
        <tr><td>Opis:</td><td><textarea name="opis" rows="4" style="width:300px;"></textarea></td></tr>
        <tr><td>Cena netto (zł):</td><td><input type="number" name="cena_netto" step="0.01" required></td></tr>
        <tr><td>Podatek VAT (%):</td><td><input type="number" name="podatek_vat" value="23" step="0.01"></td></tr>
        <tr><td>Ilość w magazynie:</td><td><input type="number" name="ilosc" value="0"></td></tr>
        <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia"></td></tr>
        <tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" value="0"></td></tr>
        <tr><td>Gabaryty:</td><td><input type="text" name="gabaryty" placeholder="np. 10x20x30 cm"></td></tr>
        <tr><td>Dostępny:</td><td><input type="checkbox" name="status_dostepnosci" checked></td></tr>
        <tr><td></td><td><input type="submit" name="dodaj_produkt_submit" value="Dodaj produkt"></td></tr>
        </table>
    </form>';
}

function EdytujProdukt()
{
    global $link;

    if (!isset($_GET['id'])) {
        echo 'Brak ID produktu';
        return;
    }

    $id = intval($_GET['id']);

    if (isset($_POST['edytuj_produkt_submit'])) {
        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $opis = mysqli_real_escape_string($link, $_POST['opis']);
        $cena_netto = floatval($_POST['cena_netto']);
        $podatek_vat = floatval($_POST['podatek_vat']);
        $ilosc = intval($_POST['ilosc']);
        $status = isset($_POST['status_dostepnosci']) ? 1 : 0;
        $kategoria = intval($_POST['kategoria']);
        $gabaryty = mysqli_real_escape_string($link, $_POST['gabaryty']);
        $data_wygasniecia = !empty($_POST['data_wygasniecia']) ? "'" . mysqli_real_escape_string($link, $_POST['data_wygasniecia']) . "'" : "NULL";

        $query = "UPDATE products SET tytul='$tytul', opis='$opis', data_modyfikacji=NOW(), data_wygasniecia=$data_wygasniecia, 
                  cena_netto=$cena_netto, podatek_vat=$podatek_vat, ilosc_dostepnych_sztuk_w_magazynie=$ilosc, 
                  status_dostepnosci=$status, kategoria=$kategoria, gabaryty_produktu='$gabaryty' WHERE id=$id";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=produkty";</script>';
        return;
    }

    $query = "SELECT * FROM products WHERE id=$id LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);

    if (!$row) {
        echo 'Produkt nie znaleziony';
        return;
    }

    $data_wyg = !empty($row['data_wygasniecia']) ? date('Y-m-d\TH:i', strtotime($row['data_wygasniecia'])) : '';

    echo '<h3>Edytuj Produkt</h3>';
    echo '<a href="admin.php?funkcja=produkty">&laquo; Powrót do listy</a><br /><br />';

    echo '<form method="post" action="">
        <table>
        <tr><td>Tytuł:</td><td><input type="text" name="tytul" value="' . htmlspecialchars($row['tytul']) . '" required style="width:300px;"></td></tr>
        <tr><td>Opis:</td><td><textarea name="opis" rows="4" style="width:300px;">' . htmlspecialchars($row['opis']) . '</textarea></td></tr>
        <tr><td>Cena netto (zł):</td><td><input type="number" name="cena_netto" step="0.01" value="' . $row['cena_netto'] . '" required></td></tr>
        <tr><td>Podatek VAT (%):</td><td><input type="number" name="podatek_vat" step="0.01" value="' . $row['podatek_vat'] . '"></td></tr>
        <tr><td>Ilość w magazynie:</td><td><input type="number" name="ilosc" value="' . $row['ilosc_dostepnych_sztuk_w_magazynie'] . '"></td></tr>
        <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia" value="' . $data_wyg . '"></td></tr>
        <tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" value="' . $row['kategoria'] . '"></td></tr>
        <tr><td>Gabaryty:</td><td><input type="text" name="gabaryty" value="' . htmlspecialchars($row['gabaryty_produktu']) . '"></td></tr>
        <tr><td>Dostępny:</td><td><input type="checkbox" name="status_dostepnosci" ' . ($row['status_dostepnosci'] == 1 ? 'checked' : '') . '></td></tr>
        <tr><td></td><td><input type="submit" name="edytuj_produkt_submit" value="Zapisz zmiany"></td></tr>
        </table>
    </form>';
}

function UsunProdukt()
{
    global $link;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $query = "DELETE FROM products WHERE id=$id LIMIT 1";
        mysqli_query($link, $query);
        echo '<script>window.location.href="admin.php?funkcja=produkty";</script>';
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
        } elseif ($_GET['funkcja'] == 'produkty') {
            PokazProdukty();
        } elseif ($_GET['funkcja'] == 'dodaj_produkt') {
            DodajProdukt();
        } elseif ($_GET['funkcja'] == 'edytuj_produkt') {
            EdytujProdukt();
        } elseif ($_GET['funkcja'] == 'usun_produkt') {
            UsunProdukt();
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
