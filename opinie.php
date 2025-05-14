<?php
session_start();
// Połączenie z bazą danych (mysqli)
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'domki_letniskowe';

$conn = mysqli_connect($host, $user, $pass, $db);
$opinie = [];
if ($conn) {
    $sql = "SELECT o.content, o.rating, u.username, u.email, o.id, o.created_at 
            FROM opinions o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.id DESC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $opinie[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opinie - Domki Letniskowe</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .opinie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .opinia-karta {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .opinia-tekst {
            font-style: italic;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .opinia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .opinia-autor {
            font-weight: bold;
            color: var(--primary-color);
        }
        .opinia-data {
            color: #777;
        }
        .gwiazdki {
            color: gold;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0px 0px 1px #000;
        }
        .dodaj-opinie {
            background: var(--light-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 3rem 0;
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
        .star-rating {
            margin-bottom: 1rem;
            direction: rtl;
            text-align: left;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            display: inline-block;
            cursor: pointer;
            font-size: 1.8rem;
            color: #ddd;
            transition: color 0.2s;
            margin-right: 0.2rem;
            text-shadow: 0px 0px 1px #000;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: gold;
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
        <h1 style="text-align:center;">Opinie naszych gości</h1>
        <p style="text-align:center;">Poznaj opinie osób, które już u nas wypoczywały</p>
        
        <div class="opinie-grid">
            <?php foreach($opinie as $opinia): ?>
                <div class="opinia-karta">
                    <div class="gwiazdki">
                        <?php
                        $rating = (int)$opinia['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <div class="opinia-tekst">
                        <?= htmlspecialchars($opinia['content']) ?>
                    </div>
                    <div class="opinia-meta">
                        <span class="opinia-autor"><?= htmlspecialchars($opinia['username']) ?></span>
                        <span class="opinia-data">
                            <?= date('d.m.Y H:i', strtotime($opinia['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="dodaj-opinie">
            <h2>Dodaj swoją opinię</h2>
            <?php if(isset($_SESSION['opinia_success'])): ?>
                <div style="color:green;text-align:center;margin-bottom:1rem;">
                    <?= htmlspecialchars($_SESSION['opinia_success']) ?>
                </div>
                <?php unset($_SESSION['opinia_success']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['opinia_error'])): ?>
                <div style="color:red;text-align:center;margin-bottom:1rem;">
                    <?= htmlspecialchars($_SESSION['opinia_error']) ?>
                </div>
                <?php unset($_SESSION['opinia_error']); ?>
            <?php endif; ?>
            <form action="dodaj_opinie.php" method="POST">
                <div class="form-group">
                    <label>Ocena</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="ocena" value="5" checked>
                        <label for="star5">★</label>
                        <input type="radio" id="star4" name="ocena" value="4">
                        <label for="star4">★</label>
                        <input type="radio" id="star3" name="ocena" value="3">
                        <label for="star3">★</label>
                        <input type="radio" id="star2" name="ocena" value="2">
                        <label for="star2">★</label>
                        <input type="radio" id="star1" name="ocena" value="1">
                        <label for="star1">★</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tresc">Twoja opinia</label>
                    <textarea id="tresc" name="tresc" required></textarea>
                </div>
                <button type="submit" class="btn-rezerwuj">Dodaj opinię</button>
            </form>
        </div>
        <?php else: ?>
        <div class="dodaj-opinie" style="text-align:center;">
            <p>Aby dodać opinię, <a href="login.php">zaloguj się</a>.</p>
        </div>
        <?php endif; ?>
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
