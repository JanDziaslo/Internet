<?php
require_once 'database.php';

if (isset($_POST['action'], $_POST['idp']) && $_POST['action'] === 'delete') {
    $idp = $_POST['idp'];

    if ($idp !== '') {
        $stmt = $pdo->prepare('DELETE FROM etaty WHERE NAZWA = :idp');
        $stmt->bindValue(':idp', $idp, PDO::PARAM_STR);
        $stmt->execute();

        echo "Sukces";
    }
}
