<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $temat = trim($_POST['temat'] ?? '');
    $wiadomosc = trim($_POST['wiadomosc'] ?? '');

    // Prosta walidacja
    if ($imie && $email && $temat && $wiadomosc) {
        // Połączenie z bazą danych
        $conn = mysqli_connect('localhost', 'root', '', 'domki_letniskowe');
        if (!$conn) {
            header('Location: kontakt.php?success=0');
            exit;
        }
        // Zabezpieczenie danych
        $imie = mysqli_real_escape_string($conn, $imie);
        $email = mysqli_real_escape_string($conn, $email);
        $temat = mysqli_real_escape_string($conn, $temat);
        $wiadomosc = mysqli_real_escape_string($conn, $wiadomosc);

        $sql = "INSERT INTO kontakt (imie_nazwisko, email, temat, tresc) VALUES ('$imie', '$email', '$temat', '$wiadomosc')";
        if (mysqli_query($conn, $sql)) {
            mysqli_close($conn);
            header('Location: kontakt.php?success=1');
            exit;
        } else {
            mysqli_close($conn);
            header('Location: kontakt.php?success=0');
            exit;
        }
    }
}
header('Location: kontakt.php?success=0');
exit;
