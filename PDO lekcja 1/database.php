<?php

$host = '100.125.41.106';
$port = '13306';
$dbname = 'PDO1';
$user = 'wytrychy_user';
$pass = 'gDxajVS2BhMiqcY8xWHU34EpjRpC489T';

try {
    $pdo = new PDO( "mysql:host=$host;port=$port;dbname=$dbname", $user, $pass );
    $pdo->query('SET NAMES utf8');
} catch (PDOException $e) {
    echo 'Połączenie nie mogło zostać utworzone: ' . $e->getMessage();
    exit();
}

?>
