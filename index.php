<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domki Letniskowe</title>
    <link rel="stylesheet" href="css/style.css">
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
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Wypocznij nad jeziorem!</h1>
            <p>Odkryj magię wypoczynku w naszych komfortowych domkach</p>
        </div>
    </section>

    <main>
        <section class="oferta-skrot">
            <h2>Nasze Domki</h2>
            <div class="domki-container">
                <div class="domek-karta">
                    <img src="assets/domek1.jpg" alt="Domek Słoneczny">
                    <h3>Domek Słoneczny</h3>
                    <p>Przestronny domek z 2 sypialniami i tarasem</p>
                    <a href="oferta.php#domek1" class="btn-wiecej">Więcej</a>
                </div>
                <div class="domek-karta">
                    <img src="assets/domek2.jpg" alt="Domek Fajny">
                    <h3>Domek Brzozowy</h3>
                    <p>Przytulny domek idealny dla rodziny</p>
                    <a href="oferta.php#domek2" class="btn-wiecej">Więcej</a>
                </div>
                <div class="domek-karta">
                    <img src="assets/domek3.jpg" alt="Domek Premium">
                    <h3>Domek Premium</h3>
                    <p>Luksusowy domek z jacuzzi</p>
                    <a href="oferta.php#domek3" class="btn-wiecej">Więcej</a>
                </div>
            </div>
        </section>

        <section class="opinie" id="opinie-slider">
            <h2>Opinie naszych gości</h2>
            <div class="opinie-container">
                <div class="opinia">
                    <p>"Wspaniałe miejsce na rodzinny wypoczynek!"</p>
                    <cite>Anna K.</cite>
                </div>
                <div class="opinia">
                    <p>"Przepiękna okolica i świetnie wyposażone domki"</p>
                    <cite>Marek W.</cite>
                </div>
                <div class="opinia">
                    <p>"Na pewno tu wrócimy!"</p>
                    <cite>Karolina M.</cite>
                </div>
            </div>
        </section>

        <section class="cta">
            <h2>Gotowy na wymarzony wypoczynek?</h2>
            <a href="rezerwacja.php" class="btn-rezerwuj">Zarezerwuj już teraz!</a>
        </section>
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
</body>
</html>