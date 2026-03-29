<?php
require_once 'database.php';

//niech juz bedzie
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$imieErr = "";
$nazwiskoErr = "";
$etatErr = "";
$zespolErr = "";
$dataErr = "";
$placa_PodErr = "";
$placa_DodErr = "";
$git = false;

$id_url = (int) $_GET['id'];


$stmt = $pdo ->prepare("SELECT NAZWISKO, IMIE, ETAT, ID_SZEFA, ZATRUDNIONY, PLACA_POD, PLACA_DOD, ID_ZESP FROM pracownicy WHERE ID_PRAC = :id");
$stmt -> bindValue(':id', $id_url, PDO::PARAM_INT);
$stmt -> execute();
$pracownik = $stmt -> fetch(PDO::FETCH_ASSOC);

if ($pracownik === false)
{
    echo ''; // zeby mi jetbrains nie plakal ze if jest pusty (druciarstwo to moja pasja)
}
else {
    $imie = $pracownik['IMIE'];
    $nazwisko = $pracownik['NAZWISKO'];
    $etat = $pracownik['ETAT'];
    $szef = $pracownik['ID_SZEFA'];
    $data = $pracownik['ZATRUDNIONY'];
    $placa_Pod = $pracownik['PLACA_POD'];
    $placa_Dod = $pracownik['PLACA_DOD'];
    $zespol = $pracownik['ID_ZESP'];


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $imie = trim($_POST['imie'] ?? '');
        $imieZn = mb_strlen($imie, "UTF-8");
        $nazwisko = trim($_POST['nazwisko'] ?? ''); //zeby spacja nie przeszla przypadkiem jako poprawne imie/nazwisko
        $nazwiskoZn = mb_strlen($nazwisko, "UTF-8");
        $etat = $_POST['etat'] ?? '-- wybierz --';
        $szef = (int)($_POST['szef'] ?? 0);
        $zespol = (int)($_POST['zespol'] ?? 0);
        $data = $_POST['data'] ?? '';
        $placa_Pod = $_POST['placa_pod'] ?? '';
        $placa_Dod = $_POST['placa_dod'] ?? '';

        if ($imie == "") {
            $imieErr = "Proszę podać imię";
        } elseif ($imieZn > 20) {
            $imieErr = "Imię nie może być dłuższe niż 20 znaków";
        }

        if ($nazwisko == "") {
            $nazwiskoErr = "Proszę podać nazwisko";
        } elseif ($nazwiskoZn > 15) {
            $nazwiskoErr = "Nazwisko nie może być dłuższe niż 15 znaków";
        }

        if ($etat == "-- wybierz --") {
            $etatErr = "Proszę wybrać etat";
        }

        if ($zespol == 0) {
            $zespolErr = "Proszę wybrać zespół";
        }

        if ($data == "") {
            $dataErr = "Proszę podać datę zatrudnienia";
        } else {
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

        if ($placa_Pod == "") {
            $placa_PodErr = "Proszę podać płacę podstawową";
        } elseif ($placa_Pod < 0) {
            $placa_PodErr = "Płaca podstawowa nie może być ujemna";
        }

        if ($placa_Dod > $placa_Pod) {
            $placa_DodErr = "Płaca dodatkowa nie może być wieksza niż podstawowa";
        } elseif ($placa_Dod != '' && $placa_Dod < 0) {
            $placa_DodErr = "Płaca dodatkowa nie może być ujemna";
        }

        if ($imieErr == "" && $nazwiskoErr == "" && $etatErr == "" && $zespolErr == "" && $dataErr == "" && $placa_PodErr == "" && $placa_DodErr == "") {
            $stmt = $pdo->prepare("UPDATE pracownicy SET IMIE = :imie, NAZWISKO = :nazwisko, ETAT = :etat, ID_SZEFA = :szef, ZATRUDNIONY = :data, PLACA_POD = :placa_pod, PLACA_DOD = :placa_dod, ID_ZESP = :zespol WHERE ID_PRAC = :id");
            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':etat', $etat, PDO::PARAM_STR);
            if ($szef == 0) {
                $stmt->bindValue(':szef', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':szef', $szef, PDO::PARAM_INT);
            }
            $stmt->bindValue(':data', $data, PDO::PARAM_STR);
            $stmt->bindValue(':placa_pod', $placa_Pod, PDO::PARAM_STR);
            if ($placa_Dod === '') {
                $stmt->bindValue(':placa_dod', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':placa_dod', $placa_Dod, PDO::PARAM_STR);
            }
            if ($zespol == 0) {
                $stmt->bindValue(':zespol', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':zespol', $zespol, PDO::PARAM_INT);
            }
            $stmt->bindValue(':id', $id_url, PDO::PARAM_INT);
            $stmt->execute();
            $git = true;
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

    <title>Edytuj Pracownika</title>
</head>
<body>
<!-- blad -->
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>
<!-- git -->
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
    <form action="edytuj_prac.php?id=<?php echo $id_url; ?>" method="post" novalidate>
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
        elseif ($git)
        {
            echo '<br><div class="alert alert-success d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="16" height="16" fill="currentColor" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                  <div>
                  Pracownik został edytowany pomyślnie!
                  </div>
                  </div>';
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
                <option value="0" <?php
                if ($szef == 0)
                {
                    echo 'selected';
                }
                ?>>-- brak --</option>
                <?php
                $stmt = $pdo->query("SELECT ID_PRAC, IMIE, NAZWISKO FROM pracownicy");
                foreach ($stmt as $row) {
                    $id_prac = $row['ID_PRAC'];
                    $full = $row['IMIE'] . ' ' . $row['NAZWISKO'];
                    $sel = ($szef == $id_prac) ? 'selected' : '';
                    echo '<option value="' . $id_prac . '" ' . $sel . '>' . h($full) . '</option>';
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
                <option value="0" <?php
                if ($zespol == 0)
                {
                    echo 'selected';
                } ?>>-- wybierz --</option>
                <?php
                $stmt = $pdo->query("SELECT ID_ZESP, NAZWA FROM zespoly");
                foreach ($stmt as $row) {
                    $id_zesp = $row['ID_ZESP'];
                    $nazwa = $row['NAZWA'];
                    $sel = ($zespol == $id_zesp) ? 'selected' : '';
                    echo '<option value="' . $id_zesp . '" ' . $sel . '>' . h($nazwa) . '</option>';
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
            <button type="submit" class="btn btn-primary">Edytuj pracownika</button>
            <a href="index.php" class="btn btn-danger ms-auto">Wróć</a>
        </div>

    </form>

</div>


<script src="../CDN/js/bootstrap.bundle.min.js"></script>
</body>
</html>
