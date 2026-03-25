<?php
require_once 'database.php';

// dla zapisywania zawartosci form
$sukces = false;
$nazwa = '';
$placa_Od = '';
$placa_Do = '';

// dla bledow
$nazwaErr = "";
$placa_OdErr = "";
$placa_DoErr = "";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $nazwa = $_POST['nazwa'] ?? '';
    $nazwaZn = mb_strlen($nazwa, "UTF-8");
    $placa_Od = $_POST['placa_od'] ?? '';
    $placa_Do = $_POST['placa_do'] ?? '';

    if ($nazwa == "")
    {
        $nazwaErr = "Proszę podać nazwę etatu";
    }
    elseif ($nazwaZn > 15)
    {
        $nazwaErr = "Nazwa etatu nie może być dłuższa niż 15 znaków";
    }

    if ($placa_Od == "")
    {
        $placa_OdErr = "Proszę podać płacę minimalną";
    }
    elseif ($placa_Od < 0)
    {
        $placa_OdErr = "Płaca minimalna nie może być ujemna";
    }

    if ($placa_Do == "")
    {
        $placa_DoErr = "Proszę podać płacę maksymalną";
    }
    elseif ($placa_Do < 0)
    {
        $placa_DoErr = "Płaca maksymalna nie może być ujemna";
    }

    if ($placa_Do != "" && $placa_Od != "" && $placa_Od > $placa_Do)
    {
        $placa_DoErr = "Płaca maksymalna musi być większa lub równa płacy minimalnej";
    }

    if ($nazwaErr === '' && $placa_OdErr === '' && $placa_DoErr === '')
    {

        $stmt = $pdo->prepare("INSERT INTO etaty (NAZWA, PLACA_OD, PLACA_DO) VALUES (:nazwa, :placa_od, :placa_do)");
        $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
        $stmt->bindValue(':placa_od', $placa_Od, PDO::PARAM_STR);
        $stmt->bindValue(':placa_do', $placa_Do, PDO::PARAM_STR);
        $stmt->execute();
        $sukces = true;
        $nazwa = '';
        $placa_Od = '';
        $placa_Do = '';

    }

}
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">

    <title>Dodaj Etat</title>
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
                    <a class="nav-link active" aria-current="page" href="etaty.php">Etaty</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="zespoly.php">Zespoły</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br>
<h2 class="text-center">Dodaj etat</h2>


<div class="container">
    <form action="dodaj_etat.php" method="post" novalidate>
        <?php
        if ($sukces)
        {
            echo '<br><div class="alert alert-success d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                  <div>
                  Etat został dodany pomyślnie!
                  </div>
                  </div>';
        }
        ?>
        <div class="mb-3">
            <label for="nazwa" class="form-label">Nazwa etatu</label>
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
            <label for="placa_od" class="form-label">Płaca minimalna</label>
            <input  class="form-control <?php
            if ($placa_OdErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="placa_od" name="placa_od" type="number" step="0.1" value="<?php echo $placa_Od ?>">
            <div id="placa_odHelp" class="form-text">
                <?php
                if ($placa_OdErr != "")
                {
                    echo $placa_OdErr;
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="placa_do" class="form-label">Płaca maksymalna</label>
            <input  class="form-control <?php
            if ($placa_DoErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="placa_do" name="placa_do" type="number" step="0.1" value="<?php echo $placa_Do ?>">
            <div id="placa_doHelp" class="form-text">
                <?php
                if ($placa_DoErr != "")
                {
                    echo $placa_DoErr;
                }
                ?>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Dodaj etat</button>
            <a href="etaty.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>