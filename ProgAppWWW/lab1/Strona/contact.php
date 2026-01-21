<?php

# funkcja pokazujaca na jaki mail wysylamy wiadomosc, uzwa WyslijMailKontak($odbiorca)

function PokazKontakt()
{
    global $admin_email;

    $wynik = '
        <h2 class="heading">Formularz Kontaktowy</h2>
        <form method="post" action="index.php?id=contact">
            <div class="form-group">
                <label for="email">e-mail:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            <div class="form-group">
                <label for="temat">Temat:</label>
                <input type="text" id="temat" name="temat" required class="form-control">
            </div>
            <div class="form-group">
                <label for="tresc">Treść:</label>
                <textarea id="tresc" name="tresc" rows="6" required class="form-control"></textarea>
            </div>
            <button type="submit" name="kontakt_submit" class="form-submit-btn">Wyślij Wiadomość</button>
        </form>
    ';
    if (isset($_POST['kontakt_submit'])) {
        $odbiorca = isset($admin_email) ? $admin_email : 'admin@mojastrona.pl';
        return WyslijMailKontakt($odbiorca);
    }
    return $wynik;
}

# funkcja wysylajaca maila z poziomu strony

function WyslijMailKontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        return '<p style="color: red;">[nie_wypelniles_pola]: Musisz wypełnić wszystkie pola przed wysłaniem wiadomości.</p>' . PokazKontakt();
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

        return '<p style="color: green;">[wiadomosc_wyslana]: Twoja wiadomość została wysłana pomyślnie.</p>';
    }
}

# funkcja przypominajaca haslo - akceptuje login LUB email admina

function PrzypomnijHaslo()
{
    global $login, $pass, $admin_email;

    $form_haslo = '
        <h2 class="heading">Przypomnij Hasło</h2>
        <form method="post" action="index.php?id=forgot_pass">
            <div class="form-group">
                <label for="login_admin">Podaj login lub e-mail administratora:</label>
                <input type="text" id="login_admin" name="login_admin" required class="form-control">
            </div>
            <button type="submit" name="przypomnij_submit" class="form-submit-btn">Wyślij Hasło</button>
        </form>
    ';

    if (isset($_POST['przypomnij_submit'])) {
        $podany_login = trim($_POST['login_admin']);

        // Sprawdź czy podano prawidłowy login lub email admina
        if ($podany_login === $login || $podany_login === $admin_email) {

            $mail_subject = 'Przypomnienie hasła do Panelu Administracyjnego CMS';
            $mail_body = "Twoje hasło do panelu administratora to: " . $pass . "\n\nZalecana jest zmiana hasła po zalogowaniu.";
            $mail_sender = "noreply@mojastrona.pl";

            $header = "From: Przypominanie hasła <" . $mail_sender . ">\n";
            $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
            $header .= "X-Sender: <" . $mail_sender . ">\n";
            $header .= "X-Mailer: PRapwww mail 1.2\n";
            $header .= "X-Priority: 3\n";
            $header .= "Return-Path: <" . $mail_sender . ">\n";

            @mail($admin_email, $mail_subject, $mail_body, $header);

            return '<p style="color: green;">Wysłano hasło na adres e-mail administratora: ' . htmlspecialchars($admin_email) . '</p>';
        } else {
            return '<p style="color: red;">Podany login lub e-mail nie jest powiązany z kontem administratora.</p>' . $form_haslo;
        }
    }

    return $form_haslo;
}
