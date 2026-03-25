<?php
require_once 'database.php';

// dla zapisywania zawartosci form
$sukces = false;
$nazwa = '';
$adres = '';


// dla bledow
$nazwaErr = "";
$adresErr = "";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $nazwa = $_POST['nazwa'] ?? '';
    $nazwaZn = mb_strlen($nazwa, "UTF-8");
    $adres = $_POST['adres'] ?? '';
    $adresZn = mb_strlen($adres, "UTF-8");

    if ($nazwa == "")
    {
        $nazwaErr = "Proszę podać nazwę zespołu";
    }
    elseif ($nazwaZn > 20)
    {
        $nazwaErr = "Nazwa zespołu nie może być dłuższa niż 20 znaków";
    }

    if ($adres == "")
    {
        $adresErr = "Proszę podać adres zespołu";
    }
    elseif ($adresZn > 20)
    {
        $adresErr = "Adres zespołu nie może być dłuższy niż 20 znaków";
    }

    if ($nazwaErr === '' && $adresErr === '')
    {
        $stmtMax = $pdo->query("SELECT MAX(ID_ZESP) FROM zespoly");
        $id = (int)$stmtMax->fetchColumn() + 1;

        $stmt = $pdo->prepare("INSERT INTO zespoly (ID_ZESP, NAZWA, ADRES) VALUES (:id, :nazwa, :adres)");
        $stmt -> bindValue(':id', $id, PDO::PARAM_INT);
        $stmt -> bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
        $stmt -> bindValue(':adres', $adres, PDO::PARAM_STR);
        $stmt -> execute();
        $sukces = true;
        $nazwa = '';
        $adres = '';


    }

}
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">

    <title>Dodaj Zespół</title>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="check-circle-fill" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
</svg>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Pracownicy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="etaty.php">Etaty</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="zespoly.php">Zespoły</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br>
<h2 class="text-center">Dodaj Zespół</h2>


<div class="container">
    <form action="dodaj_zesp.php" method="post" novalidate>
        <?php
        if ($sukces)
        {
            echo '<br><div class="alert alert-success d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                  <div>
                  Zespół został dodany pomyślnie!
                  </div>
                  </div>';
        }
        ?>
        <div class="mb-3">
            <label for="nazwa" class="form-label">Nazwa Zespołu</label>
            <input  class="form-control
            <?php if ($nazwaErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="nazwa" name="nazwa" value="<?php echo $nazwa ?>">
            <div id="nazwaHelp" class="form-text">
                <?php
                if ($nazwaErr != "")
                {
                    echo $nazwaErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="adres" class="form-label">Adres</label>
            <input  class="form-control <?php
            if ($adresErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="adres" name="adres" type="text" step="0.01" min="0" value="<?php echo $adres ?>">
            <div id="placa_odHelp" class="form-text">
                <?php
                if ($adresErr != "")
                {
                    echo $adresErr;
                }
                ?>
            </div>
        </div>


        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Dodaj etat</button>
            <a href="zespoly.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>
