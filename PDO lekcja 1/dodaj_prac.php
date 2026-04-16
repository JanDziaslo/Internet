<?php
require_once 'database.php';

//niech juz bedzie
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// dla zapisywania zawartosci form
$sukces = false;
$imie = '';
$nazwisko = '';
$etat = '-- wybierz --';
$szef = '-- brak --';
$zespol = '-- wybierz --';
$data = '';
$placa_Pod = '';
$placa_Dod = '';

// dla bledow
$imieErr = "";
$nazwiskoErr = "";
$etatErr = "";
$zespolErr = "";
$dataErr = "";
$placa_PodErr = "";
$placa_DodErr = "";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $imie = trim($_POST['imie'] ?? '');
    $imieZn = mb_strlen($imie, "UTF-8");
    $nazwisko = trim($_POST['nazwisko'] ?? ''); //zeby spacja nie przeszla przypadkiem jako poprawne imie/nazwisko
    $nazwiskoZn = mb_strlen($nazwisko, "UTF-8");
    $etat = $_POST['etat'] ?? '-- wybierz --';
    $szef = $_POST['szef'] ?? '-- brak --';
    $zespol = $_POST['zespol'] ?? '-- wybierz --';
    $data = $_POST['data'] ?? '';
    $placa_Pod = $_POST['placa_pod'] ?? '';
    $placa_Dod = $_POST['placa_dod'] ?? '';

    if ($imie == "")
    {
        $imieErr = "Proszę podać imię";
    }
    elseif ($imieZn > 20)
    {
        $imieErr = "Imię nie może być dłuższe niż 20 znaków";
    }

    if ($nazwisko == "")
    {
        $nazwiskoErr = "Proszę podać nazwisko";
    }
    elseif ($nazwiskoZn > 15)
    {
        $nazwiskoErr = "Nazwisko nie może być dłuższe niż 15 znaków";
    }

    if ($etat == "-- wybierz --")
    {
        $etatErr = "Proszę wybrać etat";
    }

    if ($zespol == "-- wybierz --")
    {
        $zespolErr = "Proszę wybrać zespół";
    }

    if ($data == "")
    {
        $dataErr = "Proszę podać datę zatrudnienia";
    }
    else
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        $minData = new DateTime('1900-01-01');
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
    elseif ($placa_Pod < 0)
    {
        $placa_PodErr = "Płaca podstawowa nie może być ujemna";
    }

    if ($placa_Dod > $placa_Pod)
    {
        $placa_DodErr = "Płaca dodatkowa nie może być wieksza niż podstawowa";
    }
    elseif ($placa_Dod != '' && $placa_Dod < 0)
    {
        $placa_DodErr = "Płaca dodatkowa nie może być ujemna";
    }

    if ($imieErr === '' && $nazwiskoErr === '' && $etatErr === '' && $zespolErr === '' && $dataErr === '' && $placa_PodErr === '' && $placa_DodErr === '')
    {
        if ($placa_Dod == '')
        {
            $placa_Dod = null;
        }

        $stmtZespol = $pdo->prepare("SELECT ID_ZESP FROM zespoly WHERE NAZWA = :zespol");
        $stmtZespol->bindValue(':zespol', $zespol, PDO::PARAM_STR);
        $stmtZespol->execute();
        $idZespolu = $stmtZespol->fetchColumn();


        if ($szef == '-- brak --')
        {
            $ppszef = null;
        }
        else
        {
            $pszef = explode(" ", $szef); // rozdzielanie imienia i nazwiska
            $pimie = $pszef[0];  // imie
            $pnazwisko = $pszef[1]; // nazwisko
            $pomocnicza = $pdo -> prepare("SELECT ID_PRAC FROM pracownicy WHERE IMIE LIKE :pimie AND NAZWISKO LIKE :pnazwisko");
            $pomocnicza -> bindValue(':pimie', $pimie, PDO::PARAM_STR);
            $pomocnicza -> bindValue(':pnazwisko', $pnazwisko, PDO::PARAM_STR);
            $pomocnicza -> execute();
            $ppszef = $pomocnicza -> fetchColumn();
        }



        $stmt = $pdo->prepare("INSERT INTO pracownicy (IMIE, NAZWISKO, ETAT, ID_SZEFA, ID_ZESP, ZATRUDNIONY, PLACA_POD, PLACA_DOD)
                                     VALUES (:imie, :nazwisko, :etat, :szef, :id_zesp, :data, :placa_pod, :placa_dod)");
        $stmt -> bindValue(':imie', $imie, PDO::PARAM_STR);
        $stmt -> bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
        $stmt -> bindValue(':etat', $etat, PDO::PARAM_STR);
        $stmt -> bindValue(':szef', $ppszef, PDO::PARAM_INT);
        $stmt -> bindValue(':id_zesp', $idZespolu, PDO::PARAM_INT);
        $stmt -> bindValue(':data', $data, PDO::PARAM_STR);
        $stmt -> bindValue(':placa_pod', $placa_Pod, PDO::PARAM_INT);
        if ($placa_Dod === 0)
        {
            $stmt -> bindValue(':placa_dod', null, PDO::PARAM_NULL);
        }
        else {
            $stmt->bindValue(':placa_dod', $placa_Dod, PDO::PARAM_INT);
        }
        $stmt->execute();
        $sukces = true;
        $imie = '';
        $nazwisko = '';
        $etat = '-- wybierz --';
        $szef = '-- brak --';
        $zespol = '-- wybierz --';
        $data = '';
        $placa_Pod = '';
        $placa_Dod = '';
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
<!-- potwierdzenie -->
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
<h2 class="text-center">Dodaj pracownika</h2>


<div class="container">
    <form action="dodaj_prac.php" method="post" novalidate>
        <?php
        if ($sukces)
        {
            echo '<br><div class="alert alert-success d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                  <div>
                  Pracownik został dodany pomyślnie!
                  </div>
                  </div>';
        }
        elseif ($bazaErr)
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
        ?>
        <div class="mb-3">
            <label for="imie" class="form-label">Imię</label>
            <input  class="form-control
            <?php if ($imieErr != '')
            {
                echo "is-invalid";
            }
            ?>" id="imie" name="imie" value="<?php echo h($imie); ?>">
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
            ?>" id="nazwisko" name="nazwisko" value="<?php echo h($nazwisko); ?>">
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
                echo '<option value="' . h($nazwa) . '" ' . $sel . '>' . h($nazwa) . '</option>';
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
                echo '<option value="' . h($full) . '" ' . $sel . '>' . h($full) . '</option>';
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
                    echo '<option value="' . h($nazwa) . '" ' . $sel . '>' . h($nazwa) . '</option>';
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
            ?>" id="data" name="data" type="date" value="<?php echo h($data); ?>">
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
            ?>" id="placa_pod" name="placa_pod" type="number" step="0.1" value="<?php echo h($placa_Pod); ?>">
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
            ?>" id="placa_dod" name="placa_dod" type="number" step="0.1" value="<?php echo h($placa_Dod); ?>">
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
            <button type="submit" class="btn btn-primary">Dodaj pracownika</button>
            <a href="index.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
<script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
</body>
</html>