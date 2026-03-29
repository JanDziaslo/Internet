<?php
require_once 'database.php';

$id_url = $_GET['id'];

$stmt = $pdo ->prepare("SELECT NAZWISKO, IMIE, ETAT, ID_SZEFA, ZATRUDNIONY, PLACA_POD, PLACA_DOD, ID_ZESP FROM pracownicy WHERE ID_PRAC = :id");
$stmt -> bindValue(':id', $id_url, PDO::PARAM_INT);
$stmt -> execute();
$pracownik = $stmt -> fetch(PDO::FETCH_ASSOC);

$imie = $pracownik['IMIE'];
$nazwisko = $pracownik['NAZWISKO'];
$etat = $pracownik['ETAT'];
$szef = $pracownik['ID_SZEFA'];
$data = $pracownik['ZATRUDNIONY'];
$placa_Pod = $pracownik['PLACA_POD'];
$placa_Dod = $pracownik['PLACA_DOD'];
$zespol = $pracownik['ID_ZESP'];


?>

<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">

    <title>Edytuj Pracownika</title>
</head>
<body>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>

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
<br>
<h2 class="text-center">Edytuj pracownika</h2>


<div class="container">
    <form action="edytuj_prac.php" method="post" novalidate>
        <?php
        if ($pracownik === false )
        {
            echo '<br><div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                    <div>
                    Pracownik o podanym ID nie istnieje!
                    </div>
                    </div>';
            exit;
        }
        ?>
        <div class="mb-3">
            <label for="imie" class="form-label">Imię</label>
            <input  class="form-control
            <?php if ($imieErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="imie" name="imie" value="<?php echo $imie ?>">
            <div id="nazwiskoHelp" class="form-text">
                <?php
                if ($imieErr != "")
                {
                    echo $imieErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="nazwisko" class="form-label">Nazwisko</label>
            <input  class="form-control <?php
            if ($nazwiskoErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="nazwisko" name="nazwisko" value="<?php echo $nazwisko ?>">
            <div id="nazwiskoHelp" class="form-text">
                <?php
                if ($nazwiskoErr != "")
                {
                    echo $nazwiskoErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="etat" class="form-label">Etat</label>
            <select class="form-select <?php
            if ($etatErr != '')
            {
                echo 'is-invalid';
            }
            ?>" id="etat" name="etat">
                <option value="-- wybierz --" <?php
                if ($etat == '-- wybierz --')
                {
                    echo 'selected';
                } ?>>-- wybierz --</option>
                <?php
                $stmt = $pdo->query("SELECT NAZWA FROM etaty");
                foreach ($stmt as $row) {
                    $nazwa = $row['NAZWA'];
                    $sel = ($etat == $nazwa) ? 'selected' : '';
                    echo '<option value="' . $nazwa . '" ' . $sel . '>' . $nazwa . '</option>';
                }
                ?>
            </select>
            <div id="etatHelp" class="form-text">
                <?php
                if ($etatErr != '')
                {
                    echo $etatErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="szef" class="form-label">Szef</label>
            <select class="form-select" id="szef" name="szef">
                <option value="-- brak --" <?php
                if ($szef == '-- brak --')
                {
                    echo 'selected';
                }
                ?>>-- brak --</option>
                <?php
                $stmt = $pdo->query("SELECT IMIE, NAZWISKO FROM pracownicy");
                foreach ($stmt as $row) {
                    $full = $row['IMIE'] . ' ' . $row['NAZWISKO'];
                    $sel = ($szef == $full) ? 'selected' : '';
                    echo '<option value="' . $full . '" ' . $sel . '>' . $full . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="zespol" class="form-label">Zespół</label>
            <select class="form-select <?php
            if ($zespolErr != '')
            {
                echo 'is-invalid';
            }
            ?>" id="zespol" name="zespol">
                <option value="-- wybierz --" <?php
                if ($zespol == '-- wybierz --')
                {
                    echo 'selected';
                } ?>>-- wybierz --</option>
                <?php
                $stmt = $pdo->query("SELECT NAZWA FROM zespoly");
                foreach ($stmt as $row) {
                    $nazwa = $row['NAZWA'];
                    $sel = ($zespol == $nazwa) ? 'selected' : '';
                    echo '<option value="' . $nazwa . '" ' . $sel . '>' . $nazwa . '</option>';
                }
                ?>
            </select>
            <div id="zespolHelp" class="form-text">
                <?php
                if ($zespolErr != '')
                {
                    echo $zespolErr;
                }
                ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="data" class="form-label">Data zatrudnienia</label>
            <input  class="form-control <?php
            if ($dataErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="data" name="data" type="date" value="<?php echo $data ?>">
            <div id="dataHelp" class="form-text">
                <?php
                if ($dataErr != "")
                {
                    echo $dataErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="placa_pod" class="form-label">Płaca podstawowa</label>
            <input  class="form-control <?php
            if ($placa_PodErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="placa_pod" name="placa_pod" type="number" step="0.1" value="<?php echo $placa_Pod ?>">
            <div id="placa_podHelp" class="form-text">
                <?php
                if ($placa_PodErr != "")
                {
                    echo $placa_PodErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="placa_dod" class="form-label">Płaca dodatkowa</label>
            <input  class="form-control <?php
            if ($placa_DodErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="placa_dod" name="placa_dod" type="number" step="0.1" value="<?php echo $placa_Dod ?>">
            <div id="placa_dodHelp" class="form-text">
                <?php
                if ($placa_DodErr != "")
                {
                    echo $placa_DodErr;
                }
                ?>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Edytuj pracownika</button>
            <a href="index.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>
