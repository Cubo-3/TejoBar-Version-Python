-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2025 a las 14:24:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tejobar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apartados`
--

CREATE TABLE `apartados` (
  `idApartado` int(11) NOT NULL,
  `idPersona` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fechaApartado` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','comprado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cancha`
--

CREATE TABLE `cancha` (
  `idCancha` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cancha`
--

INSERT INTO `cancha` (`idCancha`, `estado`, `disponibilidad`) VALUES
(1, 1, 'Disponible'),
(2, 1, 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `idCompra` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `total` double DEFAULT NULL,
  `idJugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`idCompra`, `fecha`, `total`, `idJugador`) VALUES
(1, '2025-07-15', 28000, 101),
(2, '2025-07-16', 37500, 102),
(3, '2025-07-17', 56000, 103);

--
-- Disparadores `compra`
--
DELIMITER $$
CREATE TRIGGER `asignar_fecha_compra` BEFORE INSERT ON `compra` FOR EACH ROW BEGIN
  SET NEW.fecha = CURDATE();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `idEquipo` int(11) NOT NULL,
  `nombreEquipo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`idEquipo`, `nombreEquipo`) VALUES
(1, 'Equipo A'),
(2, 'Equipo B'),
(3, 'Equipo C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `idHistorial` bigint(20) UNSIGNED NOT NULL,
  `idPersona` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fechaEntrega` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` varchar(255) NOT NULL DEFAULT 'entregado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador`
--

CREATE TABLE `jugador` (
  `idPersona` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `rut` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `jugador`
--

INSERT INTO `jugador` (`idPersona`, `estado`, `rut`) VALUES
(101, 1, 'RUT101'),
(102, 1, 'RUT102'),
(103, 1, 'RUT103'),
(105, 1, 'RUT105'),
(106, 1, 'RUT106');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador_equipo`
--

CREATE TABLE `jugador_equipo` (
  `idJugador` int(11) NOT NULL,
  `idEquipo` int(11) NOT NULL,
  `esCapitan` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `jugador_equipo`
--

INSERT INTO `jugador_equipo` (`idJugador`, `idEquipo`, `esCapitan`) VALUES
(101, 1, 1),
(102, 2, 1),
(103, 3, 1),
(105, 2, 0),
(106, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_20_162630_create_historial_table', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido`
--

CREATE TABLE `partido` (
  `idPartido` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(20) NOT NULL,
  `capitan` varchar(100) NOT NULL,
  `cancha` int(11) NOT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `partido`
--

INSERT INTO `partido` (`idPartido`, `fecha`, `hora`, `capitan`, `cancha`, `estado`) VALUES
(2, '2025-07-24', '10:00-12:00 AM', 'Kevin Franco', 2, 'Confirmada'),
(3, '2025-06-25', '08:00-10:00 AM', 'Andres Ibarra', 1, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `idPersona` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contrasena` varchar(100) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `rol` enum('jugador','capitan','admin') NOT NULL DEFAULT 'jugador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`idPersona`, `nombre`, `correo`, `contrasena`, `numero`, `rol`) VALUES
(101, 'Jugador', 'jugador@gmail.com', '1234', '3001234567', 'jugador'),
(102, 'Capitan', 'capitan@gmail.com', '1234', '3001234568', 'capitan'),
(103, 'Andres Ibarra', 'andres@example.com', '1234', '3001234569', 'jugador'),
(104, 'Andres Cardona', 'andres.cardona@example.com', 'admin2025', '3001234570', 'admin'),
(105, 'admin', 'admin@gmail.com', '1234', '3001234567', 'admin'),
(106, 'Felipe Parra', 'pipe343123702@gmail.com', '1234', '3214623965', 'jugador');

--
-- Disparadores `persona`
--
DELIMITER $$
CREATE TRIGGER `correo_unico_persona` BEFORE INSERT ON `persona` FOR EACH ROW BEGIN
  IF EXISTS(SELECT 1 FROM persona WHERE correo = NEW.correo) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo ya existe.';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `fechaVencimiento` date DEFAULT NULL,
  `urlImg` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `nombre`, `precio`, `stock`, `fechaVencimiento`, `urlImg`) VALUES
(1, 'Cerveza Artesanal', 9680, 50, '2025-12-31', 'cerveza.jpg'),
(2, 'Salchipapa', 12000, 40, '2025-12-31', 'salchipapa.jpg'),
(3, 'Picada', 25000, 30, '2025-12-31', 'picada.jpg'),
(4, 'Empanadas x5', 7000, 60, '2025-12-31', 'empanadas.png'),
(5, 'Refresco', 2500, 100, '2025-12-31', 'refrescos.jpg'),
(6, 'Aguardiente Antioqueño', 35000, 25, '2025-12-31', 'aguardiente.jpg');

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `nombre_producto_mayusculas` BEFORE INSERT ON `producto` FOR EACH ROW BEGIN
  SET NEW.nombre = UPPER(NEW.nombre);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_precio_producto` BEFORE INSERT ON `producto` FOR EACH ROW BEGIN
  IF NEW.precio < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El precio no puede ser negativo.';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1M0f5aEY7COptBLAwJXLflDsvHnN7xLiKYtkFNGD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoicHlhWXpmV1RQUXhSanAzZDQ1NGh4ekdkc0dDc25kZ1BGT2t1S1FkUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvcHJvZHVjdG9zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJjYXBpdGFuIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409775),
('2aMQkiGWF4zXvb9PkdK9QQKuOHd8bFQwsZQZynAo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiNklXUUFQOFlKSEhiSXhzRTVNb3hlRjZPdFZ0anRudmlMb2xielhHVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6ImNhcGl0YW4iO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409937),
('8Jm4xaZ9nYBnZSpEVCIZkZ2Im7Ukqp3Fje3kzOaT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiS3JjWkZBd1RDeUZJVVFHU0xWWGd4SGp5bFFPbUh4ekRRc1R6cThlMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvcHJvZHVjdG9zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409645),
('AGICS4Y25UOTe7kKe7MxJjTq6S2IAAVoHCDlpRJp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTozOntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im5ldyI7YTowOnt9czozOiJvbGQiO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiY3kyWVBtNE9HY2pJcUlUOEREdjhFTGs4V3puRkNnWk5PcDBWWDdxVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1758411571),
('Ar4Eq1DLM5cAioZnH6EkjUlFBQ3dRtfRq5LrtMCX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoieGpGdnJlR2VtaHRsRTFqdDIwaUVYUVZESEZYdDRndTAwbzNaMWJyRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jcmVhci1lcXVpcG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6Imp1Z2Fkb3IiO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409730),
('ciS7DtnTnZ5RaEVYz65dU9kIWXkGyVjZDUprqvpQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6IjdnSXVHUWFoUmVmZGlBTkJWdmFOTU5qWHpYSFd0OEVwSkp3OGhPYW0iO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY3JlYXItZXF1aXBvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJjYXBpdGFuIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMztzOjc6InN1Y2Nlc3MiO3M6NzM6IuKchSBFcXVpcG8gImFsY29uZXMgbmVncm9zIiBjcmVhZG8gY29ycmVjdGFtZW50ZS4gQWhvcmEgZXJlcyBlbCBjYXBpdMOhbi4iO30=', 1758409747),
('cyRHfkIaJGUpHUSzYeytoD6bYxxTRBUED0ivWm3f', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoieDd4ZmdwMjQ2dWdIRllLY0tTbjdYclJLYkFqa2t1dEZBZk13SmdoRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6ImlkUGVyc29uYSI7aToxMDY7czo2OiJub21icmUiO3M6MTI6IkZlbGlwZSBQYXJyYSI7czozOiJyb2wiO3M6NzoianVnYWRvciI7czoxMToicmVtZW1iZXJfbWUiO2I6MTtzOjEzOiJsYXN0X2FjdGl2aXR5IjtpOjE3NTg0MDk1MTM7fQ==', 1758409513),
('EIV7wYiOms0J3BOiv7eVWgYgs6GWS3fi4KKMPJjt', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiUldpZG16YW5hYURuRHE3TDlEeFA4S1o5MGxXZHR2dVZ4SEtNMkg4MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvaGlzdG9yaWFsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409675),
('HmzAwAfT4dd4a1cmOmUvWAoEX76NZCOpnmLK6alv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoieEJXRTVSaUNxTjBOQ1pBQlZLQTA2bUYzam56aktsUnF4MFd6SmZLNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvcHJvZHVjdG9zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409677),
('JejmqtWhCupeUdT7HOwjQ2tSGJpR83jqvHdFSpq0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiUE1QNzFpcUpIeFJnc2Q5OGYyOGJZcTl1M1RVZUlUZ2JhckpuMUd4OCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lcXVpcG9zLWRpc3BvbmlibGVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409676),
('k0aA2reioMWOG3uHoXVm6OSa4TCN0dVqY6rY3ZK0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiRVJJU1NkWUpTR2t0RGloaHZlVGJTTTB1bkhtbTVQdG5YWVBxR2JSZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6Imp1Z2Fkb3IiO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409638),
('KzoN0DLtgpiOsaFzc2KCBfh8r44vdU0aYuISp9OQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoieE9iSnIxWGw0TERtR3M0d1NVQ09LNnd1UkZHSkJKT3AyVDlnV1BYdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWN0b3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6Imp1Z2Fkb3IiO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409571),
('lGf6CmEDnToMi1ffcHrrrSOjQDQClEpVpuFZ6HiJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiOU9Ya2x4elJpUGRwVDc3cFQxaFVPT0w2eHBhZ1VYODcySjdSV2ZLSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6ImlkUGVyc29uYSI7aToxMDY7czo2OiJub21icmUiO3M6MTI6IkZlbGlwZSBQYXJyYSI7czozOiJyb2wiO3M6NzoiY2FwaXRhbiI7czoxMToicmVtZW1iZXJfbWUiO2I6MTtzOjEzOiJsYXN0X2FjdGl2aXR5IjtpOjE3NTg0MDk1MTM7fQ==', 1758409768),
('MA6FXjRj8EQtO41dnurcJ02jsOKlt69MGK6Nm5W9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6IkNldUFWVnZFZnZxb0JMNzdIS3g2THc4dVNBSGQ4d3VhODB6RzJLUXYiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZXF1aXBvcy1kaXNwb25pYmxlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6ImlkUGVyc29uYSI7aToxMDY7czo2OiJub21icmUiO3M6MTI6IkZlbGlwZSBQYXJyYSI7czozOiJyb2wiO3M6NzoianVnYWRvciI7czoxMToicmVtZW1iZXJfbWUiO2I6MTtzOjEzOiJsYXN0X2FjdGl2aXR5IjtpOjE3NTg0MDk1MTM7czo3OiJzdWNjZXNzIjtzOjUxOiLinIUgVGUgaGFzIHVuaWRvIGFsIGVxdWlwbyAiRXF1aXBvIEEiIGNvcnJlY3RhbWVudGUiO30=', 1758409949),
('MOJqABsgN3YkkK7m1Thn4C107oYr3H5GZhkMSPbX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiQjVBcktXdDdMYnFBaEY2YWF6MDZTN0g1OHNSRVVnOGFucjNwZW1aeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvaGlzdG9yaWFsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409682),
('p4gHe6THqfX7pr4ccTglfUPhjEuEFmXNdzswk1Yr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoidGd4cFJabUFpYjJsblkwTjZPMmF3S1didkJ3aUVObDY0czNBa1FiSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lcXVpcG9zLWRpc3BvbmlibGVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409943),
('t8wOYKC2KEujoyRCQB0DU0frvj7ouzZSn5CwN6vS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiYUVhdUV3SkVHQUVINVJYTmVoQXI4aVFzR2o4OTVHR0tiaDh0bGlHNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6ImlkUGVyc29uYSI7aToxMDY7czo2OiJub21icmUiO3M6MTI6IkZlbGlwZSBQYXJyYSI7czozOiJyb2wiO3M6NzoiY2FwaXRhbiI7czoxMToicmVtZW1iZXJfbWUiO2I6MTtzOjEzOiJsYXN0X2FjdGl2aXR5IjtpOjE3NTg0MDk1MTM7fQ==', 1758409934),
('tTd6o29H6dpjcpbolWU4fPrmcasddhuZWIhAjSR4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiNUFUMUd4Wml6VkpXM1ZMa3hSWUxEcHFrWVhhZVkwdVFBS3kyUDBDbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6ImlkUGVyc29uYSI7aToxMDY7czo2OiJub21icmUiO3M6MTI6IkZlbGlwZSBQYXJyYSI7czozOiJyb2wiO3M6NzoianVnYWRvciI7czoxMToicmVtZW1iZXJfbWUiO2I6MTtzOjc6InN1Y2Nlc3MiO3M6MjQ6IkJpZW52ZW5pZG8sIEZlbGlwZSBQYXJyYSI7fQ==', 1758409513),
('uQhqI22BcWdCMiFr6xjtfRzc7nw4EhrVkomPgL5t', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6Im5jdzVUVnpYWXBoY3V4V1VCQkRtakZaSEdaN0hrUkJlSFRqYjViTW8iO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMztzOjc6InN1Y2Nlc3MiO3M6NzU6IuKchSBFcXVpcG8gImFsY29uZXMgbmVncm9zIiBlbGltaW5hZG8gY29ycmVjdGFtZW50ZS4gQWhvcmEgZXJlcyB1biBqdWdhZG9yLiI7fQ==', 1758409942),
('uqNUFVsyEuLp3UiyfpyXPQUIlnyIZw26MDyxAuqk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', 'YTozOntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im5ldyI7YTowOnt9czozOiJvbGQiO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiRXNqclNUNlZOTkRlN3ZyWklMYU1YUzA0cjV5aXJLNk9SWTk4WWtXTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1758715273),
('Vnx62ht9xnXM4Je9nTbJR6xbwf7NtbbhAxq0ezzE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiaktiR0lnSGpaV1hGVlF1QnFPWnp0WHZLUTZ4cVRzMHpkSVNxU3VIVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lcXVpcG9zLWRpc3BvbmlibGVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiaWRQZXJzb25hIjtpOjEwNjtzOjY6Im5vbWJyZSI7czoxMjoiRmVsaXBlIFBhcnJhIjtzOjM6InJvbCI7czo3OiJqdWdhZG9yIjtzOjExOiJyZW1lbWJlcl9tZSI7YjoxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc1ODQwOTUxMzt9', 1758409727),
('Wut7QVsz9l12BPQu0j4cIpsa0mdzmraQEHXsz2v3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoid0xOUHJoMUppa09LS1Q2TnFJUEhHNElmd0lVRXJQY1d1Wk9tbnd0MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6ImNhcGl0YW4iO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409771),
('Xs5mUTp83qX1hZsIEHx4QC9aEzMr50yqAxCfF4Ij', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 OPR/121.0.0.0', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoicFlZdmFnYXlyeW5TdWh1NHR5bXVvZXJMVVhpMGdSWEVRNTU2R2dnbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo5OiJpZFBlcnNvbmEiO2k6MTA2O3M6Njoibm9tYnJlIjtzOjEyOiJGZWxpcGUgUGFycmEiO3M6Mzoicm9sIjtzOjc6Imp1Z2Fkb3IiO3M6MTE6InJlbWVtYmVyX21lIjtiOjE7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzU4NDA5NTEzO30=', 1758409598);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneo`
--

CREATE TABLE `torneo` (
  `idPartido` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `equipo1` int(11) DEFAULT NULL,
  `equipo2` int(11) DEFAULT NULL,
  `cancha` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `torneo`
--

INSERT INTO `torneo` (`idPartido`, `fecha`, `equipo1`, `equipo2`, `cancha`) VALUES
(1, '2025-07-24 10:00:00', 1, 2, 1),
(2, '2025-07-24 10:00:00', 2, 3, 2),
(3, '2025-06-25 08:00:00', 3, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `apartados`
--
ALTER TABLE `apartados`
  ADD PRIMARY KEY (`idApartado`),
  ADD KEY `idPersona` (`idPersona`),
  ADD KEY `idProducto` (`idProducto`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cancha`
--
ALTER TABLE `cancha`
  ADD PRIMARY KEY (`idCancha`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idCompra`),
  ADD KEY `idJugador` (`idJugador`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`idEquipo`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`idHistorial`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD PRIMARY KEY (`idPersona`);

--
-- Indices de la tabla `jugador_equipo`
--
ALTER TABLE `jugador_equipo`
  ADD PRIMARY KEY (`idJugador`,`idEquipo`),
  ADD KEY `idEquipo` (`idEquipo`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partido`
--
ALTER TABLE `partido`
  ADD PRIMARY KEY (`idPartido`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`idPersona`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `torneo`
--
ALTER TABLE `torneo`
  ADD PRIMARY KEY (`idPartido`),
  ADD KEY `equipo1` (`equipo1`),
  ADD KEY `equipo2` (`equipo2`),
  ADD KEY `cancha` (`cancha`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `apartados`
--
ALTER TABLE `apartados`
  MODIFY `idApartado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cancha`
--
ALTER TABLE `cancha`
  MODIFY `idCancha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idCompra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `idEquipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `idHistorial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `partido`
--
ALTER TABLE `partido`
  MODIFY `idPartido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `idPersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `torneo`
--
ALTER TABLE `torneo`
  MODIFY `idPartido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `apartados`
--
ALTER TABLE `apartados`
  ADD CONSTRAINT `apartados_ibfk_1` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`) ON DELETE CASCADE,
  ADD CONSTRAINT `apartados_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`idJugador`) REFERENCES `jugador` (`idPersona`);

--
-- Filtros para la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD CONSTRAINT `jugador_ibfk_1` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`idPersona`);

--
-- Filtros para la tabla `jugador_equipo`
--
ALTER TABLE `jugador_equipo`
  ADD CONSTRAINT `jugador_equipo_ibfk_1` FOREIGN KEY (`idJugador`) REFERENCES `jugador` (`idPersona`),
  ADD CONSTRAINT `jugador_equipo_ibfk_2` FOREIGN KEY (`idEquipo`) REFERENCES `equipo` (`idEquipo`);

--
-- Filtros para la tabla `torneo`
--
ALTER TABLE `torneo`
  ADD CONSTRAINT `torneo_ibfk_1` FOREIGN KEY (`equipo1`) REFERENCES `equipo` (`idEquipo`),
  ADD CONSTRAINT `torneo_ibfk_2` FOREIGN KEY (`equipo2`) REFERENCES `equipo` (`idEquipo`),
  ADD CONSTRAINT `torneo_ibfk_3` FOREIGN KEY (`cancha`) REFERENCES `cancha` (`idCancha`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
