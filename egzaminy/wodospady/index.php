<?php

$polaczenie = mysqli_connect("mysql", "root", "", "wodospady", 3306);

$zapytanie = 'SELECT * FROM kontynenty;';

$wynik = mysqli_query($polaczenie, $zapytanie);

if (isset($_GET['id']))
{
    $idkontynent = (int) $_GET['id'];
    $zapytanie2 = "SELECT idObiekt, panstwo,nazwa, wartoscCechy FROM obiekty WHERE idRodzaj= 10 AND idKontynent=$idkontynent;";
    $wynik2 = mysqli_query($polaczenie, $zapytanie2);
}
else
{
    $zapytanie2 = 'SELECT idObiekt, panstwo,nazwa, wartoscCechy FROM obiekty WHERE idRodzaj= 10 AND idKontynent=6;';
    $wynik2 = mysqli_query($polaczenie, $zapytanie2);
}

?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wodospady</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <header>
        <h2>Łowcy wodospadów</h2>
    </header>

    <main>
        <aside>
            <?php

            while ($wiersz = mysqli_fetch_array($wynik)) {
                echo '<a href="index.php?id=' . $wiersz['idKontynent'] . '">' . $wiersz['nazwa'] . '</a>';
            }

            ?>
        </aside>
        <section>
            <table>
                <tr>
                    <td>Indentyfikator</td>
                    <td>Państwo</td>
                    <td>Nazwa wodospadu</td>
                    <td>Wysokość</td>
                </tr>
                <?php
                while ($wiersz = mysqli_fetch_array($wynik2))
                {
                    echo '<tr>';
                    echo '<th>'. $wiersz['idObiekt']. '</th>';
                    echo '<th>' . $wiersz['panstwo'] . '</th>';
                    echo '<th>' . $wiersz['nazwa'] . '</th>';
                    echo '<th>' . $wiersz['wartoscCechy'] . '</th>';
                    echo '</tr>';
                }

                ?>
            </table>
            <h4>Wpisz osiągnięcie do bazy</h4>
            <form action="index.php" method="post">
                <label for="id">identyfikator wodospadu</label>
                <input type="number" name="id" id="id">
                <label for="turysta">turysta</label>
                <select name="turysta" id="turysta">
                    <option value="">Skrypt 3</option>
                </select>
                <button type="submit">Wpisz</button>
            </form>
        </section>
    </main>

    <article>
        <h3>Wodospady w Polsce</h3>
        <img src="pliki2/kamienczyk.jpg" alt="wodospady">
        <img src="siklawica.jpg" alt="wodospady">
        <img src="pliki2/siklawa.jpg" alt="wodospady">
        <img src="pliki2/wilczki.jpg" alt="wodospady">
    </article>

    <footer>
        <p>Autor: Jan Dziąsło</p>
    </footer>
</body>
</html>