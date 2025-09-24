<?php
session_start();
if (!($_SESSION['authorized'] ?? false)) {
    exit('Доступ запрещён');
}

function uploadCover($file, $currentCover = '') {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return $currentCover; // не меняем, если не загружено
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5 МБ

    if (!in_array($file['type'], $allowedTypes)) {
        return $currentCover; // неподдерживаемый формат
    }
    if ($file['size'] > $maxSize) {
        return $currentCover; // слишком большой
    }

    // Генерируем уникальное имя
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'cover_' . uniqid() . '.' . strtolower($ext);
    $uploadDir = '../data/covers/';
    $uploadPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return 'covers/' . $filename;
    }

    return $currentCover;
}

$booksFile = '../data/books.json';
$books = json_decode(file_get_contents($booksFile), true);
if (!is_array($books)) {
    $books = [];
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $newId = count($books) > 0 ? max(array_column($books, 'id')) + 1 : 1;
    $genres = !empty($_POST['genres']) ? array_map('trim', explode(',', $_POST['genres'])) : [];
    $coverPath = uploadCover($_FILES['cover'] ?? []);
    $newBook = [
        'id' => $newId,
        'cover' => $coverPath,
        'title' => trim($_POST['title'] ?? ''),
        'author' => trim($_POST['author'] ?? ''),
        'year' => !empty($_POST['year']) ? (int)$_POST['year'] : null,
        'publisher' => trim($_POST['publisher'] ?? ''),
        'genres' => $genres,
        'shelf' => trim($_POST['shelf'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'isbn' => trim($_POST['isbn'] ?? ''),
        'position' => !empty($_POST['position']) ? (int)$_POST['position'] : null,
        'status' => $_POST['status'] ?? 'В библиотеке'
    ];

    $books[] = $newBook;
    file_put_contents($booksFile, json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: index.php?msg=added');
    exit;

} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    // Используем обычную функцию вместо fn()
    $books = array_filter($books, function($book) use ($id) {
        return $book['id'] !== $id;
    });
    // Сбрасываем ключи, чтобы не было дыр
    $books = array_values($books);
    file_put_contents($booksFile, json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: index.php?msg=deleted');
    exit;

} elseif ($action === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $found = false;
    foreach ($books as &$book) {
        if ($book['id'] === $id) {
            $book['cover'] = uploadCover($_FILES['cover'] ?? [], $book['cover']);
            $book['title'] = trim($_POST['title'] ?? $book['title']);
            $book['author'] = trim($_POST['author'] ?? $book['author']);
            $book['year'] = !empty($_POST['year']) ? (int)$_POST['year'] : null;
            $book['publisher'] = trim($_POST['publisher'] ?? $book['publisher']);
            $book['genres'] = !empty($_POST['genres']) ? array_map('trim', explode(',', $_POST['genres'])) : [];
            $book['shelf'] = trim($_POST['shelf'] ?? $book['shelf']);
            $book['description'] = trim($_POST['description'] ?? $book['description']);
            $book['isbn'] = trim($_POST['isbn'] ?? $book['isbn']);
            $book['position'] = !empty($_POST['position']) ? (int)$_POST['position'] : null;
            $book['status'] = $_POST['status'] ?? $book['status'];

            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents($booksFile, json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header('Location: index.php?msg=updated');
    exit;
}

// Если действие не распознано
header('Location: index.php?msg=error');
exit;