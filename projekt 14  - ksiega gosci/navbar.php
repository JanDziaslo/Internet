<?php
/**
 * @param string $activePage - 'index' lub 'admin'
 */
$activePage = $activePage ?? 'index';
$isAdmin = isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Księga gości</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'index' ? ' active' : '' ?>"<?= $activePage === 'index' ? ' aria-current="page"' : '' ?> href="index.php">Wpisy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'admin' ? ' active' : '' ?>"<?= $activePage === 'admin' ? ' aria-current="page"' : '' ?> href="admin.php">Panel Administratora</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <span class="nav-link text-muted">Zalogowany jako: <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Wyloguj</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
