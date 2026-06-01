<?php
require_once 'database.php';

define('CONTENT_MAX_LENGTH', 5000);

function bbcode_to_html($text) {
    $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

    $patterns = [
        '/\[url=(.*?)\](.*?)\[\/url\]/is',
        '/\[url\](.*?)\[\/url\]/is',
        '/\[email\](.*?)\[\/email\]/is',
        '/\[img\](.*?)\[\/img\]/is',
        '/\[b\](.*?)\[\/b\]/is',
        '/\[i\](.*?)\[\/i\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[s\](.*?)\[\/s\]/is',
        '/\[center\](.*?)\[\/center\]/is',
        '/\[color=(.*?)\](.*?)\[\/color\]/is',
        '/\[size=(.*?)\](.*?)\[\/size\]/is',
        '/\[quote\](.*?)\[\/quote\]/is',
        '/\[code\](.*?)\[\/code\]/is',
    ];

    $replacements = [
        '<a href="$1">$2</a>',
        '<a href="$1">$1</a>',
        '<a href="mailto:$1">$1</a>',
        '<img src="$1" alt="">',
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<u>$1</u>',
        '<s>$1</s>',
        '<div style="text-align:center">$1</div>',
        '<span style="color:$1">$2</span>',
        '<span style="font-size:$1">$2</span>',
        '<blockquote>$1</blockquote>',
        '<pre><code>$1</code></pre>',
    ];

    $text = preg_replace($patterns, $replacements, $text);

    $text = preg_replace('/\[\*\](.*?)\[\/\*\]/is', '<li>$1</li>', $text);
    $text = preg_replace('/\[list\](.*?)\[\/list\]/is', '<ul>$1</ul>', $text);

    $text = nl2br($text);

    return $text;
}

$message = '';
$messageType = '';

if (!$bazaErr && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_name = trim($_POST['author_name'] ?? '');
    $author_email = trim($_POST['author_email'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $errors = [];
    if ($author_name === '') {
        $errors[] = 'Imię / pseudonim jest wymagane.';
    }
    if ($content === '') {
        $errors[] = 'Treść wpisu jest wymagana.';
    } elseif (mb_strlen($content) > CONTENT_MAX_LENGTH) {
        $errors[] = 'Treść wpisu nie może przekraczać ' . CONTENT_MAX_LENGTH . ' znaków.';
    }
    if ($author_email !== '' && !filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Podaj poprawny adres email.';
    }

    if (empty($errors)) {
        $content_html = bbcode_to_html($content);

        $stmt = $pdo->prepare("INSERT INTO entries (author_name, author_email, content, content_html, ip_address, status, created_at) VALUES (:author_name, :author_email, :content, :content_html, :ip_address, 'pending', NOW())");
        $stmt->execute([
            ':author_name' => $author_name,
            ':author_email' => $author_email ?: null,
            ':content' => $content,
            ':content_html' => $content_html,
            ':ip_address' => $ip_address,
        ]);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Wpis został dodany i oczekuje na moderację.']);
            exit;
        }

        $message = 'Wpis został dodany i oczekuje na moderację.';
        $messageType = 'success';
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $message = implode('<br>', $errors);
        $messageType = 'danger';
    }
}

$ajaxMode = isset($_GET['ajax']);

// Główna zawartość strony
ob_start();
?>
<div class="container mt-4">
    <?php if ($bazaErr): ?>
        <div class="alert alert-danger">Przepraszamy, wystąpił błąd połączenia z bazą danych.</div>
    <?php else: ?>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div id="alert-container"></div>

        <h1 class="mb-4 text-center">Księga Gości</h1>

        <div class="text-end mb-3">
            <button id="toggle-form-btn" class="btn btn-primary" type="button">
                <i class="me-1">+</i> Dodaj wpis
            </button>
        </div>

        <div id="form-container" class="card mb-4 d-none">
            <div class="card-header">
                <h5 class="mb-0">Dodaj nowy wpis</h5>
            </div>
            <div class="card-body">
                <form id="entry-form" method="post" action="index.php">
                    <div class="mb-3">
                        <label for="author_name" class="form-label">Imię / pseudonim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="author_name" name="author_name" required maxlength="128">
                    </div>
                    <div class="mb-3">
                        <label for="author_email" class="form-label">Adres email (opcjonalnie)</label>
                        <input type="email" class="form-control" id="author_email" name="author_email" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Treść <span class="text-danger">*</span></label>
                        <div class="btn-toolbar mb-2" role="toolbar">
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="b" title="Pogrubienie">B</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="i" title="Kursywa"><em>I</em></button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="u" title="Podkreślenie"><u>U</u></button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="s" title="Przekreślenie"><s>S</s></button>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="url" title="Link">URL</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="email" title="Email">@</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="img" title="Obrazek">IMG</button>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="quote" title="Cytat">CYTAT</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="code" title="Kod">KOD</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="center" title="Wyśrodkowanie">WYŚR</button>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="color" title="Kolor czcionki">COLOR</button>
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="size" title="Rozmiar czcionki">SIZE</button>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary bbcode-btn" data-tag="list" title="Lista">LISTA</button>
                            </div>
                        </div>
                        <textarea class="form-control" id="content" name="content" rows="6" maxlength="<?= CONTENT_MAX_LENGTH ?>" required></textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted"><span id="char-count">0</span> / <?= CONTENT_MAX_LENGTH ?> znaków</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Dodaj wpis</button>
                </form>
            </div>
        </div>

        <h2 class="mb-3 text-center">Wpisy</h2><br>

        <div id="entries-container">
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Ładowanie wpisów...
            </div>
        </div>
    <?php endif; ?>
 </div>
<script>
// Pomocnicze funkcje
window.escapeHtml = function(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
};

window.formatDate = function(dateStr) {
    var d = new Date(dateStr);
    var day = String(d.getDate()).padStart(2, '0');
    var month = String(d.getMonth() + 1).padStart(2, '0');
    var year = d.getFullYear();
    var hours = String(d.getHours()).padStart(2, '0');
    var minutes = String(d.getMinutes()).padStart(2, '0');
    return day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
};

window.nl2br = function(text) {
    return text.replace(/\n/g, '<br>');
};

// Ładowanie wpisów przez Ajax
window.loadEntries = function() {
    $.ajax({
        url: 'api/entries.php',
        method: 'GET',
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            renderEntries(response.entries || []);
        } else {
            $('#entries-container').html(
                '<div class="alert alert-danger">Nie udało się załadować wpisów.</div>'
            );
        }
    })
    .fail(function() {
        $('#entries-container').html(
            '<div class="alert alert-danger">Błąd połączenia z serwerem.</div>'
        );
    });
};

window.renderEntries = function(entries) {
    if (entries.length === 0) {
        $('#entries-container').html(
            '<div class="alert alert-info">Brak wpisów w księdze gości.</div>'
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
        html += '</div>';
    });

    $('#entries-container').html(html);
};
</script>
<?php
$mainContent = ob_get_clean();

if ($ajaxMode) {
    echo '<div id="main-content">' . $mainContent . '</div>';
    exit;
}
?>
<!doctype html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="book-half.svg">
    <title>Księga Gości</title>
    <link rel="stylesheet" href="../CDN/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php $activePage = 'index'; require 'navbar.php'; ?>

    <div id="main-content">
        <?= $mainContent ?>
    </div>

    <?php require 'footer.php'; ?>

    <script src="../CDN/js/bootstrap.bundle.min.js"></script>
    <script src="../CDN/jqeury/jquery-4.0.0.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
