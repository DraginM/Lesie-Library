<?php
session_start();
if (!($_SESSION['authorized'] ?? false)) {
    header('Location: login.php');
    exit;
}

$books = json_decode(file_get_contents('../data/books.json'), true);
$bookId = (int)$_GET['id'];
$book = null;

foreach ($books as $b) {
    if ($b['id'] === $bookId) {
        $book = $b;
        break;
    }
}

if (!$book) {
    die('Книга не найдена');
}

if ($_POST) {
    $genres = $_POST['genres'] ? explode(',', $_POST['genres']) : [];
    $genres = array_map('trim', $genres);

    foreach ($books as &$b) {
        if ($b['id'] === $bookId) {
            $b['title'] = $_POST['title'];
            $b['author'] = $_POST['author'];
            $b['year'] = $_POST['year'] ? (int)$_POST['year'] : null;
            $b['publisher'] = $_POST['publisher'];
            $b['genres'] = $genres;
            $b['shelf'] = $_POST['shelf'];
            $b['description'] = $_POST['description'];
            $b['isbn'] = $_POST['isbn'];
            $b['status'] = $_POST['status'];
            break;
        }
    }

    file_put_contents('../data/books.json', json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: index.php?msg=updated');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Админка — Книги</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/admin-books.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>📚 Управление книгами</h1>
            <a class="btn prime" href="logout.php">Выйти</a>
        </div>
    </header>

    <section class="main">
        <div class="container">
            <h2>Изменить книгу</h2>
            <form id="bookForm" class="modal" method="POST" action="actions.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                <div class="top">
                    <?php if (!empty($book['cover'])): ?>
                        <img src="../data/<?= htmlspecialchars($book['cover']) ?>" alt="Обложка" id="coverPreview">
                    <?php else: ?>
                        <img src="../png/placeholder-book.jpg" alt="Обложка" id="coverPreview">
                    <?php endif; ?>
                    <div class="text">
                        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" placeholder="Название" required>
                        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" placeholder="Автор" required>
                        <input type="number" name="year" value="<?= $book['year'] ?>" placeholder="Год">
                        <input type="text" name="publisher" value="<?= htmlspecialchars($book['publisher'] ?? '') ?>" placeholder="Издательство">
                        <input type="text" name="genres" value="<?= implode(', ', $book['genres'] ?? []) ?>" placeholder="Жанры (через запятую)">
                        <input type="text" name="shelf" value="<?= htmlspecialchars($book['shelf']) ?>" placeholder="Полка">
                        <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>" placeholder="ISBN">
                        <input type="number" name="position" value="<?= $book['position'] ?>" placeholder="Номер в картотеке">
                        <select name="status">
                            <?php
                            $statuses = ['В наличии', 'На руках', 'Ремонт'];
                            foreach ($statuses as $status):
                                $selected = ($book['status'] ?? 'В наличии') === $status ? 'selected' : '';
                            ?>
                            <option value="<?= $status ?>" <?= $selected ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" id="coverInput">
                    </div>
                </div>
                <textarea name="description" placeholder="Описание"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
                <div class="bottom">
                    <a href="./index.php" class="btn secc">Отмена</a>
                    <button class="btn secc" type="submit">Сохранить</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('coverInput').addEventListener('change', function(e) {
            const preview = document.getElementById('coverPreview');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>