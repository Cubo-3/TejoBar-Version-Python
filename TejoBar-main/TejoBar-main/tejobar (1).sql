-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-03-2026 a las 16:36:46
-- Versión del servidor: 12.2.2-MariaDB
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
-- Estructura de tabla para la tabla `auth_group`
--

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_group_permissions`
--

CREATE TABLE `auth_group_permissions` (
  `id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_permission`
--

CREATE TABLE `auth_permission` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  `codename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auth_permission`
--

INSERT INTO `auth_permission` (`id`, `name`, `content_type_id`, `codename`) VALUES
(1, 'Can add log entry', 1, 'add_logentry'),
(2, 'Can change log entry', 1, 'change_logentry'),
(3, 'Can delete log entry', 1, 'delete_logentry'),
(4, 'Can view log entry', 1, 'view_logentry'),
(5, 'Can add permission', 3, 'add_permission'),
(6, 'Can change permission', 3, 'change_permission'),
(7, 'Can delete permission', 3, 'delete_permission'),
(8, 'Can view permission', 3, 'view_permission'),
(9, 'Can add group', 2, 'add_group'),
(10, 'Can change group', 2, 'change_group'),
(11, 'Can delete group', 2, 'delete_group'),
(12, 'Can view group', 2, 'view_group'),
(13, 'Can add user', 4, 'add_user'),
(14, 'Can change user', 4, 'change_user'),
(15, 'Can delete user', 4, 'delete_user'),
(16, 'Can view user', 4, 'view_user'),
(17, 'Can add content type', 5, 'add_contenttype'),
(18, 'Can change content type', 5, 'change_contenttype'),
(19, 'Can delete content type', 5, 'delete_contenttype'),
(20, 'Can view content type', 5, 'view_contenttype'),
(21, 'Can add session', 6, 'add_session'),
(22, 'Can change session', 6, 'change_session'),
(23, 'Can delete session', 6, 'delete_session'),
(24, 'Can view session', 6, 'view_session'),
(25, 'Can add persona', 15, 'add_persona'),
(26, 'Can change persona', 15, 'change_persona'),
(27, 'Can delete persona', 15, 'delete_persona'),
(28, 'Can view persona', 15, 'view_persona'),
(29, 'Can add jugador', 12, 'add_jugador'),
(30, 'Can change jugador', 12, 'change_jugador'),
(31, 'Can delete jugador', 12, 'delete_jugador'),
(32, 'Can view jugador', 12, 'view_jugador'),
(33, 'Can add equipo', 10, 'add_equipo'),
(34, 'Can change equipo', 10, 'change_equipo'),
(35, 'Can delete equipo', 10, 'delete_equipo'),
(36, 'Can view equipo', 10, 'view_equipo'),
(37, 'Can add cancha', 8, 'add_cancha'),
(38, 'Can change cancha', 8, 'change_cancha'),
(39, 'Can delete cancha', 8, 'delete_cancha'),
(40, 'Can view cancha', 8, 'view_cancha'),
(41, 'Can add producto', 16, 'add_producto'),
(42, 'Can change producto', 16, 'change_producto'),
(43, 'Can delete producto', 16, 'delete_producto'),
(44, 'Can view producto', 16, 'view_producto'),
(45, 'Can add apartado', 7, 'add_apartado'),
(46, 'Can change apartado', 7, 'change_apartado'),
(47, 'Can delete apartado', 7, 'delete_apartado'),
(48, 'Can view apartado', 7, 'view_apartado'),
(49, 'Can add historial', 11, 'add_historial'),
(50, 'Can change historial', 11, 'change_historial'),
(51, 'Can delete historial', 11, 'delete_historial'),
(52, 'Can view historial', 11, 'view_historial'),
(53, 'Can add jugador equipo', 13, 'add_jugadorequipo'),
(54, 'Can change jugador equipo', 13, 'change_jugadorequipo'),
(55, 'Can delete jugador equipo', 13, 'delete_jugadorequipo'),
(56, 'Can view jugador equipo', 13, 'view_jugadorequipo'),
(57, 'Can add partido', 14, 'add_partido'),
(58, 'Can change partido', 14, 'change_partido'),
(59, 'Can delete partido', 14, 'delete_partido'),
(60, 'Can view partido', 14, 'view_partido'),
(61, 'Can add torneo', 17, 'add_torneo'),
(62, 'Can change torneo', 17, 'change_torneo'),
(63, 'Can delete torneo', 17, 'delete_torneo'),
(64, 'Can view torneo', 17, 'view_torneo'),
(65, 'Can add compra', 9, 'add_compra'),
(66, 'Can change compra', 9, 'change_compra'),
(67, 'Can delete compra', 9, 'delete_compra'),
(68, 'Can view compra', 9, 'view_compra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_user`
--

CREATE TABLE `auth_user` (
  `id` int(11) NOT NULL,
  `password` varchar(128) NOT NULL,
  `last_login` datetime(6) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `username` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(254) NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `date_joined` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auth_user`
--

INSERT INTO `auth_user` (`id`, `password`, `last_login`, `is_superuser`, `username`, `first_name`, `last_name`, `email`, `is_staff`, `is_active`, `date_joined`) VALUES
(1, 'pbkdf2_sha256$1000000$vnEZbVoQf2dhKqB6R1kmvJ$utfOa8qdLgSVp4Z5xBHe8eL3+vJDxP259fYf9TQba4E=', '2026-03-05 12:15:32.027052', 0, 'pipe@gmail.com', 'felipe', '', 'pipe@gmail.com', 0, 1, '2026-03-05 12:15:17.007786'),
(2, 'pbkdf2_sha256$1000000$vvtThvajrIzfJ3gUuTnAfq$zRj3Yl7Ij+sqSrMHoGQC0EjvR4PuTPPMSrUp1QYoRDg=', '2026-03-05 13:39:57.939992', 0, 'cardozo@gmail.com', 'cardozo', '', 'cardozo@gmail.com', 0, 1, '2026-03-05 13:39:47.342012');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_user_groups`
--

CREATE TABLE `auth_user_groups` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_user_user_permissions`
--

CREATE TABLE `auth_user_user_permissions` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `django_admin_log`
--

CREATE TABLE `django_admin_log` (
  `id` int(11) NOT NULL,
  `action_time` datetime(6) NOT NULL,
  `object_id` longtext DEFAULT NULL,
  `object_repr` varchar(200) NOT NULL,
  `action_flag` smallint(5) UNSIGNED NOT NULL CHECK (`action_flag` >= 0),
  `change_message` longtext NOT NULL,
  `content_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_content_type`
--

CREATE TABLE `django_content_type` (
  `id` int(11) NOT NULL,
  `app_label` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `django_content_type`
--

INSERT INTO `django_content_type` (`id`, `app_label`, `model`) VALUES
(1, 'admin', 'logentry'),
(2, 'auth', 'group'),
(3, 'auth', 'permission'),
(4, 'auth', 'user'),
(5, 'contenttypes', 'contenttype'),
(6, 'sessions', 'session'),
(7, 'tejobar_app', 'apartado'),
(8, 'tejobar_app', 'cancha'),
(9, 'tejobar_app', 'compra'),
(10, 'tejobar_app', 'equipo'),
(11, 'tejobar_app', 'historial'),
(12, 'tejobar_app', 'jugador'),
(13, 'tejobar_app', 'jugadorequipo'),
(14, 'tejobar_app', 'partido'),
(15, 'tejobar_app', 'persona'),
(16, 'tejobar_app', 'producto'),
(17, 'tejobar_app', 'torneo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_migrations`
--

CREATE TABLE `django_migrations` (
  `id` bigint(20) NOT NULL,
  `app` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `applied` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `django_migrations`
--

INSERT INTO `django_migrations` (`id`, `app`, `name`, `applied`) VALUES
(1, 'contenttypes', '0001_initial', '2026-03-05 11:52:12.264280'),
(2, 'auth', '0001_initial', '2026-03-05 11:52:12.467039'),
(3, 'admin', '0001_initial', '2026-03-05 11:52:12.510580'),
(4, 'admin', '0002_logentry_remove_auto_add', '2026-03-05 11:52:12.516990'),
(5, 'admin', '0003_logentry_add_action_flag_choices', '2026-03-05 11:52:12.522388'),
(6, 'contenttypes', '0002_remove_content_type_name', '2026-03-05 11:52:12.564687'),
(7, 'auth', '0002_alter_permission_name_max_length', '2026-03-05 11:52:12.586273'),
(8, 'auth', '0003_alter_user_email_max_length', '2026-03-05 11:52:12.602346'),
(9, 'auth', '0004_alter_user_username_opts', '2026-03-05 11:52:12.611046'),
(10, 'auth', '0005_alter_user_last_login_null', '2026-03-05 11:52:12.633273'),
(11, 'auth', '0006_require_contenttypes_0002', '2026-03-05 11:52:12.634506'),
(12, 'auth', '0007_alter_validators_add_error_messages', '2026-03-05 11:52:12.643595'),
(13, 'auth', '0008_alter_user_username_max_length', '2026-03-05 11:52:12.661299'),
(14, 'auth', '0009_alter_user_last_name_max_length', '2026-03-05 11:52:12.679183'),
(15, 'auth', '0010_alter_group_name_max_length', '2026-03-05 11:52:12.696242'),
(16, 'auth', '0011_update_proxy_permissions', '2026-03-05 11:52:12.710088'),
(17, 'auth', '0012_alter_user_first_name_max_length', '2026-03-05 11:52:12.733219'),
(18, 'sessions', '0001_initial', '2026-03-05 11:52:12.754744'),
(19, 'tejobar_app', '0001_initial', '2026-03-05 12:14:02.182721');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_session`
--

CREATE TABLE `django_session` (
  `session_key` varchar(40) NOT NULL,
  `session_data` longtext NOT NULL,
  `expire_date` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `tejobar_app_apartado`
--

CREATE TABLE `tejobar_app_apartado` (
  `id` bigint(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_apartado` datetime(6) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `persona_id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_cancha`
--

CREATE TABLE `tejobar_app_cancha` (
  `id` bigint(20) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_compra`
--

CREATE TABLE `tejobar_app_compra` (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `total` double NOT NULL,
  `jugador_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_equipo`
--

CREATE TABLE `tejobar_app_equipo` (
  `id` bigint(20) NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_historial`
--

CREATE TABLE `tejobar_app_historial` (
  `id` bigint(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_entrega` datetime(6) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `persona_id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_jugador`
--

CREATE TABLE `tejobar_app_jugador` (
  `persona_id` bigint(20) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `rut` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tejobar_app_jugador`
--

INSERT INTO `tejobar_app_jugador` (`persona_id`, `estado`, `rut`) VALUES
(1, 1, 'RUT1'),
(2, 1, 'RUT2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_jugadorequipo`
--

CREATE TABLE `tejobar_app_jugadorequipo` (
  `id` bigint(20) NOT NULL,
  `es_capitan` tinyint(1) NOT NULL,
  `equipo_id` bigint(20) NOT NULL,
  `jugador_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_partido`
--

CREATE TABLE `tejobar_app_partido` (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(20) NOT NULL,
  `capitan` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `cancha_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_persona`
--

CREATE TABLE `tejobar_app_persona` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(254) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `rol` varchar(10) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tejobar_app_persona`
--

INSERT INTO `tejobar_app_persona` (`id`, `nombre`, `correo`, `numero`, `rol`, `user_id`) VALUES
(1, 'felipe', 'pipe@gmail.com', '3214623965', 'capitan', 1),
(2, 'cardozo', 'cardozo@gmail.com', '2836123213', 'jugador', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_producto`
--

CREATE TABLE `tejobar_app_producto` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` double NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tejobar_app_producto`
--

INSERT INTO `tejobar_app_producto` (`id`, `nombre`, `precio`, `stock`, `fecha_vencimiento`, `imagen`) VALUES
(1, 'Cerveza Artesanal', 9680, 50, '2025-12-31', 'productos/cerveza.jpg'),
(2, 'Salchipapa', 12000, 40, '2025-12-31', 'productos/salchipapa.jpg'),
(3, 'Picada', 25000, 30, '2025-12-31', 'productos/picada.jpg'),
(4, 'Empanadas x5', 7000, 60, '2025-12-31', 'productos/empanadas.png'),
(5, 'Refresco', 2500, 100, '2025-12-31', 'productos/refrescos.jpg'),
(6, 'Aguardiente Antioqueño', 35000, 25, '2025-12-31', 'productos/aguardiente.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_torneo`
--

CREATE TABLE `tejobar_app_torneo` (
  `id` bigint(20) NOT NULL,
  `fecha` datetime(6) NOT NULL,
  `cancha_id` bigint(20) NOT NULL,
  `equipo1_id` bigint(20) NOT NULL,
  `equipo2_id` bigint(20) NOT NULL,
  `partido_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `auth_group`
--
ALTER TABLE `auth_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_group_permissions_group_id_permission_id_0cd325b0_uniq` (`group_id`,`permission_id`),
  ADD KEY `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` (`permission_id`);

--
-- Indices de la tabla `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_permission_content_type_id_codename_01ab375a_uniq` (`content_type_id`,`codename`);

--
-- Indices de la tabla `auth_user`
--
ALTER TABLE `auth_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_user_groups_user_id_group_id_94350c0c_uniq` (`user_id`,`group_id`),
  ADD KEY `auth_user_groups_group_id_97559544_fk_auth_group_id` (`group_id`);

--
-- Indices de la tabla `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_user_user_permissions_user_id_permission_id_14a6b632_uniq` (`user_id`,`permission_id`),
  ADD KEY `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` (`permission_id`);

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
-- Indices de la tabla `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `django_admin_log_content_type_id_c4bce8eb_fk_django_co` (`content_type_id`),
  ADD KEY `django_admin_log_user_id_c564eba6_fk_auth_user_id` (`user_id`);

--
-- Indices de la tabla `django_content_type`
--
ALTER TABLE `django_content_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `django_content_type_app_label_model_76bd3d3b_uniq` (`app_label`,`model`);

--
-- Indices de la tabla `django_migrations`
--
ALTER TABLE `django_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `django_session`
--
ALTER TABLE `django_session`
  ADD PRIMARY KEY (`session_key`),
  ADD KEY `django_session_expire_date_a5c62663` (`expire_date`);

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
-- Indices de la tabla `tejobar_app_apartado`
--
ALTER TABLE `tejobar_app_apartado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_apartado_persona_id_a2febe62_fk_tejobar_a` (`persona_id`),
  ADD KEY `tejobar_app_apartado_producto_id_43ddaabd_fk_tejobar_a` (`producto_id`);

--
-- Indices de la tabla `tejobar_app_cancha`
--
ALTER TABLE `tejobar_app_cancha`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tejobar_app_compra`
--
ALTER TABLE `tejobar_app_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_compra_jugador_id_d8358613_fk_tejobar_a` (`jugador_id`);

--
-- Indices de la tabla `tejobar_app_equipo`
--
ALTER TABLE `tejobar_app_equipo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_equipo` (`nombre_equipo`);

--
-- Indices de la tabla `tejobar_app_historial`
--
ALTER TABLE `tejobar_app_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_historia_persona_id_9c50863f_fk_tejobar_a` (`persona_id`),
  ADD KEY `tejobar_app_historia_producto_id_294da51e_fk_tejobar_a` (`producto_id`);

--
-- Indices de la tabla `tejobar_app_jugador`
--
ALTER TABLE `tejobar_app_jugador`
  ADD PRIMARY KEY (`persona_id`);

--
-- Indices de la tabla `tejobar_app_jugadorequipo`
--
ALTER TABLE `tejobar_app_jugadorequipo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tejobar_app_jugadorequipo_jugador_id_equipo_id_7efc32c1_uniq` (`jugador_id`,`equipo_id`),
  ADD KEY `tejobar_app_jugadore_equipo_id_54bd2712_fk_tejobar_a` (`equipo_id`);

--
-- Indices de la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_partido_cancha_id_6e918378_fk_tejobar_app_cancha_id` (`cancha_id`);

--
-- Indices de la tabla `tejobar_app_persona`
--
ALTER TABLE `tejobar_app_persona`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indices de la tabla `tejobar_app_producto`
--
ALTER TABLE `tejobar_app_producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tejobar_app_torneo`
--
ALTER TABLE `tejobar_app_torneo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partido_id` (`partido_id`),
  ADD KEY `tejobar_app_torneo_cancha_id_c1ee7a63_fk_tejobar_app_cancha_id` (`cancha_id`),
  ADD KEY `tejobar_app_torneo_equipo1_id_ff361566_fk_tejobar_app_equipo_id` (`equipo1_id`),
  ADD KEY `tejobar_app_torneo_equipo2_id_85c85b97_fk_tejobar_app_equipo_id` (`equipo2_id`);

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
-- AUTO_INCREMENT de la tabla `auth_group`
--
ALTER TABLE `auth_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auth_permission`
--
ALTER TABLE `auth_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `auth_user`
--
ALTER TABLE `auth_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `django_admin_log`
--
ALTER TABLE `django_admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `django_content_type`
--
ALTER TABLE `django_content_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `django_migrations`
--
ALTER TABLE `django_migrations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- AUTO_INCREMENT de la tabla `tejobar_app_apartado`
--
ALTER TABLE `tejobar_app_apartado`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_cancha`
--
ALTER TABLE `tejobar_app_cancha`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_compra`
--
ALTER TABLE `tejobar_app_compra`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_equipo`
--
ALTER TABLE `tejobar_app_equipo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_historial`
--
ALTER TABLE `tejobar_app_historial`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_jugadorequipo`
--
ALTER TABLE `tejobar_app_jugadorequipo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_persona`
--
ALTER TABLE `tejobar_app_persona`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_producto`
--
ALTER TABLE `tejobar_app_producto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_torneo`
--
ALTER TABLE `tejobar_app_torneo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

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
-- Filtros para la tabla `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD CONSTRAINT `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  ADD CONSTRAINT `auth_group_permissions_group_id_b120cbf9_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`);

--
-- Filtros para la tabla `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD CONSTRAINT `auth_permission_content_type_id_2f476e4b_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`);

--
-- Filtros para la tabla `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  ADD CONSTRAINT `auth_user_groups_group_id_97559544_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`),
  ADD CONSTRAINT `auth_user_groups_user_id_6a12ed8b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

--
-- Filtros para la tabla `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  ADD CONSTRAINT `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  ADD CONSTRAINT `auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`idJugador`) REFERENCES `jugador` (`idPersona`);

--
-- Filtros para la tabla `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  ADD CONSTRAINT `django_admin_log_user_id_c564eba6_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

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
-- Filtros para la tabla `tejobar_app_apartado`
--
ALTER TABLE `tejobar_app_apartado`
  ADD CONSTRAINT `tejobar_app_apartado_persona_id_a2febe62_fk_tejobar_a` FOREIGN KEY (`persona_id`) REFERENCES `tejobar_app_persona` (`id`),
  ADD CONSTRAINT `tejobar_app_apartado_producto_id_43ddaabd_fk_tejobar_a` FOREIGN KEY (`producto_id`) REFERENCES `tejobar_app_producto` (`id`);

--
-- Filtros para la tabla `tejobar_app_compra`
--
ALTER TABLE `tejobar_app_compra`
  ADD CONSTRAINT `tejobar_app_compra_jugador_id_d8358613_fk_tejobar_a` FOREIGN KEY (`jugador_id`) REFERENCES `tejobar_app_jugador` (`persona_id`);

--
-- Filtros para la tabla `tejobar_app_historial`
--
ALTER TABLE `tejobar_app_historial`
  ADD CONSTRAINT `tejobar_app_historia_persona_id_9c50863f_fk_tejobar_a` FOREIGN KEY (`persona_id`) REFERENCES `tejobar_app_persona` (`id`),
  ADD CONSTRAINT `tejobar_app_historia_producto_id_294da51e_fk_tejobar_a` FOREIGN KEY (`producto_id`) REFERENCES `tejobar_app_producto` (`id`);

--
-- Filtros para la tabla `tejobar_app_jugador`
--
ALTER TABLE `tejobar_app_jugador`
  ADD CONSTRAINT `tejobar_app_jugador_persona_id_73a8c8e8_fk_tejobar_a` FOREIGN KEY (`persona_id`) REFERENCES `tejobar_app_persona` (`id`);

--
-- Filtros para la tabla `tejobar_app_jugadorequipo`
--
ALTER TABLE `tejobar_app_jugadorequipo`
  ADD CONSTRAINT `tejobar_app_jugadore_equipo_id_54bd2712_fk_tejobar_a` FOREIGN KEY (`equipo_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_jugadore_jugador_id_f0e794f1_fk_tejobar_a` FOREIGN KEY (`jugador_id`) REFERENCES `tejobar_app_jugador` (`persona_id`);

--
-- Filtros para la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  ADD CONSTRAINT `tejobar_app_partido_cancha_id_6e918378_fk_tejobar_app_cancha_id` FOREIGN KEY (`cancha_id`) REFERENCES `tejobar_app_cancha` (`id`);

--
-- Filtros para la tabla `tejobar_app_persona`
--
ALTER TABLE `tejobar_app_persona`
  ADD CONSTRAINT `tejobar_app_persona_user_id_20abbeff_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

--
-- Filtros para la tabla `tejobar_app_torneo`
--
ALTER TABLE `tejobar_app_torneo`
  ADD CONSTRAINT `tejobar_app_torneo_cancha_id_c1ee7a63_fk_tejobar_app_cancha_id` FOREIGN KEY (`cancha_id`) REFERENCES `tejobar_app_cancha` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_equipo1_id_ff361566_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo1_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_equipo2_id_85c85b97_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo2_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_partido_id_ca779875_fk_tejobar_app_partido_id` FOREIGN KEY (`partido_id`) REFERENCES `tejobar_app_partido` (`id`);

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
