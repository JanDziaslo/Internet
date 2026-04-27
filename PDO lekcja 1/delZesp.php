<?php
require_once 'database.php';

if (isset($_POST['action'], $_POST['idp']) && $_POST['action'] === 'delete') {
    $idp = (int)$_POST['idp'];

    if ($idp > 0) {
        $stmt = $pdo->prepare('DELETE FROM zespoly WHERE ID_ZESP = :idp');
        $stmt->bindValue(':idp', $idp, PDO::PARAM_INT);
        $stmt->execute();

        echo "Sukces";
    }
}
