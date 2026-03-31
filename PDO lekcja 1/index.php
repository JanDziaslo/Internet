<?php
require_once 'database.php';

if (isset($_GET['action'], $_GET['idp']) && $_GET['action'] === 'delete') {
    $idp = (int)$_GET['idp'];

    $stmt = $pdo ->prepare("SELECT ID_PRAC FROM pracownicy WHERE ID_PRAC = :idp");
    $stmt -> bindValue(':idp', $idp, PDO::PARAM_INT);
    $stmt -> execute();
    $pracownik = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($idp === 0 )
    {
        echo '<script> alert("Pracownik o podanym id nie istnieje"); history.back();</script>';
        exit();
    }
    elseif ($pracownik === false)
    {
        echo '<script> alert("Pracownik o podanym id nie istnieje"); history.back() </script>';
        exit();
    }
    else
    {
        echo '<script>
            if (confirm("Czy na pewno chcesz usunąć tego pracownika?")) {
                window.location.href = "?action=con&idp=' . $idp . '";}
            else {
                history.back();
            }
            </script>';
    }
}
if (isset($_GET['action'], $_GET['idp']) && $_GET['action'] === 'con') {
    $idp = (int)$_GET['idp'];

    $stmt = $pdo ->prepare("SELECT ID_PRAC FROM pracownicy WHERE ID_PRAC = :idp");
    $stmt -> bindValue(':idp', $idp, PDO::PARAM_INT);
    $stmt -> execute();
    $pracownik = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($idp === 0 )
    {
        echo '<script> alert("Pracownik o podanym id nie istnieje"); history.back(); </script>';
        exit();
    }
    elseif ($pracownik === false)
    {
        echo '<script> alert("Pracownik o podanym id nie istnieje"); history.back() </script>';
        exit();
    }
    else
    {
        $stmt = $pdo->prepare('DELETE FROM pracownicy WHERE ID_PRAC = :idp');
        $stmt->bindValue(':idp', $idp, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: zespoly.php');
        exit();
    }
}

?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">

    <title>Pracownicy</title>
</head>
<body>
<!-- edycja -->
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="pencil" viewBox="0 0 16 16">
    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
</svg>
<!-- usuwanie -->
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="smietnik" viewBox="0 0 16 16">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>

<!-- blad -->
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>

<?php

if ($bazaErr)
        {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                  <div class="text-center">
                    <div class="text-center">Połączenie z bazą danych nie zostało nawiązane: <br>';
            echo  '  
                  </div>
                  </div>';
            exit();
        }

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
            <div class="col-md-6 text-left">
                <input type="submit" class="btn btn-primary" name="submit" value="Szukaj" />
            </div>
            <div class="col-md-2 text-end">
            <a class="btn btn-success" href="dodaj_prac.php" role="button">Dodaj Pracownika</a>
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
                    <th>Akcje</th>
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
                    echo '<td><a href="edytuj_prac.php?id='.$row['ID_PRAC'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button></a>';
                    echo '<a href="index.php?action=delete&idp='.$row['ID_PRAC'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button></a>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>