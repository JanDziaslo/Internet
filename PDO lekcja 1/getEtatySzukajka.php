<?php
require_once 'database.php';

$stmt = $pdo->prepare("SELECT * FROM etaty WHERE NAZWA LIKE :szukaj");
$stmt->bindValue(':szukaj', '%'.$_POST['search'].'%', PDO::PARAM_STR);
$stmt->execute();
$html = '';

foreach ($stmt as $row){
    echo '<tr>';
    echo '<td>'.$row['NAZWA'].'</td>';
    echo '<td>'.$row['PLACA_OD'].'</td>';
    echo '<td>'.$row['PLACA_DO'].'</td>';
    echo '<td><button type="button" onclick="edycja_etat(\''.$row['NAZWA'].'\')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button>';
    echo '<button type="button" onclick="delEtat(\''.$row['NAZWA'].'\')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button>';
    echo '</tr>';
}

echo $html;
