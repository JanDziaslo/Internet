<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <title>Etaty</title>
</head>
<body>

    <?php

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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>
</html>