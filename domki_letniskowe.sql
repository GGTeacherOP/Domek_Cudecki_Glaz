-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 07:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `domki_letniskowe`
--

-- --------------------------------------------------------

--
-- Table structure for table `cabins`
--

CREATE TABLE `cabins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cabins`
--

INSERT INTO `cabins` (`id`, `name`, `description`, `price_per_night`, `image_url`) VALUES
(1, 'Domek Słoneczny', 'Przestronny domek z dwiema sypialniami, idealny dla rodziny lub grupy przyjaciół.', 350.00, 'assets/img/domek1.jpg'),
(2, 'Domek Brzozowy', 'Przytulny domek idealny dla pary lub małej rodziny.', 280.00, 'assets/img/domek2.jpg'),
(3, 'Domek Premium', 'Luksusowy domek dla najbardziej wymagających gości.', 550.00, 'assets/img/domek3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cabin_expenses`
--

CREATE TABLE `cabin_expenses` (
  `id` int(11) NOT NULL,
  `cabin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cabin_expenses`
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
(10, 3, 'Pest control', 200.00, '2025-04-03');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `position`, `email`, `phone`) VALUES
(1, 'Jan Kowalski', 'Manager', 'jan.kowalski@example.com', '500123456'),
(2, 'Anna Nowak', 'Recepcjonistka', 'anna.nowak@example.com', '510987654'),
(3, 'Piotr Wiśniewski', 'Technik', 'piotr.wisniewski@example.com', '503112233'),
(4, 'Ewa Zawadzka', 'Sprzątaczka', 'ewa.zawadzka@example.com', '512223344'),
(5, 'Kamil Dąbrowski', 'Ochroniarz', 'kamil.dabrowski@example.com', '511334455');

-- --------------------------------------------------------

--
-- Table structure for table `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salaries`
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
(10, 5, 3900.00, '2025-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL,
  `cabin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `request_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_requests`
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
(10, 2, 'Zgłoszenie: luz w kontakcie elektrycznym', 'in_progress', '2025-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `opinions`
--

CREATE TABLE `opinions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opinions`
--

INSERT INTO `opinions` (`id`, `user_id`, `content`, `rating`, `created_at`) VALUES
(1, 1, 'Wspaniałe miejsce na rodzinny wypoczynek!', 5, '2024-05-01 12:00:00'),
(2, 2, 'Przepiękna okolica i świetnie wyposażone domki.', 4, '2024-05-03 15:30:00'),
(3, 3, 'Na pewno tu wrócimy!', 5, '2024-05-05 09:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'AnnaK', 'anna@example.com', 'haslo1', 'client'),
(2, 'MarekW', 'marek@example.com', 'haslo2', 'client'),
(3, 'KarolinaM', 'karolina@example.com', 'haslo3', 'client'),
(4, 'AdminJan', 'jan.admin@example.com', 'bezpiecznehaslo1', 'admin'),
(5, 'AdminEwa', 'ewa.admin@example.com', 'superadmin2', 'admin'),
(6, 'AdminPiotr', 'piotr.admin@example.com', 'tajnehaslo3', 'admin'),
(7, 'bobek1', 'bobek1@gmail.com', 'bobek123', 'client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cabins`
--
ALTER TABLE `cabins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indexes for table `opinions`
--
ALTER TABLE `opinions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cabin_id` (`cabin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cabins`
--
ALTER TABLE `cabins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `opinions`
--
ALTER TABLE `opinions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cabin_expenses`
--
ALTER TABLE `cabin_expenses`
  ADD CONSTRAINT `cabin_expenses_ibfk_1` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opinions`
--
ALTER TABLE `opinions`
  ADD CONSTRAINT `opinions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
