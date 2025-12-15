-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-12-2025 a las 03:50:11
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario_autopartes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autopartes`
--

CREATE TABLE `autopartes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `anio` year(4) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categoria_id` int(11) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Ruta imagen pequeña',
  `imagen_grande` varchar(255) DEFAULT NULL COMMENT 'Ruta imagen grande',
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Disponible, 0=No disponible',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autopartes`
--

INSERT INTO `autopartes` (`id`, `nombre`, `marca`, `modelo`, `anio`, `precio`, `stock`, `categoria_id`, `thumbnail`, `imagen_grande`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(41, 'Pastillas de freno delanteras', 'Toyota', 'Corolla', '2016', 29.90, 24, 7, 'https://w7.pngwing.com/pngs/919/1008/png-transparent-car-brake-pad-brake-pad-car-auto-part-brake.png', 'https://w7.pngwing.com/pngs/919/1008/png-transparent-car-brake-pad-brake-pad-car-auto-part-brake.png', 'Juego de pastillas cerámicas para disco delantero, libre de asbesto.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(42, 'Disco de freno ventilado 256mm', 'TRW', 'Suzuki Swift', '2018', 42.00, 10, 7, 'https://www.pikpng.com/pngl/b/404-4047016_disco-de-freno-frenos-de-disco-ventilado-png.png', 'https://www.pikpng.com/pngl/b/404-4047016_disco-de-freno-frenos-de-disco-ventilado-png.png', 'Disco ventilado alta resistencia; se recomienda cambio por par.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(43, 'Filtro de aceite 90915-YZZF2', 'Toyota', 'Hilux', '2018', 8.50, 55, 1, 'https://png.pngtree.com/png-vector/20241018/ourlarge/pngtree-modern-engine-oil-filter-isolated-designed-for-automotive-repair-and-replacement-png-image_14114532.png', 'https://png.pngtree.com/png-vector/20241018/ourlarge/pngtree-modern-engine-oil-filter-isolated-designed-for-automotive-repair-and-replacement-png-image_14114532.png', 'Filtro de aceite OEM equivalente, rosca M20x1.5.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(44, 'Batería 12V 65Ah libre de mantenimiento', 'ACDelco', 'Universal', '2022', 119.00, 12, 1, 'https://e-tech.mx/wp-content/uploads/2023/12/braubi575at1_A.png', 'https://e-tech.mx/wp-content/uploads/2023/12/braubi575at1_A.png', 'Batería sellada libre de mantenimiento, garantía 12 meses.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(45, 'Bujía Iridium IK20', 'Denso', 'Honda Civic', '2015', 12.75, 80, 1, 'https://w7.pngwing.com/pngs/885/625/png-transparent-car-ngk-spark-plug-iridium-autolite-car-car-motorcycle-platinum.png', 'https://w7.pngwing.com/pngs/885/625/png-transparent-car-ngk-spark-plug-iridium-autolite-car-car-motorcycle-platinum.png', 'Bujía iridio paso 1.1 mm, mejora encendido y consumo.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(46, 'Correa de tiempo 141 dientes', 'Gates', 'Hyundai Accent', '2013', 32.40, 20, 1, 'https://media.autodoc.de/360_photos/222634/h-preview.jpg', 'https://media.autodoc.de/360_photos/222634/h-preview.jpg', 'Correa de distribución reforzada para motor 1.6L.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(47, 'Bomba de agua', 'AISIN', 'Mazda 3', '2014', 74.95, 9, 1, 'https://w7.pngwing.com/pngs/281/862/png-transparent-car-machine-household-hardware-water-pump-car-auto-part-water-pump.png', 'https://w7.pngwing.com/pngs/281/862/png-transparent-car-machine-household-hardware-water-pump-car-auto-part-water-pump.png', 'Bomba con junta incluida; recomienda cambiar con la correa.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(48, 'Radiador aluminio/plástico', 'TYC', 'Chevrolet Aveo', '2011', 129.50, 7, 1, 'https://w7.pngwing.com/pngs/758/380/png-transparent-radiator-metal-aluminium-radiator-metal-aluminium-champion-cooling-systems.png', 'https://w7.pngwing.com/pngs/758/380/png-transparent-radiator-metal-aluminium-radiator-metal-aluminium-champion-cooling-systems.png', 'Radiador con núcleo de aluminio; incluye tapón y drenaje.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(49, 'Aceite motor 5W-30 sintético 1L', 'Mobil 1', 'Universal', '2024', 11.90, 100, 1, 'https://w7.pngwing.com/pngs/813/188/png-transparent-car-mobil-1-exxonmobil-motor-oil-protect-motor-oil-retail-car-oil.png', 'https://w7.pngwing.com/pngs/813/188/png-transparent-car-mobil-1-exxonmobil-motor-oil-protect-motor-oil-retail-car-oil.png', 'Lubricante sintético API SP, protección hasta 10,000 km.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(50, 'Termostato 82°C', 'Motorad', 'Nissan Versa', '2015', 18.60, 22, 1, 'https://www.autocraft.com.co/wp-content/uploads/2021/01/TERMOSTATO-NPR-82%C2%B0C-MODELO-2000-1024x1024.png', 'https://www.autocraft.com.co/wp-content/uploads/2021/01/TERMOSTATO-NPR-82%C2%B0C-MODELO-2000-1024x1024.png', 'Termostato calibrado a 82°C con empaque.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(51, 'Compresor de A/C 7SEU17C', 'Denso', 'Audi A4', '2012', 369.00, 2, 1, 'https://c0.klipartz.com/pngpicture/369/15/gratis-png-compresor-de-vehiculos-t-cci-fabricacion-aire-acondicionado-negocio-vehiculo.png', 'https://c0.klipartz.com/pngpicture/369/15/gratis-png-compresor-de-vehiculos-t-cci-fabricacion-aire-acondicionado-negocio-vehiculo.png', 'Compresor nuevo; requiere aceite PAG y vacío del sistema.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(52, 'Alternador 110A', 'Bosch', 'Nissan Sentra', '2012', 189.00, 5, 4, 'https://png.pngtree.com/png-vector/20230830/ourlarge/pngtree-alternator-electromechanical-rotor-car-image_9933296.png', 'https://png.pngtree.com/png-vector/20230830/ourlarge/pngtree-alternator-electromechanical-rotor-car-image_9933296.png', 'Alternador reconstruido con prueba de banco y garantía 6 meses.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(53, 'Sensor de oxígeno (O2) upstream', 'NTK', 'Toyota Yaris', '2012', 69.00, 10, 4, 'https://www.densoautoparts.com/wp-content/uploads/2022/10/Oxygen-Sensor2-web.png', 'https://www.densoautoparts.com/wp-content/uploads/2022/10/Oxygen-Sensor2-web.png', 'Sensor precatalizador de 4 cables, conector directo.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(54, 'Bobina de encendido', 'Delphi', 'Kia Picanto', '2014', 39.90, 15, 4, 'https://c0.klipartz.com/pngpicture/965/964/gratis-png-bobina-de-encendido-del-coche-sistema-de-encendido-en-linea-cuatro-motor-coche.png', 'https://c0.klipartz.com/pngpicture/965/964/gratis-png-bobina-de-encendido-del-coche-sistema-de-encendido-en-linea-cuatro-motor-coche.png', 'Bobina individual por cilindro; voltaje 12V.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(55, 'Amortiguador trasero gas', 'KYB', 'Kia Rio', '2017', 58.90, 14, 6, 'https://c0.klipartz.com/pngpicture/164/315/gratis-png-amortiguador-amortiguador-gas-amortiguadores.png', 'https://c0.klipartz.com/pngpicture/164/315/gratis-png-amortiguador-amortiguador-gas-amortiguadores.png', 'Amortiguador de gas presurizado para eje trasero, par recomendado.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(56, 'Kit de embrague completo', 'Sachs', 'Volkswagen Jetta', '2013', 245.00, 4, 8, 'https://sogo4x4.com/wp-content/uploads/cuis22e2r1201.png', 'https://sogo4x4.com/wp-content/uploads/cuis22e2r1201.png', 'Incluye prensa, disco y collarín; no incluye volante bimasa.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(57, 'Faro delantero derecho', 'Depo', 'Ford Ranger', '2019', 155.00, 6, 2, 'https://www.farosypilotos.es/wp-content/uploads/2024/10/Faro-Delantero-Derecho-Ford-Ranger-2013-2016-Negro-www.farosypilotos.es-3.jpg', 'https://www.farosypilotos.es/wp-content/uploads/2024/10/Faro-Delantero-Derecho-Ford-Ranger-2013-2016-Negro-www.farosypilotos.es-3.jpg', 'Faro halógeno con regulación eléctrica; DOT/SAE.', 1, '2025-12-11 16:04:39', '2025-12-15 01:40:25'),
(58, 'Juego de limpiaparabrisas 24\"/16\"', 'Bosch', 'Universal', '2023', 17.50, 55, 2, '/img/autopartes/limpia-24-16-thumb.jpg', '/img/autopartes/limpia-24-16.jpg', 'Escobillas aerotwin, adaptadores múltiples incluidos.', 1, '2025-12-11 16:04:39', '2025-12-11 16:04:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `autoparte_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen`, `estado`, `fecha_creacion`) VALUES
(1, 'Motor', 'Piezas relacionadas con el motor del vehículo', 'https://www.solverdca.com.ar/storage/2019/04/motor.e3.jpg', 1, '2025-12-10 18:33:24'),
(2, 'Carrocería', 'Puertas, cofres, parachoques y paneles', 'https://static.motor.es/fotos-diccionario/2020/03//carroceria_1584179739.jpg', 1, '2025-12-10 18:33:24'),
(3, 'Vidrios', 'Parabrisas, ventanas y espejos', 'https://www.shutterstock.com/image-illustration/car-glass-rear-window-heating-260nw-2382588993.jpg', 1, '2025-12-10 18:33:24'),
(4, 'Eléctrico', 'Componentes eléctricos y electrónicos', 'https://autecoblue.com/wp-content/uploads/sites/54/2024/03/como-funcionan-los-carros-electricos.jpg', 1, '2025-12-10 18:33:24'),
(5, 'Interior', 'Asientos, tableros y accesorios internos', 'https://i.ebayimg.com/thumbs/images/g/DFcAAeSwPqNpEgiW/s-l1200.webp', 1, '2025-12-10 18:33:24'),
(6, 'Suspensión', 'Amortiguadores, muelles y componentes', 'https://www.carico-auto.com/eimages/CAR_2022_news1754831_63021_4541_315105.jpeg', 1, '2025-12-10 18:33:24'),
(7, 'Frenos', 'Discos, pastillas y sistemas de frenado', 'https://www.wagnerbrake.com/content/loc-na/loc-us/fmmp-wagner/es_US/products/brakes/_jcr_content/header/foreground-image.img.png/Wagner_BrakeLanding-HeroProductIMG_760x600-1506694787346.png', 1, '2025-12-10 18:33:24'),
(8, 'Transmisión', 'Cajas de cambio y componentes', 'https://mundotransmisiones.com/wp-content/uploads/2023/05/Mundo-Transmisiones-3.png', 1, '2025-12-10 18:33:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `autoparte_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'NULL si es comentario anónimo',
  `nombre_usuario` varchar(100) DEFAULT NULL COMMENT 'Para usuarios no registrados',
  `comentario` text NOT NULL,
  `publicar` tinyint(1) DEFAULT 0 COMMENT '1=Publicado, 0=Pendiente moderación',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `autoparte_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `venta_id`, `autoparte_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 41, 1, 29.90, 29.90),
(2, 2, 42, 4, 42.00, 168.00),
(3, 3, 42, 4, 42.00, 168.00),
(4, 4, 43, 5, 8.50, 42.50);

--
-- Disparadores `detalle_venta`
--
DELIMITER $$
CREATE TRIGGER `after_detalle_venta_insert` AFTER INSERT ON `detalle_venta` FOR EACH ROW BEGIN
    UPDATE autopartes 
    SET stock = stock - NEW.cantidad 
    WHERE id = NEW.autoparte_id;
    
    -- Insertar en vendido_parte
    INSERT INTO vendido_parte (autoparte_id, venta_id, cantidad, precio, usuario_id)
    SELECT NEW.autoparte_id, NEW.venta_id, NEW.cantidad, NEW.precio_unitario, v.usuario_id
    FROM ventas v WHERE v.id = NEW.venta_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_rol`
--

CREATE TABLE `permisos_rol` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL COMMENT 'usuarios, inventario, categorias, reportes, etc',
  `puede_crear` tinyint(1) DEFAULT 0,
  `puede_leer` tinyint(1) DEFAULT 0,
  `puede_actualizar` tinyint(1) DEFAULT 0,
  `puede_eliminar` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_rol`
--

INSERT INTO `permisos_rol` (`id`, `rol_id`, `modulo`, `puede_crear`, `puede_leer`, `puede_actualizar`, `puede_eliminar`) VALUES
(1, 1, 'usuarios', 1, 1, 1, 1),
(2, 1, 'roles', 1, 1, 1, 1),
(3, 1, 'categorias', 1, 1, 1, 1),
(4, 1, 'inventario', 1, 1, 1, 1),
(5, 1, 'ventas', 1, 1, 1, 1),
(6, 1, 'reportes', 1, 1, 1, 1),
(7, 1, 'estadisticas', 1, 1, 1, 1),
(8, 1, 'comentarios', 1, 1, 1, 1),
(9, 2, 'categorias', 0, 1, 0, 0),
(10, 2, 'inventario', 1, 1, 1, 0),
(11, 2, 'ventas', 0, 1, 0, 0),
(12, 2, 'comentarios', 0, 1, 1, 1),
(13, 3, 'catalogo', 0, 1, 0, 0),
(14, 3, 'carrito', 1, 1, 1, 1),
(15, 3, 'compras', 1, 1, 0, 0),
(16, 3, 'comentarios', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
(1, 'Administrador', 'Control total del sistema', '2025-12-10 18:33:24'),
(2, 'Operador', 'Gestión de inventario y ventas', '2025-12-10 18:33:24'),
(3, 'Cliente', 'Usuario que realiza compras', '2025-12-10 18:33:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` varchar(128) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `datos` text DEFAULT NULL,
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_sesion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol_id`, `estado`, `fecha_creacion`, `ultima_sesion`) VALUES
(1, 'Juan botacio', 'botaciojuan3@gmail.com', '$2y$10$SiI2XXU9zP8pnT9./vEJ0uVM4z887cbCMLzqVJLQASV02O/z1Mhxy', 1, 1, '2025-12-11 00:52:18', '2025-12-15 02:16:14'),
(2, 'Danna Dawkins', 'danna@operador.com', '$2y$10$Xvl.lJoqyTPWQ0kyer2R.ed.gr5lmr.5CB3niKGt/UmfRqOqFD8j.', 2, 1, '2025-12-11 07:25:43', '2025-12-12 14:33:49'),
(5, 'Daniella De Leon', 'Daniella@gmail.com', '$2y$10$fszq4Pp7BwhLv3ZOThaifONbxLaFKRCeFo68Z.UyOKP..jYiwaQUy', 3, 1, '2025-12-11 16:06:25', '2025-12-12 13:26:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendido_parte`
--

CREATE TABLE `vendido_parte` (
  `id` int(11) NOT NULL,
  `autoparte_id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'Usuario que realizó la compra',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vendido_parte`
--

INSERT INTO `vendido_parte` (`id`, `autoparte_id`, `venta_id`, `cantidad`, `precio`, `usuario_id`, `fecha`) VALUES
(1, 41, 1, 1, 29.90, 5, '2025-12-12 13:33:41'),
(2, 42, 2, 4, 42.00, 5, '2025-12-12 13:36:55'),
(3, 42, 3, 4, 42.00, 5, '2025-12-12 13:37:10'),
(4, 43, 4, 5, 8.50, 5, '2025-12-12 13:37:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `itbms` decimal(10,2) NOT NULL COMMENT 'Impuesto 7%',
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'completada' COMMENT 'completada, cancelada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `subtotal`, `itbms`, `total`, `fecha_venta`, `estado`) VALUES
(1, 5, 29.90, 2.09, 31.99, '2025-12-12 13:33:41', 'completada'),
(2, 5, 168.00, 11.76, 179.76, '2025-12-12 13:36:55', 'completada'),
(3, 5, 168.00, 11.76, 179.76, '2025-12-12 13:37:10', 'completada'),
(4, 5, 42.50, 2.98, 45.48, '2025-12-12 13:37:55', 'completada');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_inventario_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_inventario_completo` (
`id` int(11)
,`nombre` varchar(200)
,`marca` varchar(100)
,`modelo` varchar(100)
,`anio` year(4)
,`precio` decimal(10,2)
,`stock` int(11)
,`thumbnail` varchar(255)
,`imagen_grande` varchar(255)
,`descripcion` text
,`estado` tinyint(1)
,`categoria` varchar(100)
,`categoria_id` int(11)
,`fecha_creacion` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas_completas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas_completas` (
`venta_id` int(11)
,`fecha_venta` timestamp
,`subtotal` decimal(10,2)
,`itbms` decimal(10,2)
,`total` decimal(10,2)
,`cliente` varchar(100)
,`cliente_email` varchar(100)
,`total_items` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_inventario_completo`
--
DROP TABLE IF EXISTS `vista_inventario_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_inventario_completo`  AS SELECT `a`.`id` AS `id`, `a`.`nombre` AS `nombre`, `a`.`marca` AS `marca`, `a`.`modelo` AS `modelo`, `a`.`anio` AS `anio`, `a`.`precio` AS `precio`, `a`.`stock` AS `stock`, `a`.`thumbnail` AS `thumbnail`, `a`.`imagen_grande` AS `imagen_grande`, `a`.`descripcion` AS `descripcion`, `a`.`estado` AS `estado`, `c`.`nombre` AS `categoria`, `c`.`id` AS `categoria_id`, `a`.`fecha_creacion` AS `fecha_creacion` FROM (`autopartes` `a` join `categorias` `c` on(`a`.`categoria_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas_completas`
--
DROP TABLE IF EXISTS `vista_ventas_completas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_completas`  AS SELECT `v`.`id` AS `venta_id`, `v`.`fecha_venta` AS `fecha_venta`, `v`.`subtotal` AS `subtotal`, `v`.`itbms` AS `itbms`, `v`.`total` AS `total`, `u`.`nombre` AS `cliente`, `u`.`email` AS `cliente_email`, count(`dv`.`id`) AS `total_items` FROM ((`ventas` `v` join `usuarios` `u` on(`v`.`usuario_id` = `u`.`id`)) left join `detalle_venta` `dv` on(`v`.`id` = `dv`.`venta_id`)) GROUP BY `v`.`id` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autopartes`
--
ALTER TABLE `autopartes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_marca` (`marca`),
  ADD KEY `idx_modelo` (`modelo`),
  ADD KEY `idx_anio` (`anio`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_estado` (`estado`);
ALTER TABLE `autopartes` ADD FULLTEXT KEY `idx_busqueda` (`nombre`,`marca`,`modelo`,`descripcion`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_parte` (`usuario_id`,`autoparte_id`),
  ADD KEY `autoparte_id` (`autoparte_id`),
  ADD KEY `idx_carrito_usuario` (`usuario_id`,`fecha_agregado`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_autoparte` (`autoparte_id`),
  ADD KEY `idx_publicar` (`publicar`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autoparte_id` (`autoparte_id`),
  ADD KEY `idx_venta` (`venta_id`);

--
-- Indices de la tabla `permisos_rol`
--
ALTER TABLE `permisos_rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rol_modulo` (`rol_id`,`modulo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_actividad` (`ultima_actividad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `vendido_parte`
--
ALTER TABLE `vendido_parte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_autoparte` (`autoparte_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha_venta`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_ventas_fecha_usuario` (`fecha_venta`,`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autopartes`
--
ALTER TABLE `autopartes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `permisos_rol`
--
ALTER TABLE `permisos_rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vendido_parte`
--
ALTER TABLE `vendido_parte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `autopartes`
--
ALTER TABLE `autopartes`
  ADD CONSTRAINT `autopartes_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`autoparte_id`) REFERENCES `autopartes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`autoparte_id`) REFERENCES `autopartes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`autoparte_id`) REFERENCES `autopartes` (`id`);

--
-- Filtros para la tabla `permisos_rol`
--
ALTER TABLE `permisos_rol`
  ADD CONSTRAINT `permisos_rol_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `vendido_parte`
--
ALTER TABLE `vendido_parte`
  ADD CONSTRAINT `vendido_parte_ibfk_1` FOREIGN KEY (`autoparte_id`) REFERENCES `autopartes` (`id`),
  ADD CONSTRAINT `vendido_parte_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `vendido_parte_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
