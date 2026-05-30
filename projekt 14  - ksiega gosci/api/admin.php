<?php
require_once '../database.php';

header('Content-Type: application/json');

// Obsługa żądań POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metoda nie jest obsługiwana.']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($pdo);
        break;
    case 'get_entries':
        handleGetEntries($pdo);
        break;
    case 'approve':
        handleApprove($pdo);
        break;
    case 'reject':
        handleReject($pdo);
        break;
    case 'delete':
        handleDelete($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Nieznana akcja.']);
}


// Logowanie administratora

function handleLogin($pdo) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Podaj nazwę użytkownika i hasło.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Zalogowano pomyślnie
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            echo json_encode(['success' => true, 'message' => 'Zalogowano pomyślnie.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nieprawidłowa nazwa użytkownika lub hasło.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    }
}


//  Pobranie wpisów do moderacji i zaakceptowanych
function handleGetEntries($pdo) {
    // Sprawdzenie czy użytkownik jest zalogowany
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany.']);
        return;
    }

    try {
        // Wpisy oczekujące na moderację
        $stmt = $pdo->prepare("SELECT id, author_name, author_email, content, content_html, ip_address, created_at FROM entries WHERE status = 'pending' ORDER BY created_at DESC");
        $stmt->execute();
        $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Wpisy zaakceptowane
        $stmt = $pdo->prepare("SELECT id, author_name, author_email, content, content_html, created_at FROM entries WHERE status = 'approved' ORDER BY created_at DESC");
        $stmt->execute();
        $approved = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'pending' => $pending,
            'approved' => $approved
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    }
}


 // Akceptacja wpisu

function handleApprove($pdo) {
    // Sprawdzenie czy użytkownik jest zalogowany
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany.']);
        return;
    }

    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe ID wpisu.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("UPDATE entries SET status = 'approved' WHERE id = :id AND status = 'pending'");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Wpis został zaakceptowany.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Wpis nie został znaleziony lub został już przetworzony.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    }
}


 // Odrzucenie wpisu

function handleReject($pdo) {
    // Sprawdzenie czy użytkownik jest zalogowany
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany.']);
        return;
    }

    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe ID wpisu.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM entries WHERE id = :id AND status = 'pending'");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Wpis został odrzucony.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Wpis nie został znaleziony lub został już przetworzony.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    }
}


// Usunięcie wpisu
function handleDelete($pdo) {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany.']);
        return;
    }

    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe ID wpisu.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM entries WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Wpis został usunięty.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Wpis nie został znaleziony.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    }
}
