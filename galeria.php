<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie Domków</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style ogólne */
        .galerie-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        h1.galerie-tytul {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
            font-size: 2.5rem;
            padding-top:20px;
        }
        
        /* Zakładki */
        .domki-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .tab-btn {
            padding: 0.8rem 2rem;
            background: #f5f5f5;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .tab-btn.active {
            background: #2c3e50;
            color: white;
        }
        
        .tab-btn:hover:not(.active) {
            background: #e0e0e0;
        }
        
        /* Pojemniki galerii */
        .galeria-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .galeria-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Style galerii */
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }
        
        .galeria-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            aspect-ratio: 4/3;
        }
        
        .galeria-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .galeria-item:hover .galeria-img {
            transform: scale(1.05);
        }
        
        .galeria-opis {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .galeria-item:hover .galeria-opis {
            transform: translateY(0);
        }
        
        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .lightbox-img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }
        
        .lightbox-close {
            position: absolute;
            top: 2rem;
            right: 2rem;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .galeria-grid {
                grid-template-columns: 1fr;
            }
            
            .domki-tabs {
                flex-direction: column;
                align-items: center;
            }
        }
main {
    position: relative; /* Wymagane dla pozycjonowania tła */
    min-height: 100vh;
    padding-top: 85px; 
    max-width: 100%;
}

.background-layer {
    position: absolute;
    top: 65px;
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

    <div class="background-layer"></div>
    
    <main class="galerie-container">
    <div class="content-wrapper">
        <h1 class="galerie-tytul">Galerie Naszych Domków</h1>
        
        <!-- Zakładki -->
        <div class="domki-tabs">
            <button class="tab-btn active" onclick="openTab('domek1')">Domek "Słoneczny"</button>
            <button class="tab-btn" onclick="openTab('domek2')">Domek "Brzozowy"</button>
            <button class="tab-btn" onclick="openTab('domek3')">Domek "Premium"</button>
        </div>
        
        <!-- Galeria Domku Słonecznego -->
        <div id="domek1" class="galeria-content active">
            <h2 style="text-align:center; margin-bottom:1.5rem;">Domek "Słoneczny"</h2>
            <div class="galeria-grid">
                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/domek1.jpg')">
                    <img src="assets/sloneczny/domek1.jpg" alt="Domek Słoneczny - widok zewnętrzny" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Front domku</h3>
                        <p>Drewniana elewacja wtopiona w las</p>
                    </div>
                </div>
                
                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/1.jpg')">
                    <img src="assets/sloneczny/1.jpg" alt="Salon w Domku Słonecznym" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Przytulny salon</h3>
                        <p>Salon wraz z w pełni wyposażoną kuchnią</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/2.jpg')">
                    <img src="assets/sloneczny/2.jpg" alt="Sypialnia w Domku Słonecznym" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Sypialnia</h3>
                        <p>Piękna sypialnia wyposażona w 2 łóżka</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/3.jpg')">
                    <img src="assets/sloneczny/3.jpg" alt="Łazienka w Domku Słonecznym" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Łazienka</h3>
                        <p>Łazienka wraz z prysznicem</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/4.jpg')">
                    <img src="assets/sloneczny/4.jpg" alt="Widok z okna" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Widok z okna</h3>
                        <p>Zapierający dech w piersiach widok na pobliski strumyk</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/sloneczny/5.jpg')">
                    <img src="assets/sloneczny/5.jpg" alt="Plan domku" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Plan domku</h3>
                        <p>Plan domku Słonecznego</p>
                    </div>
                </div>
                
                
            </div>
        </div>
    
        <!-- Galeria Domku Brzozowego -->
        <div id="domek2" class="galeria-content">
            <h2 style="text-align:center; margin-bottom:1.5rem;">Domek "Brzozowy"</h2>
            <div class="galeria-grid">
                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/domek1.jpg')">
                    <img src="assets/brzozowy/domek1.jpg" alt="Domek Brzozowy- widok zewnetrzny" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Domek Brzozowy</h3>
                        <p>Widok na front domku Brzozowego</p>
                    </div>
                </div>
                
                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/2.jpg')">
                    <img src="assets/brzozowy/2.jpg" alt="Salon" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Piękny salon</h3>
                        <p>Widok na salon z pierwszego piętra</p>
                    </div>
                </div>
                
                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/3.jpg')">
                    <img src="assets/brzozowy/3.jpg" alt="Sypialnia" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Sypialnia</h3>
                        <p>Sypialnia z łózkiem małżeńskim i balkonem</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/4.jpg')">
                    <img src="assets/brzozowy/4.jpg" alt="Łazienka" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Łazienka</h3>
                        <p>Piękna łazienka z drewnianym wykończeniem</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/5.jpg')">
                    <img src="assets/brzozowy/5.jpg" alt="Widok" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Widok</h3>
                        <p>Widok z balkonu na pobliski las</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/brzozowy/6.jpg')">
                    <img src="assets/brzozowy/6.jpg" alt="Plan" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Plan domku</h3>
                        <p>Plan domku Brzozowego</p>
                    </div>
                </div>
            </div>
        </div>
        
       
        <div id="domek3" class="galeria-content">
            <h2 style="text-align:center; margin-bottom:1.5rem;">Domek "Premium"</h2>
            <div class="galeria-grid">
                <div class="galeria-item" onclick="openLightbox('assets/premium/domek1.jpg')">
                    <img src="assets/premium/domek1.jpg" alt="Widok z tarasu" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Domek Premium</h3>
                        <p>Piękny domek pomiędzy drzewami</p>
                    </div>
                </div>
                
                <div class="galeria-item" onclick="openLightbox('assets/premium/2.jpg')">
                    <img src="assets/premium/2.jpg" alt="Kuchnia" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Kuchnia</h3>
                        <p>Nowoczesna kuchnia z pełnym wyposażeniem</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/premium/3.jpg')">
                    <img src="assets/premium/3.jpg" alt="Sypialnia" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Sypialnia</h3>
                        <p>Jedna z sypialni z łózkiem małżeńskim</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/premium/5.jpg')">
                    <img src="assets/premium/5.jpg" alt="Łazienka" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Łazienka</h3>
                        <p>Nowoczesna łazienka</p>
                    </div>
                </div>

                <div class="galeria-item" onclick="openLightbox('assets/premium/4.jpg')">
                    <img src="assets/premium/4.jpg" alt="Sauna" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Sauna</h3>
                        <p>Sauna z niesamowitym widokiem na pobliskie jezioro</p>
                    </div>
                </div>
                
                <div class="galeria-item" onclick="openLightbox('assets/premium/6.jpg')">
                    <img src="assets/premium/6.jpg" alt="Plan" class="galeria-img">
                    <div class="galeria-opis">
                        <h3>Plan domku</h3>
                        <p>Plan domku premium</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
    
    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img id="lightbox-img" class="lightbox-img">
    </div>
    
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
        // Przełączanie zakładek
        function openTab(tabId) {
            // Ukryj wszystkie galerie
            document.querySelectorAll('.galeria-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Pokaż wybraną galerię
            document.getElementById(tabId).classList.add('active');
            
            // Aktualizuj przyciski
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }
        
        // Lightbox
        function openLightbox(imgSrc) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            lightboxImg.src = imgSrc;
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Zamknij po kliknięciu poza obrazek
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
    </script>
</body>
</html>