<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Domki Letniskowe</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .kontakt-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .kontakt-info {
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .kontakt-form {
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1rem;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .map-container {
            margin-top: 3rem;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: 0;
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
                <li><a href="admin.php">Panel admin</a></li>
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
        <h1 style="text-align:center;">Kontakt</h1>
        <p style="text-align:center;">Skontaktuj się z nami - odpowiemy na wszystkie pytania!</p>
        
        <div class="kontakt-container">
            <div class="kontakt-info">
                <h2>Dane kontaktowe</h2>
                <p><strong>Adres:</strong> ul. Jeziorna 1, 00-000 Miasto</p>
                <p><strong>Telefon:</strong> +48 123 456 789</p>
                <p><strong>Email:</strong> info@domkiletniskowe.pl</p>
                
                <h3>Godziny pracy recepcji:</h3>
                <p>Poniedziałek - Piątek: 8:00 - 20:00</p>
                <p>Sobota - Niedziela: 9:00 - 19:00</p>
                
                <h3>Jak dojechać:</h3>
                <p>Nasz ośrodek jest położony 5 km od centrum miasta, nad malowniczym jeziorem. Dojazd jest dobrze oznakowany - wystarczy kierować się na Jezioro Piękne.</p>
            </div>
            
            <div class="kontakt-form">
                <h2>Formularz kontaktowy</h2>
                <form action="wyslij_kontakt.php" method="POST">
                    <div class="form-group">
                        <label for="imie">Imię i nazwisko</label>
                        <input type="text" id="imie" name="imie" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="temat">Temat</label>
                        <input type="text" id="temat" name="temat" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="wiadomosc">Treść wiadomości</label>
                        <textarea id="wiadomosc" name="wiadomosc" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-rezerwuj">Wyślij wiadomość</button>
                </form>
            </div>
        </div>
        
        <div class="map-container">
            <h2 style="text-align:center;">Nasza lokalizacja</h2>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d17162.823949174377!2d21.62748945384036!3d50.54392241866374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x473d49b794e76d9d%3A0x910582f0f2b03d1e!2sJezioro%20Tarnobrzeskie!5e1!3m2!1spl!2spl!4v1746788863267!5m2!1spl!2spl" width="1200" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>        </div>
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
