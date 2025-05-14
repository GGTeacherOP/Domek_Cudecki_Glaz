<?php
session_start();

// Pobierz 3 najnowsze opinie z bazy
$opinie = [];
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'domki_letniskowe';
$conn = mysqli_connect($host, $user, $pass, $db);
if ($conn) {
    $sql = "SELECT o.content, o.rating, u.username, o.created_at 
            FROM opinions o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.id DESC 
            LIMIT 3";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $opinie[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_close($conn);
}
?>
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
                <li><a href="atrakcje.php">Atrakcje</a></li>
                <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_email'])): ?>
                <li><a href="admin.php">Panel użytkownika</a></li>
                <li class="login-btn" style="color:var(--primary-color); font-weight:bold; background:none;">
                    Witaj, <?= htmlspecialchars($_SESSION['user_name']) ?>
                </li>
                <li class="login-btn"><a href="logout.php">Wyloguj</a></li>
                <?php else: ?>
                <li class="login-btn"><a href="login.php">Login</a></li>
                <?php endif; ?>
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
                <?php foreach($opinie as $opinia): ?>
                <div class="opinia">
                    <div class="gwiazdki" style="color:gold;font-size:1.2rem;">
                        <?php
                        $rating = (int)$opinia['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <p>"<?= htmlspecialchars($opinia['content']) ?>"</p>
                    <cite><?= htmlspecialchars($opinia['username']) ?>, <?= date('d.m.Y', strtotime($opinia['created_at'])) ?></cite>
                </div>
                <?php endforeach; ?>
                <?php if (empty($opinie)): ?>
                <div class="opinia">
                    <p>Brak opinii do wyświetlenia.</p>
                </div>
                <?php endif; ?>
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