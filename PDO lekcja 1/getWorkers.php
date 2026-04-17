<?php
require_once 'database.php';

$stmt = $pdo->query('SELECT p.*, z.NAZWA AS NAZWA_ZESPOLU, sz.IMIE AS IMIE_SZEFA, sz.NAZWISKO AS NAZWISKO_SZEFA
                           FROM pracownicy p
                           LEFT JOIN zespoly z ON p.ID_ZESP = z.ID_ZESP
                           LEFT JOIN pracownicy sz ON p.ID_SZEFA = sz.ID_PRAC');

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
    echo '<td><a href="edytuj_prac.php?id='.$row['ID_PRAC'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button></a>';
    echo '<a href="index.php?action=delete&idp='.$row['ID_PRAC'].'"><button type="button" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button></a>';
    echo '</tr>';
}


echo $html;