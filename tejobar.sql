-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-03-2026 a las 03:22:09
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
