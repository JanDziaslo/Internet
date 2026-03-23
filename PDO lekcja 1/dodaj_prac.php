<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <title>Dodaj Pracownika</title>
</head>
<body>

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

<div class="container"><br><br>
    <form action="dodaj_prac.php" method="post" novalidate>
        <div class="mb-3">
            <label for="imie" class="form-label">Imię</label>
            <input  class="form-control" id="imie" name="imie">
            <div id="nazwiskoHelp" class="form-text"></div> <!-- przyda sie na pozniej -->
        </div>
        <div class="mb-3">
            <label for="nazwisko" class="form-label">Nazwisko</label>
            <input  class="form-control" id="nazwisko" name="nazwisko">
            <div id="nazwiskoHelp" class="form-text"></div> <!-- przyda sie na pozniej -->
        </div>
        <label for="etat" class="form-label">Etat</label>
        <select class="form-select" id="etat">
            <option selected></option>
            <?php
            $stmt = $pdo->query("SELECT NAZWA FROM etaty");
            foreach ($stmt as $row)
                echo '<option>' . $row['NAZWA'] . '</option>'
            ?>
        </select>
        <label for="szef" class="form-label">Szef</label>
        <select class="form-select" id="szef">
            <option selected></option>
            <?php
            $stmt = $pdo->query("SELECT IMIE, NAZWISKO FROM pracownicy");
            foreach ($stmt as $row)
                echo '<option>' . $row['IMIE'] . " ". $row['NAZWISKO']. '</option>'
            ?>
        </select>
        <div class="mb-3">
            <label for="data" class="form-label">Data zatrudnienia</label>
            <input  class="form-control" id="data" name="data" type="date">
            <div id="dataHelp" class="form-text"></div> <!-- przyda sie na pozniej -->
        </div>
        <div class="mb-3">
            <label for="placa_pod" class="form-label">Płaca podstawowa</label>
            <input  class="form-control" id="placa_pod" name="placa_pod" type="number">
            <div id="placa_podHelp" class="form-text"></div> <!-- przyda sie na pozniej -->
        </div>
        <div class="mb-3">
            <label for="placa_dod" class="form-label">Płaca dodatkowa</label>
            <input  class="form-control" id="placa_dod" name="placa_dod" type="number">
            <div id="placa_dodHelp" class="form-text"></div> <!-- przyda sie na pozniej -->
        </div>


        <button type="submit" class="btn btn-primary">Dodaj pracownika</button>
    </form>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>
</html>