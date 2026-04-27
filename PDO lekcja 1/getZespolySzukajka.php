<?php
require_once 'database.php';

$stmt = $pdo->prepare("SELECT * FROM zespoly WHERE NAZWA LIKE :szukaj");
$stmt->bindValue(':szukaj', '%'.$_POST['search'].'%', PDO::PARAM_STR);
$stmt->execute();
$html = '';

foreach ($stmt as $row){
    echo '<tr>';
    echo '<td>'.$row['ID_ZESP'].'</td>';
    echo '<td>'.$row['NAZWA'].'</td>';
    echo '<td>'.$row['ADRES'].'</td>';
    echo '<td><button type="button" onclick="edycja_zesp('.$row['ID_ZESP'].')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="white"><use xlink:href="#pencil"></use></svg></button>';
    echo '<button type="button" onclick="delZesp('.$row['ID_ZESP'].')" class="btn btn-outline-secondary me-2"><svg width="16" height="16" fill="red"><use xlink:href="#smietnik"></use></svg></button>';
    echo '</tr>';
}

echo $html;
