<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <title>Pracownicy</title>
</head>
<body>

<?php

if(isset($_POST['submit']) && $_POST['search']!=''){
    $stmt = $pdo->prepare("SELECT p.*, z.NAZWA AS NAZWA_ZESPOLU, sz.IMIE AS IMIE_SZEFA, sz.NAZWISKO AS NAZWISKO_SZEFA
                           FROM pracownicy p
                           LEFT JOIN zespoly z ON p.ID_ZESP = z.ID_ZESP
                           LEFT JOIN pracownicy sz ON p.ID_SZEFA = sz.ID_PRAC
                           WHERE p.IMIE LIKE :szukaj OR p.NAZWISKO LIKE :szukaj OR sz.IMIE LIKE :szukaj OR sz.NAZWISKO LIKE :szukaj");
    $stmt -> bindValue(':szukaj', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
}else{
    $stmt = $pdo->query('SELECT p.*, z.NAZWA AS NAZWA_ZESPOLU, sz.IMIE AS IMIE_SZEFA, sz.NAZWISKO AS NAZWISKO_SZEFA
                         FROM pracownicy p
                         LEFT JOIN zespoly z ON p.ID_ZESP = z.ID_ZESP
                         LEFT JOIN pracownicy sz ON p.ID_SZEFA = sz.ID_PRAC');
}

?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Pracownicy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="etaty.php">Etaty</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="zespoly.php">Zespoły</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <form action="" method="post">
        <div class="row my-5">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" />
            </div>
            <div class="col-md-8 text-left">
                <input type="submit" class="btn btn-primary" name="submit" value="Szukaj" />
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-12">

            <table class="table">
                <thead>
                <tr>
                    <th>Id prac</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Etat</th>
                    <th>Szef</th>
                    <th>Zatrudniony</th>
                    <th>Placa pod</th>
                    <th>Placa dod</th>
                    <th>Nazwa zespołu</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($stmt as $row){
                    echo '<tr>';
                    echo '<td>'.$row['ID_PRAC'].'</td>';
                    echo '<td>'.$row['IMIE'].'</td>';
                    echo '<td>'.$row['NAZWISKO'].'</td>';
                    echo '<td>'.$row['ETAT'].'</td>';
                    echo '<td>'.($row['IMIE_SZEFA'] ? $row['IMIE_SZEFA'].' '.$row['NAZWISKO_SZEFA'] : '-').'</td>';
                    echo '<td>'.$row['ZATRUDNIONY'].'</td>';
                    echo '<td>'.$row['PLACA_POD'].'</td>';
                    echo '<td>'.$row['PLACA_DOD'].'</td>';
                    echo '<td>'.$row['NAZWA_ZESPOLU'].'</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>
</html>