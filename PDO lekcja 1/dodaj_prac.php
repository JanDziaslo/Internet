<?php
require_once 'database.php';


$imieErr = "";
$nazwiskoErr = "";
$etatErr = "";
$dataErr = "";
$placa_PodErr = "";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']); //zeby spacja nie przeszla przypadkiem jako poprawne imie/nazwisko
    $etat = $_POST['etat'];
    $szef = $_POST['szef'];
    $data = $_POST['data'];
    $placa_Pod = $_POST['placa_pod'];
    $placa_Dod = $_POST['placa_dod'];

    if ($imie == "")
    {
        $imieErr = "Proszę podać imię";
    }

    if ($nazwisko == "")
    {
        $nazwiskoErr = "Proszę podać nazwisko";
    }

    if ($etat == "-- wybierz --")
    {
        $etatErr = "Proszę wybrać etat";
    }

    if ($data == "")
    {
        $dataErr = "Proszę podać datę zatrudnienia";
    }
    else
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        $minData = new DateTime('2000-01-01');
        $maxData = new DateTime('+1 year');

        if (!$dataObj || $dataObj->format('Y-m-d') !== $data)
            $dataErr = "Nieprawidłowy format daty";
        elseif ($dataObj < $minData)
            $dataErr = "Data jest zbyt odległa w przeszłości";
        elseif ($dataObj > $maxData)
            $dataErr = "Data nie może być dalej niż rok w przyszłości";
    }

    if ($placa_Pod == "")
    {
        $placa_PodErr = "Proszę podać płacę podstawową";
    }
    if ($imie && $nazwisko && $etat !== "-- wybierz --" && !$dataErr && $data && $placa_Pod != '')
    {
        echo "gotowe do wyslania";
    }

}
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">

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
            <input  class="form-control
            <?php if ($imieErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="imie" name="imie">
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
            ?>" id="nazwisko" name="nazwisko">
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
            <option selected>-- wybierz --</option>
            <?php
            $stmt = $pdo->query("SELECT NAZWA FROM etaty");
            foreach ($stmt as $row)
                echo '<option>' . $row['NAZWA'] . '</option>'
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
            <option selected>-- brak --</option>
            <?php
            $stmt = $pdo->query("SELECT IMIE, NAZWISKO FROM pracownicy");
            foreach ($stmt as $row)
                echo '<option>' . $row['IMIE'] . " ". $row['NAZWISKO']. '</option>'
            ?>
        </select>
        </div>
        <div class="mb-3">
            <label for="data" class="form-label">Data zatrudnienia</label>
            <input  class="form-control <?php
            if ($dataErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="data" name="data" type="date">
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
            ?>" id="placa_pod" name="placa_pod" type="number">
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
            <input  class="form-control" id="placa_dod" name="placa_dod" type="number">
            <div id="placa_dodHelp" class="form-text"></div>
        </div>


        <button type="submit" class="btn btn-primary">Dodaj pracownika</button>
    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>