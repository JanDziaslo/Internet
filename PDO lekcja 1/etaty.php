<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">
    <title>Etaty</title>
</head>
<body>

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
            echo h($e). '  
                  </div>
                  </div>';
            exit();
        }

    if(isset($_POST['submit']) && $_POST['search']!=''){
        $stmt = $pdo->prepare("SELECT * FROM etaty WHERE NAZWA LIKE :nazwa");
        $stmt -> bindValue(':nazwa', '%'.$_POST['search'].'%', PDO::PARAM_STR);
        $stmt->execute();
    }else{
        $stmt = $pdo->query('SELECT * FROM etaty');
    }

    ?>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Pracownicy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="etaty.php">Etaty</a>
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
                    <a class="btn btn-success" href="dodaj_etat.php" role="button">Dodaj Etat</a>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Nazwa Etatu</th>
                        <th>Płaca od</th>
                        <th>Płaca do</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($stmt as $row){
                        echo '<tr>';
                        echo '<td>'.$row['NAZWA'].'</td>';
                        echo '<td>'.$row['PLACA_OD'].'</td>';
                        echo '<td>'.$row['PLACA_DO'].'</td>';
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