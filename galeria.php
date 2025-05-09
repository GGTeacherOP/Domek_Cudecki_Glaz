<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Galeria</title>
    <link rel="stylesheet" href="css/style_galeria.css">
</head>
<body>
<header>
<a href="index.php"><img src="assets/logo.png" alt="Logo Domki Letniskowe" class="logo"></a>
        <nav>
            <ul>
                <li><a href="oferta.php">Nasza Oferta</a></li>
                <li><a href="galeria.php">Galeria</a></li>
                <li><a href="kontakt.php">Kontakt</a></li>
                <li><a href="opinie.php">Opinie</a></li>
                <li><a href="admin.php">Panel admin</a></li>
                <li class="login-btn"><a href="login.php">Login</a></li>
            </ul>
        </nav>
</header>
<main>
    <h2 style="text-align:center;">Galeria zdjęć</h2>
    <div class="slider-container">
        <div class="slider">
            <img src="assets/domek1.jpg" class="slide active" alt="1">
            <img src="assets/domek2.jpg" class="slide" alt="2">
            <img src="assets/domek3.jpg" class="slide" alt="3">
        </div>
        <button class="slider-btn prev">&#10094;</button>
        <button class="slider-btn next">&#10095;</button>
    </div>
</main>
<footer>
    <div class="footer-content">
        <div class="kontakt">
            <h3>Kontakt</h3>
            <p>Tel: +48 123 456 789</p>
            <p>Email: info@domkiletniskowe.pl</p>
            <p>Adres: ul. Jeziorna 1, 00-000 Miasto</p>
        </div>
        <div class="social-media">
            <h3>Znajdź nas</h3>
            <a href="#" class="social-link">Facebook</a>
            <a href="#" class="social-link">Instagram</a>
            <a href="#" class="social-link">Twitter</a>
        </div>
    </div>
</footer>

<script>
        document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelectorAll('.slide');
            const prevBtn = document.querySelector('.prev');
            const nextBtn = document.querySelector('.next');
            let current = 0;

            function updateSlide() {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === current);
                });
            }

            prevBtn.addEventListener('click', () => {
                current = (current - 1 + slides.length) % slides.length;
                updateSlide();
            });

            nextBtn.addEventListener('click', () => {
                current = (current + 1) % slides.length;
                updateSlide();
            });
        });
    </script>
</body>
</html>