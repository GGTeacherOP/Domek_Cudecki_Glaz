<?php
// Rozpoczęcie sesji
session_start();

// Usunięcie wszystkich zmiennych sesyjnych
session_unset();

// Zniszczenie sesji
session_destroy();

// Przekierowanie do strony głównej
header('Location: index.php');
exit;
?>
