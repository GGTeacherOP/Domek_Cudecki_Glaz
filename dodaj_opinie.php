<?php
// Rozpoczęcie sesji PHP
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinia_error'] = 'Musisz być zalogowany, aby dodać opinię.';
    header('Location: opinie.php');
    exit;
}

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i walidacja danych
    $user_id = (int)$_SESSION['user_id'];
    $ocena = isset($_POST['ocena']) ? (int)$_POST['ocena'] : 5;
    $tresc = isset($_POST['tresc']) ? trim($_POST['tresc']) : '';

    // Sprawdzenie poprawności danych
    if ($ocena < 1 || $ocena > 5 || empty($tresc)) {
        $_SESSION['opinia_error'] = 'Wszystkie pola są wymagane i ocena musi być od 1 do 5.';
        header('Location: opinie.php');
        exit;
    }

    // Konfiguracja i połączenie z bazą danych
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

    // Zabezpieczenie i zapisanie opinii (domyślnie niezatwierdzonej)
    $tresc_esc = mysqli_real_escape_string($conn, $tresc);
    $sql = "INSERT INTO opinions (user_id, content, rating, approved) VALUES ($user_id, '$tresc_esc', $ocena, FALSE)";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['opinia_success'] = 'Dziękujemy za dodanie opinii! Opinia będzie widoczna po zatwierdzeniu przez administratora.';
    } else {
        $_SESSION['opinia_error'] = 'Nie udało się dodać opinii.';
    }
    mysqli_close($conn);
    header('Location: opinie.php');
    exit;
} else {
    // Przekierowanie jeśli nie jest to żądanie POST
    header('Location: opinie.php');
    exit;
}