<?php

# funkcje koszyka zakupowego

function addToCart($id_prod, $ile_sztuk = 1)
{
    if (!isset($_SESSION['count'])) {
        $_SESSION['count'] = 1;
    } else {
        $_SESSION['count']++;
    }

    $nr = $_SESSION['count'];
    $wielkosc = 'standard';

    $prod[$nr]['id_prod'] = $id_prod;
    $prod[$nr]['ile_sztuk'] = $ile_sztuk;
    $prod[$nr]['wielkosc'] = $wielkosc;
    $prod[$nr]['data'] = time();

    $nr_0 = $nr . '_0';
    $nr_1 = $nr . '_1';
    $nr_2 = $nr . '_2';
    $nr_3 = $nr . '_3';
    $nr_4 = $nr . '_4';

    $_SESSION[$nr_0] = $nr;
    $_SESSION[$nr_1] = $prod[$nr]['id_prod'];
    $_SESSION[$nr_2] = $prod[$nr]['ile_sztuk'];
    $_SESSION[$nr_3] = $prod[$nr]['wielkosc'];
    $_SESSION[$nr_4] = $prod[$nr]['data'];
}

function removeFromCart($nr)
{
    unset($_SESSION[$nr . '_0']);
    unset($_SESSION[$nr . '_1']);
    unset($_SESSION[$nr . '_2']);
    unset($_SESSION[$nr . '_3']);
    unset($_SESSION[$nr . '_4']);
}

# funkcja wyswietlajaca sklep z produktami 

function PokazSklep($link)
{
    if (isset($_POST['add_to_cart'])) {
        addToCart((int)$_POST['product_id'], 1);
        echo '<div style="background: rgba(16, 168, 163, 0.1); border: 1px solid #0c7976; color: #0c7976; padding: 10px; margin: 10px 0; text-align: center; border-radius: 5px;">Produkt dodany do koszyka!</div>';
    }

    $dzisiaj = date('Y-m-d H:i:s');

    // Pobieranie produktów
    $query = "SELECT p.*, c.nazwa as nazwa_kategorii 
              FROM products p 
              LEFT JOIN categories c ON p.kategoria = c.id 
              WHERE p.status_dostepnosci = 1 AND p.ilosc_dostepnych_sztuk_w_magazynie > 0 
              AND (p.data_wygasniecia IS NULL OR p.data_wygasniecia > '$dzisiaj' OR p.data_wygasniecia = '0000-00-00 00:00:00')
              ORDER BY p.id DESC LIMIT 100";
    $result = mysqli_query($link, $query);

    $view = '
    <div style="max-width: 1000px; margin: 0 auto; font-family: sans-serif;">
        <h2 style="border-bottom: 2px solid #0c7976; color: #0c7976; padding-bottom: 10px;">Oferta Sprzedaży</h2>
        
        <div style="text-align: right; margin-bottom: 20px;">
            <a href="index.php?id=cart" style="text-decoration: none; background: #10a8a3; color: white; font-weight: bold; border: none; padding: 8px 15px; border-radius: 5px; transition: 0.3s;">Przejdź do koszyka &rarr;</a>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(255,255,255,0.8); border-radius: 10px; overflow: hidden;">
            <thead style="background-color: #0c7976; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Obraz</th>
                    <th style="padding: 12px; text-align: left;">Nazwa Produktu</th>
                    <th style="padding: 12px; text-align: left;">Kategoria</th>
                    <th style="padding: 12px; text-align: right;">Cena</th>
                    <th style="padding: 12px; text-align: center;">Dostępność</th>
                    <th style="padding: 12px; text-align: center;">Opcje</th>
                </tr>
            </thead>
            <tbody>';

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $cena_brutto = $row['cena_netto'] * (1 + $row['podatek_vat'] / 100);

            # wyswietl zdjecie z BLOB lub placeholder
            if (!empty($row['zdjecie'])) {
                $img_src = 'data:image/jpeg;base64,' . base64_encode($row['zdjecie']);
            } else {
                $img_src = 'https://via.placeholder.com/50?text=--';
            }

            $view .= '
            <tr style="border-bottom: 1px solid #ddd; transition: background 0.2s;">
                <td style="padding: 12px;"><img src="' . $img_src . '" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ccc;"></td>
                <td style="padding: 12px; font-weight: bold; color: #333;">' . htmlspecialchars($row['tytul']) . '</td>
                <td style="padding: 12px; color: #666; font-style: italic;">' . htmlspecialchars($row['nazwa_kategorii'] ?? 'Inne') . '</td>
                <td style="padding: 12px; text-align: right; color: #0c7976; font-weight: bold;">' . number_format($cena_brutto, 2) . ' zł</td>
                <td style="padding: 12px; text-align: center;">' . $row['ilosc_dostepnych_sztuk_w_magazynie'] . ' szt.</td>
                <td style="padding: 12px; text-align: center;">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="product_id" value="' . $row['id'] . '">
                        <button type="submit" name="add_to_cart" style="background: #10a8a3; border: none; color: white; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-size: 0.9em; font-weight: bold;">+ Dodaj</button>
                    </form>
                </td>
            </tr>';
        }
    } else {
        $view .= '<tr><td colspan="6" style="padding: 20px; text-align: center; color: #777;">Aktualnie brak produktów w ofercie.</td></tr>';
    }

    $view .= '</tbody></table></div>';
    return $view;
}

# funkcja wyswietlajaca zawartosc koszyka 

function PokazZawartoscKoszyka($link)
{
    if (isset($_GET['remove'])) {
        removeFromCart((int)$_GET['remove']);
        header('Location: index.php?id=cart');
        exit;
    }

    $wynik = '
    <div style="max-width: 800px; margin: 0 auto; font-family: sans-serif;">
        <h2 style="color: #0c7976; border-bottom: 1px solid #10a8a3; padding-bottom: 10px;">Koszyk Zamówień</h2>';

    $suma_brutto_calosc = 0;
    $czy_pusty = true;

    if (isset($_SESSION['count']) && $_SESSION['count'] > 0) {
        $tabela = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(255,255,255,0.8); border-radius: 10px; overflow: hidden;">
                    <thead>
                        <tr style="background-color: #0c7976; color: white;">
                            <th style="padding: 12px; text-align: left;">Produkt</th>
                            <th style="padding: 12px; text-align: right;">Cena jedn.</th>
                            <th style="padding: 12px; text-align: center;">Ilość</th>
                            <th style="padding: 12px; text-align: right;">Suma</th>
                            <th style="padding: 12px; text-align: center;">Ustawienia</th>
                        </tr>
                    </thead>
                    <tbody>';

        for ($i = 1; $i <= $_SESSION['count']; $i++) {
            if (isset($_SESSION[$i . '_1'])) {
                $czy_pusty = false;
                $id_p = $_SESSION[$i . '_1'];
                $ile = $_SESSION[$i . '_2'];

                $res = mysqli_query($link, "SELECT tytul, cena_netto, podatek_vat FROM products WHERE id=$id_p LIMIT 1");
                $p = mysqli_fetch_array($res);

                if ($p) {
                    $cena_b = $p['cena_netto'] * (1 + $p['podatek_vat'] / 100);
                    $wartosc = $cena_b * $ile;
                    $suma_brutto_calosc += $wartosc;

                    $tabela .= "
                    <tr style='border-bottom: 1px solid #f0f0f0;'>
                        <td style='padding: 12px; color: #333;'>{$p['tytul']}</td>
                        <td style='padding: 12px; text-align: right;'>" . number_format($cena_b, 2) . " zł</td>
                        <td style='padding: 12px; text-align: center;'>$ile</td>
                        <td style='padding: 12px; text-align: right; font-weight: bold; color: #0c7976;'>" . number_format($wartosc, 2) . " zł</td>
                        <td style='padding: 12px; text-align: center;'>
                            <a href='index.php?id=cart&remove=$i' style='color: #d32f2f; text-decoration: none; font-size: 0.9em; font-weight: bold;'>[usuń]</a>
                        </td>
                    </tr>";
                }
            }
        }
        $tabela .= "
            <tr style='background-color: rgba(16, 168, 163, 0.1);'>
                <td colspan='3' style='padding: 15px; text-align: right; font-weight: bold; color: #0c7976;'>Łączna kwota do zapłaty:</td>
                <td style='padding: 15px; text-align: right; font-size: 1.2em; color: #0c7976; font-weight: bold;'>" . number_format($suma_brutto_calosc, 2) . " zł</td>
                <td></td>
            </tr>
        </tbody></table>";

        $tabela .= '
        <div style="margin-top: 30px; text-align: right;">
            <a href="index.php?id=shop" style="text-decoration: none; color: #0c7976; margin-right: 20px; font-weight: bold;">&larr; Wróć do zakupów</a>
            <button style="background: #0c7976; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 1em; font-weight: bold;">Przejdź do kasy</button>
        </div>';
    }

    if ($czy_pusty) {
        return $wynik . '<p style="padding: 20px; color: #777;">Twój koszyk jest pusty.</p>
                         <p><a href="index.php?id=shop" style="color: #0c7976; font-weight: bold;">Przejdź do sklepu</a></p></div>';
    }

    return $wynik . $tabela . '</div>';
}
