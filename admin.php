<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'domki_letniskowe';
$mysqli = mysqli_connect($host, $user, $pass, $db);

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'client';
$change_pass_msg = '';
$delete_opinia_msg = '';

// Obsługa zmiany hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $new2 = $_POST['new_password2'] ?? '';
    if ($old === '' || $new === '' || $new2 === '') {
        $change_pass_msg = 'Wszystkie pola są wymagane.';
    } elseif ($new !== $new2) {
        $change_pass_msg = 'Nowe hasła nie są takie same.';
    } else {
        $q = mysqli_query($mysqli, "SELECT password FROM users WHERE id=$user_id LIMIT 1");
        $row = $q ? mysqli_fetch_assoc($q) : null;
        if (!$row || $row['password'] !== $old) {
            $change_pass_msg = 'Stare hasło jest nieprawidłowe.';
        } else {
            $new_esc = mysqli_real_escape_string($mysqli, $new);
            if (mysqli_query($mysqli, "UPDATE users SET password='$new_esc' WHERE id=$user_id")) {
                $change_pass_msg = 'Hasło zostało zmienione.';
            } else {
                $change_pass_msg = 'Błąd podczas zmiany hasła.';
            }
        }
    }
}

// Obsługa usuwania opinii (tylko admin)
if ($user_role === 'admin' && isset($_POST['delete_opinia_id'])) {
    $opinia_id = (int)$_POST['delete_opinia_id'];
    if (mysqli_query($mysqli, "DELETE FROM opinions WHERE id=$opinia_id")) {
        $delete_opinia_msg = 'Opinia została usunięta.';
    } else {
        $delete_opinia_msg = 'Błąd podczas usuwania opinii.';
    }
}

// Pobierz opinie do panelu admina
$opinie = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT o.id, o.content, o.rating, u.username, o.created_at FROM opinions o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            // Upewnij się, że wartości nie są puste
            $row['content'] = $row['content'] ?? '';
            $row['rating'] = $row['rating'] ?? '';
            $row['username'] = $row['username'] ?? '';
            $row['created_at'] = $row['created_at'] ?? '';
            $opinie[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel użytkownika</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .panel-container { max-width: 700px; margin: 120px auto 0 auto; background: var(--light-bg); padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        .panel-container h2 { text-align:center; margin-bottom:2rem;}
        .form-group { margin-bottom: 1.5rem;}
        .form-group label { display:block; margin-bottom:0.5rem; font-weight:bold;}
        .form-group input { width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:5px; font-size:1rem;}
        .btn-panel { background: var(--accent-color); color: white; border: none; padding: 1rem 2rem; font-size: 1.1rem; border-radius: 5px; cursor: pointer; transition: background 0.3s;}
        .btn-panel:hover { background: #38a169;}
        .panel-msg { text-align:center; margin-bottom:1rem;}
        .opinie-admin-table { width:100%; border-collapse:collapse; margin-top:2rem;}
        .opinie-admin-table th, .opinie-admin-table td { border:1px solid #ccc; padding:0.5rem;}
        .opinie-admin-table th { background:#eee;}
        .delete-btn { background:#e53e3e; color:#fff; border:none; padding:0.4rem 1rem; border-radius:4px; cursor:pointer;}
        .delete-btn:hover { background:#c53030;}
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
                <li><a href="admin.php">Panel użytkownika</a></li>
                <li class="login-btn" style="color:var(--primary-color); font-weight:bold; background:none;">
                    Witaj, <?= htmlspecialchars($_SESSION['user_name']) ?>
                </li>
                <li class="login-btn"><a href="logout.php">Wyloguj</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="panel-container">
            <h2>Panel użytkownika</h2>
            <h3>Zmiana hasła</h3>
            <?php if ($change_pass_msg): ?>
                <div class="panel-msg" style="color:<?= strpos($change_pass_msg, 'zostało zmienione')!==false?'green':'red' ?>"><?= htmlspecialchars($change_pass_msg) ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off" style="margin-bottom:2rem;">
                <div class="form-group">
                    <label for="old_password">Stare hasło</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nowe hasło</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password2">Powtórz nowe hasło</label>
                    <input type="password" id="new_password2" name="new_password2" required>
                </div>
                <button type="submit" name="change_password" class="btn-panel">Zmień hasło</button>
            </form>
            <?php if ($user_role === 'admin'): ?>
                <h3>Usuń opinię użytkownika</h3>
                <?php if ($delete_opinia_msg): ?>
                    <div class="panel-msg" style="color:<?= strpos($delete_opinia_msg, 'została usunięta')!==false?'green':'red' ?>"><?= htmlspecialchars($delete_opinia_msg) ?></div>
                <?php endif; ?>
                <table class="opinie-admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Użytkownik</th>
                        <th>Ocena</th>
                        <th>Treść</th>
                        <th>Data</th>
                        <th>Akcja</th>
                    </tr>
                    <?php foreach($opinie as $opinia): ?>
                    <tr>
                        <td><?= $opinia['id'] ?></td>
                        <td><?= htmlspecialchars($opinia['username']) ?></td>
                        <td><?= (int)$opinia['rating'] ?></td>
                        <td><?= htmlspecialchars($opinia['content']) ?></td>
                        <td><?= htmlspecialchars($opinia['created_at']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_opinia_id" value="<?= $opinia['id'] ?>">
                                <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tę opinię?')">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
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