<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nasza Oferta - Domki Letniskowe</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .domek-szczegoly {
            margin: 3rem 0;
            padding: 2rem;
            background: var(--light-bg);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .domek-szczegoly img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .domek-cechy {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .cecha {
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .cena {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        .domek-opis {
            line-height: 1.8;
        }
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

    <main style="padding-top: 100px;">
        <h1 style="text-align:center;">Nasza Oferta</h1>
        <p style="text-align:center;">Wybierz domek idealny dla siebie i swojej rodziny</p>
        
        <div class="domek-szczegoly" id="domek1">
            <h2>Domek Słoneczny</h2>
            <img src="assets/sloneczny/domek1.jpg" alt="Domek Słoneczny">
            <div class="domek-cechy">
                <span class="cecha">2 sypialnie</span>
                <span class="cecha">Taras</span>
                <span class="cecha">Do 4 osób</span>
                <span class="cecha">Wi-Fi</span>
                <span class="cecha">TV</span>
                <span class="cecha">Miejsce na grilla</span>
            </div>
            <p class="cena">350 zł / doba</p>
            <div class="domek-opis">
                <p>Przestronny domek z dwiema sypialniami, idealny dla rodziny lub grupy przyjaciół. Domek wyposażony jest w wygodny taras z widokiem na jezioro, w pełni wyposażoną kuchnię oraz łazienkę z prysznicem.</p>
                <p>Dodatkowe atrakcje to miejsce na grilla w ogrodzie, dostęp do prywatnej plaży i możliwość wypożyczenia łódki.</p>
            </div>
            <a href="rezerwacja.php?domek=sloneczny" class="btn-rezerwuj">Zarezerwuj</a>
        </div>
        
        <div class="domek-szczegoly" id="domek2">
            <h2>Domek Brzozowy</h2>
            <img src="assets/brzozowy/domek1.jpg" alt="Domek Brzozowy">
            <div class="domek-cechy">
                <span class="cecha">1 sypialnia</span>
                <span class="cecha">Salon z rozkładaną kanapą</span>
                <span class="cecha">Do 3 osób</span>
                <span class="cecha">Wi-Fi</span>
                <span class="cecha">TV</span>
                <span class="cecha">Ogrzewanie</span>
            </div>
            <p class="cena">280 zł / doba</p>
            <div class="domek-opis">
                <p>Przytulny domek idealny dla pary lub małej rodziny. Posiada jedną sypialnię oraz salon z rozkładaną kanapą. W pełni wyposażona kuchnia pozwoli przygotować ulubione posiłki.</p>
                <p>Domek znajduje się w cieniu brzóz, co zapewnia przyjemny chłód nawet w upalne dni. Z okien rozpościera się piękny widok na pobliski las.</p>
            </div>
            <a href="rezerwacja.php?domek=brzozowy" class="btn-rezerwuj">Zarezerwuj</a>
        </div>
        
        <div class="domek-szczegoly" id="domek3">
            <h2>Domek Premium</h2>
            <img src="assets/premium/domek1.jpg" alt="Domek Premium">
            <div class="domek-cechy">
                <span class="cecha">3 sypialnie</span>
                <span class="cecha">Jacuzzi</span>
                <span class="cecha">Do 6 osób</span>
                <span class="cecha">Wi-Fi</span>
                <span class="cecha">Smart TV</span>
                <span class="cecha">Kominek</span>
                <span class="cecha">Sauna</span>
            </div>
            <p class="cena">550 zł / doba</p>
            <div class="domek-opis">
                <p>Luksusowy domek dla najbardziej wymagających gości. Trzy przestronne sypialnie, nowoczesna kuchnia oraz salon z kominkiem pozwolą cieszyć się komfortowym wypoczynkiem.</p>
                <p>Główną atrakcją jest prywatne jacuzzi na tarasie oraz sauna. Domek położony jest w najbardziej ustronnej części ośrodka, co gwarantuje prywatność i ciszę.</p>
            </div>
            <a href="rezerwacja.php?domek=premium" class="btn-rezerwuj">Zarezerwuj</a>
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
</body>
</html>
