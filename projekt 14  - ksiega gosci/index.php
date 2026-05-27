<?php
require_once 'database.php';

$entries = [];
if (!$bazaErr) {
    $stmt = $pdo->prepare("SELECT id, author_name, author_email, content, content_html, created_at FROM entries WHERE status = 'approved' ORDER BY created_at DESC");
    $stmt->execute();
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Księga Gości</title>
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary ">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Księga gości</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Wpisy</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item d-flex">
                        <a class="nav-link me-2" href="admin.php">Panel Administratora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($bazaErr): ?>
            <div class="alert alert-danger">Przepraszamy, wystąpił błąd połączenia z bazą danych.</div>
        <?php elseif (empty($entries)): ?>
            <div class="alert alert-info">Brak wpisów w księdze gości.</div>
        <?php else: ?>
            <h1 class="mb-4 text-center">Wpisy</h1>
            <?php foreach ($entries as $entry): ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <strong><?= htmlspecialchars($entry['author_name']) ?></strong>
                            <?php if ($entry['author_email']): ?>
                                <a href="mailto:<?= htmlspecialchars($entry['author_email']) ?>" class="ms-2 text-decoration-none small">
                                    <?= htmlspecialchars($entry['author_email']) ?>
                                </a>
                            <?php endif; ?>
                        </span>
                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($entry['created_at'])) ?></small>
                    </div>
                    <div class="card-body">
                        <?php if ($entry['content_html']): ?>
                            <?= $entry['content_html'] ?>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($entry['content'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>



    <script src="../CDN/js/bootstrap.bundle.min.js"></script>
    <script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
