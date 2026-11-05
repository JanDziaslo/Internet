<?php
$polaczenie = mysqli_connect('192.168.51.192', 'root', '', 'matura');

$zapytanie2 = 'SELECT DISTINCT przedmiot FROM arkusz;';
$wynik2 = mysqli_query($polaczenie,$zapytanie2);

$zapytanie3 = 'SELECT MIN(rok) as najstarszy, MAX(rok) as najmloddszy from arkusz;';
$wynik3 = mysqli_query($polaczenie, $zapytanie3);
?>



<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Matura</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <header>
        <h1>System informacyjna dla maturzystów</h1>
    </header>

    <aside>
        <img src="pliki3-2/ma.jpg" alt="Matura"><br>
        <img src="pliki3-2/tu.jpg" alt="Matura"><br>
        <img src="pliki3-2/ra.jpg" alt="Matura">
    </aside>

    <section id="pierwszy">
        <h3>Wybierz ucznia z listy</h3>
        <p>skrypt 3</p>
    </section>

    <section id="drugi">
        <div class="bloki">
            <h4>Przedmioty</h4>
            <?php
                while ($wiersz = mysqli_fetch_array($wynik2)) {
                    echo $wiersz['przedmiot'] . ' ';
                }
            ?>
        </div>

        <div class="bloki">
            <h4>Lata</h4>
        </div>

        <div class="bloki">
            <h4>Najlepszy wynik</h4>
        </div>

        <div class="bloki">
            <h4>Najgorszy wynik</h4>
        </div>
    </section>

    <footer>
        <p>Stronę wykonał: JanDiąsło</p>
    </footer>
</body>
</html>