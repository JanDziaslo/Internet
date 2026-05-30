<?php
require_once 'database.php';

// Sprawdzenie czy użytkownik jest zalogowany
$isLoggedIn = isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administratora - Księga Gości</title>
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php $activePage = 'admin'; require 'navbar.php'; ?>

    <div class="container mt-4">
        <?php if ($bazaErr): ?>
            <div class="alert alert-danger">Przepraszamy, wystąpił błąd połączenia z bazą danych.</div>
        <?php else: ?>

            <?php if (!$isLoggedIn): ?>
                <!-- Formularz logowania -->
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0 text-center">Logowanie do panelu admina</h4>
                            </div>
                            <div class="card-body">
                                <div id="login-alert"></div>
                                <form id="login-form">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nazwa użytkownika</label>
                                        <input type="text" class="form-control" id="username" name="username" required maxlength="64" autocomplete="username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Hasło</label>
                                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Zaloguj się</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Panel administracyjny -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="mb-0">Panel Moderacji</h1>
                    <button id="refresh-btn" class="btn btn-outline-secondary">
                        ↻ Odśwież listę
                    </button>
                </div>

                <div id="admin-alert"></div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Wpisy oczekujące na moderację</h5>
                        <span id="pending-count" class="badge bg-warning text-dark">0</span>
                    </div>
                    <div class="card-body">
                        <div id="pending-entries">
                            <div class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Ładowanie wpisów...
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Wpisy zaakceptowane</h5>
                        <span id="approved-count" class="badge bg-success">0</span>
                    </div>
                    <div class="card-body">
                        <div id="approved-entries">
                            <div class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Ładowanie wpisów...
                            </div>
                        </div>
                    </div>
                </div>

                <script src="../CDN/js/bootstrap.bundle.min.js"></script>
                <script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
                <script>
                $(function() {
                    // Załaduj wpisy
                    loadEntries();

                    // Odśwież listę
                    $('#refresh-btn').on('click', function() {
                        loadEntries();
                    });

                    // Załaduj wpisy z API
                    function loadEntries() {
                        $.ajax({
                            url: 'api/admin.php',
                            method: 'POST',
                            data: { action: 'get_entries' },
                            dataType: 'json'
                        })
                        .done(function(response) {
                            if (response.success) {
                                renderPendingEntries(response.pending || []);
                                renderApprovedEntries(response.approved || []);
                            } else {
                                showAlert('admin-alert', response.message || 'Błąd ładowania wpisów.', 'danger');
                            }
                        })
                        .fail(function() {
                            showAlert('admin-alert', 'Błąd połączenia z serwerem.', 'danger');
                        });
                    }

                    // Wyświetl wpisy oczekujące
                    function renderPendingEntries(entries) {
                        $('#pending-count').text(entries.length);

                        if (entries.length === 0) {
                            $('#pending-entries').html(
                                '<div class="text-center text-muted py-3">Brak wpisów do moderacji.</div>'
                            );
                            return;
                        }

                        var html = '';
                        entries.forEach(function(entry) {
                            html += '<div class="card mb-3 entry-card" data-id="' + entry.id + '">';
                            html += '<div class="card-header d-flex justify-content-between align-items-center">';
                            html += '<span>';
                            html += '<strong>' + escapeHtml(entry.author_name) + '</strong>';
                            if (entry.author_email) {
                                html += ' <a href="mailto:' + escapeHtml(entry.author_email) + '" class="ms-2 text-decoration-none small">' + escapeHtml(entry.author_email) + '</a>';
                            }
                            html += ' <small class="text-muted ms-2">(IP: ' + escapeHtml(entry.ip_address) + ')</small>';
                            html += '</span>';
                            html += '<small class="text-muted">' + formatDate(entry.created_at) + '</small>';
                            html += '</div>';
                            html += '<div class="card-body">';
                            if (entry.content_html) {
                                html += entry.content_html;
                            } else {
                                html += nl2br(escapeHtml(entry.content));
                            }
                            html += '</div>';
                            html += '<div class="card-footer d-flex gap-2">';
                            html += '<button class="btn btn-success btn-sm approve-btn" data-id="' + entry.id + '">✓ Zaakceptuj</button>';
                            html += '<button class="btn btn-danger btn-sm reject-btn" data-id="' + entry.id + '">✗ Odrzuć</button>';
                            html += '</div>';
                            html += '</div>';
                        });

                        $('#pending-entries').html(html);
                    }

                    // Wyświetl wpisy zaakceptowane
                    function renderApprovedEntries(entries) {
                        $('#approved-count').text(entries.length);

                        if (entries.length === 0) {
                            $('#approved-entries').html(
                                '<div class="text-center text-muted py-3">Brak zaakceptowanych wpisów.</div>'
                            );
                            return;
                        }

                        var html = '';
                        entries.forEach(function(entry) {
                            html += '<div class="card mb-3 entry-card">';
                            html += '<div class="card-header d-flex justify-content-between align-items-center">';
                            html += '<span>';
                            html += '<strong>' + escapeHtml(entry.author_name) + '</strong>';
                            if (entry.author_email) {
                                html += ' <a href="mailto:' + escapeHtml(entry.author_email) + '" class="ms-2 text-decoration-none small">' + escapeHtml(entry.author_email) + '</a>';
                            }
                            html += '</span>';
                            html += '<small class="text-muted">' + formatDate(entry.created_at) + '</small>';
                            html += '</div>';
                            html += '<div class="card-body">';
                            if (entry.content_html) {
                                html += entry.content_html;
                            } else {
                                html += nl2br(escapeHtml(entry.content));
                            }
                            html += '</div>';
                            html += '<div class="card-footer">';
                            html += '<button class="btn btn-outline-danger btn-sm delete-btn" data-id="' + entry.id + '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg> Usuń</button>';
                            html += '</div>';
                            html += '</div>';
                        });

                        $('#approved-entries').html(html);
                    }

                    // Akceptacja wpisu
                    $(document).on('click', '.approve-btn', function() {
                        var id = $(this).data('id');
                        var $card = $(this).closest('.entry-card');

                        $.ajax({
                            url: 'api/admin.php',
                            method: 'POST',
                            data: { action: 'approve', id: id },
                            dataType: 'json'
                        })
                        .done(function(response) {
                            if (response.success) {
                                showAlert('admin-alert', 'Wpis został zaakceptowany.', 'success');
                                loadEntries();
                            } else {
                                showAlert('admin-alert', response.message || 'Błąd akceptacji wpisu.', 'danger');
                            }
                        })
                        .fail(function() {
                            showAlert('admin-alert', 'Błąd połączenia z serwerem.', 'danger');
                        });
                    });

                    // Odrzucenie wpisu
                    $(document).on('click', '.reject-btn', function() {
                        var id = $(this).data('id');

                        $.ajax({
                            url: 'api/admin.php',
                            method: 'POST',
                            data: { action: 'reject', id: id },
                            dataType: 'json'
                        })
                        .done(function(response) {
                            if (response.success) {
                                showAlert('admin-alert', 'Wpis został odrzucony.', 'success');
                                loadEntries();
                            } else {
                                showAlert('admin-alert', response.message || 'Błąd odrzucenia wpisu.', 'danger');
                            }
                        })
                        .fail(function() {
                            showAlert('admin-alert', 'Błąd połączenia z serwerem.', 'danger');
                        });
                    });

                    // Usunięcie wpisu
                    $(document).on('click', '.delete-btn', function() {
                        var id = $(this).data('id');

                        if (!confirm('Czy na pewno chcesz usunąć ten wpis? Tej operacji nie można cofnąć.')) {
                            return;
                        }

                        $.ajax({
                            url: 'api/admin.php',
                            method: 'POST',
                            data: { action: 'delete', id: id },
                            dataType: 'json'
                        })
                        .done(function(response) {
                            if (response.success) {
                                showAlert('admin-alert', 'Wpis został usunięty.', 'success');
                                loadEntries();
                            } else {
                                showAlert('admin-alert', response.message || 'Błąd usuwania wpisu.', 'danger');
                            }
                        })
                        .fail(function() {
                            showAlert('admin-alert', 'Błąd połączenia z serwerem.', 'danger');
                        });
                    });

                    // Wyświetl alert
                    function showAlert(containerId, message, type) {
                        $('#' + containerId).html(
                            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                    }

                    // Escape HTML
                    function escapeHtml(text) {
                        if (!text) return '';
                        var div = document.createElement('div');
                        div.appendChild(document.createTextNode(text));
                        return div.innerHTML;
                    }

                    // Formatuj datę
                    function formatDate(dateStr) {
                        var d = new Date(dateStr);
                        var day = String(d.getDate()).padStart(2, '0');
                        var month = String(d.getMonth() + 1).padStart(2, '0');
                        var year = d.getFullYear();
                        var hours = String(d.getHours()).padStart(2, '0');
                        var minutes = String(d.getMinutes()).padStart(2, '0');
                        return day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
                    }

                    // nl2br
                    function nl2br(text) {
                        return text.replace(/\n/g, '<br>');
                    }
                });
                </script>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <?php if (!$isLoggedIn): ?>
    <script src="../CDN/js/bootstrap.bundle.min.js"></script>
    <script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
    <script>
    $(function() {
        $('#login-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Logowanie...');

            $.ajax({
                url: 'api/admin.php',
                method: 'POST',
                data: form.serialize() + '&action=login',
                dataType: 'json'
            })
            .done(function(response) {
                if (response.success) {
                    $('#login-alert').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        response.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                    );
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    $('#login-alert').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        (response.message || 'Nieprawidłowe dane logowania.') +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                    );
                }
            })
            .fail(function() {
                $('#login-alert').html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    'Błąd połączenia z serwerem.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                );
            })
            .always(function() {
                submitBtn.prop('disabled', false).text('Zaloguj się');
            });
        });
    });
    </script>
    <?php endif; ?>
    <?php require 'footer.php'; ?>
</body>
</html>
