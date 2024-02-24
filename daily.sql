-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-05-2023 a las 03:23:05
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `daily`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `name` varchar(48) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `name`) VALUES
(1, 'restaurante'),
(2, 'local');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `categoria_id` int(2) NOT NULL,
  `contenido` varchar(1200) NOT NULL,
  `direccion` varchar(120) NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `user_id`, `categoria_id`, `contenido`, `direccion`, `imagen`, `fecha`) VALUES
(1, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:08'),
(2, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:37'),
(3, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:40'),
(4, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:41'),
(5, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:42'),
(6, 1, 1, 'asdasdasd', 'xxxxxxxx', 'image.png', '2023-05-18 23:17:43'),
(7, 1, 1, 'Este pollo en salsa de la casa de un restaurante gourmet es una verdadera delicia. El pollo está tierno y jugoso, y la salsa es suave, cremosa y perfectamente equilibrada en sabor. ¡Una experiencia culinaria inolvidable!\n\n\n#asdasd #asdasd #ssss', 'aaaaaaa alulu', 'image.png', '2023-05-18 23:19:51'),
(8, 1, 1, 'asdasd\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nadsasd', 'x', 'image.png', '2023-05-19 00:27:32'),
(9, 1, 1, 'asdasd\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nadsasd', 'x', 'image.png', '2023-05-19 00:35:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(48) NOT NULL,
  `password` varchar(48) NOT NULL,
  `email` varchar(64) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `genero` int(1) NOT NULL,
  `pais_registro` text NOT NULL,
  `ip_registro` varchar(32) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `email`, `nombre`, `apellido`, `genero`, `pais_registro`, `ip_registro`, `fecha_registro`) VALUES
(1, 'Sorac', 'carlos26', 'sorac.dev@gmail.com', '', '', 1, 'No disponible', '127.0.0.1', '2023-05-18 16:37:31'),
(2, 'El_lokotraketo', 'carlos26', 'carlos@gmail.com', '', '', 1, 'No disponible', '127.0.0.1', '2023-05-18 21:40:08'),
(3, 'isabel', 'isabel0728', 'isabele@gmail.com', '', '', 2, 'No disponible', '127.0.0.1', '2023-05-19 00:53:52'),
(4, 'elbicho.siuu', 'carlos2612', 'roasada@gmai.com', '', '', 1, 'No disponible', '127.0.0.1', '2023-05-18 17:44:27'),
(5, 'kekele_asdasd', 'carlos22555', 'asdfijdsg@gasdf.com', '', '', 1, 'No disponible', '127.0.0.1', '2023-05-18 17:46:10'),
(6, 'lsosoadIEW', 'oidsgnisudghe', 'carlosasdasd@gmail.conm', '', '', 1, 'No disponible', '127.0.0.1', '2023-05-18 17:46:40');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
