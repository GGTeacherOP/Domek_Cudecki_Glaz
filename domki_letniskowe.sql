-- phpMyAdmin SQL Dump
-- wersja 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 18 Maj 2025, 19:08
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `domki_letniskowe`
--

-- --------------------------------------------------------

--
-- Struktura tabeli `cabins`
--

CREATE TABLE `cabins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `cabins`
--

INSERT INTO `cabins` (`id`, `name`, `description`, `price_per_night`, `image_url`) VALUES
(1, 'Domek Słoneczny', 'Przestronny domek z dwiema sypialniami, idealny dla rodziny lub grupy przyjaciół.', 350.00, 'assets/img/domek1.jpg'),
(2, 'Domek Brzozowy', 'Przytulny domek idealny dla pary lub małej rodziny.', 280.00, 'assets/img/domek2.jpg'),
(3, 'Domek Premium', 'Luksusowy domek dla najbardziej wymagających gości.', 550.00, 'assets/img/domek3.jpg');
-- --------------------------------------------------------

--
-- Struktura tabeli `cabin_expenses`
--

CREATE TABLE `cabin_expenses` (
  `id` int(11) NOT NULL,
  `cabin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `cabin_expenses`
--

INSERT INTO `cabin_expenses` (`id`, `cabin_id`, `description`, `amount`, `expense_date`) VALUES
(1, 1, 'Wymiana grzejnika', 780.50, '2025-04-10'),
(2, 1, 'Zakup pościeli i ręczników', 320.00, '2025-04-15'),
(3, 2, 'Naprawa prysznica', 250.00, '2025-04-12'),
(4, 3, 'Nowe zasłony i rolety', 600.00, '2025-04-18'),
(5, 1, 'Środki czystości', 120.00, '2025-04-05'),
(6, 2, 'Odświeżenie malowania ścian', 1150.00, '2025-03-28'),
(7, 3, 'Serwis klimatyzacji', 450.00, '2025-04-25'),
(8, 1, 'Nowa lodówka', 1450.00, '2025-03-30'),
(9, 2, 'Wymiana żarówek LED', 90.00, '2025-04-01'),
(10, 3, 'Pest control', 200.00, '2025-04-03'),
(11, 1, 'Przegląd kominka', 300.00, '2025-04-20'),
(12, 2, 'Naprawa dachu', 1500.00, '2025-04-22'),
(13, 3, 'Wymiana okien', 2500.00, '2025-04-28'),
(14, 1, 'Malowanie zewnętrzne', 2000.00, '2025-05-05'),
(15, 2, 'Czyszczenie basenu', 500.00, '2025-05-10'),
(16, 3, 'Serwis pieca', 700.00, '2025-05-15'),
(17, 1, 'Wymiana drzwi wejściowych', 800.00, '2025-05-20'),
(18, 2, 'Instalacja alarmu', 1200.00, '2025-05-25'),
(19, 3, 'Naprawa schodów', 900.00, '2025-05-30');

-- --------------------------------------------------------

--
-- Struktura tabeli `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `employees`
--

INSERT INTO `employees` (`id`, `name`, `position`, `email`, `phone`) VALUES
(1, 'Jan Kowalski', 'Manager', 'jan.kowalski@example.com', '500123456'),
(2, 'Anna Nowak', 'Recepcjonistka', 'anna.nowak@example.com', '510987654'),
(3, 'Piotr Wiśniewski', 'Technik', 'piotr.wisniewski@example.com', '503112233'),
(4, 'Ewa Zawadzka', 'Sprzątaczka', 'ewa.zawadzka@example.com', '512223344'),
(5, 'Kamil Dąbrowski', 'Ochroniarz', 'kamil.dabrowski@example.com', '511334455'),
(6, 'Marek Nowak', 'Kierownik ds. rezerwacji', 'marek.nowak@example.com', '500987654'),
(7, 'Zofia Kowalczyk', 'Recepcjonistka', 'zofia.kowalczyk@example.com', '510123456'),
(8, 'Tomasz Wiśniewski', 'Technik', 'tomasz.wisniewski@example.com', '503987654'),
(9, 'Agnieszka Zawadzka', 'Sprzątaczka', 'agnieszka.zawadzka@example.com', '512345678'),
(10, 'Jakub Dąbrowski', 'Ochroniarz', 'jakub.dabrowski@example.com', '511223344');

-- --------------------------------------------------------

--
-- Struktura tabeli `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `employee_salaries`
--

INSERT INTO `employee_salaries` (`id`, `employee_id`, `salary`, `payment_date`) VALUES
(1, 1, 6200.00, '2025-04-30'),
(2, 2, 4200.00, '2025-04-30'),
(3, 3, 4800.00, '2025-04-30'),
(4, 4, 3600.00, '2025-04-30'),
(5, 5, 3900.00, '2025-04-30'),
(6, 1, 6200.00, '2025-03-31'),
(7, 2, 4200.00, '2025-03-31'),
(8, 3, 4800.00, '2025-03-31'),
(9, 4, 3600.00, '2025-03-31'),
(10, 5, 3900.00, '2025-03-31'),
(11, 6, 7000.00, '2025-04-30'),
(12, 7, 4500.00, '2025-04-30'),
(13, 8, 5000.00, '2025-04-30'),
(14, 9, 3800.00, '2025-04-30'),
(15, 10, 4100.00, '2025-04-30'),
(16, 6, 7000.00, '2025-03-31'),
(17, 7, 4500.00, '2025-03-31'),
(18, 8, 5000.00, '2025-03-31'),
(19, 9, 3800.00, '2025-03-31'),
(20, 10, 4100.00, '2025-03-31');

-- --------------------------------------------------------

--
-- Struktura tabeli `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL,
  `cabin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `request_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `cabin_id`, `description`, `status`, `request_date`) VALUES
(1, 1, 'Zgłoszenie: przeciekający kran w kuchni', 'pending', '2025-05-01'),
(2, 2, 'Zgłoszenie: brak ciepłej wody', 'in_progress', '2025-04-28'),
(3, 3, 'Zgłoszenie: zepsuty zamek w drzwiach wejściowych', 'completed', '2025-04-25'),
(4, 1, 'Zgłoszenie: nie działa klimatyzacja', 'pending', '2025-05-02'),
(5, 2, 'Zgłoszenie: problemy z pilotem do TV', 'completed', '2025-04-20'),
(6, 3, 'Zgłoszenie: zatkana toaleta', 'in_progress', '2025-04-30'),
(7, 2, 'Zgłoszenie: brudne okna zgłoszone przez gościa', 'completed', '2025-04-15'),
(8, 3, 'Zgłoszenie: awaria Wi-Fi', 'pending', '2025-05-03'),
(9, 1, 'Zgłoszenie: drzwi balkonowe się nie domykają', 'pending', '2025-05-05'),
(10, 2, 'Zgłoszenie: luz w kontakcie elektrycznym', 'in_progress', '2025-05-04'),
(11, 1, 'Zgłoszenie: uszkodzony stół w jadalni', 'completed', '2025-04-18'),
(12, 2, 'Zgłoszenie: nie działa lodówka', 'pending', '2025-05-06'),
(13, 3, 'Zgłoszenie: zepsuty telewizor', 'in_progress', '2025-05-07'),
(14, 1, 'Zgłoszenie: brak prądu w łazience', 'pending', '2025-05-08'),
(15, 2, 'Zgłoszenie: cieknący kran w łazience', 'completed', '2025-04-22'),
(16, 3, 'Zgłoszenie: zablokowana toaleta', 'in_progress', '2025-05-09'),
(17, 1, 'Zgłoszenie: hałas z klimatyzacji', 'pending', '2025-05-10'),
(18, 2, 'Zgłoszenie: uszkodzona deska sedesowa', 'completed', '2025-04-25'),
(19, 3, 'Zgłoszenie: nie działa pilot do telewizora', 'in_progress', '2025-05-11'),
(20, 1, 'Zgłoszenie: zacieki na suficie w kuchni', 'pending', '2025-05-12');

-- --------------------------------------------------------

--
-- Struktura tabeli `opinions`
--

CREATE TABLE `opinions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `opinions`
--

INSERT INTO `opinions` (`id`, `user_id`, `content`, `rating`, `created_at`) VALUES
(1, 1, 'Wspaniałe miejsce na rodzinny wypoczynek!', 5, '2024-05-01 12:00:00'),
(2, 2, 'Przepiękna okolica i świetnie wyposażone domki.', 4, '2024-05-03 15:30:00'),
(3, 3, 'Na pewno tu wrócimy!', 5, '2024-05-05 09:45:00'),
(4, 1, 'Domek bardzo czysty i zadbany, polecam!', 5, '2024-05-10 10:00:00'),
(5, 2, 'Bardzo miła obsługa, domek spełnił nasze oczekiwania.', 4, '2024-05-12 14:30:00'),
(6, 3, 'Cisza i spokój, idealne miejsce na relaks.', 5, '2024-05-15 16:45:00'),
(7, 1, 'Domek dobrze wyposażony, blisko do atrakcji.', 4, '2024-05-18 11:15:00'),
(8, 2, 'Wspaniałe widoki z okna, polecam na romantyczny wypad.', 5, '2024-05-20 09:00:00'),
(9, 3, 'Domek z duszą, na pewno tu wrócimy.', 5, '2024-05-22 13:30:00'),
(10, 1, 'Nie ma to jak poranna kawa na tarasie z widokiem na góry.', 5, '2024-05-25 08:00:00');

-- --------------------------------------------------------

--
-- Struktura tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cabin_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `telefon` varchar(40) DEFAULT NULL,
  `uwagi` text DEFAULT NULL,
  `do_zaplaty` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'AnnaK', 'anna@example.com', 'haslo1', 'client'),
(2, 'MarekW', 'marek@example.com', 'haslo2', 'client'),
(3, 'KarolinaM', 'karolina@example.com', 'haslo3', 'client'),
(4, 'AdminJan', 'jan.admin@example.com', 'bezpiecznehaslo1', 'admin'),
(5, 'AdminEwa', 'ewa.admin@example.com', 'superadmin2', 'admin'),
(6, 'AdminPiotr', 'piotr.admin@example.com', 'tajnehaslo3', 'admin'),
(7, 'bobek1', 'bobek1@gmail.com', 'bobek123', 'client');

-- --------------------------------------------------------

-- Tabela kontakt do przechowywania wiadomości od użytkowników

CREATE TABLE `kontakt` (
  `id` int(11) NOT NULL,
  `imie_nazwisko` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `temat` varchar(200) NOT NULL,
  `tresc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `kontakt`
--

INSERT INTO `kontakt` (`id`, `imie_nazwisko`, `email`, `temat`, `tresc`) VALUES
(1, 'Jan Nowak', 'jan.nowak@gmail.com', 'Rezerwacja domku', 'Chciałbym zarezerwować domek na weekend.'),
(2, 'Anna Kowalska', 'anna.kowalska@gmail.com', 'Pytanie o wyposażenie', 'Czy w domku jest pralka?'),
(3, 'Piotr Zieliński', 'piotr.zielinski@gmail.com', 'Dostępność terminu', 'Czy domek jest wolny w lipcu?'),
(4, 'Ewa Wiśniewska', 'ewa.wisniewska@gmail.com', 'Zwierzęta', 'Czy można przyjechać z psem?'),
(5, 'Kamil Mazur', 'kamil.mazur@gmail.com', 'Dodatkowe łóżko', 'Czy można dostać dostawkę dla dziecka?'),
(6, 'Karolina Dąbrowska', 'karolina.dabrowska@gmail.com', 'Parking', 'Czy jest miejsce parkingowe?'),
(7, 'Marek Lewandowski', 'marek.lewandowski@gmail.com', 'Grill', 'Czy jest możliwość grillowania?'),
(8, 'Magdalena Kaczmarek', 'magda.kaczmarek@gmail.com', 'Internet', 'Czy jest dostęp do Wi-Fi?'),
(9, 'Tomasz Szymański', 'tomasz.szymanski@gmail.com', 'Rowery', 'Czy można wypożyczyć rowery?'),
(10, 'Paulina Wójcik', 'paulina.wojcik@gmail.com', 'Doba hotelowa', 'Od której godziny można się zameldować?');

-- --------------------------------------------------------

-- Tabela attractions - atrakcje w okolicy domków

CREATE TABLE `attractions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `attractions`
--

INSERT INTO `attractions` (`id`, `name`, `description`, `distance_km`) VALUES
(1, 'Jezioro Łabędzia', 'Malownicze jezioro oddalone o 2 km od domku. Idealne miejsce na kąpiele, wędkowanie i kajaki. W sezonie działa wypożyczalnia sprzętu wodnego.', 2.00),
(2, 'Puszcza Zielona', 'Rozległy kompleks leśny z trasami rowerowymi i szlakami pieszymi. Można spotkać dzikie zwierzęta i zbierać grzyby (w sezonie).', 1.80),
(3, 'Zamek Krzyżacki', 'Historyczny zamek z XIV wieku, oddalony o 15 km. Organizowane są nocne zwiedzania z przewodnikiem w kostiumach.', 15.00),
(4, 'Strefa Spa & Wellness', 'Luksusowe SPA oferujące masaże, baseny termalne i sauny. Znajduje się w odległości 10 km od domku.', 10.00),
(5, 'Muzeum Regionalne', 'Interaktywne muzeum prezentujące historię regionu. Działa tu także kawiarnia z domowymi ciastami.', 3.20),
(6, 'Trasy rowerowe', 'Sieć oznakowanych tras rowerowych o różnym poziomie trudności. Można wypożyczyć rowery w pobliskiej wypożyczalni.', 0.50),
(7, 'Park Linowy "Leśna Przygoda"', 'Adrenalina dla całej rodziny! Trasy o różnym poziomie trudności, zjazdy tyrolskie i mosty linowe zawieszone w koronach drzew. Dla dzieci specjalna strefa z opiekunem.', 2.70),
(8, 'Punkt Widokowy "Góra Panorama"', 'Najpiękniejsza panorama w regionie! Łatwa ścieżka (1.5 km) prowadzi na szczyt, skąd widać jezioro, lasy i okoliczne wioski. Idealne miejsce na zachód słońca.', 1.50),
(9, 'Skansen "Dawna Wieś"', 'Żywe muzeum tradycji! Zobacz XVIII-wieczne chaty, warsztaty rzemieślnicze i pokazy wypieku chleba. W weekendy organizowane są warsztaty dla dzieci.', 4.40),
(10, 'Spływ Rzeką Nurt', '3-godzinna trasa kajakowa przez malownicze zakola rzeki. Wypożyczalnia zapewnia suchy bagaż i transport z powrotem. Dla rodzin dostępne stabilne kanadyjki.', 2.30);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `cabins`
--
ALTER TABLE `cabins`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indeksy dla tabeli `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeksy dla tabeli `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indeksy dla tabeli `opinions`
--
ALTER TABLE `opinions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `attractions`
--
ALTER TABLE `attractions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `cabins`
--
ALTER TABLE `cabins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `opinions`
--
ALTER TABLE `opinions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `attractions`
--
ALTER TABLE `attractions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  ADD CONSTRAINT `cabin_expenses_ibfk_1` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `opinions`
--
ALTER TABLE `opinions`
  ADD CONSTRAINT `opinions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `kontakt`
--
-- ALTER TABLE `kontakt`
--   ADD CONSTRAINT `kontakt_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- Widok: Suma wydatków na każdy domek
CREATE OR REPLACE VIEW view_cabin_expenses_summary AS
SELECT
  c.id AS cabin_id,
  c.name AS cabin_name,
  SUM(e.amount) AS total_expenses
FROM cabins c
LEFT JOIN cabin_expenses e ON c.id = e.cabin_id
GROUP BY c.id, c.name;

-- --------------------------------------------------------
-- Widok: Ostatnia wypłata każdego pracownika
CREATE OR REPLACE VIEW view_employee_latest_salary AS
SELECT
  e.id AS employee_id,
  e.name AS employee_name,
  s.salary,
  s.payment_date
FROM employees e
JOIN (
  SELECT employee_id, MAX(payment_date) AS max_date
  FROM employee_salaries
  GROUP BY employee_id
) latest ON e.id = latest.employee_id
JOIN employee_salaries s ON s.employee_id = latest.employee_id AND s.payment_date = latest.max_date;

-- --------------------------------------------------------
-- Widok: Oczekujące zgłoszenia serwisowe z nazwą domku
CREATE OR REPLACE VIEW view_pending_maintenance_requests AS
SELECT
  mr.id AS request_id,
  c.name AS cabin_name,
  mr.description,
  mr.status,
  mr.request_date
FROM maintenance_requests mr
JOIN cabins c ON mr.cabin_id = c.id
WHERE mr.status = 'pending';

-- --------------------------------------------------------
-- Widok: Atrakcje w promieniu 3 km od domków
CREATE OR REPLACE VIEW view_attractions_nearby AS
SELECT
  id,
  name,
  description,
  distance_km
FROM attractions
WHERE distance_km <= 3.00;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
