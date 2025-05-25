<?php
// Rozpoczęcie sesji PHP
session_start();

// Inicjalizacja zmiennych dla komunikatów
$register_error = '';
$register_success = '';

// Obsługa formularza rejestracji
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i oczyszczenie danych z formularza
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Walidacja danych formularza
    if ($username === '' || $email === '' || $password === '' || $password2 === '') {
        $register_error = 'Wszystkie pola są wymagane.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = 'Nieprawidłowy adres email.';
    } elseif ($password !== $password2) {
        $register_error = 'Hasła nie są takie same.';
    } else {
        // Konfiguracja połączenia z bazą danych
        $host = 'localhost';
        $db = 'domki_letniskowe';
        $user = 'root';
        $pass = '';
        $mysqli = mysqli_connect($host, $user, $pass, $db);

        if (!$mysqli) {
            $register_error = 'Błąd połączenia z bazą danych.';
        } else {
            // Zabezpieczenie danych przed SQL Injection
            $username_esc = mysqli_real_escape_string($mysqli, $username);
            $email_esc = mysqli_real_escape_string($mysqli, $email);
            $password_esc = mysqli_real_escape_string($mysqli, $password); // UWAGA: hasła nie są szyfrowane

            // Sprawdź czy email lub username już istnieje
            $check = mysqli_query($mysqli, "SELECT id FROM users WHERE email='$email_esc' OR username='$username_esc' LIMIT 1");
            if ($check && mysqli_num_rows($check) > 0) {
                $register_error = 'Użytkownik o podanym emailu lub nazwie już istnieje.';
            } else {
                $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username_esc', '$email_esc', '$password_esc', 'client')";
                if (mysqli_query($mysqli, $sql)) {
                    $register_success = 'Rejestracja zakończona sukcesem! Możesz się teraz zalogować.';
                } else {
                    $register_error = 'Błąd podczas rejestracji.';
                }
            }
            mysqli_close($mysqli);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 120px auto 0 auto;
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn-register {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-register:hover {
            background: #38a169;
        }
        .register-error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
        .register-success {
            color: green;
            text-align: center;
            margin-bottom: 1rem;
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
    <main>
        <div class="register-container">
            <h2>Rejestracja</h2>
            <?php if ($register_error): ?>
                <div class="register-error"><?= htmlspecialchars($register_error) ?></div>
            <?php endif; ?>
            <?php if ($register_success): ?>
                <div class="register-success"><?= htmlspecialchars($register_success) ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Nazwa użytkownika</label>
                    <input type="text" id="username" name="username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password2">Powtórz hasło</label>
                    <input type="password" id="password2" name="password2" required>
                </div>
                <button type="submit" class="btn-register">Zarejestruj się</button>
            </form>
            <div style="text-align:center;margin-top:1rem;">
                Masz już konto? <a href="login.php">Zaloguj się</a>
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
</body>
</html>
