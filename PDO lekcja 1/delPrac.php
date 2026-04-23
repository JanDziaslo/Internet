<?php
require_once 'database.php';

if (isset($_POST['action'], $_POST['idp']) && $_POST['action'] === 'delete') {
    $idp = (int)$_POST['idp'];

    if ($idp > 0) {
        $stmt = $pdo->prepare('DELETE FROM pracownicy WHERE ID_PRAC = :idp');
        $stmt->bindValue(':idp', $idp, PDO::PARAM_INT);
        $stmt->execute();

        echo "Sukces";
    }
}