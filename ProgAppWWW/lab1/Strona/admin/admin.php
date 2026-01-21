<?php
session_start();
include('../cfg.php');

# Funkcja logowania do panelu admina
function FormularzLogowania()
{
    $wynik = '
        <div style="max-width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
            <h2 style="margin-top: 0;">Panel CMS</h2>
            <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
                <p><label>Login:<br><input type="text" name="login_email" style="width: 100%; padding: 5px;"></label></p>
                <p><label>Hasło:<br><input type="password" name="login_pass" style="width: 100%; padding: 5px;"></label></p>
                <p><input type="submit" name="xl_submit" value="Zaloguj" style="padding: 8px 20px;"></p>
            </form>
        </div>
    ';
    return $wynik;
}

# --- FUNKCJE ZARZĄDZANIA PODSTRONAMI ---

function ListaPodstron($link)
{
    echo '<h2>Lista Podstron</h2>';
    echo '<p><a href="admin.php?action=add">[+ Dodaj nową podstronę]</a></p>';
    $query = "SELECT id, page_title, status FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Status</th><th>Akcje</th></tr>";
    while ($row = mysqli_fetch_array($result)) {
        $status = $row['status'] == 1 ? 'Aktywna' : 'Nieaktywna';
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['page_title'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td><a href='admin.php?action=edit&id=" . $row['id'] . "'>Edytuj</a> | <a href='admin.php?action=delete&id=" . $row['id'] . "' onclick=\"return confirm('Usunąć?');\">Usuń</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

function EdytujPodstrone($link, $id)
{
    $id = (int)$id;
    $result = mysqli_query($link, "SELECT * FROM page_list WHERE id = $id LIMIT 1");
    $data = mysqli_fetch_array($result);
    if (!$data) return '<p style="color: red;">Nie znaleziono podstrony.</p>';

    return '
        <h2>Edycja podstrony: ' . htmlspecialchars($data['page_title']) . '</h2>
        <form method="post" action="admin.php?action=edit&id=' . $id . '">
            <input type="hidden" name="id_edycji" value="' . $id . '">
            <p><label>Tytuł:<br><input type="text" name="page_title" value="' . htmlspecialchars($data['page_title']) . '" style="width: 400px;"></label></p>
            <p><label>Treść:<br><textarea name="page_content" rows="10" cols="60">' . htmlspecialchars($data['page_content']) . '</textarea></label></p>
            <p><label><input type="checkbox" name="status" value="1" ' . ($data['status'] == 1 ? 'checked' : '') . '> Aktywna</label></p>
            <p><input type="submit" name="edit_submit" value="Zapisz"></p>
        </form>
        <p><a href="admin.php">&larr; Powrót do listy</a></p>';
}

function DodajNowaPodstrone()
{
    return '
        <h2>Dodaj nową podstronę</h2>
        <form method="post" action="admin.php?action=add">
            <p><label>Tytuł:<br><input type="text" name="page_title" style="width: 400px;"></label></p>
            <p><label>Treść:<br><textarea name="page_content" rows="10" cols="60"></textarea></label></p>
            <p><label><input type="checkbox" name="status" value="1" checked> Aktywna</label></p>
            <p><input type="submit" name="add_submit" value="Dodaj"></p>
        </form>
        <p><a href="admin.php">&larr; Powrót do listy</a></p>';
}

function UsunPodstrone($link, $id)
{
    $id = (int)$id;
    if (mysqli_query($link, "DELETE FROM page_list WHERE id = $id LIMIT 1")) {
        return '<p style="color: green;">Podstrona ID ' . $id . ' została usunięta.</p>';
    }
    return '<p style="color: red;">Błąd usuwania.</p>';
}

# --- FUNKCJE ZARZĄDZANIA KATEGORIAMI ---

function PokazKategorie($link)
{
    echo '<h2>Zarządzanie Kategoriami</h2>';
    echo '<p><a href="admin.php?action=add_cat">[+ Dodaj kategorię]</a></p>';

    $query = "SELECT * FROM categories WHERE matka = 0 ORDER BY id ASC LIMIT 50";
    $result = mysqli_query($link, $query);

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Nazwa</th><th>Podkategorie</th><th>Akcje</th></tr>";

    while ($matka = mysqli_fetch_array($result)) {
        $id_matki = $matka['id'];
        $query_dzieci = "SELECT COUNT(*) as cnt FROM categories WHERE matka = $id_matki";
        $cnt_result = mysqli_query($link, $query_dzieci);
        $cnt = mysqli_fetch_array($cnt_result)['cnt'];

        echo "<tr>";
        echo "<td>" . $matka['id'] . "</td>";
        echo "<td>" . $matka['nazwa'] . "</td>";
        echo "<td>" . $cnt . "</td>";
        echo "<td><a href='admin.php?action=edit_cat&id=" . $matka['id'] . "'>Edytuj</a> | <a href='admin.php?action=delete_cat&id=" . $matka['id'] . "' onclick=\"return confirm('Usunąć kategorię?');\">Usuń</a></td>";
        echo "</tr>";

        # Podkategorie
        $query_dzieci = "SELECT * FROM categories WHERE matka = $id_matki ORDER BY id ASC LIMIT 50";
        $result_dzieci = mysqli_query($link, $query_dzieci);
        while ($dziecko = mysqli_fetch_array($result_dzieci)) {
            echo "<tr style='background: #f5f5f5;'>";
            echo "<td style='padding-left: 20px;'>&rarr; " . $dziecko['id'] . "</td>";
            echo "<td>" . $dziecko['nazwa'] . "</td>";
            echo "<td>-</td>";
            echo "<td><a href='admin.php?action=edit_cat&id=" . $dziecko['id'] . "'>Edytuj</a> | <a href='admin.php?action=delete_cat&id=" . $dziecko['id'] . "'>Usuń</a></td>";
            echo "</tr>";
        }
    }
    echo "</table>";
}

function DodajKategorie($link)
{
    $query = "SELECT id, nazwa FROM categories WHERE matka = 0 LIMIT 50";
    $result = mysqli_query($link, $query);
    $options = '<option value="0">-- Brak (Kategoria główna) --</option>';
    while ($row = mysqli_fetch_array($result)) {
        $options .= '<option value="' . $row['id'] . '">' . $row['nazwa'] . '</option>';
    }
    return '
        <h2>Dodaj Kategorię</h2>
        <form method="post" action="admin.php?action=add_cat">
            <p><label>Nazwa:<br><input type="text" name="cat_nazwa" style="width: 300px;"></label></p>
            <p><label>Kategoria nadrzędna:<br><select name="cat_matka">' . $options . '</select></label></p>
            <p><input type="submit" name="cat_add_submit" value="Dodaj"></p>
        </form>
        <p><a href="admin.php?action=show_cats">&larr; Powrót do kategorii</a></p>';
}

function EdytujKategorie($link, $id)
{
    $id = (int)$id;
    $row = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM categories WHERE id = $id LIMIT 1"));
    if (!$row) return '<p style="color: red;">Nie znaleziono kategorii.</p>';

    $query = "SELECT id, nazwa FROM categories WHERE matka = 0 AND id != $id LIMIT 50";
    $result = mysqli_query($link, $query);
    $options = '<option value="0" ' . ($row['matka'] == 0 ? 'selected' : '') . '>-- Brak (Kategoria główna) --</option>';
    while ($opt = mysqli_fetch_array($result)) {
        $options .= '<option value="' . $opt['id'] . '" ' . ($row['matka'] == $opt['id'] ? 'selected' : '') . '>' . $opt['nazwa'] . '</option>';
    }

    return '
        <h2>Edytuj Kategorię: ' . $row['nazwa'] . '</h2>
        <form method="post" action="admin.php?action=edit_cat&id=' . $id . '">
            <p><label>Nazwa:<br><input type="text" name="cat_nazwa" value="' . $row['nazwa'] . '" style="width: 300px;"></label></p>
            <p><label>Kategoria nadrzędna:<br><select name="cat_matka">' . $options . '</select></label></p>
            <p><input type="submit" name="cat_edit_submit" value="Zapisz"></p>
        </form>
        <p><a href="admin.php?action=show_cats">&larr; Powrót do kategorii</a></p>';
}

# --- FUNKCJE ZARZĄDZANIA PRODUKTAMI ---

function SprawdzDostepnosc($produkt)
{
    $dzisiaj = date('Y-m-d H:i:s');
    if ($produkt['status_dostepnosci'] != 1) return 'Niedostępny';
    if ($produkt['ilosc_dostepnych_sztuk_w_magazynie'] <= 0) return 'Brak';
    if (!empty($produkt['data_wygasniecia']) && $produkt['data_wygasniecia'] < $dzisiaj) return 'Wygasł';
    return 'Dostępny';
}

function PokazProdukty($link)
{
    echo '<h2>Zarządzanie Produktami</h2>';
    echo '<p><a href="admin.php?action=add_prod">[+ Dodaj produkt]</a></p>';

    $query = "SELECT * FROM products ORDER BY id DESC LIMIT 100";
    $result = mysqli_query($link, $query);

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Cena netto</th><th>Ilość</th><th>Status</th><th>Akcje</th></tr>";

    while ($row = mysqli_fetch_array($result)) {
        $status = SprawdzDostepnosc($row);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['tytul'] . "</td>";
        echo "<td>" . number_format($row['cena_netto'], 2) . " zł</td>";
        echo "<td>" . $row['ilosc_dostepnych_sztuk_w_magazynie'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td><a href='admin.php?action=edit_prod&id=" . $row['id'] . "'>Edytuj</a> | <a href='admin.php?action=delete_prod&id=" . $row['id'] . "' onclick=\"return confirm('Usunąć produkt?');\">Usuń</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

function DodajProdukt($link)
{
    $kat_res = mysqli_query($link, "SELECT id, nazwa FROM categories ORDER BY nazwa ASC LIMIT 50");
    $opts = '<option value="0">-- Brak kategorii --</option>';
    while ($k = mysqli_fetch_array($kat_res)) {
        $opts .= '<option value="' . $k['id'] . '">' . $k['nazwa'] . '</option>';
    }
    return '
    <h2>Dodaj Produkt</h2>
    <form method="post" action="admin.php?action=add_prod" enctype="multipart/form-data">
        <table border="0" cellpadding="5">
            <tr><td>Tytuł:</td><td><input type="text" name="p_tytul" style="width: 300px;"></td></tr>
            <tr><td>Opis:</td><td><textarea name="p_opis" rows="4" cols="40"></textarea></td></tr>
            <tr><td>Cena netto (zł):</td><td><input type="number" step="0.01" name="p_cena"></td></tr>
            <tr><td>VAT (%):</td><td><input type="number" step="0.01" name="p_vat" value="23"></td></tr>
            <tr><td>Ilość w magazynie:</td><td><input type="number" name="p_ilosc" value="0"></td></tr>
            <tr><td>Kategoria:</td><td><select name="p_kat">' . $opts . '</select></td></tr>
            <tr><td>Gabaryty:</td><td><input type="text" name="p_gabaryty" placeholder="np. 10x20x30 cm"></td></tr>
            <tr><td>Zdjęcie:</td><td><input type="file" name="p_zdj_file" accept="image/*"></td></tr>
            <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="p_wyg"></td></tr>
            <tr><td>Status:</td><td><label><input type="checkbox" name="p_stat" value="1" checked> Aktywny</label></td></tr>
            <tr><td></td><td><input type="submit" name="submit_add_prod" value="Dodaj produkt"></td></tr>
        </table>
    </form>
    <p><a href="admin.php?action=show_prods">&larr; Powrót do produktów</a></p>';
}

function EdytujProdukt($link, $id)
{
    $id = (int)$id;
    $p = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM products WHERE id = $id LIMIT 1"));
    if (!$p) return '<p style="color: red;">Nie znaleziono produktu.</p>';

    $data_wyg = !empty($p['data_wygasniecia']) ? date('Y-m-d\TH:i', strtotime($p['data_wygasniecia'])) : '';

    $img_preview = '';
    if (!empty($p['zdjecie'])) {
        $img_preview = '<img src="data:image/jpeg;base64,' . base64_encode($p['zdjecie']) . '" style="max-width: 100px; max-height: 100px;"><br>';
    }

    return '
    <h2>Edytuj Produkt: ' . $p['tytul'] . '</h2>
    <form method="post" action="admin.php?action=edit_prod&id=' . $id . '" enctype="multipart/form-data">
        <table border="0" cellpadding="5">
            <tr><td>Tytuł:</td><td><input type="text" name="p_tytul" value="' . htmlspecialchars($p['tytul']) . '" style="width: 300px;"></td></tr>
            <tr><td>Opis:</td><td><textarea name="p_opis" rows="4" cols="40">' . htmlspecialchars($p['opis']) . '</textarea></td></tr>
            <tr><td>Cena netto (zł):</td><td><input type="number" step="0.01" name="p_cena" value="' . $p['cena_netto'] . '"></td></tr>
            <tr><td>VAT (%):</td><td><input type="number" step="0.01" name="p_vat" value="' . $p['podatek_vat'] . '"></td></tr>
            <tr><td>Ilość w magazynie:</td><td><input type="number" name="p_ilosc" value="' . $p['ilosc_dostepnych_sztuk_w_magazynie'] . '"></td></tr>
            <tr><td>Gabaryty:</td><td><input type="text" name="p_gabaryty" value="' . htmlspecialchars($p['gabaryty_produktu']) . '"></td></tr>
            <tr><td>Zdjęcie:</td><td>' . $img_preview . '<input type="file" name="p_zdj_file" accept="image/*"></td></tr>
            <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="p_wyg" value="' . $data_wyg . '"></td></tr>
            <tr><td>Status:</td><td><label><input type="checkbox" name="p_stat" value="1" ' . ($p['status_dostepnosci'] == 1 ? 'checked' : '') . '> Aktywny</label></td></tr>
            <tr><td></td><td><input type="submit" name="submit_edit_prod" value="Zapisz zmiany"></td></tr>
        </table>
    </form>
    <p><a href="admin.php?action=show_prods">&larr; Powrót do produktów</a></p>';
}

# --- GŁÓWNA LOGIKA PANELU ---

$error_message = '';
if (!isset($_SESSION['logged_in'])) {
    if (isset($_POST['xl_submit'])) {
        if (trim($_POST['login_email']) === $login && trim($_POST['login_pass']) === $pass) {
            $_SESSION['logged_in'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            $error_message = '<p style="color: red; text-align: center;">Błąd logowania - nieprawidłowe dane.</p>';
        }
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $content = '';

    # OBSŁUGA POST - Podstrony
    if (isset($_POST['edit_submit'])) {
        $id_up = (int)$_POST['id_edycji'];
        $t = mysqli_real_escape_string($link, $_POST['page_title']);
        $c = mysqli_real_escape_string($link, $_POST['page_content']);
        $s = isset($_POST['status']) ? 1 : 0;
        if (mysqli_query($link, "UPDATE page_list SET page_title='$t', page_content='$c', status=$s WHERE id=$id_up LIMIT 1")) {
            $content = '<p style="color: green;">Zaktualizowano podstronę.</p>';
            $action = '';
        }
    }
    if (isset($_POST['add_submit'])) {
        $t = mysqli_real_escape_string($link, $_POST['page_title']);
        $c = mysqli_real_escape_string($link, $_POST['page_content']);
        $s = isset($_POST['status']) ? 1 : 0;
        if (mysqli_query($link, "INSERT INTO page_list (page_title, page_content, status) VALUES ('$t', '$c', $s)")) {
            $content = '<p style="color: green;">Dodano podstronę.</p>';
            $action = '';
        }
    }

    # OBSŁUGA POST - Kategorie
    if (isset($_POST['cat_add_submit'])) {
        $n = mysqli_real_escape_string($link, $_POST['cat_nazwa']);
        $m = (int)$_POST['cat_matka'];
        if (mysqli_query($link, "INSERT INTO categories (nazwa, matka) VALUES ('$n', $m)")) {
            $content = '<p style="color: green;">Dodano kategorię.</p>';
            $action = 'show_cats';
        }
    }
    if (isset($_POST['cat_edit_submit'])) {
        $n = mysqli_real_escape_string($link, $_POST['cat_nazwa']);
        $m = (int)$_POST['cat_matka'];
        if (mysqli_query($link, "UPDATE categories SET nazwa='$n', matka=$m WHERE id=$id LIMIT 1")) {
            $content = '<p style="color: green;">Zaktualizowano kategorię.</p>';
            $action = 'show_cats';
        }
    }

    # OBSŁUGA POST - Produkty
    if (isset($_POST['submit_add_prod'])) {
        $t = mysqli_real_escape_string($link, $_POST['p_tytul']);
        $o = mysqli_real_escape_string($link, $_POST['p_opis']);
        $cn = (float)$_POST['p_cena'];
        $vat = (float)$_POST['p_vat'];
        $il = (int)$_POST['p_ilosc'];
        $ka = (int)$_POST['p_kat'];
        $gab = mysqli_real_escape_string($link, $_POST['p_gabaryty']);
        $dw = !empty($_POST['p_wyg']) ? "'" . mysqli_real_escape_string($link, $_POST['p_wyg']) . "'" : "NULL";
        $st = isset($_POST['p_stat']) ? 1 : 0;

        $zdjecie_blob = "NULL";
        if (isset($_FILES['p_zdj_file']) && $_FILES['p_zdj_file']['error'] == 0) {
            $zdjecie_blob = "'" . mysqli_real_escape_string($link, file_get_contents($_FILES['p_zdj_file']['tmp_name'])) . "'";
        }

        $q = "INSERT INTO products (tytul, opis, data_utworzenia, data_modyfikacji, data_wygasniecia, cena_netto, podatek_vat, ilosc_dostepnych_sztuk_w_magazynie, status_dostepnosci, kategoria, gabaryty_produktu, zdjecie) 
              VALUES ('$t', '$o', NOW(), NOW(), $dw, $cn, $vat, $il, $st, $ka, '$gab', $zdjecie_blob)";
        if (mysqli_query($link, $q)) {
            $content = '<p style="color: green;">Dodano produkt.</p>';
            $action = 'show_prods';
        }
    }

    if (isset($_POST['submit_edit_prod'])) {
        $t = mysqli_real_escape_string($link, $_POST['p_tytul']);
        $o = mysqli_real_escape_string($link, $_POST['p_opis']);
        $il = (int)$_POST['p_ilosc'];
        $cn = (float)$_POST['p_cena'];
        $vat = (float)$_POST['p_vat'];
        $gab = mysqli_real_escape_string($link, $_POST['p_gabaryty']);
        $dw = !empty($_POST['p_wyg']) ? "'" . mysqli_real_escape_string($link, $_POST['p_wyg']) . "'" : "NULL";
        $st = isset($_POST['p_stat']) ? 1 : 0;

        $sql_extra = "";
        if (isset($_FILES['p_zdj_file']) && $_FILES['p_zdj_file']['error'] == 0) {
            $zdjecie_blob = mysqli_real_escape_string($link, file_get_contents($_FILES['p_zdj_file']['tmp_name']));
            $sql_extra = ", zdjecie='$zdjecie_blob'";
        }

        $query_update = "UPDATE products SET tytul='$t', opis='$o', data_modyfikacji=NOW(), data_wygasniecia=$dw, cena_netto=$cn, podatek_vat=$vat, ilosc_dostepnych_sztuk_w_magazynie=$il, status_dostepnosci=$st, gabaryty_produktu='$gab' $sql_extra WHERE id=$id LIMIT 1";
        if (mysqli_query($link, $query_update)) {
            $content = '<p style="color: green;">Zaktualizowano produkt.</p>';
            $action = 'show_prods';
        }
    }

    # --- WIDOK ---
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Panel Admina</title></head><body style="font-family: Arial, sans-serif; margin: 20px;">';

    echo '<div style="margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">';
    echo '<h1>Panel Administracyjny</h1>';
    echo '<ul style="list-style-type: square; padding-left: 20px;">';
    echo '<li><a href="admin.php">Zarządzaj Podstronami</a></li>';
    echo '<li><a href="admin.php?action=show_cats">Zarządzaj Kategoriami</a></li>';
    echo '<li><a href="admin.php?action=show_prods">Zarządzaj Produktami</a></li>';
    echo '<li><a href="../index.php">Wróć do strony głównej</a></li>';
    echo '</ul>';
    echo '</div>';

    echo $content;

    if ($action === 'edit' && $id > 0) echo EdytujPodstrone($link, $id);
    elseif ($action === 'add') echo DodajNowaPodstrone();
    elseif ($action === 'delete' && $id > 0) {
        echo UsunPodstrone($link, $id);
        ListaPodstron($link);
    } elseif ($action === 'show_cats') PokazKategorie($link);
    elseif ($action === 'add_cat') echo DodajKategorie($link);
    elseif ($action === 'edit_cat' && $id > 0) echo EdytujKategorie($link, $id);
    elseif ($action === 'delete_cat' && $id > 0) {
        mysqli_query($link, "DELETE FROM categories WHERE id=$id OR matka=$id LIMIT 10");
        PokazKategorie($link);
    } elseif ($action === 'show_prods') PokazProdukty($link);
    elseif ($action === 'add_prod') echo DodajProdukt($link);
    elseif ($action === 'edit_prod' && $id > 0) echo EdytujProdukt($link, $id);
    elseif ($action === 'delete_prod' && $id > 0) {
        mysqli_query($link, "DELETE FROM products WHERE id=$id LIMIT 1");
        PokazProdukty($link);
    } else ListaPodstron($link);

    echo '</body></html>';
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Logowanie - Panel Admina</title></head><body style="font-family: Arial, sans-serif; background: #f0f0f0;">';
    echo $error_message;
    echo FormularzLogowania();
    echo '</body></html>';
}
