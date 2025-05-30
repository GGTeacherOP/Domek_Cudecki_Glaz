<?php
session_start();

// Inicjalizacja zmiennych dla komunikatów
$rezerwacja_success = '';
$rezerwacja_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Konfiguracja połączenia z bazą danych
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'domki_letniskowe';
    $conn = mysqli_connect($host, $user, $pass, $db);

    // Pobieranie danych z formularza
    $domek = $_POST['domek'] ?? '';
    $data_przyjazdu = $_POST['data_przyjazdu'] ?? '';
    $data_wyjazdu = $_POST['data_wyjazdu'] ?? '';
    $ilosc_osob = (int)($_POST['ilosc_osob'] ?? 1);
    $imie = trim($_POST['imie'] ?? '');
    $nazwisko = trim($_POST['nazwisko'] ?? '');
    // Pobierz email z sesji jeśli zalogowany, w przeciwnym razie z formularza
    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
    } else {
        $email = trim($_POST['email'] ?? '');
    }
    $telefon = trim($_POST['telefon'] ?? '');
    $uwagi = trim($_POST['uwagi'] ?? '');

    // Walidacja podstawowych danych
    if (
        !$domek || !$data_przyjazdu || !$data_wyjazdu || !$imie || !$nazwisko || !$email || !$telefon
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strtotime($data_przyjazdu) === false || strtotime($data_wyjazdu) === false
        || strtotime($data_wyjazdu) <= strtotime($data_przyjazdu)
    ) {
        $rezerwacja_error = 'Wszystkie pola są wymagane, a daty muszą być poprawne.';
    } elseif (!$conn) {
        $rezerwacja_error = 'Błąd połączenia z bazą danych.';
    } else {
        // Mapowanie nazw domków na ich ID w bazie
        $domek_map = [
            'sloneczny' => 1,
            'brzozowy' => 2,
            'premium' => 3
        ];
        $cabin_id = $domek_map[$domek] ?? null;

        // Oblicz kwotę do zapłaty
        $cenaDzien = 0;
        switch($domek) {
            case 'sloneczny': $cenaDzien = 350; break;
            case 'brzozowy': $cenaDzien = 280; break;
            case 'premium': $cenaDzien = 550; break;
        }
        $dni = 0;
        if ($data_przyjazdu && $data_wyjazdu) {
            $przyjazd = strtotime($data_przyjazdu);
            $wyjazd = strtotime($data_wyjazdu);
            $dni = ($wyjazd - $przyjazd) / (60*60*24);
        }
        $do_zaplaty = $dni > 0 ? $dni * $cenaDzien : 0;

        // Sprawdzanie dostępności terminu
        if (!$cabin_id) {
            $rezerwacja_error = 'Wybrano nieprawidłowy domek.';
        } else {
            // Sprawdzenie kolizji terminów w bazie
            $start = mysqli_real_escape_string($conn, $data_przyjazdu);
            $end = mysqli_real_escape_string($conn, $data_wyjazdu);
            $check_sql = "SELECT 1 FROM reservations 
                WHERE cabin_id = $cabin_id 
                AND status != 'cancelled'
                AND (
                    (start_date < '$end' AND end_date > '$start')
                )
                LIMIT 1";
            $check_res = mysqli_query($conn, $check_sql);
            if ($check_res && mysqli_num_rows($check_res) > 0) {
                $rezerwacja_error = 'Wybrany domek jest już zarezerwowany w tym terminie. Proszę wybrać inne daty lub domek.';
            } else {
                // Jeśli użytkownik zalogowany, pobierz jego id, w przeciwnym razie NULL
                $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'NULL';

                // Zapisz rezerwację z dodatkowymi polami
                $imie_esc = mysqli_real_escape_string($conn, $imie);
                $nazwisko_esc = mysqli_real_escape_string($conn, $nazwisko);
                $telefon_esc = mysqli_real_escape_string($conn, $telefon);
                $uwagi_esc = mysqli_real_escape_string($conn, $uwagi);

                $sql = "INSERT INTO reservations (user_id, cabin_id, start_date, end_date, status, imie, nazwisko, telefon, uwagi, do_zaplaty) VALUES (" .
                    ($user_id === 'NULL' ? "NULL" : $user_id) . ", $cabin_id, '$data_przyjazdu', '$data_wyjazdu', 'pending', '$imie_esc', '$nazwisko_esc', '$telefon_esc', '$uwagi_esc', '$do_zaplaty')";

                if (mysqli_query($conn, $sql)) {
                    $rezerwacja_success = 'Rezerwacja została zapisana! Skontaktujemy się z Tobą w celu potwierdzenia.';
                } else {
                    $rezerwacja_error = 'Błąd podczas zapisywania rezerwacji.';
                }
                mysqli_close($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezerwacja - Domki Letniskowe</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .rezerwacja-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .rezerwacja-form {
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .rezerwacja-podsumowanie {
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .form-row {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 1.5rem;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .btn-rezerwuj-teraz {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn-rezerwuj-teraz:hover {
            background: #38a169;
        }
        
        .podsumowanie-pozycja {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px dashed #ddd;
        }
        
        .podsumowanie-suma {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #ddd;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
        }
        
/* Styl kalendarza */
        input[type="date"] {
            position: relative;
            width: 100%;
            box-sizing: border-box;
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
        <h1 style="text-align:center;">Rezerwacja</h1>
        <p style="text-align:center;">Wypełnij formularz, aby zarezerwować pobyt w naszym ośrodku</p>
        <?php if ($rezerwacja_success): ?>
            <div style="background:#d4edda;color:#155724;padding:1rem;border-radius:8px;text-align:center;margin:1rem auto;max-width:600px;">
                <?= htmlspecialchars($rezerwacja_success) ?>
            </div>
        <?php elseif ($rezerwacja_error): ?>
            <div style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:8px;text-align:center;margin:1rem auto;max-width:600px;">
                <?= htmlspecialchars($rezerwacja_error) ?>
            </div>
        <?php endif; ?>
        <?php
        // Pobranie parametru domku z adresu URL (jeśli istnieje)
        $selected_domek = isset($_GET['domek']) ? $_GET['domek'] : '';
        ?>
        
        <div class="rezerwacja-container">
            <div class="rezerwacja-form">
                <h2>Dane rezerwacji</h2>
                <form action="" method="POST" id="rezerwacja-form">
                    <div class="form-group">
                        <label for="domek">Wybierz domek</label>
                        <select id="domek" name="domek" required>
                            <option value="">-- Wybierz domek --</option>
                            <option value="sloneczny" <?php if($selected_domek == 'sloneczny') echo 'selected'; ?>>Domek Słoneczny - 350 zł/doba</option>
                            <option value="brzozowy" <?php if($selected_domek == 'brzozowy') echo 'selected'; ?>>Domek Brzozowy - 280 zł/doba</option>
                            <option value="premium" <?php if($selected_domek == 'premium') echo 'selected'; ?>>Domek Premium - 550 zł/doba</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_przyjazdu">Data przyjazdu</label>
                            <input type="date" id="data_przyjazdu" name="data_przyjazdu" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="data_wyjazdu">Data wyjazdu</label>
                            <input type="date" id="data_wyjazdu" name="data_wyjazdu" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ilosc_osob">Liczba osób</label>
                        <select id="ilosc_osob" name="ilosc_osob" required>
                            <option value="1">1 osoba</option>
                            <option value="2" selected>2 osoby</option>
                            <option value="3">3 osoby</option>
                            <option value="4">4 osoby</option>
                            <option value="5">5 osób</option>
                            <option value="6">6 osób</option>
                        </select>
                    </div>
                    
                    <h2>Dane osobowe</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="imie">Imię</label>
                            <input type="text" id="imie" name="imie" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nazwisko">Nazwisko</label>
                            <input type="text" id="nazwisko" name="nazwisko" required>
                        </div>
                    </div>
                    <?php if (!isset($_SESSION['user_email'])): ?>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" readonly style="background:#eee;">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="telefon">Numer telefonu</label>
                        <input type="tel" id="telefon" name="telefon" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="uwagi">Dodatkowe uwagi</label>
                        <textarea id="uwagi" name="uwagi" rows="3" style="width: 96.49%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; resize: vertical;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-rezerwuj-teraz">Rezerwuj teraz</button>
                </form>
            </div>
            
            <div class="rezerwacja-podsumowanie">
                <h2>Podsumowanie rezerwacji</h2>
                <div class="podsumowanie-pozycja">
                    <span>Domek:</span>
                    <span id="podsumowanie-domek">-</span>
                </div>
                <div class="podsumowanie-pozycja">
                    <span>Data przyjazdu:</span>
                    <span id="podsumowanie-przyjazd">-</span>
                </div>
                <div class="podsumowanie-pozycja">
                    <span>Data wyjazdu:</span>
                    <span id="podsumowanie-wyjazd">-</span>
                </div>
                <div class="podsumowanie-pozycja">
                    <span>Liczba dni:</span>
                    <span id="podsumowanie-dni">-</span>
                </div>
                <div class="podsumowanie-pozycja">
                    <span>Liczba osób:</span>
                    <span id="podsumowanie-osoby">-</span>
                </div>
                <div class="podsumowanie-suma">
                    <span>Razem: </span>
                    <span id="podsumowanie-suma">0.00 zł</span>
                </div>
            </div>
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
    <script src="scripts/rezerwacja.js"></script>
</body>
</html>
