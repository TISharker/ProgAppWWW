<?php



function PokazKontakt()
{
    
    echo '
    <div class="form-container">
        <h2>Formularz kontaktowy</h2>
        <form action="" method="post">
            <p>
                <label for="email">Twój e-mail:</label><br>
                <input type="email" name="email" id="email" required>
            </p>
            <p>
                <label for="temat">Temat:</label><br>
                <input type="text" name="temat" id="temat" required>
            </p>
            <p>
                <label for="tresc">Treść wiadomości:</label><br>
                <textarea name="tresc" id="tresc" rows="5" required></textarea>
            </p>
            <p>
                <input type="submit" value="Wyślij wiadomość">
            </p>
        </form>
    </div>
    ';
}

function WyslijMailKontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt(); 
    } else {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['reciptient'] = $odbiorca; 

        $header = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <" . $mail['sender'] . ">\n";
        $header .= "X-Mailer: PRapwww mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\n";

        @mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

        echo '[wiadomosc_wyslana]';
    }
}

function PrzypomnijHaslo($odbiorca)
{
    
    
    $pass = "admin"; 
    
    $mail['subject'] = "Przypomnienie hasla";
    $mail['body'] = "Twoje hasło do panelu admina to: " . $pass;
    $mail['sender'] = "admin@mojastrona.pl";
    $mail['reciptient'] = $odbiorca;

    $header = "From: System Przypominania Hasla <" . $mail['sender'] . ">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
    $header .= "X-Sender: <" . $mail['sender'] . ">\n";
    $header .= "X-Mailer: PRapwww mail 1.2\n";
    $header .= "X-Priority: 3\n";
    $header .= "Return-Path: <" . $mail['sender'] . ">\n";

    @mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

    echo '[haslo_wyslane]';
}

// --- ZASTĄP KOŃCÓWKĘ PLIKU contact.php TYM KODEM ---

// 1. Sprawdzamy, czy ktoś chce przypomnieć hasło (kliknął w link z panelu admina)
if (isset($_GET['akcja']) && $_GET['akcja'] == 'przypomnij') {
    PrzypomnijHaslo("admin@twojadomena.pl"); // Tutaj wpisz swój mail admina
}
// 2. Sprawdzamy, czy wysłano formularz kontaktowy
elseif (isset($_POST['temat'])) {
    WyslijMailKontakt("twoj_adres@email.com"); 
}
// 3. W przeciwnym razie pokazujemy formularz
else {
    PokazKontakt();
}
?>

