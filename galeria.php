<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Galeria</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
main{
    padding-top: 100px;
}
.slider-container {
    position: relative;
    max-width: 900px;
    margin: 40px auto;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

.slider {
    position: relative;
    width: 100%;
    height: 600px;
}

.slide {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.slide.active {
    opacity: 1;
}

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    padding: 1rem;
    cursor: pointer;
    font-size: 1.5rem;
}

.prev { left: 10px; }
.next { right: 10px; }
    </style>
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
                <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_email'])): ?>
                <li><a href="admin.php">Panel admin</a></li>
                <?php endif; ?>
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
<script src =scripts/galeria.js></script>
</body>
</html>