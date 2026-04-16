<?php
require_once 'database.php';

//niech juz bedzie
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
// dla bledow
$nazwaErr = "";
$placa_OdErr = "";
$placa_DoErr = "";

$sukces = false;

$nazwa_url = $_GET['nazwa'] ?? '';

$stmt = $pdo -> prepare("SELECT NAZWA, PLACA_OD, PLACA_DO FROM etaty WHERE NAZWA = :nazwa");
$stmt -> bindValue(':nazwa', $nazwa_url, PDO::PARAM_STR);
$stmt -> execute();
$etat = $stmt -> fetch(PDO::FETCH_ASSOC);
if ($etat === false)
{
    echo '';
}
else {

    $nazwa = $etat['NAZWA'];
    $placa_Od = $etat['PLACA_OD'];
    $placa_Do = $etat['PLACA_DO'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nazwa = $_POST['nazwa'] ?? '';
        $nazwaZn = mb_strlen($nazwa, "UTF-8");
        $placa_Od = $_POST['placa_od'] ?? '';
        $placa_Do = $_POST['placa_do'] ?? '';

        if ($nazwa == "") {
            $nazwaErr = "Proszę podać nazwę etatu";
        } elseif ($nazwaZn > 15) {
            $nazwaErr = "Nazwa etatu nie może być dłuższa niż 15 znaków";
        }

        if ($placa_Od == "") {
            $placa_OdErr = "Proszę podać płacę minimalną";
        } elseif ($placa_Od < 0) {
            $placa_OdErr = "Płaca minimalna nie może być ujemna";
        }

        if ($placa_Do == "") {
            $placa_DoErr = "Proszę podać płacę maksymalną";
        } elseif ($placa_Do < 0) {
            $placa_DoErr = "Płaca maksymalna nie może być ujemna";
        }

        if ($placa_Do != "" && $placa_Od != "" && $placa_Od > $placa_Do) {
            $placa_DoErr = "Płaca maksymalna musi być większa lub równa płacy minimalnej";
        }

        if ($nazwaErr === '' && $placa_OdErr === '' && $placa_DoErr === '') {

            $stmt = $pdo->prepare("UPDATE etaty SET NAZWA = :nazwa, PLACA_OD = :placa_od, PLACA_DO = :placa_do WHERE NAZWA = :nazwa");
            $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
            $stmt->bindValue(':placa_od', $placa_Od, PDO::PARAM_STR);
            $stmt->bindValue(':placa_do', $placa_Do, PDO::PARAM_STR);
            $stmt->execute();
            $sukces = true;

        }

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

<!-- blad -->
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
<h2 class="text-center">Edytuj etat</h2>


<div class="container">
    <form action="edytuj_etat.php?nazwa=<?php echo $nazwa_url?>" method="post" novalidate>
        <?php
        if ($sukces)
        {
            echo '<br><div class="alert alert-success d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                  <div>
                  Etat został edytowany pomyślnie!
                  </div>
                  </div>';
        }
        elseif ($bazaErr)
        {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                  <div class="text-center">
                    <div class="text-center">Połączenie z bazą danych nie zostało nawiązane: <br>';
            echo '  
                  </div>
                  </div>';
            exit();
        }
        elseif ($etat === false)
        {
            echo '<br><div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                    <div>
                    Etat o podanej nazwie nie istnieje!
                    </div>
                    </div>';
            exit();
        }
        ?>
        <div class="mb-3">
            <label for="nazwa" class="form-label">Nazwa etatu</label>
            <input  class="form-control
            <?php if ($nazwaErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="nazwa" name="nazwa" value="<?php echo h($nazwa); ?>">
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
            ?>" id="placa_od" name="placa_od" type="number" step="0.1" value="<?php echo h($placa_Od); ?>">
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
            ?>" id="placa_do" name="placa_do" type="number" step="0.1" value="<?php echo h($placa_Do); ?>">
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
            <button type="submit" class="btn btn-primary">Edytuj etat</button>
            <a href="etaty.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
<script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
</body>
</html>