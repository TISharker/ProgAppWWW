<?php
// Plik: admin.php
session_start(); 
include('cfg.php');

// --- Formularz Logowania ---
function FormularzLogowania() {
    $wynik = '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
            <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
                <table class="logowanie">
                    <tr><td class="log4_t">Login:</td><td><input type="text" name="login_email" /></td></tr>
                    <tr><td class="log4_t">Hasło:</td><td><input type="password" name="login_pass" /></td></tr>
                    <tr><td></td><td><input type="submit" name="x1_submit" value="Zaloguj" /></td></tr>
                </table>
            </form>
    </div>
    ';
    return $wynik;
}

// --- Lista Podstron ---
function ListaPodstron() {
    global $link;
    $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);
    
    echo '<style>table, td, th { border: 1px solid black; border-collapse: collapse; padding: 5px; }</style>';
    echo '<h3>Lista Podstron</h3><table>';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Edycja</th><th>Usuwanie</th></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['page_title'] . '</td>';
        echo '<td><a href="admin.php?funkcja=edytuj&id='.$row['id'].'">Edytuj</a></td>';
        echo '<td><a href="admin.php?funkcja=usun&id='.$row['id'].'">Usuń</a></td>';
        echo '</tr>';
    }
    echo '</table><br><a href="admin.php?funkcja=dodaj">Dodaj nową podstronę</a> | <a href="index.php">Wróć na stronę</a>';
}

// --- Edycja Podstrony ---
function EdytujPodstrone() {
    global $link;
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $query = "SELECT * FROM page_list WHERE id='$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        
        echo '<h3>Edytuj Podstronę</h3>';
        echo '<form method="post" action="">';
        echo 'Tytuł: <input type="text" name="page_title" size="50" value="'.$row['page_title'].'"><br><br>';
        echo 'Treść: <textarea name="page_content" rows="20" cols="100">'.$row['page_content'].'</textarea><br>';
        echo 'Aktywna: <input type="checkbox" name="status" '.($row['status']==1 ? 'checked' : '').'><br>';
        echo '<input type="submit" name="edytuj_submit" value="Zapisz zmiany">';
        echo '</form>';
    }
    
    if (isset($_POST['edytuj_submit'])) {
        $id = htmlspecialchars($_GET['id']);
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;
        
        // Escape stringów dla bezpieczeństwa (mysqli)
        $title = mysqli_real_escape_string($link, $title);
        $content = mysqli_real_escape_string($link, $content);

        $query = "UPDATE page_list SET page_title='$title', page_content='$content', status=$status WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

// --- Dodawanie Podstrony ---
function DodajNowaPodstrone() {
    global $link;
    echo '<h3>Dodaj Nową Podstronę</h3>';
    echo '<form method="post" action="">';
    echo 'Tytuł: <input type="text" name="page_title" size="50"><br><br>';
    echo 'Treść: <textarea name="page_content" rows="20" cols="100"></textarea><br>';
    echo 'Aktywna: <input type="checkbox" name="status" checked><br>';
    echo '<input type="submit" name="dodaj_submit" value="Dodaj">';
    echo '</form>';
    
    if (isset($_POST['dodaj_submit'])) {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $title = mysqli_real_escape_string($link, $title);
        $content = mysqli_real_escape_string($link, $content);
        
        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', $status)";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

// --- Usuwanie Podstrony ---
function UsunPodstrone() {
    global $link;
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $query = "DELETE FROM page_list WHERE id='$id' LIMIT 1";
        mysqli_query($link, $query);
        header("Location: admin.php");
    }
}

// --- Logika Główna ---
if (isset($_POST['x1_submit'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['loggedin'] = true;
    } else {
        echo "Błąd logowania.<br>";
        echo FormularzLogowania();
    }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // Routing funkcji
    if (isset($_GET['funkcja'])) {
        if ($_GET['funkcja'] == 'usun') {
            UsunPodstrone();
        } elseif ($_GET['funkcja'] == 'edytuj') {
            EdytujPodstrone();
        } elseif ($_GET['funkcja'] == 'dodaj') {
            DodajNowaPodstrone();
        } else {
            ListaPodstron();
        }
    } else {
        ListaPodstron();
    }
} else {
    if (!isset($_POST['x1_submit'])) {
        echo FormularzLogowania();
    }
}
?>