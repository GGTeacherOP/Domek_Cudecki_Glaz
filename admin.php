<?php
// Rozpoczęcie sesji
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Konfiguracja połączenia z bazą danych
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'domki_letniskowe';
$mysqli = mysqli_connect($host, $user, $pass, $db);

// Pobranie danych użytkownika
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
    
    // Walidacja pól formularza
    if ($old === '' || $new === '' || $new2 === '') {
        $change_pass_msg = 'Wszystkie pola są wymagane.';
    } elseif ($new !== $new2) {
        $change_pass_msg = 'Nowe hasła nie są takie same.';
    } else {
        // Sprawdzenie starego hasła
        $q = mysqli_query($mysqli, "SELECT password FROM users WHERE id=$user_id LIMIT 1");
        $row = $q ? mysqli_fetch_assoc($q) : null;
        
        $old_password_valid = false;
        if ($row) {
            if (password_verify($old, $row['password'])) {
                $old_password_valid = true;
            } elseif ($old === $row['password']) {
                $old_password_valid = true;
            }
        }
        
        if (!$old_password_valid) {
            $change_pass_msg = 'Stare hasło jest nieprawidłowe.';
        } else {
            // Aktualizacja hasła z szyfrowaniem
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            if (mysqli_query($mysqli, "UPDATE users SET password='$new_hashed' WHERE id=$user_id")) {
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

// Obsługa aktualizacji statusu zgłoszenia serwisowego (konserwator)
if (($user_role === 'admin' || $user_role === 'konserwator') && isset($_POST['update_maintenance_status']) && isset($_POST['maintenance_id'])) {
    $maintenance_id = (int)$_POST['maintenance_id'];
    $new_status = mysqli_real_escape_string($mysqli, $_POST['new_status']);
    if (mysqli_query($mysqli, "UPDATE maintenance_requests SET status='$new_status' WHERE id=$maintenance_id")) {
        $rezerwacja_msg = 'Status zgłoszenia został zaktualizowany.';
    } else {
        $rezerwacja_msg = 'Błąd podczas aktualizacji statusu.';
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
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, r.imie, r.nazwisko, r.telefon, r.uwagi, r.do_zaplaty, u.username, u.email, c.name AS cabin_name
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
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, c.name AS cabin_name, r.uwagi, r.do_zaplaty
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
    $q = mysqli_query($mysqli, "SELECT r.id, r.start_date, r.end_date, r.status, r.imie, r.nazwisko, r.telefon, r.uwagi, r.do_zaplaty, u.username, u.email, c.name AS cabin_name
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

// Pobierz domki (cabins)
$cabins = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT * FROM cabins ORDER BY id ASC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $cabins[] = $row;
}

// Pobierz wydatki domków (cabin_expenses)
$cabin_expenses = [];
if ($user_role === 'admin' || $user_role === 'ksiegowy') {
    $res = mysqli_query($mysqli, "SELECT ce.*, c.name AS cabin_name FROM cabin_expenses ce LEFT JOIN cabins c ON ce.cabin_id = c.id ORDER BY ce.expense_date DESC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $cabin_expenses[] = $row;
}

// Pobierz pracowników (employees)
$employees = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT * FROM employees ORDER BY id ASC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $employees[] = $row;
}

// Pobierz wynagrodzenia pracowników (employee_salaries)
$employee_salaries = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT es.*, e.name AS employee_name FROM employee_salaries es LEFT JOIN employees e ON es.employee_id = e.id ORDER BY es.payment_date DESC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $employee_salaries[] = $row;
}

// Pobierz zgłoszenia serwisowe (maintenance_requests) - admin i konserwator
$maintenance_requests = [];
if ($user_role === 'admin' || $user_role === 'konserwator') {
    $res = mysqli_query($mysqli, "SELECT mr.*, c.name AS cabin_name FROM maintenance_requests mr LEFT JOIN cabins c ON mr.cabin_id = c.id ORDER BY mr.request_date DESC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $maintenance_requests[] = $row;
}

$pending_opinions = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT o.id, o.content, o.rating, u.username, o.created_at 
    FROM opinions o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.approved = FALSE 
    ORDER BY o.id DESC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $pending_opinions[] = $row;
        }
    }
}
// Pobierz atrakcje (attractions)
$attractions = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT * FROM attractions ORDER BY id ASC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $attractions[] = $row;
}

// Pobierz kontakt (kontakt)
$kontakt_msgs = [];
if ($user_role === 'admin') {
    $res = mysqli_query($mysqli, "SELECT * FROM kontakt ORDER BY id DESC");
    if ($res) while ($row = mysqli_fetch_assoc($res)) $kontakt_msgs[] = $row;
}

// Obsługa edycji i zapisu dla każdej tabeli

// Pracownicy
$edit_employee_id = isset($_POST['edit_employee_id']) ? (int)$_POST['edit_employee_id'] : null;
$save_employee_id = isset($_POST['save_employee_id']) ? (int)$_POST['save_employee_id'] : null;
if ($save_employee_id) {
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $position = mysqli_real_escape_string($mysqli, $_POST['position']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
    mysqli_query($mysqli, "UPDATE employees SET name='$name', position='$position', email='$email', phone='$phone' WHERE id=$save_employee_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Wynagrodzenia
$edit_salary_id = isset($_POST['edit_salary_id']) ? (int)$_POST['edit_salary_id'] : null;
$save_salary_id = isset($_POST['save_salary_id']) ? (int)$_POST['save_salary_id'] : null;
if ($save_salary_id) {
    $salary = (float)$_POST['salary'];
    $payment_date = mysqli_real_escape_string($mysqli, $_POST['payment_date']);
    mysqli_query($mysqli, "UPDATE employee_salaries SET salary='$salary', payment_date='$payment_date' WHERE id=$save_salary_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Domki
$edit_cabin_id = isset($_POST['edit_cabin_id']) ? (int)$_POST['edit_cabin_id'] : null;
$save_cabin_id = isset($_POST['save_cabin_id']) ? (int)$_POST['save_cabin_id'] : null;
if ($save_cabin_id) {
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $description = mysqli_real_escape_string($mysqli, $_POST['description']);
    $price_per_night = (float)$_POST['price_per_night'];
    $image_url = mysqli_real_escape_string($mysqli, $_POST['image_url']);
    mysqli_query($mysqli, "UPDATE cabins SET name='$name', description='$description', price_per_night='$price_per_night', image_url='$image_url' WHERE id=$save_cabin_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Wydatki domków
$edit_expense_id = isset($_POST['edit_expense_id']) ? (int)$_POST['edit_expense_id'] : null;
$save_expense_id = isset($_POST['save_expense_id']) ? (int)$_POST['save_expense_id'] : null;
if ($save_expense_id) {
    $description = mysqli_real_escape_string($mysqli, $_POST['description']);
    $amount = (float)$_POST['amount'];
    $expense_date = mysqli_real_escape_string($mysqli, $_POST['expense_date']);
    mysqli_query($mysqli, "UPDATE cabin_expenses SET description='$description', amount='$amount', expense_date='$expense_date' WHERE id=$save_expense_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Zgłoszenia serwisowe
$edit_maintenance_id = isset($_POST['edit_maintenance_id']) ? (int)$_POST['edit_maintenance_id'] : null;
$save_maintenance_id = isset($_POST['save_maintenance_id']) ? (int)$_POST['save_maintenance_id'] : null;
if ($save_maintenance_id && $user_role === 'admin') {
    $description = mysqli_real_escape_string($mysqli, $_POST['description']);
    $status = mysqli_real_escape_string($mysqli, $_POST['status']);
    $request_date = mysqli_real_escape_string($mysqli, $_POST['request_date']);
    mysqli_query($mysqli, "UPDATE maintenance_requests SET description='$description', status='$status', request_date='$request_date' WHERE id=$save_maintenance_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Atrakcje
$edit_attraction_id = isset($_POST['edit_attraction_id']) ? (int)$_POST['edit_attraction_id'] : null;
$save_attraction_id = isset($_POST['save_attraction_id']) ? (int)$_POST['save_attraction_id'] : null;
if ($save_attraction_id) {
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $description = mysqli_real_escape_string($mysqli, $_POST['description']);
    $distance_km = (float)$_POST['distance_km'];
    mysqli_query($mysqli, "UPDATE attractions SET name='$name', description='$description', distance_km='$distance_km' WHERE id=$save_attraction_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Kontakt
$edit_kontakt_id = isset($_POST['edit_kontakt_id']) ? (int)$_POST['edit_kontakt_id'] : null;
$save_kontakt_id = isset($_POST['save_kontakt_id']) ? (int)$_POST['save_kontakt_id'] : null;
if ($save_kontakt_id) {
    $imie_nazwisko = mysqli_real_escape_string($mysqli, $_POST['imie_nazwisko']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $temat = mysqli_real_escape_string($mysqli, $_POST['temat']);
    $tresc = mysqli_real_escape_string($mysqli, $_POST['tresc']);
    mysqli_query($mysqli, "UPDATE kontakt SET imie_nazwisko='$imie_nazwisko', email='$email', temat='$temat', tresc='$tresc' WHERE id=$save_kontakt_id");
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}
if ($user_role === 'admin' && isset($_POST['opinia_action']) && isset($_POST['opinia_id'])) {
    $opinia_id = (int)$_POST['opinia_id'];
    $action = $_POST['opinia_action'];
    
    if ($action === 'approve') {
        if (mysqli_query($mysqli, "UPDATE opinions SET approved=TRUE WHERE id=$opinia_id")) {
            $_SESSION['admin_msg'] = 'Opinia została zatwierdzona.';
        } else {
            $_SESSION['admin_msg'] = 'Błąd podczas zatwierdzania opinii.';
        }
    } elseif ($action === 'reject') {
        if (mysqli_query($mysqli, "DELETE FROM opinions WHERE id=$opinia_id")) {
            $_SESSION['admin_msg'] = 'Opinia została odrzucona.';
        } else {
            $_SESSION['admin_msg'] = 'Błąd podczas odrzucania opinii.';
        }
    }
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
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
            max-width: 98vw;
            margin: 120px auto 0 auto;
            background: var(--light-bg);
            padding: 2rem 2vw;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        /* Nadpisanie stylu main z globalnego CSS */
        main {
            max-width: 100vw !important;
            width: 100vw !important;
            margin: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
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
                max-width: 99vw;
            }
            .opinie-admin-table, .rezerwacje-admin-table {
                font-size: 0.95em;
            }
        }
        @media (max-width: 600px) {
            .panel-container {
                padding: 0.5rem 0.2rem;
                max-width: 100vw;
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
        .scroll-table { max-height: 400px; overflow-y: auto; display: block;}
        .scroll-table table { width: 100%; }
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
                            <th>Do zapłaty</th>
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
                            <td><?= number_format((float)($rez['do_zaplaty'] ?? 0), 2, ',', ' ') ?> zł</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($user_reservations)): ?>
                        <tr><td colspan="7" style="text-align:center;">Brak rezerwacji.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php elseif ($user_role === 'konserwator'): ?>
                <div class="panel-tabs">
                    <button class="panel-tab-btn" data-tab="maintenance-konserwator">Zgłoszenia serwisowe</button>
                    <button class="panel-tab-btn" data-tab="haslo-konserwator">Zmiana hasła</button>
                </div>
                
                <div class="panel-tab-content" id="tab-maintenance-konserwator">
                    <h3>Zgłoszenia serwisowe</h3>
                    <?php if ($rezerwacja_msg): ?>
                        <div class="panel-msg" style="color:green"><?= htmlspecialchars($rezerwacja_msg) ?></div>
                    <?php endif; ?>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Opis</th>
                            <th>Status</th>
                            <th>Data zgłoszenia</th>
                            <th>Akcja</th>
                        </tr>
                        <?php foreach($maintenance_requests as $mr): ?>
                        <tr>
                            <td><?= $mr['id'] ?></td>
                            <td><?= htmlspecialchars($mr['cabin_name']) ?></td>
                            <td><?= htmlspecialchars($mr['description']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="maintenance_id" value="<?= $mr['id'] ?>">
                                    <select name="new_status" onchange="this.form.submit()">
                                        <option value="pending" <?= $mr['status']=='pending'?'selected':'' ?>>Oczekuje</option>
                                        <option value="in_progress" <?= $mr['status']=='in_progress'?'selected':'' ?>>W trakcie</option>
                                        <option value="completed" <?= $mr['status']=='completed'?'selected':'' ?>>Zakończone</option>
                                    </select>
                                    <input type="hidden" name="update_maintenance_status" value="1">
                                </form>
                            </td>
                            <td><?= htmlspecialchars($mr['request_date']) ?></td>
                            <td>
                                <?php
                                    if ($mr['status'] === 'pending') echo '<span style="color:#e6b800;">Oczekuje na podjęcie</span>';
                                    elseif ($mr['status'] === 'in_progress') echo '<span style="color:#007bff;">W realizacji</span>';
                                    elseif ($mr['status'] === 'completed') echo '<span style="color:green;">Zakończone</span>';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($maintenance_requests)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak zgłoszeń.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>
                
                <div class="panel-tab-content" id="tab-haslo-konserwator">
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
            <?php elseif ($user_role === 'ksiegowy'): ?>
                <div class="panel-tabs">
                    <button class="panel-tab-btn" data-tab="cabin-expenses-ksiegowy">Wydatki domków</button>
                    <button class="panel-tab-btn" data-tab="expenses-summary">Podsumowanie wydatków</button>
                    <button class="panel-tab-btn" data-tab="employee-salaries-summary">Wynagrodzenia pracowników</button>
                    <button class="panel-tab-btn" data-tab="haslo-ksiegowy">Zmiana hasła</button>
                </div>
                
                <!-- Wydatki domków dla księgowego -->
                <div class="panel-tab-content" id="tab-cabin-expenses-ksiegowy">
                    <h3>Wydatki domków</h3>
                    <?php if (isset($expense_msg)): ?>
                        <div class="panel-msg" style="color:green"><?= htmlspecialchars($expense_msg) ?></div>
                    <?php endif; ?>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Opis</th>
                            <th>Kwota</th>
                            <th>Data wydatku</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($cabin_expenses as $ce): ?>
                        <tr>
                            <?php if ($edit_expense_id === (int)$ce['id']): ?>
                                <form method="POST">
                                    <td><?= $ce['id'] ?><input type="hidden" name="save_expense_id" value="<?= $ce['id'] ?>"></td>
                                    <td><?= htmlspecialchars($ce['cabin_name']) ?></td>
                                    <td><input type="text" name="description" value="<?= htmlspecialchars($ce['description']) ?>"></td>
                                    <td><input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($ce['amount']) ?>" required></td>
                                    <td><input type="date" name="expense_date" value="<?= htmlspecialchars($ce['expense_date']) ?>" required></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $ce['id'] ?></td>
                                <td><?= htmlspecialchars($ce['cabin_name']) ?></td>
                                <td><?= htmlspecialchars($ce['description']) ?></td>
                                <td><?= number_format((float)$ce['amount'], 2, ',', ' ') ?> zł</td>
                                <td><?= htmlspecialchars($ce['expense_date']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_expense_id" value="<?= $ce['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cabin_expenses)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak wydatków.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Podsumowanie wydatków dla księgowego -->
                <div class="panel-tab-content" id="tab-expenses-summary">
                    <h3>Podsumowanie wydatków domków</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID Domku</th>
                            <th>Nazwa Domku</th>
                            <th>Suma wydatków</th>
                        </tr>
                        <?php 
                        $summary_query = mysqli_query($mysqli, "SELECT * FROM view_cabin_expenses_summary ORDER BY total_expenses DESC");
                        $expense_summaries = [];
                        if ($summary_query) while ($row = mysqli_fetch_assoc($summary_query)) $expense_summaries[] = $row;
                        foreach($expense_summaries as $es): 
                        ?>
                        <tr>
                            <td><?= $es['cabin_id'] ?></td>
                            <td><?= htmlspecialchars($es['cabin_name']) ?></td>
                            <td><?= number_format((float)$es['total_expenses'], 2, ',', ' ') ?> zł</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($expense_summaries)): ?>
                        <tr><td colspan="3" style="text-align:center;">Brak danych o wydatkach.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>
                
                <!-- Podsumowanie wynagrodzeń dla księgowego -->
                <div class="panel-tab-content" id="tab-employee-salaries-summary">
                    <h3>Ostatnie wynagrodzenia pracowników</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID Pracownika</th>
                            <th>Imię i nazwisko</th>
                            <th>Kwota</th>
                            <th>Data wypłaty</th>
                        </tr>
                        <?php 
                        $salary_query = mysqli_query($mysqli, "SELECT * FROM view_employee_latest_salary ORDER BY payment_date DESC");
                        $employee_latest_salaries = [];
                        if ($salary_query) while ($row = mysqli_fetch_assoc($salary_query)) $employee_latest_salaries[] = $row;
                        foreach($employee_latest_salaries as $els): 
                        ?>
                        <tr>
                            <td><?= $els['employee_id'] ?></td>
                            <td><?= htmlspecialchars($els['employee_name']) ?></td>
                            <td><?= number_format((float)$els['salary'], 2, ',', ' ') ?> zł</td>
                            <td><?= htmlspecialchars($els['payment_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($employee_latest_salaries)): ?>
                        <tr><td colspan="4" style="text-align:center;">Brak danych o wynagrodzeniach.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Zmiana hasła dla księgowego -->
                <div class="panel-tab-content" id="tab-haslo-ksiegowy">
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
            <?php else: ?>
                <div class="panel-tabs">
                    <button class="panel-tab-btn" data-tab="rezerwacje-admin">Rezerwacje oczekujące</button>
                    <button class="panel-tab-btn" data-tab="wszystkie-rezerwacje">Wszystkie rezerwacje</button>
                    <button class="panel-tab-btn" data-tab="opinie-admin">Opinie użytkowników</button>
                    <button class="panel-tab-btn" data-tab="employees">Pracownicy</button>
                    <button class="panel-tab-btn" data-tab="employee-salaries">Wynagrodzenia</button>
                    <button class="panel-tab-btn" data-tab="cabins">Domki</button>
                    <button class="panel-tab-btn" data-tab="cabin-expenses">Wydatki domków</button>
                    <button class="panel-tab-btn" data-tab="maintenance">Zgłoszenia serwisowe</button>
                    <button class="panel-tab-btn" data-tab="attractions">Atrakcje</a></button>
                    <button class="panel-tab-btn" data-tab="kontakt">Kontakt</button>
                    <button class="panel-tab-btn" data-tab="haslo-admin">Zmiana hasła</button>
                    <button class="panel-tab-btn" data-tab="pending-opinions">Opinie do moderacji</button>
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
                            <th>Do zapłaty</th>
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
                            <td><?= number_format((float)($rez['do_zaplaty'] ?? 0), 2, ',', ' ') ?> zł</td>
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
                        <tr><td colspan="10" style="text-align:center;">Brak rezerwacji oczekujących na akceptację.</td></tr>
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
                            <th>Do zapłaty</th>
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
                            <td><?= number_format((float)($rez['do_zaplaty'] ?? 0), 2, ',', ' ') ?> zł</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_reservations)): ?>
                        <tr><td colspan="11" style="text-align:center;">Brak rezerwacji.</td></tr>
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

                <!-- Pracownicy -->
                <div class="panel-tab-content" id="tab-employees">
                    <h3>Pracownicy</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Imię i nazwisko</th>
                            <th>Stanowisko</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($employees as $e): ?>
                        <tr>
                            <?php if ($edit_employee_id === (int)$e['id']): ?>
                                <form method="POST">
                                    <td><?= $e['id'] ?><input type="hidden" name="save_employee_id" value="<?= $e['id'] ?>"></td>
                                    <td><input type="text" name="name" value="<?= htmlspecialchars($e['name']) ?>" required></td>
                                    <td><input type="text" name="position" value="<?= htmlspecialchars($e['position']) ?>" required></td>
                                    <td><input type="email" name="email" value="<?= htmlspecialchars($e['email']) ?>"></td>
                                    <td><input type="text" name="phone" value="<?= htmlspecialchars($e['phone']) ?>"></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $e['id'] ?></td>
                                <td><?= htmlspecialchars($e['name']) ?></td>
                                <td><?= htmlspecialchars($e['position']) ?></td>
                                <td><?= htmlspecialchars($e['email']) ?></td>
                                <td><?= htmlspecialchars($e['phone']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_employee_id" value="<?= $e['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tego pracownika?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_employee_id" value="<?= $e['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($employees)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak pracowników.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Wynagrodzenia -->
                <div class="panel-tab-content" id="tab-employee-salaries">
                    <h3>Wynagrodzenia pracowników</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Pracownik</th>
                            <th>Kwota</th>
                            <th>Data wypłaty</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($employee_salaries as $es): ?>
                        <tr>
                            <?php if ($edit_salary_id === (int)$es['id']): ?>
                                <form method="POST">
                                    <td><?= $es['id'] ?><input type="hidden" name="save_salary_id" value="<?= $es['id'] ?>"></td>
                                    <td><?= htmlspecialchars($es['employee_name']) ?></td>
                                    <td><input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($es['salary']) ?>" required></td>
                                    <td><input type="date" name="payment_date" value="<?= htmlspecialchars($es['payment_date']) ?>" required></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $es['id'] ?></td>
                                <td><?= htmlspecialchars($es['employee_name']) ?></td>
                                <td><?= number_format((float)$es['salary'], 2, ',', ' ') ?> zł</td>
                                <td><?= htmlspecialchars($es['payment_date']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_salary_id" value="<?= $es['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tę wypłatę?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_salary_id" value="<?= $es['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($employee_salaries)): ?>
                        <tr><td colspan="5" style="text-align:center;">Brak wypłat.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Domki -->
                <div class="panel-tab-content" id="tab-cabins">
                    <h3>Domki</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Opis</th>
                            <th>Cena za noc</th>
                            <th>Obrazek</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($cabins as $c): ?>
                        <tr>
                            <?php if ($edit_cabin_id === (int)$c['id']): ?>
                                <form method="POST">
                                    <td><?= $c['id'] ?><input type="hidden" name="save_cabin_id" value="<?= $c['id'] ?>"></td>
                                    <td><input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" required></td>
                                    <td><input type="text" name="description" value="<?= htmlspecialchars($c['description']) ?>"></td>
                                    <td><input type="number" step="0.01" name="price_per_night" value="<?= htmlspecialchars($c['price_per_night']) ?>" required></td>
                                    <td><input type="text" name="image_url" value="<?= htmlspecialchars($c['image_url']) ?>"></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['description']) ?></td>
                                <td><?= number_format((float)$c['price_per_night'], 2, ',', ' ') ?> zł</td>
                                <td>
                                    <?php if ($c['image_url']): ?>
                                        <img src="<?= htmlspecialchars($c['image_url']) ?>" alt="obrazek" style="max-width:80px;max-height:60px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_cabin_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć ten domek?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_cabin_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cabins)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak domków.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Wydatki domków -->
                <div class="panel-tab-content" id="tab-cabin-expenses">
                    <h3>Wydatki domków</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Opis</th>
                            <th>Kwota</th>
                            <th>Data wydatku</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($cabin_expenses as $ce): ?>
                        <tr>
                            <?php if ($edit_expense_id === (int)$ce['id']): ?>
                                <form method="POST">
                                    <td><?= $ce['id'] ?><input type="hidden" name="save_expense_id" value="<?= $ce['id'] ?>"></td>
                                    <td><?= htmlspecialchars($ce['cabin_name']) ?></td>
                                    <td><input type="text" name="description" value="<?= htmlspecialchars($ce['description']) ?>"></td>
                                    <td><input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($ce['amount']) ?>" required></td>
                                    <td><input type="date" name="expense_date" value="<?= htmlspecialchars($ce['expense_date']) ?>" required></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $ce['id'] ?></td>
                                <td><?= htmlspecialchars($ce['cabin_name']) ?></td>
                                <td><?= htmlspecialchars($ce['description']) ?></td>
                                <td><?= number_format((float)$ce['amount'], 2, ',', ' ') ?> zł</td>
                                <td><?= htmlspecialchars($ce['expense_date']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_expense_id" value="<?= $ce['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć ten wydatek?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_expense_id" value="<?= $ce['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cabin_expenses)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak wydatków.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Zgłoszenia serwisowe -->
                <div class="panel-tab-content" id="tab-maintenance">
                    <h3>Zgłoszenia serwisowe</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Domek</th>
                            <th>Opis</th>
                            <th>Status</th>
                            <th>Data zgłoszenia</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($maintenance_requests as $mr): ?>
                        <tr>
                            <?php if ($edit_maintenance_id === (int)$mr['id']): ?>
                                <form method="POST">
                                    <td><?= $mr['id'] ?><input type="hidden" name="save_maintenance_id" value="<?= $mr['id'] ?>"></td>
                                    <td><?= htmlspecialchars($mr['cabin_name']) ?></td>
                                    <td><input type="text" name="description" value="<?= htmlspecialchars($mr['description']) ?>"></td>
                                    <td>
                                        <select name="status">
                                            <option value="pending" <?= $mr['status']=='pending'?'selected':'' ?>>Oczekuje</option>
                                            <option value="in_progress" <?= $mr['status']=='in_progress'?'selected':'' ?>>W trakcie</option>
                                            <option value="completed" <?= $mr['status']=='completed'?'selected':'' ?>>Zakończone</option>
                                        </select>
                                    </td>
                                    <td><input type="date" name="request_date" value="<?= htmlspecialchars($mr['request_date']) ?>" required></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $mr['id'] ?></td>
                                <td><?= htmlspecialchars($mr['cabin_name']) ?></td>
                                <td><?= htmlspecialchars($mr['description']) ?></td>
                                <td>
                                    <?php
                                        if ($mr['status'] === 'pending') echo '<span style="color:#e6b800;">Oczekuje</span>';
                                        elseif ($mr['status'] === 'in_progress') echo '<span style="color:#007bff;">W trakcie</span>';
                                        elseif ($mr['status'] === 'completed') echo '<span style="color:green;">Zakończone</span>';
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($mr['request_date']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_maintenance_id" value="<?= $mr['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć to zgłoszenie?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_maintenance_id" value="<?= $mr['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($maintenance_requests)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak zgłoszeń.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Atrakcje -->
                <div class="panel-tab-content" id="tab-attractions">
                    <h3>Atrakcje w okolicy</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Opis</th>
                            <th>Odległość (km)</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($attractions as $a): ?>
                        <tr>
                            <?php if ($edit_attraction_id === (int)$a['id']): ?>
                                <form method="POST">
                                    <td><?= $a['id'] ?><input type="hidden" name="save_attraction_id" value="<?= $a['id'] ?>"></td>
                                    <td><input type="text" name="name" value="<?= htmlspecialchars($a['name']) ?>" required></td>
                                    <td><input type="text" name="description" value="<?= htmlspecialchars($a['description']) ?>"></td>
                                    <td><input type="number" step="0.01" name="distance_km" value="<?= htmlspecialchars($a['distance_km']) ?>"></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $a['id'] ?></td>
                                <td><?= htmlspecialchars($a['name']) ?></td>
                                <td><?= htmlspecialchars($a['description']) ?></td>
                                <td><?= number_format((float)$a['distance_km'], 2, ',', ' ') ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_attraction_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tę atrakcję?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_attraction_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($attractions)): ?>
                        <tr><td colspan="5" style="text-align:center;">Brak atrakcji.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>

                <!-- Kontakt -->
                <div class="panel-tab-content" id="tab-kontakt">
                    <h3>Wiadomości z formularza kontaktowego</h3>
                    <div class="scroll-table">
                    <table class="rezerwacje-admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Imię i nazwisko</th>
                            <th>Email</th>
                            <th>Temat</th>
                            <th>Treść</th>
                            <th>Opcje</th>
                        </tr>
                        <?php foreach($kontakt_msgs as $k): ?>
                        <tr>
                            <?php if ($edit_kontakt_id === (int)$k['id']): ?>
                                <form method="POST">
                                    <td><?= $k['id'] ?><input type="hidden" name="save_kontakt_id" value="<?= $k['id'] ?>"></td>
                                    <td><input type="text" name="imie_nazwisko" value="<?= htmlspecialchars($k['imie_nazwisko']) ?>" required></td>
                                    <td><input type="email" name="email" value="<?= htmlspecialchars($k['email']) ?>" required></td>
                                    <td><input type="text" name="temat" value="<?= htmlspecialchars($k['temat']) ?>" required></td>
                                    <td><input type="text" name="tresc" value="<?= htmlspecialchars($k['tresc']) ?>" required></td>
                                    <td>
                                        <button type="submit" class="accept-btn">Zapisz</button>
                                        <a href="" class="delete-btn" style="text-decoration:none;" onclick="window.location.reload();return false;">Anuluj</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= $k['id'] ?></td>
                                <td><?= htmlspecialchars($k['imie_nazwisko']) ?></td>
                                <td><?= htmlspecialchars($k['email']) ?></td>
                                <td><?= htmlspecialchars($k['temat']) ?></td>
                                <td><?= htmlspecialchars($k['tresc']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_kontakt_id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tę wiadomość?')">Usuń</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_kontakt_id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="accept-btn">Edytuj</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kontakt_msgs)): ?>
                        <tr><td colspan="6" style="text-align:center;">Brak wiadomości.</td></tr>
                        <?php endif; ?>
                    </table>
                    </div>
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
            <div class="panel-tab-content" id="tab-pending-opinions">
    <h3>Opinie oczekujące na akceptację</h3>
    <?php if (isset($_SESSION['admin_msg'])): ?>
        <div class="panel-msg" style="color:green"><?= htmlspecialchars($_SESSION['admin_msg']) ?></div>
        <?php unset($_SESSION['admin_msg']); ?>
    <?php endif; ?>
    <table class="rezerwacje-admin-table">
        <tr>
            <th>ID</th>
            <th>Użytkownik</th>
            <th>Ocena</th>
            <th>Treść</th>
            <th>Data</th>
            <th>Akcja</th>
        </tr>
        <?php foreach($pending_opinions as $opinia): ?>
        <tr>
            <td><?= $opinia['id'] ?></td>
            <td><?= htmlspecialchars($opinia['username']) ?></td>
            <td><?= (int)$opinia['rating'] ?></td>
            <td><?= htmlspecialchars($opinia['content']) ?></td>
            <td><?= htmlspecialchars($opinia['created_at']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="opinia_id" value="<?= $opinia['id'] ?>">
                    <button type="submit" name="opinia_action" value="approve" class="accept-btn">Akceptuj</button>
                    <button type="submit" name="opinia_action" value="reject" class="reject-btn">Odrzuć</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($pending_opinions)): ?>
        <tr><td colspan="6" style="text-align:center;">Brak opinii oczekujących na moderację.</td></tr>
        <?php endif; ?>
    </table>
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