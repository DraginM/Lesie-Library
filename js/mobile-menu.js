document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.querySelector('header .menu');
    const overlay = document.querySelector('.mobile-menu');

    if (!menuButton || !overlay) return;

    function openMenu() {
        overlay.style.display = 'flex';
        menuButton.classList.add('close');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        overlay.style.display = 'none';
        menuButton.classList.remove('close');
        document.body.style.overflow = '';
    }

    menuButton.addEventListener('click', function () {
        if (overlay.style.display === 'flex') {
            closeMenu();
        } else {
            openMenu();
        }
    });



    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') {
            closeMenu();
        }
    });

    const navLinks = overlay.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });
});