<?php
session_start();

$rezerwacja_success = '';
$rezerwacja_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Połączenie z bazą
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'domki_letniskowe';
    $conn = mysqli_connect($host, $user, $pass, $db);

    // Pobierz dane z formularza
    $domek = $_POST['domek'] ?? '';
    $data_przyjazdu = $_POST['data_przyjazdu'] ?? '';
    $data_wyjazdu = $_POST['data_wyjazdu'] ?? '';
    $ilosc_osob = (int)($_POST['ilosc_osob'] ?? 1);
    $imie = trim($_POST['imie'] ?? '');
    $nazwisko = trim($_POST['nazwisko'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $uwagi = trim($_POST['uwagi'] ?? '');

    // Prosta walidacja
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
        // Pobierz id domku z tabeli cabins
        $domek_map = [
            'sloneczny' => 1,
            'brzozowy' => 2,
            'premium' => 3
        ];
        $cabin_id = $domek_map[$domek] ?? null;

        if (!$cabin_id) {
            $rezerwacja_error = 'Wybrano nieprawidłowy domek.';
        } else {
            // Jeśli użytkownik zalogowany, pobierz jego id, w przeciwnym razie NULL
            $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'NULL';

            // Zapisz rezerwację
            $imie_esc = mysqli_real_escape_string($conn, $imie);
            $nazwisko_esc = mysqli_real_escape_string($conn, $nazwisko);
            $email_esc = mysqli_real_escape_string($conn, $email);
            $telefon_esc = mysqli_real_escape_string($conn, $telefon);
            $uwagi_esc = mysqli_real_escape_string($conn, $uwagi);

            // Dodatkowe dane osobowe można zapisać w osobnej tabeli lub w uwagach (tu: w uwagach)
            $uwagi_full = "Imię: $imie_esc, Nazwisko: $nazwisko_esc, Email: $email_esc, Telefon: $telefon_esc. Uwagi: $uwagi_esc";

            $sql = "INSERT INTO reservations (user_id, cabin_id, start_date, end_date, status) VALUES (" .
                ($user_id === 'NULL' ? "NULL" : $user_id) . ", $cabin_id, '$data_przyjazdu', '$data_wyjazdu', 'pending')";

            if (mysqli_query($conn, $sql)) {
                $rezerwacja_success = 'Rezerwacja została zapisana! Skontaktujemy się z Tobą w celu potwierdzenia.';
            } else {
                $rezerwacja_error = 'Błąd podczas zapisywania rezerwacji.';
            }
            mysqli_close($conn);
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
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
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
