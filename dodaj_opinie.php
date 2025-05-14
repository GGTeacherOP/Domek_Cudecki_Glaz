<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinia_error'] = 'Musisz być zalogowany, aby dodać opinię.';
    header('Location: opinie.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $ocena = isset($_POST['ocena']) ? (int)$_POST['ocena'] : 5;
    $tresc = isset($_POST['tresc']) ? trim($_POST['tresc']) : '';

    if ($ocena < 1 || $ocena > 5 || empty($tresc)) {
        $_SESSION['opinia_error'] = 'Wszystkie pola są wymagane i ocena musi być od 1 do 5.';
        header('Location: opinie.php');
        exit;
    }

    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'domki_letniskowe';

    $conn = mysqli_connect($host, $user, $pass, $db);
    if (!$conn) {
        $_SESSION['opinia_error'] = 'Błąd połączenia z bazą danych.';
        header('Location: opinie.php');
        exit;
    }

    $tresc_esc = mysqli_real_escape_string($conn, $tresc);
    $sql = "INSERT INTO opinions (user_id, content, rating) VALUES ($user_id, '$tresc_esc', $ocena)";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['opinia_success'] = 'Dziękujemy za dodanie opinii!';
    } else {
        $_SESSION['opinia_error'] = 'Nie udało się dodać opinii.';
    }
    mysqli_close($conn);
    header('Location: opinie.php');
    exit;
} else {
    header('Location: opinie.php');
    exit;
}
