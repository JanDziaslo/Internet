<?php
$polaczenie = mysqli_connect('localhost', 'root', '', 'pogoda');

$zapytanie1 = "SELECT m.nazwa AS miasto, m.kraj AS kraj, p.temperatura AS temperatura FROM miejscowosc m JOIN pomiary p ON m.id = p.id_miejscowosc WHERE p.id_miesiac = 7;";
$wynik1 = mysqli_query($polaczenie, $zapytanie1);




?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Pogoda</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>

    <header>
        <div id="naglowek1">
            <img src="slonce.png" alt="Słonecznie">
        </div>

        <div id="naglowek2">
            <h1>Pogoda w Europie</h1>
        </div>
    </header>

    <main>
        <section id="lewa">
            <h2>Temperatury w lipcu</h2>
            <table>
                <tr>
                    <th>Miasto</th>
                    <th>Kraj</th>
                    <th>Temperatura</th>
                    <th>Pogoda</th>
                </tr>
                    <?php
                        while ($wiersz = mysqli_fetch_array($wynik1)) {
                            echo '<tr>';
                            echo '<td>' . $wiersz['miasto']. '</td>';
                            echo '<td>'. $wiersz['kraj'] . '</td>';
                            echo '<td>'. $wiersz['temperatura'] . '</td>';
                            if ($wiersz['temperatura'] > 30) {
                                echo '<td>';
                                echo '<img src="slonce.png" alt="slonce">';
                                echo '</td>';
                            }
                            elseif ($wiersz['temperatura']< 26) {
                                echo '<td>';
                                echo '<img src="deszcz.png" alt="deszcz">';
                                echo '</td>';
                            }
                            else {
                                echo '<td>';
                                echo '<img src="chmurry.png" alt="chmury">';
                                echo '</td>';
                            }
                            echo '</tr>';
                        }

                    ?>
            </table>
        </section>
        <img src="" alt="">
        <section id="prawy">
            <h2>Średnie temperatury w roku</h2>
            <a href="index.php?id=1">Styczeń</a>
            <a href="index.php?id=2">Luty</a>
            <a href="index.php?id=3">Marzec</a>
            <a href="index.php?id=4">Kwiecień</a>
            <a href="index.php?id=5">Maj</a>
            <a href="index.php?id=6">Czerwiec</a>
            <a href="index.php?id=7">Lipiec</a>
            <a href="index.php?id=8">Sierpień</a>
            <a href="index.php?id=9">Wrzesień</a>
            <a href="index.php?id=10">Pażdziernik</a>
            <a href="index.php?id=11">Listopad</a>
            <a href="index.php?id=12">Grudzień</a>
            <p>Srednia temperatura dla wybranego miesiaca wynosi</p>

            <?php

            if (isset($_GET['id'])) {
                $id= $_GET['id'];

                $zapytanie2 = "SELECT ROUND(AVG(temperatura), 2) AS nigger FROM pomiary WHERE id_miesiac =".  $id;
                $wynik2 = mysqli_query($polaczenie, $zapytanie2);


                foreach ($wynik2 as $wiersz) {
                    echo '<p>' . $wiersz['nigger'] . ' stopni </p>';
                }
            }
            ?>
        </section>
    </main>

    <footer>
        <p>Numer zdającego: e53e583182e6464cfb635d8fe8d20c8f0a3256ad</p>
    </footer>
</body>
</html>
<?php
mysqli_close($polaczenie);
?>