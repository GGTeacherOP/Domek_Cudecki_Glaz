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
$rezerwacja_msg = '';

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

// Obsługa akceptacji/odrzucenia rezerwacji (tylko admin)
if ($user_role === 'admin' && isset($_POST['rezerwacja_id']) && isset($_POST['rezerwacja_action'])) {
    $rez_id = (int)$_POST['rezerwacja_id'];
    $action = $_POST['rezerwacja_action'];
    if ($action === 'accept') {
        if (mysqli_query($mysqli, "UPDATE reservations SET status='confirmed' WHERE id=$rez_id")) {
            $rezerwacja_msg = 'Rezerwacja została zaakceptowana.';
        } else {
            $rezerwacja_msg = 'Błąd podczas akceptowania rezerwacji.';
        }
    } elseif ($action === 'reject') {
        if (mysqli_query($mysqli, "UPDATE reservations SET status='cancelled' WHERE id=$rez_id")) {
            $rezerwacja_msg = 'Rezerwacja została odrzucona.';
        } else {
            $rezerwacja_msg = 'Błąd podczas odrzucania rezerwacji.';
        }
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

// Pobierz rezerwacje oczekujące (tylko admin)
$pending_reservations = [];
if ($user_role === 'admin') {
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, r.imie, r.nazwisko, r.telefon, r.uwagi, u.username, u.email, c.name AS cabin_name
        FROM reservations r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN cabins c ON r.cabin_id = c.id
        WHERE r.status = 'pending'
        ORDER BY r.start_date ASC");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $pending_reservations[] = $row;
        }
    }
}

// Pobierz rezerwacje użytkownika (tylko klient)
$user_reservations = [];
if ($user_role === 'client') {
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, c.name AS cabin_name, r.uwagi
        FROM reservations r
        LEFT JOIN cabins c ON r.cabin_id = c.id
        WHERE r.user_id = $user_id
        ORDER BY r.start_date DESC");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $user_reservations[] = $row;
        }
    }
}

// Pobierz wszystkie rezerwacje (tylko admin)
$all_reservations = [];
if ($user_role === 'admin') {
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, r.imie, r.nazwisko, r.telefon, r.uwagi, u.username, u.email, c.name AS cabin_name
        FROM reservations r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN cabins c ON r.cabin_id = c.id
        ORDER BY r.start_date DESC");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $all_reservations[] = $row;
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
        .panel-container {
            max-width: none;
            width: 100%;
            margin: 120px 0 0 0;
            background: var(--light-bg);
            padding: 2rem 2vw;
            border-radius: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .panel-container h2 { text-align:center; margin-bottom:2rem;}
        .form-group { margin-bottom: 1.5rem;}
        .form-group label { display:block; margin-bottom:0.5rem; font-weight:bold;}
        .form-group input { width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:5px; font-size:1rem;}
        .btn-panel { background: var(--accent-color); color: white; border: none; padding: 1rem 2rem; font-size: 1.1rem; border-radius: 5px; cursor: pointer; transition: background 0.3s;}
        .btn-panel:hover { background: #38a169;}
        .panel-msg { text-align:center; margin-bottom:1rem;}
        .opinie-admin-table, .rezerwacje-admin-table {
            width: 100%;
            border-collapse:collapse;
            margin-top:2rem;
            background: transparent;
        }
        .opinie-admin-table th, .opinie-admin-table td,
        .rezerwacje-admin-table th, .rezerwacje-admin-table td {
            border:1px solid #ccc;
            padding:0.5rem;
            word-break: break-word;
        }
        .opinie-admin-table th, .rezerwacje-admin-table th { background:#eee;}
        .delete-btn { background:#e53e3e; color:#fff; border:none; padding:0.4rem 1rem; border-radius:4px; cursor:pointer;}
        .delete-btn:hover { background:#c53030;}
        .accept-btn { background:#38a169; color:#fff; border:none; padding:0.4rem 1rem; border-radius:4px; cursor:pointer;}
        .accept-btn:hover { background:#2f855a;}
        .reject-btn { background:#e53e3e; color:#fff; border:none; padding:0.4rem 1rem; border-radius:4px; cursor:pointer;}
        .reject-btn:hover { background:#c53030;}
        @media (max-width: 900px) {
            .panel-container {
                padding: 1rem 0.5rem;
            }
            .opinie-admin-table, .rezerwacje-admin-table {
                font-size: 0.95em;
            }
        }
        @media (max-width: 600px) {
            .panel-container {
                padding: 0.5rem 0.2rem;
            }
            .opinie-admin-table, .rezerwacje-admin-table {
                font-size: 0.85em;
            }
        }
        .panel-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        .panel-tab-btn {
            background: #eee;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 5px 5px 0 0;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: bold;
            color: #333;
            transition: background 0.2s;
        }
        .panel-tab-btn.active, .panel-tab-btn:hover {
            background: var(--accent-color);
            color: #fff;
        }
        .panel-tab-content { display: none; }
        .panel-tab-content.active { display: block; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Zakładki tylko dla klienta
            var tabBtns = document.querySelectorAll('.panel-tab-btn');
            var tabContents = document.querySelectorAll('.panel-tab-content');
            if (tabBtns.length) {
                function activateTab(idx) {
                    tabBtns.forEach((btn, i) => btn.classList.toggle('active', i === idx));
                    tabContents.forEach((tab, i) => tab.classList.toggle('active', i === idx));
                }
                tabBtns.forEach((btn, idx) => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        activateTab(idx);
                        window.location.hash = btn.getAttribute('data-tab');
                    });
                });
                // Aktywuj zakładkę z hash lub domyślną
                let hash = window.location.hash.replace('#','');
                let found = false;
                tabBtns.forEach((btn, idx) => {
                    if (btn.getAttribute('data-tab') === hash) {
                        activateTab(idx);
                        found = true;
                    }
                });
                if (!found) activateTab(0);
            }
        });
    </script>
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
            <?php if ($user_role === 'client'): ?>
                <div class="panel-tabs">
                    <button class="panel-tab-btn" data-tab="haslo">Zmiana hasła</button>
                    <button class="panel-tab-btn" data-tab="moje-rezerwacje">Moje rezerwacje</button>
                </div>
                <div class="panel-tab-content" id="tab-haslo">
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
                </div>
                <div class="panel-tab-content" id="tab-moje-rezerwacje">
                    <h3>Moje rezerwacje</h3>
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Status</th>
                            <th>Uwagi</th>
                        </tr>
                        <?php foreach($user_reservations as $rez): ?>
                        <tr>
                            <td><?= $rez['id'] ?></td>
                            <td><?= htmlspecialchars($rez['cabin_name']) ?></td>
                            <td><?= htmlspecialchars($rez['start_date']) ?></td>
                            <td><?= htmlspecialchars($rez['end_date']) ?></td>
                            <td>
                                <?php
                                    if ($rez['status'] === 'pending') echo '<span style="color:#e6b800;">Oczekuje</span>';
                                    elseif ($rez['status'] === 'confirmed') echo '<span style="color:green;">Potwierdzona</span>';
                                    elseif ($rez['status'] === 'cancelled') echo '<span style="color:red;">Odrzucona</span>';
                                    else echo htmlspecialchars($rez['status']);
                                ?>
                            </td>
                            <td><?= htmlspecialchars($rez['uwagi']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($user_reservations)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak rezerwacji.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php else: ?>
                <div class="panel-tabs">
                    <button class="panel-tab-btn" data-tab="rezerwacje-admin">Rezerwacje oczekujące</button>
                    <button class="panel-tab-btn" data-tab="wszystkie-rezerwacje">Wszystkie rezerwacje</button>
                    <button class="panel-tab-btn" data-tab="opinie-admin">Opinie użytkowników</button>
                    <button class="panel-tab-btn" data-tab="haslo-admin">Zmiana hasła</button>
                </div>
                <div class="panel-tab-content" id="tab-rezerwacje-admin">
                    <h3>Rezerwacje oczekujące na akceptację</h3>
                    <?php if ($rezerwacja_msg): ?>
                        <div class="panel-msg" style="color:<?= strpos($rezerwacja_msg, 'zaakceptowana')!==false?'green':'red' ?>"><?= htmlspecialchars($rezerwacja_msg) ?></div>
                    <?php endif; ?>
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Imię i nazwisko</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Uwagi</th>
                            <th>Akcja</th>
                        </tr>
                        <?php foreach($pending_reservations as $rez): ?>
                        <tr>
                            <td><?= $rez['id'] ?></td>
                            <td><?= htmlspecialchars($rez['cabin_name']) ?></td>
                            <td><?= htmlspecialchars($rez['start_date']) ?></td>
                            <td><?= htmlspecialchars($rez['end_date']) ?></td>
                            <td><?= htmlspecialchars(trim($rez['imie'].' '.$rez['nazwisko'])) ?></td>
                            <td><?= htmlspecialchars($rez['telefon']) ?></td>
                            <td><?= htmlspecialchars($rez['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($rez['uwagi']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="rezerwacja_id" value="<?= $rez['id'] ?>">
                                    <button type="submit" name="rezerwacja_action" value="accept" class="accept-btn" onclick="return confirm('Zaakceptować tę rezerwację?')">Akceptuj</button>
                                    <button type="submit" name="rezerwacja_action" value="reject" class="reject-btn" onclick="return confirm('Odrzucić tę rezerwację?')">Odrzuć</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pending_reservations)): ?>
                        <tr><td colspan="9" style="text-align:center;">Brak rezerwacji oczekujących na akceptację.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="panel-tab-content" id="tab-wszystkie-rezerwacje">
                    <h3>Wszystkie rezerwacje klientów</h3>
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Imię i nazwisko</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Użytkownik</th>
                            <th>Status</th>
                            <th>Uwagi</th>
                        </tr>
                        <?php foreach($all_reservations as $rez): ?>
                        <tr>
                            <td><?= $rez['id'] ?></td>
                            <td><?= htmlspecialchars($rez['cabin_name']) ?></td>
                            <td><?= htmlspecialchars($rez['start_date']) ?></td>
                            <td><?= htmlspecialchars($rez['end_date']) ?></td>
                            <td><?= htmlspecialchars(trim($rez['imie'].' '.$rez['nazwisko'])) ?></td>
                            <td><?= htmlspecialchars($rez['telefon']) ?></td>
                            <td><?= htmlspecialchars($rez['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($rez['username'] ?? '-') ?></td>
                            <td>
                                <?php
                                    if ($rez['status'] === 'pending') echo '<span style="color:#e6b800;">Oczekuje</span>';
                                    elseif ($rez['status'] === 'confirmed') echo '<span style="color:green;">Potwierdzona</span>';
                                    elseif ($rez['status'] === 'cancelled') echo '<span style="color:red;">Odrzucona</span>';
                                    else echo htmlspecialchars($rez['status']);
                                ?>
                            </td>
                            <td><?= htmlspecialchars($rez['uwagi']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_reservations)): ?>
                        <tr><td colspan="10" style="text-align:center;">Brak rezerwacji.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="panel-tab-content" id="tab-opinie-admin">
                    <h3>Opinie użytkowników</h3>
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
                </div>
                <div class="panel-tab-content" id="tab-haslo-admin">
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
                </div>
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