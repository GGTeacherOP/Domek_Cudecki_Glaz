<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atrakcje w okolicy</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Dodatkowe style dla tej podstrony */
        .atrakcje-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }
        .atrakcja {
            background: #f9f9f9;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .atrakcja:hover {
            transform: translateY(-5px);
        }
        .atrakcja img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .atrakcja-tekst {
            padding: 15px;
        }
        .atrakcja h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .atrakcja p {
            color: #666;
        }
        @media (max-width: 768px) {
            .atrakcje-lista {
                grid-template-columns: 1fr;
            }
        }
        main {
    position: relative; /* Wymagane dla pozycjonowania tła */
    min-height: 100vh;
    padding-top: 80px; /* Dostosuj do headera */
    max-width: 100%;
}

.background-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('assets/gory-tlo.jpg');
    background-size: cover;
    background-position: center;
    opacity: 0.3; /* Przezroczystość 30% */
    z-index: -1; /* Pod treścią */
}

.content-wrapper {
    position: relative; /* Utrzymuje treść nad tłem */
    padding: 20px;
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
                <li><a href="atrakcje.html">Atrakcje</a></li>
                <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_email'])): ?>
                <li><a href="admin.php">Panel admin</a></li>
                <?php endif; ?>
                <li class="login-btn"><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Główna zawartość -->
    <main>
    <div class="background-layer"></div> <!-- Nowy element dla tła -->
    <div class="content-wrapper"> <!-- Kontener dla treści -->
        <h2 style="text-align: center; margin: 30px 0;">Atrakcje w okolicy</h2>
        
        <div class="atrakcje-lista">
            <!-- Atrakcja 1 -->
            <div class="atrakcja">
                <img src="assets/jezioro.jpg" alt="Jezioro Łabędzia">
                <div class="atrakcja-tekst">
                    <h3>Jezioro Łabędzia</h3>
                    <p>Malownicze jezioro oddalone o 2 km od domku. Idealne miejsce na kąpiele, wędkowanie i kajaki. W sezonie działa wypożyczalnia sprzętu wodnego.</p>
                </div>
            </div>
            
            <!-- Atrakcja 2 -->
            <div class="atrakcja">
                <img src="assets/las.jpg" alt="Puszcza Zielona">
                <div class="atrakcja-tekst">
                    <h3>Puszcza Zielona</h3>
                    <p>Rozległy kompleks leśny z trasami rowerowymi i szlakami pieszymi. Można spotkać dzikie zwierzęta i zbierać grzyby (w sezonie).</p>
                </div>
            </div>
            
            <!-- Atrakcja 3 -->
            <div class="atrakcja">
                <img src="assets/zamek.jpg" alt="Zamek Krzyżacki">
                <div class="atrakcja-tekst">
                    <h3>Zamek Krzyżacki</h3>
                    <p>Historyczny zamek z XIV wieku, oddalony o 15 km. Organizowane są nocne zwiedzania z przewodnikiem w kostiumach.</p>
                </div>
            </div>
            
            <!-- Atrakcja 4 -->
            <div class="atrakcja">
                <img src="assets/spa.jpg" alt="Strefa Spa & Wellness">
                <div class="atrakcja-tekst">
                    <h3>Strefa Spa & Wellness</h3>
                    <p>Luksusowe SPA oferujące masaże, baseny termalne i sauny. Znajduje się w odległości 10 km od domku.</p>
                </div>
            </div>
            
            <!-- Atrakcja 5 -->
            <div class="atrakcja">
                <img src="assets/muzeum.jpg" alt="Muzeum Regionalne">
                <div class="atrakcja-tekst">
                    <h3>Muzeum Regionalne</h3>
                    <p>Interaktywne muzeum prezentujące historię regionu. Działa tu także kawiarnia z domowymi ciastami.</p>
                </div>
            </div>
            
            <!-- Atrakcja 6 -->
            <div class="atrakcja">
                <img src="assets/rowery.jpg" alt="Trasy rowerowe">
                <div class="atrakcja-tekst">
                    <h3>Trasy rowerowe</h3>
                    <p>Sieć oznakowanych tras rowerowych o różnym poziomie trudności. Można wypożyczyć rowery w pobliskiej wypożyczalni.</p>
                </div>
            </div>
            <!-- Atrakcja 7 -->
            <div class="atrakcja">
                <img src="assets/park-linowy.jpg" alt="Park linowy w lesie">
                <div class="atrakcja-tekst">
                    <h3>Park Linowy "Leśna Przygoda"</h3>
                    <p>Adrenalina dla całej rodziny! Trasy o różnym poziomie trudności, zjazdy tyrolskie i mosty linowe zawieszone w koronach drzew. Dla dzieci specjalna strefa z opiekunem.</p>
                </div>
            </div>
            <!-- Atrakcja 8 -->
            <div class="atrakcja">
                <img src="assets/widok.jpg" alt="Widok z góry na dolinę">
                <div class="atrakcja-tekst">
                    <h3>Punkt Widokowy "Góra Panorama"</h3>
                    <p>Najpiękniejsza panorama w regionie! Łatwa ścieżka (1.5 km) prowadzi na szczyt, skąd widać jezioro, lasy i okoliczne wioski. Idealne miejsce na zachód słońca.</p>
                </div>
            </div>
            <!-- Atrakcja 9 -->
            <div class="atrakcja">
                <img src="assets/skansen.jpg" alt="Drewniane chaty w skansenie">
                <div class="atrakcja-tekst">
                    <h3>Skansen "Dawna Wieś"</h3>
                    <p>Żywe muzeum tradycji! Zobacz XVIII-wieczne chaty, warsztaty rzemieślnicze i pokazy wypieku chleba. W weekendy organizowane są warsztaty dla dzieci.</p>
                </div>
            </div>
            <!-- Atrakcja 10 -->
            <div class="atrakcja">
                <img src="assets/splyw.jpg" alt="Kajaki na rzece">
                <div class="atrakcja-tekst">
                    <h3>Spływ Rzeką Nurt</h3>
                    <p>3-godzinna trasa kajakowa przez malownicze zakola rzeki. Wypożyczalnia zapewnia suchy bagaż i transport z powrotem. Dla rodzin dostępne stabilne kanadyjki.</p>
                </div>
            </div>
        </div>
    </div>
    </main>

    <!-- Stopka -->
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