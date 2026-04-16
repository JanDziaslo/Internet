<?php
require_once 'database.php';

if (isset($_GET['action'], $_GET['idp']) && $_GET['action'] === 'delete') {
    $idp = (int)$_GET['idp'];

    $stmt = $pdo ->prepare("SELECT ID_ZESP FROM zespoly WHERE ID_ZESP = :idp");
    $stmt -> bindValue(':idp', $idp, PDO::PARAM_INT);
    $stmt -> execute();
    $zespol = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($idp === 0 )
    {
        echo '<script> alert("Zespół o podanym id nie istnieje"); history.back();</script>';
        exit();
    }
    elseif ($zespol === false)
    {
        echo '<script> alert("Zespół o podanym id nie istnieje"); history.back() </script>';
        exit();

    }
    else
    {
        echo '<script>
            if (confirm("Czy na pewno chcesz usunąć ten zespół?")) {
                window.location.href = "?action=con&idp=' . $idp . '";}
            else {
                history.back();
            }
            </script>';
    }
}
if (isset($_GET['action'], $_GET['idp']) && $_GET['action'] === 'con') {
    $idp = (int)$_GET['idp'];

    $stmt = $pdo ->prepare("SELECT ID_ZESP FROM zespoly WHERE ID_ZESP = :idp");
    $stmt -> bindValue(':idp', $idp, PDO::PARAM_INT);
    $stmt -> execute();
    $zespol = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($idp === 0 )
    {
        echo '<script> alert("Zespół o podanym id nie istnieje"); history.back(); </script>';
        exit();
    }
    elseif ($zespol === false)
    {
        echo '<script> alert("Zespół o podanym id nie istnieje"); history.back(); </script>';
        exit();
    }
    else
    {
        $stmt = $pdo->prepare('DELETE FROM zespoly WHERE ID_ZESP = :idp');
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
    <title>Zespoly</title>
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
    $stmt = $pdo->prepare("SELECT * FROM zespoly WHERE NAZWA LIKE :nazwa");
    $stmt -> bindValue(':nazwa', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
}elseif (isset($_POST['reset'])){
    $stmt = $pdo->query('SELECT * FROM zespoly');
}
else{
    $stmt = $pdo->query('SELECT * FROM zespoly');
}

?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="index.php">Pracownicy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="etaty.php">Etaty</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="zespoly.php">Zespoły</a>
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
            <div class="col-md-1 text-left">
                <input type="submit" class="btn btn-primary" name="submit" value="Szukaj" />
            </div>
            <div class="col-md-3 ">
                <input type="submit" class="btn btn-danger" name="reset" value="Resetuj">
            </div>
            <div class="col-md-4 text-end">
                <a class="btn btn-success" href="dodaj_zesp.php" role="button">Dodaj Zespół</a>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-12">

            <table class="table">
                <thead>
                <tr>
                    <th>ID Zespołu</th>
                    <th>Nazwa</th>
                    <th>Adres</th>
                    <th>Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($stmt as $row){
                    echo '<tr>';
                    echo '<td>'.$row['ID_ZESP'].'</td>';
                    echo '<td>'.$row['NAZWA'].'</td>';
                    echo '<td>'.$row['ADRES'].'</td>';
                    echo '<td><a href="edytuj_zesp.php?id='.$row['ID_ZESP'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button></a>';
                    echo '<a href="zespoly.php?action=delete&idp='.$row['ID_ZESP'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button></a>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
<script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
</body>
</html>