async function loadBooks() {
    const booksContainer = document.querySelector('.books-list');
    if (!booksContainer) return;

    try {
        const response = await fetch('../data/books.json');
        const books = await response.json();
        displayBooks(books);
    } catch (error) {
        console.error('Ошибка загрузки книг:', error);
        booksContainer.innerHTML = '<p>Книги временно недоступны :(</p>';
    }
}

function displayBooks(books) {
    const container = document.querySelector('.books-list');
    container.innerHTML = '';

    books.forEach(book => {
        const bookCard = document.createElement('div');
        bookCard.className = 'book';
        bookCard.dataset.id = book.id;

        const coverUrl = book.cover ? `./data/${book.cover}` : './png/placeholder-book.jpg';

        bookCard.innerHTML = `
            <img src="${coverUrl}" alt="${book.title}" onerror="this.src='./png/placeholder-book.jpg'">
            <span class="name">${book.title}</span>
            <span class="author">${book.author}</span>
        `;

        bookCard.addEventListener('click', () => openBookModal(book));
        container.appendChild(bookCard);
    });
}

function openBookModal(book) {
    const modal = document.createElement('div');
    modal.classList.add('modal');
    modal.innerHTML = `
        <div class="top">
            <img src="./data/${book.cover}" onerror="this.src='./png/placeholder-book.jpg'">
            <div class="text">
                <h3>${book.title}</h3>
                <span><strong>Автор:</strong> ${book.author}</span>
                <span><strong>Год:</strong> ${book.year}</span>
                <span><strong>Издательство:</strong> ${book.publisher}</span>
                <span><strong>Жанры:</strong> ${book.genres?.join(', ')}</span>
                <span><strong>Номер:</strong> ${book.position}</span>
                <span><strong>В наличии:</strong> ${book.status}</span>
            </div>
        </div>
        <p>${book.description || 'Описание отсутствует.'}</p>
        <button class="btn secc" onclick="this.parentElement.remove()">
            Закрыть
        </button>
    `;
    document.body.appendChild(modal);
}

document.addEventListener('DOMContentLoaded', loadBooks);