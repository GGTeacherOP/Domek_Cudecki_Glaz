<?php
session_start();

// Obsługa formularza logowania
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Połączenie z bazą danych
        $host = 'localhost';
        $db = 'domki_letniskowe';
        $user = 'root';
        $pass = '';
        $mysqli = mysqli_connect($host, $user, $pass, $db);

        if (mysqli_connect_errno()) {
            throw new Exception('Błąd połączenia z bazą danych.');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Sprawdzenie użytkownika (hasło jako zwykły tekst, nieszyfrowane)
        $result = mysqli_query($mysqli, "SELECT * FROM users WHERE email='$email' LIMIT 1");
        
        if (!$result) {
            throw new Exception('Błąd zapytania do bazy danych.');
        }
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name'] = $row['username']; // Dodaj nazwę użytkownika do sesji
                $_SESSION['user_role'] = $row['role'];
                header('Location: index.php');
                exit;
            } else {
                throw new Exception('Nieprawidłowy email lub hasło.');
            }
        } else {
            throw new Exception('Nieprawidłowy email lub hasło.');
        }
    } catch (Exception $e) {
        $login_error = $e->getMessage();
    } finally {
        if (isset($mysqli) && $mysqli) {
            mysqli_close($mysqli);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 120px auto 0 auto;
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .login-container h2 {
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
        .btn-login {
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
        .btn-login:hover {
            background: #38a169;
        }
        .login-error {
            color: red;
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
        <div class="login-container">
            <h2>Logowanie</h2>
            <?php if ($login_error): ?>
                <div class="login-error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Zaloguj się</button>
            </form>
            <div style="text-align:center;margin-top:1rem;">
                Nie masz konta? <a href="register.php">Zarejestruj się</a>
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
