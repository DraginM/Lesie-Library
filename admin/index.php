<?php
session_start();
if (!($_SESSION['authorized'] ?? false)) {
    header('Location: login.php');
    exit;
}

$books = json_decode(file_get_contents('../data/books.json'), true);
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
            <h2>Добавить книгу</h2>
            <form id="bookForm" class="modal" method="POST" action="actions.php">
                <input type="hidden" name="action" value="add">
                <div class="top">
                    <img id="coverPreview" src="../png/placeholder-book.jpg" alt="Предпросмотр">
                    <div class="text">
                        <input type="text" name="title" placeholder="Название" required>
                        <input type="text" name="author" placeholder="Автор" required>
                        <input type="number" name="year" placeholder="Год">
                        <input type="text" name="publisher" placeholder="Издательство">
                        <input type="text" name="genres" placeholder="Жанры (через запятую)">
                        <input type="text" name="shelf" placeholder="Полка">
                        <input type="text" name="isbn" placeholder="ISBN">
                        <input type="number" name="position" placeholder="Номер в картотеке">
                        <select name="status">
                            <option value="В библиотеке">В библиотеке</option>
                            <option value="На руках">На руках</option>
                            <option value="Ремонт">Ремонт</option>
                        </select>
                        <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" id="coverInput">
                    </div>
                </div>
                <textarea name="description" placeholder="Описание"></textarea>
                <button class="btn secc" type="submit">Добавить</button>
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

    <section class="table">
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Автор</th>
                        <th>Год</th>
                        <th>Жанры</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= $book['year'] ?: '-' ?></td>
                        <td><?= implode(', ', $book['genres'] ?? []) ?></td>
                        <td class="actions">
                            <form method="GET" action="edit.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn secc">Изменить</button>
                            </form>
                            <form method="POST" action="actions.php" style="display:inline;" onsubmit="return confirm('Удалить книгу?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn secc">Удалить</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>