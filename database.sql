-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2024 at 08:21 PM
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
-- Database: `deportes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pista`
--
CREATE DATABASE IF NOT EXISTS deportes_db;

CREATE TABLE `pista` (
  `id` int(11) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `tipo` varchar(6) NOT NULL,
  `max_jugadores` int(11) NOT NULL DEFAULT 2,
  `disponible` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pista`
--

INSERT INTO `pista` (`id`, `nombre`, `tipo`, `max_jugadores`, `disponible`) VALUES
(1, 'Cancha A1', 'futbol', 12, 1),
(2, 'Cancha B1', 'balonc', 6, 1),
(5, 'Cancha P1', 'padel', 4, 0),
(6, 'Cancha D1', 'futbol', 12, 1),
(8, 'cancha p1', 'futbol', 12, 0),
(9, 'cancha z1', 'padel', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `socio` int(11) NOT NULL,
  `pista` int(11) NOT NULL,
  `fecha` varchar(8) NOT NULL,
  `hora` int(11) NOT NULL DEFAULT 0,
  `iluminar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reserva`
--

INSERT INTO `reserva` (`id`, `socio`, `pista`, `fecha`, `hora`, `iluminar`) VALUES
(1, 1, 1, '10122024', 11, 0),
(2, 2, 1, '10122024', 23, 0),
(3, 5, 1, '10122024', 23, 0),
(4, 1, 1, '20122024', 10, 1),
(8, 1, 2, '10122024', 18, 1),
(9, 1, 1, '10122024', 11, 1),
(11, 1, 2, '10122024', 13, 1),
(12, 1, 1, '20122024', 11, 0),
(13, 1, 1, '20122024', 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `socio`
--

CREATE TABLE `socio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(9) DEFAULT NULL,
  `edad` int(11) NOT NULL DEFAULT 0,
  `penalizado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socio`
--

INSERT INTO `socio` (`id`, `nombre`, `telefono`, `edad`, `penalizado`) VALUES
(1, 'Luis Jimenez', '6540851', 30, 0),
(2, 'Juana Susana', '7894532', 70, 0),
(4, 'Julian Garcia', '425041', 34, 0),
(5, 'Juanito Perez', '25840', 23, 0),
(6, 'Miauricio Sanchez', '789502', 20, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pista`
--
ALTER TABLE `pista`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foranea1` (`pista`),
  ADD KEY `foranea2` (`socio`);

--
-- Indexes for table `socio`
--
ALTER TABLE `socio`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pista`
--
ALTER TABLE `pista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `socio`
--
ALTER TABLE `socio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `foranea1` FOREIGN KEY (`pista`) REFERENCES `pista` (`id`),
  ADD CONSTRAINT `foranea2` FOREIGN KEY (`socio`) REFERENCES `socio` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
