<?php
require_once 'database.php';

$stmt = $pdo->prepare("SELECT p.*, z.NAZWA AS NAZWA_ZESPOLU, sz.IMIE AS IMIE_SZEFA, sz.NAZWISKO AS NAZWISKO_SZEFA
                           FROM pracownicy p
                           LEFT JOIN zespoly z ON p.ID_ZESP = z.ID_ZESP
                           LEFT JOIN pracownicy sz ON p.ID_SZEFA = sz.ID_PRAC
                           WHERE p.IMIE LIKE :szukaj OR p.NAZWISKO LIKE :szukaj OR sz.IMIE LIKE :szukaj OR sz.NAZWISKO LIKE :szukaj");
    $stmt -> bindValue(':szukaj', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
$html = '';

foreach ($stmt as $row){
    echo '<tr>';
    echo '<td>'.$row['ID_PRAC'].'</td>';
    echo '<td>'.$row['IMIE'].'</td>';
    echo '<td>'.$row['NAZWISKO'].'</td>';
    echo '<td>'.$row['ETAT'].'</td>';
    echo '<td>'.($row['IMIE_SZEFA'] ? $row['IMIE_SZEFA'].' '.$row['NAZWISKO_SZEFA'] : '-').'</td>';
    echo '<td>'.$row['ZATRUDNIONY'].'</td>';
    echo '<td>'.$row['PLACA_POD'].'</td>';
    echo '<td>'.$row['PLACA_DOD'].'</td>';
    echo '<td>'.$row['NAZWA_ZESPOLU'].'</td>';
    echo '<td><button type="button" onclick="edycja_prac('.$row['ID_PRAC'].')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button>';
    echo '<button type="button" onclick="delPrac('.$row['ID_PRAC'].')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button>';
    echo '</tr>';
}


echo $html;
?>