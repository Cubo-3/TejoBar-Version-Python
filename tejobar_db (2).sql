-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2026 a las 04:43:41
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
-- Base de datos: `tejobar_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_group`
--

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_group_permissions`
--

CREATE TABLE `auth_group_permissions` (
  `id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_permission`
--

CREATE TABLE `auth_permission` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  `codename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auth_permission`
--

INSERT INTO `auth_permission` (`id`, `name`, `content_type_id`, `codename`) VALUES
(1, 'Can add log entry', 1, 'add_logentry'),
(2, 'Can change log entry', 1, 'change_logentry'),
(3, 'Can delete log entry', 1, 'delete_logentry'),
(4, 'Can view log entry', 1, 'view_logentry'),
(5, 'Can add permission', 2, 'add_permission'),
(6, 'Can change permission', 2, 'change_permission'),
(7, 'Can delete permission', 2, 'delete_permission'),
(8, 'Can view permission', 2, 'view_permission'),
(9, 'Can add group', 3, 'add_group'),
(10, 'Can change group', 3, 'change_group'),
(11, 'Can delete group', 3, 'delete_group'),
(12, 'Can view group', 3, 'view_group'),
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
(25, 'Can add cancha', 7, 'add_cancha'),
(26, 'Can change cancha', 7, 'change_cancha'),
(27, 'Can delete cancha', 7, 'delete_cancha'),
(28, 'Can view cancha', 7, 'view_cancha'),
(29, 'Can add equipo', 8, 'add_equipo'),
(30, 'Can change equipo', 8, 'change_equipo'),
(31, 'Can delete equipo', 8, 'delete_equipo'),
(32, 'Can view equipo', 8, 'view_equipo'),
(33, 'Can add persona', 9, 'add_persona'),
(34, 'Can change persona', 9, 'change_persona'),
(35, 'Can delete persona', 9, 'delete_persona'),
(36, 'Can view persona', 9, 'view_persona'),
(37, 'Can add producto', 10, 'add_producto'),
(38, 'Can change producto', 10, 'change_producto'),
(39, 'Can delete producto', 10, 'delete_producto'),
(40, 'Can view producto', 10, 'view_producto'),
(41, 'Can add jugador', 11, 'add_jugador'),
(42, 'Can change jugador', 11, 'change_jugador'),
(43, 'Can delete jugador', 11, 'delete_jugador'),
(44, 'Can view jugador', 11, 'view_jugador'),
(45, 'Can add partido', 12, 'add_partido'),
(46, 'Can change partido', 12, 'change_partido'),
(47, 'Can delete partido', 12, 'delete_partido'),
(48, 'Can view partido', 12, 'view_partido'),
(49, 'Can add historial', 13, 'add_historial'),
(50, 'Can change historial', 13, 'change_historial'),
(51, 'Can delete historial', 13, 'delete_historial'),
(52, 'Can view historial', 13, 'view_historial'),
(53, 'Can add apartado', 14, 'add_apartado'),
(54, 'Can change apartado', 14, 'change_apartado'),
(55, 'Can delete apartado', 14, 'delete_apartado'),
(56, 'Can view apartado', 14, 'view_apartado'),
(57, 'Can add torneo', 15, 'add_torneo'),
(58, 'Can change torneo', 15, 'change_torneo'),
(59, 'Can delete torneo', 15, 'delete_torneo'),
(60, 'Can view torneo', 15, 'view_torneo'),
(61, 'Can add compra', 16, 'add_compra'),
(62, 'Can change compra', 16, 'change_compra'),
(63, 'Can delete compra', 16, 'delete_compra'),
(64, 'Can view compra', 16, 'view_compra'),
(65, 'Can add jugador equipo', 17, 'add_jugadorequipo'),
(66, 'Can change jugador equipo', 17, 'change_jugadorequipo'),
(67, 'Can delete jugador equipo', 17, 'delete_jugadorequipo'),
(68, 'Can view jugador equipo', 17, 'view_jugadorequipo'),
(69, 'Can add novedad', 18, 'add_novedad'),
(70, 'Can change novedad', 18, 'change_novedad'),
(71, 'Can delete novedad', 18, 'delete_novedad'),
(72, 'Can view novedad', 18, 'view_novedad'),
(73, 'Can add categoria', 19, 'add_categoria'),
(74, 'Can change categoria', 19, 'change_categoria'),
(75, 'Can delete categoria', 19, 'delete_categoria'),
(76, 'Can view categoria', 19, 'view_categoria'),
(77, 'Can add historial equipo', 20, 'add_historialequipo'),
(78, 'Can change historial equipo', 20, 'change_historialequipo'),
(79, 'Can delete historial equipo', 20, 'delete_historialequipo'),
(80, 'Can view historial equipo', 20, 'view_historialequipo');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auth_user`
--

INSERT INTO `auth_user` (`id`, `password`, `last_login`, `is_superuser`, `username`, `first_name`, `last_name`, `email`, `is_staff`, `is_active`, `date_joined`) VALUES
(1, 'pbkdf2_sha256$600000$xJOJU3KiqopVUyHTxsubzZ$K+9xx2jKzBWelD1XxgpjWJ1VRMEQsi8qfXJF8bjKlAI=', '2026-03-26 03:29:18.730497', 1, 'admin', '', '', 'admin@gmail.com', 1, 1, '2026-03-19 11:35:24.594818'),
(2, 'pbkdf2_sha256$600000$v3D0U7XYK7l7l5ml62Feh8$n3t3hb58umcuaxsPwnp6enaUOvAVjYbhanKUynBNSHQ=', '2026-03-26 03:08:48.572282', 0, 'jugador@gmail.com', 'jugador', '', 'jugador@gmail.com', 0, 1, '2026-03-19 11:39:09.075462'),
(3, 'pbkdf2_sha256$600000$nSgBjLiZMzNkzahAJY0CgE$49FrLovbnSraFO1QTRnK6+H65vDJ9phHJHaV4P/sd3s=', NULL, 0, 'jugador1@gmail.com', 'jugador 1', '', 'jugador1@gmail.com', 0, 1, '2026-03-19 11:44:44.650730'),
(5, 'pbkdf2_sha256$600000$NPBLc0o7LKoe9nH8JsAi2I$87eEMj8gWyc5xJNPlXC71J/POeLBBG+c0r9Q9FBLkIU=', '2026-03-19 12:17:48.204251', 0, 'jugador4@gmail.com', 'jugador 4', '', 'jugador4@gmail.com', 0, 1, '2026-03-19 12:01:47.639982'),
(6, 'pbkdf2_sha256$600000$aSCskTXmsdDC4SjGcHaoJi$xZbtJLq1fTGdVmLThdm1O0+RIZJQWS8IwrHKHT9NNDw=', '2026-03-26 00:26:32.250182', 0, 'ande@gmail.com', 'Kevin', '', 'ande@gmail.com', 0, 1, '2026-03-26 00:26:24.878720');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_user_groups`
--

CREATE TABLE `auth_user_groups` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auth_user_user_permissions`
--

CREATE TABLE `auth_user_user_permissions` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_content_type`
--

CREATE TABLE `django_content_type` (
  `id` int(11) NOT NULL,
  `app_label` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `django_content_type`
--

INSERT INTO `django_content_type` (`id`, `app_label`, `model`) VALUES
(1, 'admin', 'logentry'),
(3, 'auth', 'group'),
(2, 'auth', 'permission'),
(4, 'auth', 'user'),
(5, 'contenttypes', 'contenttype'),
(6, 'sessions', 'session'),
(14, 'tejobar_app', 'apartado'),
(7, 'tejobar_app', 'cancha'),
(19, 'tejobar_app', 'categoria'),
(16, 'tejobar_app', 'compra'),
(8, 'tejobar_app', 'equipo'),
(13, 'tejobar_app', 'historial'),
(20, 'tejobar_app', 'historialequipo'),
(11, 'tejobar_app', 'jugador'),
(17, 'tejobar_app', 'jugadorequipo'),
(18, 'tejobar_app', 'novedad'),
(12, 'tejobar_app', 'partido'),
(9, 'tejobar_app', 'persona'),
(10, 'tejobar_app', 'producto'),
(15, 'tejobar_app', 'torneo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_migrations`
--

CREATE TABLE `django_migrations` (
  `id` bigint(20) NOT NULL,
  `app` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `applied` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `django_migrations`
--

INSERT INTO `django_migrations` (`id`, `app`, `name`, `applied`) VALUES
(1, 'contenttypes', '0001_initial', '2026-03-19 11:32:47.015714'),
(2, 'auth', '0001_initial', '2026-03-19 11:32:47.281568'),
(3, 'admin', '0001_initial', '2026-03-19 11:32:47.358956'),
(4, 'admin', '0002_logentry_remove_auto_add', '2026-03-19 11:32:47.364411'),
(5, 'admin', '0003_logentry_add_action_flag_choices', '2026-03-19 11:32:47.370571'),
(6, 'contenttypes', '0002_remove_content_type_name', '2026-03-19 11:32:47.415423'),
(7, 'auth', '0002_alter_permission_name_max_length', '2026-03-19 11:32:47.446924'),
(8, 'auth', '0003_alter_user_email_max_length', '2026-03-19 11:32:47.456726'),
(9, 'auth', '0004_alter_user_username_opts', '2026-03-19 11:32:47.462267'),
(10, 'auth', '0005_alter_user_last_login_null', '2026-03-19 11:32:47.494407'),
(11, 'auth', '0006_require_contenttypes_0002', '2026-03-19 11:32:47.496283'),
(12, 'auth', '0007_alter_validators_add_error_messages', '2026-03-19 11:32:47.501912'),
(13, 'auth', '0008_alter_user_username_max_length', '2026-03-19 11:32:47.516559'),
(14, 'auth', '0009_alter_user_last_name_max_length', '2026-03-19 11:32:47.525449'),
(15, 'auth', '0010_alter_group_name_max_length', '2026-03-19 11:32:47.535920'),
(16, 'auth', '0011_update_proxy_permissions', '2026-03-19 11:32:47.542069'),
(17, 'auth', '0012_alter_user_first_name_max_length', '2026-03-19 11:32:47.550546'),
(18, 'sessions', '0001_initial', '2026-03-19 11:32:47.570204'),
(19, 'tejobar_app', '0001_initial', '2026-03-19 11:32:48.091254'),
(20, 'tejobar_app', '0002_novedad', '2026-03-19 11:32:48.135275'),
(21, 'tejobar_app', '0003_partido_equipo1_partido_equipo2', '2026-03-19 11:32:48.212018'),
(22, 'tejobar_app', '0004_remove_partido_capitan', '2026-03-19 11:32:48.220044'),
(23, 'tejobar_app', '0005_cancha_precio_por_hora_partido_hora_fin_and_more', '2026-03-19 11:32:48.249515'),
(24, 'tejobar_app', '0006_partido_unique_cancha_horario', '2026-03-19 11:32:48.259212'),
(25, 'tejobar_app', '0007_remove_partido_unique_cancha_horario_and_more', '2026-03-19 11:32:48.274828'),
(26, 'tejobar_app', '0008_categoria', '2026-03-19 11:32:48.294918'),
(27, 'tejobar_app', '0009_producto_categoria', '2026-03-19 11:32:48.349580'),
(28, 'tejobar_app', '0010_alter_jugadorequipo_unique_together_and_more', '2026-03-19 11:32:48.726402'),
(29, 'tejobar_app', '0011_alter_novedad_cantidad_alter_novedad_producto_and_more', '2026-03-19 11:32:48.830208'),
(30, 'tejobar_app', '0012_alter_cancha_precio_por_hora', '2026-03-19 11:32:48.834199'),
(31, 'tejobar_app', '0013_alter_apartado_estado_alter_novedad_producto_and_more', '2026-03-26 00:07:29.039958'),
(32, 'tejobar_app', '0014_apartado_cliente_nombre_apartado_cliente_telefono_and_more', '2026-03-26 00:44:05.410111'),
(33, 'tejobar_app', '0015_alter_producto_precio_alter_producto_stock', '2026-03-26 03:07:20.669252'),
(34, 'tejobar_app', '0016_historialequipo', '2026-03-26 03:15:46.468839'),
(35, 'tejobar_app', '0017_producto_descripcion', '2026-03-26 03:38:50.362335');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `django_session`
--

CREATE TABLE `django_session` (
  `session_key` varchar(40) NOT NULL,
  `session_data` longtext NOT NULL,
  `expire_date` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `django_session`
--

INSERT INTO `django_session` (`session_key`, `session_data`, `expire_date`) VALUES
('73zrleqw7s58qm8q5du58fv8kqou3b44', '.eJxVjMsOwiAQRf-FtSEMDyku3fcbyMAMUjU0Ke3K-O_apAvd3nPOfYmI21rj1nmJE4mLAHH63RLmB7cd0B3bbZZ5busyJbkr8qBdjjPx83q4fwcVe_3WyjCgw1CyV8X4nApYTYHAG8uWAVgXQkoDOBiKISoqwzlgCE57JCveH_bFOFs:1w5bPK:feMvI4nP4uUzezjl596tqKiVmJAZk46NKMU8V6wQLbE', '2026-04-09 03:29:18.788741'),
('dcyk7gy4582htnm5zkwxfohlvja2s1b8', '.eJxVzMsOgjAQheF3mbVpOkwvwNK9z0Cm7dSiBhIKK-O7myYsdHv-L-cNEx97mY4q2zQnGAHh8rsFjk9ZWkgPXu6riuuyb3NQjaizVnVbk7yup_07KFwLjOCYsjYdOZ09GjI26uyHTJYsJzPYSBxQxGbsMPdGUo9OMDpJnpqGzxfFtDdu:1w3CKS:BjfavTgPb6nP5ikaO9LjmjLQlBx4Jlj9I4m47IhVneg', '2026-04-02 12:18:20.856781'),
('eksafxo6bx79hrbiaekkk6rtr3y4oppi', '.eJxVjDsOwyAQBe9CHSG-BlKm9xnQsgvBSYQlY1dR7h5bcpG0b2bem0XY1hq3npc4EbsyyS6_WwJ85nYAekC7zxznti5T4ofCT9r5OFN-3U7376BCr3stvLcQTAYrVaCgsSQlSA8ZnbRKag9eE4oyWIU6STDZFOvMDopL5D37fAHQvjd2:1w4zoi:t6yZ8_toRp4itZxBGr5_GitgnE0R4e7f3aWjBvd_ej0', '2026-04-07 11:21:00.320080'),
('v7z3wscitnxbqjyxnke8jx4bsutmsv50', '.eJxVjM0OwiAQhN-FsyEsPyt49O4zkAU2UjWQlPZkfHfbpAc9TTLfN_MWkdalxnXwHKciLkKL02-XKD-57aA8qN27zL0t85TkrsiDDnnrhV_Xw_07qDTqtvZnB-xLURisYsyFSbGz6LO2CdGQ1oAuOwC1BRoMwbMBDgmMc0Di8wXG3Ta0:1w4zri:M9QgI2CR6L30MBaW9g45zeRiAfY9TRTINg50pRJXRNU', '2026-04-07 11:24:06.347074'),
('we1mv0vvtu1hbmy0ujpr2xrtide12ppf', '.eJxVjMsOwiAQRf-FtSEMDyku3fcbyMAMUjU0Ke3K-O_apAvd3nPOfYmI21rj1nmJE4mLAHH63RLmB7cd0B3bbZZ5busyJbkr8qBdjjPx83q4fwcVe_3WyjCgw1CyV8X4nApYTYHAG8uWAVgXQkoDOBiKISoqwzlgCE57JCveH_bFOFs:1w5Yb7:3Ld6Z85zfR4eV7PLll0dExjwBp3M_JXI9Lr5hXXSg3s', '2026-04-09 00:29:17.277934');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_apartado`
--

CREATE TABLE `tejobar_app_apartado` (
  `id` bigint(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_apartado` datetime(6) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `persona_id` bigint(20) DEFAULT NULL,
  `producto_id` bigint(20) NOT NULL,
  `cliente_nombre` varchar(150) DEFAULT NULL,
  `cliente_telefono` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_apartado`
--

INSERT INTO `tejobar_app_apartado` (`id`, `cantidad`, `fecha_apartado`, `estado`, `persona_id`, `producto_id`, `cliente_nombre`, `cliente_telefono`) VALUES
(2, 6, '2026-03-24 11:25:36.826269', 'comprado', 2, 2, NULL, NULL),
(3, 5, '2026-03-24 11:32:20.626100', 'comprado', 2, 2, NULL, NULL),
(4, 1, '2026-03-25 23:51:02.617209', 'cancelado', 1, 2, NULL, NULL),
(5, 1, '2026-03-26 00:47:38.587262', 'comprado', NULL, 4, NULL, NULL),
(6, 1, '2026-03-26 02:18:05.075629', 'comprado', NULL, 4, 'juan', NULL),
(7, 10, '2026-03-26 02:18:05.086972', 'comprado', NULL, 2, 'juan', NULL),
(8, 1, '2026-03-26 02:21:45.525597', 'comprado', NULL, 4, 'juan', NULL),
(9, 10, '2026-03-26 02:21:45.771802', 'comprado', NULL, 2, 'juan', NULL),
(10, 10, '2026-03-26 02:22:42.619749', 'comprado', NULL, 4, 'juan', NULL),
(11, 10, '2026-03-26 02:22:42.622115', 'comprado', NULL, 2, 'juan', NULL),
(12, 1, '2026-03-26 02:22:50.629079', 'comprado', NULL, 4, NULL, NULL),
(13, 1, '2026-03-26 03:09:07.753587', 'cancelado', 2, 4, NULL, NULL),
(14, 1, '2026-03-26 03:09:13.717734', 'comprado', 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_cancha`
--

CREATE TABLE `tejobar_app_cancha` (
  `id` bigint(20) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL,
  `precio_por_hora` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_cancha`
--

INSERT INTO `tejobar_app_cancha` (`id`, `estado`, `disponibilidad`, `precio_por_hora`) VALUES
(1, 1, 'Cancha grande', 8000),
(2, 1, 'cancha pequeña', 8000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_categoria`
--

CREATE TABLE `tejobar_app_categoria` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` longtext DEFAULT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_categoria`
--

INSERT INTO `tejobar_app_categoria` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Alcohol', 'bebidad alcoholicas distribuidas por bavaria', 1),
(2, 'Papas', '', 1),
(9, 'Bebidas', 'Categoría autogenerada por carga masiva', 1),
(10, 'Licores', 'Categoría autogenerada por carga masiva', 1),
(11, 'Comidas', 'Categoría autogenerada por carga masiva', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_compra`
--

CREATE TABLE `tejobar_app_compra` (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `total` double NOT NULL,
  `jugador_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_equipo`
--

CREATE TABLE `tejobar_app_equipo` (
  `id` bigint(20) NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_equipo`
--

INSERT INTO `tejobar_app_equipo` (`id`, `nombre_equipo`) VALUES
(2, 'Equipo prueba');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_historial`
--

INSERT INTO `tejobar_app_historial` (`id`, `cantidad`, `precio`, `total`, `fecha_entrega`, `estado`, `created_at`, `updated_at`, `persona_id`, `producto_id`) VALUES
(1, 6, 3500.00, 21000.00, '2026-03-24 11:26:53.089132', 'entregado', '2026-03-24 11:26:53.089154', '2026-03-24 11:26:57.773445', 2, 2),
(2, 5, 3500.00, 17500.00, '2026-03-24 11:32:30.419899', 'entregado', '2026-03-24 11:32:30.419918', '2026-03-24 11:32:39.889292', 2, 2),
(3, 1, 10000000.00, 10000000.00, '2026-03-26 03:31:52.105043', 'por_entregar', '2026-03-26 03:31:52.105067', '2026-03-26 03:31:52.105073', 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_historialequipo`
--

CREATE TABLE `tejobar_app_historialequipo` (
  `id` bigint(20) NOT NULL,
  `fecha_ingreso` datetime(6) NOT NULL,
  `fecha_salida` datetime(6) DEFAULT NULL,
  `fue_capitan` tinyint(1) NOT NULL,
  `equipo_id` bigint(20) NOT NULL,
  `jugador_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tejobar_app_historialequipo`
--

INSERT INTO `tejobar_app_historialequipo` (`id`, `fecha_ingreso`, `fecha_salida`, `fue_capitan`, `equipo_id`, `jugador_id`) VALUES
(1, '2026-03-26 03:22:38.184328', '2026-03-26 03:22:43.724347', 0, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_jugador`
--

CREATE TABLE `tejobar_app_jugador` (
  `persona_id` bigint(20) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `rut` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_jugador`
--

INSERT INTO `tejobar_app_jugador` (`persona_id`, `estado`, `rut`) VALUES
(2, 1, 'RUT2'),
(3, 1, 'RUT3'),
(6, 1, 'RUT6'),
(7, 1, 'RUT7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_jugadorequipo`
--

CREATE TABLE `tejobar_app_jugadorequipo` (
  `id` bigint(20) NOT NULL,
  `es_capitan` tinyint(1) NOT NULL,
  `equipo_id` bigint(20) NOT NULL,
  `jugador_id` bigint(20) DEFAULT NULL,
  `correo_invitado` varchar(254) DEFAULT NULL,
  `nombre_invitado` varchar(100) DEFAULT NULL,
  `telefono_invitado` varchar(20) DEFAULT NULL,
  `tipo_usuario` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_jugadorequipo`
--

INSERT INTO `tejobar_app_jugadorequipo` (`id`, `es_capitan`, `equipo_id`, `jugador_id`, `correo_invitado`, `nombre_invitado`, `telefono_invitado`, `tipo_usuario`) VALUES
(2, 1, 2, 6, NULL, NULL, NULL, 'registrado'),
(3, 0, 2, 3, NULL, NULL, NULL, 'registrado'),
(6, 0, 2, NULL, NULL, 'felipe', NULL, 'invitado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_novedad`
--

CREATE TABLE `tejobar_app_novedad` (
  `id` bigint(20) NOT NULL,
  `tipo_novedad` varchar(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` datetime(6) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `producto_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_novedad`
--

INSERT INTO `tejobar_app_novedad` (`id`, `tipo_novedad`, `cantidad`, `fecha`, `descripcion`, `producto_id`) VALUES
(1, 'agregado', 50, '2026-03-19 11:38:36.679068', 'Nuevo producto o lote agregado', NULL),
(2, 'vendido', 2, '2026-03-19 11:39:59.123872', 'Separado/Vendido por sistema', NULL),
(3, 'vencido', 48, '2026-03-24 11:17:25.446814', 'Stock caducado automáticamente', NULL),
(4, 'agregado', 2, '2026-03-24 11:17:25.457648', 'Stock devuelto: Carrito abandonado.', NULL),
(5, 'vencido', 2, '2026-03-24 11:20:03.830775', 'Stock caducado automáticamente', NULL),
(6, 'agregado', 90, '2026-03-24 11:25:18.289449', 'Nuevo producto o lote agregado', 2),
(7, 'vendido', 6, '2026-03-24 11:25:36.828102', 'Separado/Vendido por sistema', 2),
(8, 'vendido', 6, '2026-03-24 11:26:53.090444', 'Pago en efectivo procesado por Admin: admin', 2),
(9, 'vendido', 5, '2026-03-24 11:32:20.627150', 'Separado/Vendido por sistema', 2),
(10, 'vendido', 5, '2026-03-24 11:32:30.421495', 'Pago en efectivo procesado por Admin: admin', 2),
(11, 'vendido', 1, '2026-03-25 23:51:02.619634', 'Separado/Vendido por sistema', 2),
(12, 'agregado', 1, '2026-03-25 23:51:23.127003', 'Cancelación administrativa y devolución de stock. Admin: admin', 2),
(13, 'agregado', 12, '2026-03-26 00:11:06.997140', 'Nuevo producto o lote agregado', NULL),
(14, 'perdida', 12, '2026-03-26 00:11:16.606868', 'Producto eliminado con stock restante: 1214224', NULL),
(15, 'agregado', 23, '2026-03-26 00:12:09.035385', 'Nuevo producto o lote agregado', 4),
(16, 'vendido', 1, '2026-03-26 00:47:38.585711', 'Venta física a cliente: Anónimo', 4),
(17, 'vendido', 1, '2026-03-26 02:18:05.056252', 'Venta física (POS): juan', 4),
(18, 'vendido', 10, '2026-03-26 02:18:05.086295', 'Venta física (POS): juan', 2),
(19, 'vendido', 1, '2026-03-26 02:21:45.524970', 'Venta física (POS): juan', 4),
(20, 'vendido', 10, '2026-03-26 02:21:45.762391', 'Venta física (POS): juan', 2),
(21, 'vendido', 10, '2026-03-26 02:22:42.618730', 'Venta física (POS): juan', 4),
(22, 'vendido', 10, '2026-03-26 02:22:42.621820', 'Venta física (POS): juan', 2),
(23, 'vendido', 1, '2026-03-26 02:22:50.628365', 'Venta física (POS): Anónimo', 4),
(24, 'agregado', 10, '2026-03-26 02:36:23.841516', 'Nuevo producto o lote agregado', 5),
(25, 'vendido', 1, '2026-03-26 03:09:07.786516', 'Separado/Vendido por sistema', 4),
(26, 'vendido', 1, '2026-03-26 03:09:13.753525', 'Separado/Vendido por sistema', 2),
(27, 'vendido', 1, '2026-03-26 03:31:52.125285', 'Pago en efectivo procesado por Admin: admin', 2),
(28, 'agregado', 1, '2026-03-26 03:31:59.099718', 'Cancelación administrativa y devolución de stock. Admin: admin', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_partido`
--

CREATE TABLE `tejobar_app_partido` (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `cancha_id` bigint(20) NOT NULL,
  `equipo1_id` bigint(20) DEFAULT NULL,
  `equipo2_id` bigint(20) DEFAULT NULL,
  `hora_fin` datetime(6) DEFAULT NULL,
  `hora_inicio` datetime(6) DEFAULT NULL,
  `pago_cancha` tinyint(1) NOT NULL,
  `hora_reserva_fin` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_persona`
--

INSERT INTO `tejobar_app_persona` (`id`, `nombre`, `correo`, `numero`, `rol`, `user_id`) VALUES
(1, 'Admin', 'admin@gmail.com', '000', 'admin', 1),
(2, 'jugador', 'jugador@gmail.com', 'jugador@gmail.com', 'jugador', 2),
(3, 'jugador 1', 'jugador1@gmail.com', '123456789', 'jugador', 3),
(4, 'jugador 2', 'jugador2@gmail.com', '1234567890', 'jugador', NULL),
(6, 'jugador 4', 'jugador4@gmail.com', '1234567892', 'capitan', 5),
(7, 'Kevin', 'ande@gmail.com', 'ande@gmail.com', 'jugador', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tejobar_app_producto`
--

CREATE TABLE `tejobar_app_producto` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `categoria_id` bigint(20) DEFAULT NULL,
  `descripcion` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tejobar_app_producto`
--

INSERT INTO `tejobar_app_producto` (`id`, `nombre`, `precio`, `stock`, `fecha_vencimiento`, `imagen`, `categoria_id`, `descripcion`) VALUES
(2, 'poker', 10000000.00, 48, '2026-04-17', 'productos/aguardiente_JDdzqH9.jpg', 1, NULL),
(4, 'Cerveza Caliente', 12.00, 9, '3000-02-22', '', 1, NULL),
(5, 'picada', 10000.00, 10, NULL, 'productos/picada_HaN8dJ0.jpg', NULL, NULL),
(6, 'Cerveza Aguila', 4500.00, 120, NULL, '', 9, 'Cerveza fria 330ml'),
(7, 'Cerveza Poker', 4300.00, 100, NULL, '', 9, 'Cerveza tradicional 330ml'),
(8, 'Aguardiente Antioqueño', 65000.00, 25, NULL, '', 10, 'Botella 750ml'),
(9, 'Gaseosa Coca-Cola', 3500.00, 80, NULL, '', 9, 'Gaseosa 400ml'),
(10, 'Picada Mixta', 28000.00, 15, NULL, '', 11, 'Picada para 2 personas');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `tejobar_app_categoria`
--
ALTER TABLE `tejobar_app_categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

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
-- Indices de la tabla `tejobar_app_historialequipo`
--
ALTER TABLE `tejobar_app_historialequipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_historia_equipo_id_165742c1_fk_tejobar_a` (`equipo_id`),
  ADD KEY `tejobar_app_historia_jugador_id_f8cd1e3a_fk_tejobar_a` (`jugador_id`);

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
  ADD KEY `tejobar_app_jugadore_equipo_id_54bd2712_fk_tejobar_a` (`equipo_id`),
  ADD KEY `tejobar_app_jugadorequipo_jugador_id_f0e794f1` (`jugador_id`);

--
-- Indices de la tabla `tejobar_app_novedad`
--
ALTER TABLE `tejobar_app_novedad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_novedad_producto_id_ee81f2e6_fk_tejobar_a` (`producto_id`);

--
-- Indices de la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_partido_cancha_id_6e918378_fk_tejobar_app_cancha_id` (`cancha_id`),
  ADD KEY `tejobar_app_partido_equipo1_id_453ef3b4_fk_tejobar_app_equipo_id` (`equipo1_id`),
  ADD KEY `tejobar_app_partido_equipo2_id_8942cc6d_fk_tejobar_app_equipo_id` (`equipo2_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `tejobar_app_producto_categoria_id_4fa5dd79_fk_tejobar_a` (`categoria_id`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `auth_user`
--
ALTER TABLE `auth_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT de la tabla `django_admin_log`
--
ALTER TABLE `django_admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `django_content_type`
--
ALTER TABLE `django_content_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `django_migrations`
--
ALTER TABLE `django_migrations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_apartado`
--
ALTER TABLE `tejobar_app_apartado`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_cancha`
--
ALTER TABLE `tejobar_app_cancha`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_categoria`
--
ALTER TABLE `tejobar_app_categoria`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_compra`
--
ALTER TABLE `tejobar_app_compra`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_equipo`
--
ALTER TABLE `tejobar_app_equipo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_historial`
--
ALTER TABLE `tejobar_app_historial`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_historialequipo`
--
ALTER TABLE `tejobar_app_historialequipo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_jugadorequipo`
--
ALTER TABLE `tejobar_app_jugadorequipo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_novedad`
--
ALTER TABLE `tejobar_app_novedad`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_persona`
--
ALTER TABLE `tejobar_app_persona`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_producto`
--
ALTER TABLE `tejobar_app_producto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tejobar_app_torneo`
--
ALTER TABLE `tejobar_app_torneo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  ADD CONSTRAINT `django_admin_log_user_id_c564eba6_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

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
-- Filtros para la tabla `tejobar_app_historialequipo`
--
ALTER TABLE `tejobar_app_historialequipo`
  ADD CONSTRAINT `tejobar_app_historia_equipo_id_165742c1_fk_tejobar_a` FOREIGN KEY (`equipo_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_historia_jugador_id_f8cd1e3a_fk_tejobar_a` FOREIGN KEY (`jugador_id`) REFERENCES `tejobar_app_jugador` (`persona_id`);

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
-- Filtros para la tabla `tejobar_app_novedad`
--
ALTER TABLE `tejobar_app_novedad`
  ADD CONSTRAINT `tejobar_app_novedad_producto_id_ee81f2e6_fk_tejobar_a` FOREIGN KEY (`producto_id`) REFERENCES `tejobar_app_producto` (`id`);

--
-- Filtros para la tabla `tejobar_app_partido`
--
ALTER TABLE `tejobar_app_partido`
  ADD CONSTRAINT `tejobar_app_partido_cancha_id_6e918378_fk_tejobar_app_cancha_id` FOREIGN KEY (`cancha_id`) REFERENCES `tejobar_app_cancha` (`id`),
  ADD CONSTRAINT `tejobar_app_partido_equipo1_id_453ef3b4_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo1_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_partido_equipo2_id_8942cc6d_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo2_id`) REFERENCES `tejobar_app_equipo` (`id`);

--
-- Filtros para la tabla `tejobar_app_persona`
--
ALTER TABLE `tejobar_app_persona`
  ADD CONSTRAINT `tejobar_app_persona_user_id_20abbeff_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

--
-- Filtros para la tabla `tejobar_app_producto`
--
ALTER TABLE `tejobar_app_producto`
  ADD CONSTRAINT `tejobar_app_producto_categoria_id_4fa5dd79_fk_tejobar_a` FOREIGN KEY (`categoria_id`) REFERENCES `tejobar_app_categoria` (`id`);

--
-- Filtros para la tabla `tejobar_app_torneo`
--
ALTER TABLE `tejobar_app_torneo`
  ADD CONSTRAINT `tejobar_app_torneo_cancha_id_c1ee7a63_fk_tejobar_app_cancha_id` FOREIGN KEY (`cancha_id`) REFERENCES `tejobar_app_cancha` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_equipo1_id_ff361566_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo1_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_equipo2_id_85c85b97_fk_tejobar_app_equipo_id` FOREIGN KEY (`equipo2_id`) REFERENCES `tejobar_app_equipo` (`id`),
  ADD CONSTRAINT `tejobar_app_torneo_partido_id_ca779875_fk_tejobar_app_partido_id` FOREIGN KEY (`partido_id`) REFERENCES `tejobar_app_partido` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
