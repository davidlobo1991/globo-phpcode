-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-11-2017 a las 08:53:12
-- Versión del servidor: 5.5.56-MariaDB
-- Versión de PHP: 5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `globobalear_globo`
--

--
-- Procedimientos
--
CREATE DEFINER=`globobalear`@`%` PROCEDURE `sp_reservations_availability` (IN `in_passe` INT UNSIGNED, IN `in_seller` INT UNSIGNED)  NO SQL
SELECT
    st.title,
    st.acronym, 
    st.default_quantity,
     IFNULL(seats_sold, 0) as seats_sold,
    ABS(
        IFNULL(seats_sold, 0) - IFNULL(total_seats, 0)
    ) AS total_solded
FROM
    seat_types AS st
LEFT JOIN(
    SELECT st.id,
         SUM(rt.quantity) AS seats_sold
FROM
    seat_types st
LEFT JOIN reservation_tickets rt ON
    st.id = rt.seat_type_id
LEFT JOIN reservations r ON
    r.id = rt.reservation_id
LEFT JOIN channels rc ON
    r.channel_id = rc.id
LEFT JOIN ticket_types t ON
    t.id = rt.ticket_type_id
WHERE
    r.finished = 1 AND r.canceled_date IS NULL AND t.take_place != 0 AND rc.passes_seller_id = in_seller AND r.pass_id = in_passe
GROUP BY
    st.id,r.reconcile
) seat_types_quantities
ON
    st.id = seat_types_quantities.id
LEFT JOIN(
    SELECT
        st.id,
        pst.seats_available AS total_seats
    FROM
        seat_types st
    INNER JOIN pass_seat_type pst ON
        st.id = pst.seat_type_id AND pst.pass_id = in_passe
) seat_types_total
ON
    st.id = seat_types_total.id
ORDER BY
    st.sort


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agent_types`
--

CREATE TABLE `agent_types` (`id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `agent_types`
--

INSERT INTO `agent_types` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Hotels', NULL, NULL, NULL),
(2, 'Traditional', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id`, `name`, `acronym`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Mallorca North East', 'NE', NULL, '2017-09-14 13:01:47', '2017-09-14 13:01:47'),
(2, 'Mallorca North', 'NO', NULL, '2017-09-14 13:02:03', '2017-09-14 13:02:03'),
(3, 'Mallorca South', 'SO', NULL, '2017-09-14 13:02:23', '2017-09-14 13:02:23'),
(4, 'Mallorca South East', 'SE', NULL, '2017-09-14 13:02:32', '2017-09-14 13:02:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buses`
--

CREATE TABLE `buses` (
  `id` int(10) UNSIGNED NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT '0',
  `transporter_id` int(10) UNSIGNED DEFAULT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `pass_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `buses`
--

INSERT INTO `buses` (`id`, `capacity`, `transporter_id`, `route_id`, `pass_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 100, 1, 1, 5, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(2, 100, 1, 1, 6, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(3, 100, 1, 1, 7, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(4, 100, 1, 1, 8, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(5, 100, 1, 1, 9, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(6, 100, 1, 1, 10, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(7, 100, 1, 1, 11, NULL, '2017-09-20 11:07:27', '2017-09-20 11:07:27'),
(8, 100, 1, 1, 12, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(9, 100, 1, 1, 13, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(10, 100, 1, 1, 14, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(11, 100, 1, 1, 15, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(12, 100, 1, 1, 16, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(13, 100, 1, 1, 17, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(14, 100, 1, 1, 18, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(15, 100, 1, 1, 19, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(16, 100, 1, 1, 20, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(17, 100, 1, 1, 21, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(18, 100, 1, 1, 22, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(19, 100, 1, 1, 23, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(20, 100, 1, 1, 24, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(21, 100, 1, 1, 25, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(22, 100, 1, 1, 26, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(23, 100, 1, 1, 27, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(24, 100, 1, 1, 28, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(25, 100, 1, 1, 29, NULL, '2017-09-20 11:07:28', '2017-09-20 11:07:28'),
(26, 100, 1, 1, 30, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(27, 100, 1, 1, 31, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(28, 100, 1, 1, 32, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(29, 100, 1, 1, 33, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(30, 100, 1, 1, 34, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(31, 100, 1, 1, 35, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(32, 100, 1, 1, 36, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(33, 100, 1, 1, 37, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(34, 100, 1, 1, 38, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(35, 100, 1, 1, 39, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(36, 100, 1, 1, 40, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(37, 100, 1, 1, 41, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(38, 100, 1, 1, 42, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(39, 100, 1, 1, 43, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(40, 100, 1, 1, 44, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(41, 100, 1, 1, 45, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(42, 100, 1, 1, 46, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(43, 100, 1, 1, 47, NULL, '2017-09-20 11:07:29', '2017-09-20 11:07:29'),
(44, 100, 1, 1, 48, NULL, '2017-09-20 11:07:30', '2017-09-20 11:07:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cartes`
--

CREATE TABLE `cartes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enable` tinyint(1) NOT NULL DEFAULT '1',
  `show_id` int(10) UNSIGNED NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cartes`
--

INSERT INTO `cartes` (`id`, `name`, `is_enable`, `show_id`, `seat_type_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(6, 'VIP VAMPIRICA', 1, 1, 1, NULL, '2017-09-19 06:28:19', '2017-09-19 06:47:18'),
(7, 'PLATINIUM VAMPIRICA', 1, 1, 2, NULL, '2017-09-19 06:57:49', '2017-09-19 06:58:18'),
(8, 'VIP Halloween', 1, 3, 1, NULL, '2017-10-30 08:44:06', '2017-10-30 08:48:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carte_menu`
--

CREATE TABLE `carte_menu` (
  `carte_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carte_menu`
--

INSERT INTO `carte_menu` (`carte_id`, `menu_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(8, 3, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `channels`
--

CREATE TABLE `channels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passes_seller_id` int(10) UNSIGNED NOT NULL,
  `is_enable` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `channels`
--

INSERT INTO `channels` (`id`, `name`, `passes_seller_id`, `is_enable`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Tour Ops', 2, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09'),
(2, 'Agents', 3, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09'),
(3, 'Telesales', 1, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09'),
(4, 'Box Office', 1, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09'),
(5, 'Internal', 1, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09'),
(6, 'Online', 1, 1, NULL, '2016-10-05 05:03:09', '2016-10-05 05:03:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cities`
--

CREATE TABLE `cities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cities`
--

INSERT INTO `cities` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Alcudia', NULL, '2017-09-14 13:00:58', '2017-09-14 13:00:58'),
(2, 'Cala Barca', NULL, '2017-09-14 13:01:12', '2017-09-14 13:01:12'),
(3, 'Cala Egos', NULL, '2017-09-14 13:01:21', '2017-09-14 13:01:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `countries`
--

INSERT INTO `countries` (`id`, `code`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(0, '-', '-', NULL, NULL, NULL),
(4, 'AF', 'Afghanistan', NULL, NULL, NULL),
(5, 'AL', 'Albania', NULL, NULL, NULL),
(6, 'DZ', 'Algeria', NULL, NULL, NULL),
(7, 'DS', 'American Samoa', NULL, NULL, NULL),
(8, 'AD', 'Andorra', NULL, NULL, NULL),
(9, 'AO', 'Angola', NULL, NULL, NULL),
(10, 'AI', 'Anguilla', NULL, NULL, NULL),
(11, 'AQ', 'Antarctica', NULL, NULL, NULL),
(12, 'AG', 'Antigua and Barbuda', NULL, NULL, NULL),
(13, 'AR', 'Argentina', NULL, NULL, NULL),
(14, 'AM', 'Armenia', NULL, NULL, NULL),
(15, 'AW', 'Aruba', NULL, NULL, NULL),
(16, 'AU', 'Australia', NULL, NULL, NULL),
(17, 'AT', 'Austria', NULL, NULL, NULL),
(18, 'AZ', 'Azerbaijan', NULL, NULL, NULL),
(19, 'BS', 'Bahamas', NULL, NULL, NULL),
(20, 'BH', 'Bahrain', NULL, NULL, NULL),
(21, 'BD', 'Bangladesh', NULL, NULL, NULL),
(22, 'BB', 'Barbados', NULL, NULL, NULL),
(23, 'BY', 'Belarus', NULL, NULL, NULL),
(24, 'BE', 'Belgium', NULL, NULL, NULL),
(25, 'BZ', 'Belize', NULL, NULL, NULL),
(26, 'BJ', 'Benin', NULL, NULL, NULL),
(27, 'BM', 'Bermuda', NULL, NULL, NULL),
(28, 'BT', 'Bhutan', NULL, NULL, NULL),
(29, 'BO', 'Bolivia', NULL, NULL, NULL),
(30, 'BA', 'Bosnia and Herzegovina', NULL, NULL, NULL),
(31, 'BW', 'Botswana', NULL, NULL, NULL),
(32, 'BV', 'Bouvet Island', NULL, NULL, NULL),
(33, 'BR', 'Brazil', NULL, NULL, NULL),
(34, 'IO', 'British Indian Ocean Territory', NULL, NULL, NULL),
(35, 'BN', 'Brunei Darussalam', NULL, NULL, NULL),
(36, 'BG', 'Bulgaria', NULL, NULL, NULL),
(37, 'BF', 'Burkina Faso', NULL, NULL, NULL),
(38, 'BI', 'Burundi', NULL, NULL, NULL),
(39, 'KH', 'Cambodia', NULL, NULL, NULL),
(40, 'CM', 'Cameroon', NULL, NULL, NULL),
(41, 'CA', 'Canada', NULL, NULL, NULL),
(42, 'CV', 'Cape Verde', NULL, NULL, NULL),
(43, 'KY', 'Cayman Islands', NULL, NULL, NULL),
(44, 'CF', 'Central African Republic', NULL, NULL, NULL),
(45, 'TD', 'Chad', NULL, NULL, NULL),
(46, 'CL', 'Chile', NULL, NULL, NULL),
(47, 'CN', 'China', NULL, NULL, NULL),
(48, 'CX', 'Christmas Island', NULL, NULL, NULL),
(49, 'CC', 'Cocos (Keeling) Islands', NULL, NULL, NULL),
(50, 'CO', 'Colombia', NULL, NULL, NULL),
(51, 'KM', 'Comoros', NULL, NULL, NULL),
(52, 'CG', 'Congo', NULL, NULL, NULL),
(53, 'CK', 'Cook Islands', NULL, NULL, NULL),
(54, 'CR', 'Costa Rica', NULL, NULL, NULL),
(55, 'HR', 'Croatia (Hrvatska)', NULL, NULL, NULL),
(56, 'CU', 'Cuba', NULL, NULL, NULL),
(57, 'CY', 'Cyprus', NULL, NULL, NULL),
(58, 'CZ', 'Czech Republic', NULL, NULL, NULL),
(59, 'DK', 'Denmark', NULL, NULL, NULL),
(60, 'DJ', 'Djibouti', NULL, NULL, NULL),
(61, 'DM', 'Dominica', NULL, NULL, NULL),
(62, 'DO', 'Dominican Republic', NULL, NULL, NULL),
(63, 'TP', 'East Timor', NULL, NULL, NULL),
(64, 'EC', 'Ecuador', NULL, NULL, NULL),
(65, 'EG', 'Egypt', NULL, NULL, NULL),
(66, 'SV', 'El Salvador', NULL, NULL, NULL),
(67, 'GQ', 'Equatorial Guinea', NULL, NULL, NULL),
(68, 'ER', 'Eritrea', NULL, NULL, NULL),
(69, 'EE', 'Estonia', NULL, NULL, NULL),
(70, 'ET', 'Ethiopia', NULL, NULL, NULL),
(71, 'FK', 'Falkland Islands (Malvinas)', NULL, NULL, NULL),
(72, 'FO', 'Faroe Islands', NULL, NULL, NULL),
(73, 'FJ', 'Fiji', NULL, NULL, NULL),
(74, 'FI', 'Finland', NULL, NULL, NULL),
(75, 'FR', 'France', NULL, NULL, NULL),
(76, 'FX', 'France, Metropolitan', NULL, NULL, NULL),
(77, 'GF', 'French Guiana', NULL, NULL, NULL),
(78, 'PF', 'French Polynesia', NULL, NULL, NULL),
(79, 'TF', 'French Southern Territories', NULL, NULL, NULL),
(80, 'GA', 'Gabon', NULL, NULL, NULL),
(81, 'GM', 'Gambia', NULL, NULL, NULL),
(82, 'GE', 'Georgia', NULL, NULL, NULL),
(83, 'DE', 'Germany', NULL, NULL, NULL),
(84, 'GH', 'Ghana', NULL, NULL, NULL),
(85, 'GI', 'Gibraltar', NULL, NULL, NULL),
(86, 'GK', 'Guernsey', NULL, NULL, NULL),
(87, 'GR', 'Greece', NULL, NULL, NULL),
(88, 'GL', 'Greenland', NULL, NULL, NULL),
(89, 'GD', 'Grenada', NULL, NULL, NULL),
(90, 'GP', 'Guadeloupe', NULL, NULL, NULL),
(91, 'GU', 'Guam', NULL, NULL, NULL),
(92, 'GT', 'Guatemala', NULL, NULL, NULL),
(93, 'GN', 'Guinea', NULL, NULL, NULL),
(94, 'GW', 'Guinea-Bissau', NULL, NULL, NULL),
(95, 'GY', 'Guyana', NULL, NULL, NULL),
(96, 'HT', 'Haiti', NULL, NULL, NULL),
(97, 'HM', 'Heard and Mc Donald Islands', NULL, NULL, NULL),
(98, 'HN', 'Honduras', NULL, NULL, NULL),
(99, 'HK', 'Hong Kong', NULL, NULL, NULL),
(100, 'HU', 'Hungary', NULL, NULL, NULL),
(101, 'IS', 'Iceland', NULL, NULL, NULL),
(102, 'IN', 'India', NULL, NULL, NULL),
(103, 'IM', 'Isle of Man', NULL, NULL, NULL),
(104, 'ID', 'Indonesia', NULL, NULL, NULL),
(105, 'IR', 'Iran (Islamic Republic of)', NULL, NULL, NULL),
(106, 'IQ', 'Iraq', NULL, NULL, NULL),
(107, 'IE', 'Ireland', NULL, NULL, NULL),
(108, 'IL', 'Israel', NULL, NULL, NULL),
(109, 'IT', 'Italy', NULL, NULL, NULL),
(110, 'CI', 'Ivory Coast', NULL, NULL, NULL),
(111, 'JE', 'Jersey', NULL, NULL, NULL),
(112, 'JM', 'Jamaica', NULL, NULL, NULL),
(113, 'JP', 'Japan', NULL, NULL, NULL),
(114, 'JO', 'Jordan', NULL, NULL, NULL),
(115, 'KZ', 'Kazakhstan', NULL, NULL, NULL),
(116, 'KE', 'Kenya', NULL, NULL, NULL),
(117, 'KI', 'Kiribati', NULL, NULL, NULL),
(118, 'KP', 'Korea, Democratic People\'s Republic of', NULL, NULL, NULL),
(119, 'KR', 'Korea, Republic of', NULL, NULL, NULL),
(120, 'XK', 'Kosovo', NULL, NULL, NULL),
(121, 'KW', 'Kuwait', NULL, NULL, NULL),
(122, 'KG', 'Kyrgyzstan', NULL, NULL, NULL),
(123, 'LA', 'Lao People\'s Democratic Republic', NULL, NULL, NULL),
(124, 'LV', 'Latvia', NULL, NULL, NULL),
(125, 'LB', 'Lebanon', NULL, NULL, NULL),
(126, 'LS', 'Lesotho', NULL, NULL, NULL),
(127, 'LR', 'Liberia', NULL, NULL, NULL),
(128, 'LY', 'Libyan Arab Jamahiriya', NULL, NULL, NULL),
(129, 'LI', 'Liechtenstein', NULL, NULL, NULL),
(130, 'LT', 'Lithuania', NULL, NULL, NULL),
(131, 'LU', 'Luxembourg', NULL, NULL, NULL),
(132, 'MO', 'Macau', NULL, NULL, NULL),
(133, 'MK', 'Macedonia', NULL, NULL, NULL),
(134, 'MG', 'Madagascar', NULL, NULL, NULL),
(135, 'MW', 'Malawi', NULL, NULL, NULL),
(136, 'MY', 'Malaysia', NULL, NULL, NULL),
(137, 'MV', 'Maldives', NULL, NULL, NULL),
(138, 'ML', 'Mali', NULL, NULL, NULL),
(139, 'MT', 'Malta', NULL, NULL, NULL),
(140, 'MH', 'Marshall Islands', NULL, NULL, NULL),
(141, 'MQ', 'Martinique', NULL, NULL, NULL),
(142, 'MR', 'Mauritania', NULL, NULL, NULL),
(143, 'MU', 'Mauritius', NULL, NULL, NULL),
(144, 'TY', 'Mayotte', NULL, NULL, NULL),
(145, 'MX', 'Mexico', NULL, NULL, NULL),
(146, 'FM', 'Micronesia, Federated States of', NULL, NULL, NULL),
(147, 'MD', 'Moldova, Republic of', NULL, NULL, NULL),
(148, 'MC', 'Monaco', NULL, NULL, NULL),
(149, 'MN', 'Mongolia', NULL, NULL, NULL),
(150, 'ME', 'Montenegro', NULL, NULL, NULL),
(151, 'MS', 'Montserrat', NULL, NULL, NULL),
(152, 'MA', 'Morocco', NULL, NULL, NULL),
(153, 'MZ', 'Mozambique', NULL, NULL, NULL),
(154, 'MM', 'Myanmar', NULL, NULL, NULL),
(155, 'NA', 'Namibia', NULL, NULL, NULL),
(156, 'NR', 'Nauru', NULL, NULL, NULL),
(157, 'NP', 'Nepal', NULL, NULL, NULL),
(158, 'NL', 'Netherlands', NULL, NULL, NULL),
(159, 'AN', 'Netherlands Antilles', NULL, NULL, NULL),
(160, 'NC', 'New Caledonia', NULL, NULL, NULL),
(161, 'NZ', 'New Zealand', NULL, NULL, NULL),
(162, 'NI', 'Nicaragua', NULL, NULL, NULL),
(163, 'NE', 'Niger', NULL, NULL, NULL),
(164, 'NG', 'Nigeria', NULL, NULL, NULL),
(165, 'NU', 'Niue', NULL, NULL, NULL),
(166, 'NF', 'Norfolk Island', NULL, NULL, NULL),
(167, 'MP', 'Northern Mariana Islands', NULL, NULL, NULL),
(168, 'NO', 'Norway', NULL, NULL, NULL),
(169, 'OM', 'Oman', NULL, NULL, NULL),
(170, 'PK', 'Pakistan', NULL, NULL, NULL),
(171, 'PW', 'Palau', NULL, NULL, NULL),
(172, 'PS', 'Palestine', NULL, NULL, NULL),
(173, 'PA', 'Panama', NULL, NULL, NULL),
(174, 'PG', 'Papua New Guinea', NULL, NULL, NULL),
(175, 'PY', 'Paraguay', NULL, NULL, NULL),
(176, 'PE', 'Peru', NULL, NULL, NULL),
(177, 'PH', 'Philippines', NULL, NULL, NULL),
(178, 'PN', 'Pitcairn', NULL, NULL, NULL),
(179, 'PL', 'Poland', NULL, NULL, NULL),
(180, 'PT', 'Portugal', NULL, NULL, NULL),
(181, 'PR', 'Puerto Rico', NULL, NULL, NULL),
(182, 'QA', 'Qatar', NULL, NULL, NULL),
(183, 'RE', 'Reunion', NULL, NULL, NULL),
(184, 'RO', 'Romania', NULL, NULL, NULL),
(185, 'RU', 'Russian Federation', NULL, NULL, NULL),
(186, 'RW', 'Rwanda', NULL, NULL, NULL),
(187, 'KN', 'Saint Kitts and Nevis', NULL, NULL, NULL),
(188, 'LC', 'Saint Lucia', NULL, NULL, NULL),
(189, 'VC', 'Saint Vincent and the Grenadines', NULL, NULL, NULL),
(190, 'WS', 'Samoa', NULL, NULL, NULL),
(191, 'SM', 'San Marino', NULL, NULL, NULL),
(192, 'ST', 'Sao Tome and Principe', NULL, NULL, NULL),
(193, 'SA', 'Saudi Arabia', NULL, NULL, NULL),
(194, 'SN', 'Senegal', NULL, NULL, NULL),
(195, 'RS', 'Serbia', NULL, NULL, NULL),
(196, 'SC', 'Seychelles', NULL, NULL, NULL),
(197, 'SL', 'Sierra Leone', NULL, NULL, NULL),
(198, 'SG', 'Singapore', NULL, NULL, NULL),
(199, 'SK', 'Slovakia', NULL, NULL, NULL),
(200, 'SI', 'Slovenia', NULL, NULL, NULL),
(201, 'SB', 'Solomon Islands', NULL, NULL, NULL),
(202, 'SO', 'Somalia', NULL, NULL, NULL),
(203, 'ZA', 'South Africa', NULL, NULL, NULL),
(204, 'GS', 'South Georgia South Sandwich Islands', NULL, NULL, NULL),
(205, 'ES', 'Spain', NULL, NULL, NULL),
(206, 'LK', 'Sri Lanka', NULL, NULL, NULL),
(207, 'SH', 'St. Helena', NULL, NULL, NULL),
(208, 'PM', 'St. Pierre and Miquelon', NULL, NULL, NULL),
(209, 'SD', 'Sudan', NULL, NULL, NULL),
(210, 'SR', 'Suriname', NULL, NULL, NULL),
(211, 'SJ', 'Svalbard and Jan Mayen Islands', NULL, NULL, NULL),
(212, 'SZ', 'Swaziland', NULL, NULL, NULL),
(213, 'SE', 'Sweden', NULL, NULL, NULL),
(214, 'CH', 'Switzerland', NULL, NULL, NULL),
(215, 'SY', 'Syrian Arab Republic', NULL, NULL, NULL),
(216, 'TW', 'Taiwan', NULL, NULL, NULL),
(217, 'TJ', 'Tajikistan', NULL, NULL, NULL),
(218, 'TZ', 'Tanzania, United Republic of', NULL, NULL, NULL),
(219, 'TH', 'Thailand', NULL, NULL, NULL),
(220, 'TG', 'Togo', NULL, NULL, NULL),
(221, 'TK', 'Tokelau', NULL, NULL, NULL),
(222, 'TO', 'Tonga', NULL, NULL, NULL),
(223, 'TT', 'Trinidad and Tobago', NULL, NULL, NULL),
(224, 'TN', 'Tunisia', NULL, NULL, NULL),
(225, 'TR', 'Turkey', NULL, NULL, NULL),
(226, 'TM', 'Turkmenistan', NULL, NULL, NULL),
(227, 'TC', 'Turks and Caicos Islands', NULL, NULL, NULL),
(228, 'TV', 'Tuvalu', NULL, NULL, NULL),
(229, 'UG', 'Uganda', NULL, NULL, NULL),
(230, 'UA', 'Ukraine', NULL, NULL, NULL),
(231, 'AE', 'United Arab Emirates', NULL, NULL, NULL),
(232, 'GB', 'United Kingdom', NULL, NULL, NULL),
(233, 'US', 'United States', NULL, NULL, NULL),
(234, 'UM', 'United States minor outlying islands', NULL, NULL, NULL),
(235, 'UY', 'Uruguay', NULL, NULL, NULL),
(236, 'UZ', 'Uzbekistan', NULL, NULL, NULL),
(237, 'VU', 'Vanuatu', NULL, NULL, NULL),
(238, 'VA', 'Vatican City State', NULL, NULL, NULL),
(239, 'VE', 'Venezuela', NULL, NULL, NULL),
(240, 'VN', 'Vietnam', NULL, NULL, NULL),
(241, 'VG', 'Virgin Islands (British)', NULL, NULL, NULL),
(242, 'VI', 'Virgin Islands (U.S.)', NULL, NULL, NULL),
(243, 'WF', 'Wallis and Futuna Islands', NULL, NULL, NULL),
(244, 'EH', 'Western Sahara', NULL, NULL, NULL),
(245, 'YE', 'Yemen', NULL, NULL, NULL),
(246, 'ZR', 'Zaire', NULL, NULL, NULL),
(247, 'ZM', 'Zambia', NULL, NULL, NULL),
(248, 'ZW', 'Zimbabwe', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identification_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `town` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_comments` text COLLATE utf8mb4_unicode_ci,
  `gender_id` int(10) UNSIGNED DEFAULT NULL,
  `country_id` int(10) UNSIGNED DEFAULT NULL,
  `languages_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_how_you_meet_us_id` int(10) UNSIGNED DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `resident` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `customers`
--

INSERT INTO `customers` (`id`, `name`, `identification_number`, `birth_date`, `phone`, `address`, `town`, `postal_code`, `email`, `internal_comments`, `gender_id`, `country_id`, `languages_id`, `customer_how_you_meet_us_id`, `is_enabled`, `newsletter`, `resident`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adrian Escalada', '1', '1976-09-08 14:46:51', '697877831', 'beatriz de pinos', 'Palma de Mallorca', '00075', 'adrianescalada@gmail.com', NULL, 1, NULL, 1, NULL, 1, 1, 1, NULL, '2017-09-19 12:46:51', '2017-09-20 11:16:45'),
(2, 'Victor Valverde', '2', NULL, '697877831', NULL, NULL, NULL, 'vvalverde@refineriaweb.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, '2017-10-05 11:25:38', '2017-10-05 11:25:38'),
(3, 'Lisandro Escalada', '1', NULL, '697877831', NULL, NULL, NULL, 'lisandroescalada@gmail.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, '2017-10-09 09:10:53', '2017-10-09 09:10:53'),
(4, 'Carlos Avila', NULL, NULL, '603601441', NULL, NULL, NULL, 'web@sonamar.com', NULL, NULL, NULL, 1, NULL, 1, 1, 1, NULL, '2017-10-25 08:18:46', '2017-10-25 12:27:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers_how_you_meet_us`
--

CREATE TABLE `customers_how_you_meet_us` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers_languages`
--

CREATE TABLE `customers_languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dishes`
--

CREATE TABLE `dishes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_allergens` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vegetarian` tinyint(1) NOT NULL DEFAULT '0',
  `dishes_type_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dishes`
--

INSERT INTO `dishes` (`id`, `name`, `description_allergens`, `vegetarian`, `dishes_type_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ENTRANTE', 'Mousse de aguacate con queso crema y cobertura de “streusel” (crujiente glaseado a base de harina tamizada, azúcar y mantequilla).', 0, 1, NULL, NULL, NULL),
(2, 'SI QUIERO', 'Solomillo de Buey con parmentier de patata, mermelada de tomate, setas y flores de pensamientos', 0, 2, NULL, NULL, NULL),
(3, 'AMANECER', 'Semifrío de coco y Brownie de chocolate negro con crema de almendras y cacao', 0, 3, NULL, NULL, NULL),
(4, 'Agua', NULL, 0, 4, NULL, NULL, NULL),
(5, '⁄2 botella Vino blanco o  1⁄2 botella Vino tinto', NULL, 0, 4, NULL, NULL, NULL),
(6, 'una copa de Champagne por persona', NULL, 0, 4, NULL, NULL, NULL),
(7, 'Entrante 1', NULL, 0, 1, NULL, '2017-10-30 08:37:23', '2017-10-30 08:37:23'),
(8, 'Entrante 2', NULL, 0, 1, NULL, '2017-10-30 08:38:06', '2017-10-30 08:38:06'),
(9, 'Entrante 3', NULL, 1, 1, NULL, '2017-10-30 08:38:29', '2017-10-30 08:38:29'),
(10, 'Principal 1', NULL, 0, 2, NULL, '2017-10-30 08:38:58', '2017-10-30 08:38:58'),
(11, 'Principal 2', NULL, 0, 2, NULL, '2017-10-30 08:39:22', '2017-10-30 08:39:22'),
(12, 'Principal 3', NULL, 1, 2, NULL, '2017-10-30 08:39:56', '2017-10-30 08:39:56'),
(13, 'Dessert 1', NULL, 0, 3, NULL, '2017-10-30 08:40:25', '2017-10-30 08:40:25'),
(14, 'Dessert 2', NULL, 1, 3, NULL, '2017-10-30 08:41:01', '2017-10-30 08:41:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dishes_types`
--

CREATE TABLE `dishes_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dishes_types`
--

INSERT INTO `dishes_types` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ENTRANTES', NULL, NULL, NULL),
(2, 'PLATO PRINCIPAL', NULL, NULL, NULL),
(3, 'POSTRE', NULL, NULL, NULL),
(4, 'BEBIDAS', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dish_menu`
--

CREATE TABLE `dish_menu` (
  `menu_id` int(10) UNSIGNED NOT NULL,
  `dish_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dish_menu`
--

INSERT INTO `dish_menu` (`menu_id`, `dish_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, 1, NULL, NULL, NULL),
(3, 3, NULL, NULL, NULL),
(3, 4, NULL, NULL, NULL),
(3, 5, NULL, NULL, NULL),
(4, 1, NULL, NULL, NULL),
(4, 2, NULL, NULL, NULL),
(4, 4, NULL, NULL, NULL),
(5, 1, NULL, NULL, NULL),
(5, 3, NULL, NULL, NULL),
(5, 5, NULL, NULL, NULL),
(6, 4, NULL, NULL, NULL),
(6, 5, NULL, NULL, NULL),
(6, 7, NULL, NULL, NULL),
(6, 8, NULL, NULL, NULL),
(6, 9, NULL, NULL, NULL),
(6, 10, NULL, NULL, NULL),
(6, 11, NULL, NULL, NULL),
(6, 12, NULL, NULL, NULL),
(6, 13, NULL, NULL, NULL),
(6, 14, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genders`
--

CREATE TABLE `genders` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `genders`
--

INSERT INTO `genders` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'male', NULL, NULL, NULL),
(2, 'female', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `global_conf`
--

CREATE TABLE `global_conf` (
  `id` int(10) UNSIGNED NOT NULL,
  `amber_trigger` int(11) NOT NULL COMMENT 'Cantidad de asientos para que se muestre en naranja en el listado',
  `family_discount` decimal(10,2) NOT NULL COMMENT 'Se añadirá como descuento por familia numerosa',
  `gold_discount` decimal(10,2) NOT NULL COMMENT 'Se añadirá este descuento para clientes Gold',
  `booking_fee` decimal(10,2) NOT NULL COMMENT 'Para todos los pases se aplicará este añadido al final de todo',
  `pound_exchange` decimal(10,8) NOT NULL COMMENT 'Precio de una libra en euros',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `global_conf`
--

INSERT INTO `global_conf` (`id`, `amber_trigger`, `family_discount`, `gold_discount`, `booking_fee`, `pound_exchange`, `created_at`, `updated_at`) VALUES
(1, 10, '0.00', '0.00', '5.00', '1.18643462', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `languages`
--

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enable` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `languages`
--

INSERT INTO `languages` (`id`, `name`, `iso`, `is_enable`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'es', 'es', 1, NULL, NULL, NULL),
(2, 'en', 'en', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id`, `name`, `seat_type_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, 'VIP', 1, NULL, '2017-09-27 08:15:35', '2017-09-27 08:15:35'),
(4, 'VIP INFANTIL', 1, NULL, '2017-09-28 10:59:31', '2017-09-28 10:59:31'),
(5, 'PLATINIUM', 2, NULL, '2017-09-28 10:59:50', '2017-09-28 10:59:50'),
(6, 'Menu 1', 1, NULL, '2017-10-30 08:42:57', '2017-10-30 08:42:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2017_07_03_123101_create_language_table', 1),
(5, '2017_07_04_170108_create_table_reservations', 1),
(6, '2017_07_04_183229_create_table_reservations_tickets', 1),
(7, '2017_07_05_123221_create_customers_module_tables', 1),
(8, '2017_07_05_129322_create_shows_module_tables', 1),
(9, '2017_07_10_129322_create_promocodes_module_tables', 1),
(10, '2017_07_10_193221_create_menus_module_tables', 1),
(11, '2017_07_11_125151_create_passes_sellers_table', 1),
(12, '2017_07_11_125252_create_channels_table', 1),
(13, '2017_07_11_130059_add_channel_reservations_table', 1),
(14, '2017_08_09_129322_create_tranportists_module_tables', 1),
(15, '2017_08_17_129322_create_payments_module_tables', 1),
(16, '2017_08_17_150043_alter_reservations_add_unfinished', 2),
(20, '2017_09_07_134548_add_sort_to_showss_table', 3),
(21, '2017_09_15_120156_add_sort_to_seat_types_table', 3),
(22, '2017_09_15_120241_add_sort_to_ticket_typess_table', 3),
(23, '2017_09_18_152633_add_show_id__seatypes_id_to_cartes_table', 3),
(24, '2017_07_05_123221_create_resellers_module_tables', 4),
(25, '2017_09_20_134859_add_resellers_type_id_to_users_table', 5),
(30, '2017_09_21_082038_create_role_permissions_table', 6),
(31, '2017_09_21_083358_create_permissions_table', 6),
(32, '2017_09_27_094437_add_seat_type_id_to_menus_table', 7),
(33, '2017_09_28_144117_create_reservation_menu_table', 8),
(34, '2017_10_05_122143_add_customer_id_to_reservationss_table', 9),
(35, '2017_10_06_115721_add_cancel_date_reason_to_reservationss_table', 10),
(36, '2017_10_09_095026_add_reseller_id_to_reservationss_table', 11),
(38, '2017_10_09_122707_create_Viewreservations_table', 12),
(40, '2017_10_10_090200_add_reconcile_to_reservationss_table', 13),
(41, '2017_10_10_083236_create_SpReservationsAvailability_table', 14),
(43, '2017_10_10_124502_create_Global_Conf_table', 15),
(44, '2017_10_20_095326_add_user_id_to_payment_method_reservation_table', 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passes`
--

CREATE TABLE `passes` (
  `id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_sale` tinyint(1) NOT NULL DEFAULT '1',
  `canceled_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `passes`
--

INSERT INTO `passes` (`id`, `datetime`, `show_id`, `comment`, `on_sale`, `canceled_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(5, '2017-09-22 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:17', '2017-09-18 12:30:17'),
(6, '2017-09-23 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:18', '2017-09-18 12:30:18'),
(7, '2017-09-24 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:19', '2017-09-18 12:30:19'),
(8, '2017-09-29 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:21', '2017-09-18 12:30:21'),
(9, '2017-09-30 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:22', '2017-09-18 12:30:22'),
(10, '2017-10-01 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:23', '2017-09-18 12:30:23'),
(11, '2017-10-06 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:25', '2017-09-18 12:30:25'),
(12, '2017-10-07 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:26', '2017-09-18 12:30:26'),
(13, '2017-10-08 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:28', '2017-09-18 12:30:28'),
(14, '2017-10-13 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:29', '2017-09-18 12:30:29'),
(15, '2017-10-14 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:30', '2017-09-18 12:30:30'),
(16, '2017-10-15 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:32', '2017-09-18 12:30:32'),
(17, '2017-10-20 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:33', '2017-09-18 12:30:33'),
(18, '2017-10-21 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:35', '2017-09-18 12:30:35'),
(19, '2017-10-22 20:00:00', 1, NULL, 0, NULL, NULL, '2017-09-18 12:30:36', '2017-10-17 11:21:54'),
(20, '2017-10-27 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:37', '2017-09-18 12:30:37'),
(21, '2017-10-28 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:39', '2017-09-18 12:30:39'),
(22, '2017-10-29 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:40', '2017-09-18 12:30:40'),
(23, '2017-11-03 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:42', '2017-09-18 12:30:42'),
(24, '2017-11-04 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:43', '2017-09-18 12:30:43'),
(25, '2017-11-05 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:44', '2017-09-18 12:30:44'),
(26, '2017-11-10 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:46', '2017-09-18 12:30:46'),
(27, '2017-11-11 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:47', '2017-09-18 12:30:47'),
(28, '2017-11-12 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:49', '2017-09-18 12:30:49'),
(29, '2017-11-17 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:51', '2017-09-18 12:30:51'),
(30, '2017-11-18 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:52', '2017-09-18 12:30:52'),
(31, '2017-11-19 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:54', '2017-09-18 12:30:54'),
(32, '2017-11-24 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:56', '2017-09-18 12:30:56'),
(33, '2017-11-25 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:57', '2017-09-18 12:30:57'),
(34, '2017-11-26 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:30:59', '2017-09-18 12:30:59'),
(35, '2017-12-01 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:01', '2017-09-18 12:31:01'),
(36, '2017-12-02 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:02', '2017-09-18 12:31:02'),
(37, '2017-12-03 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:04', '2017-09-18 12:31:04'),
(38, '2017-12-08 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:05', '2017-09-18 12:31:05'),
(39, '2017-12-09 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:07', '2017-09-18 12:31:07'),
(40, '2017-12-10 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:08', '2017-09-18 12:31:08'),
(41, '2017-12-15 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:10', '2017-09-18 12:31:10'),
(42, '2017-12-16 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:11', '2017-09-18 12:31:11'),
(43, '2017-12-17 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:13', '2017-09-18 12:31:13'),
(44, '2017-12-22 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:14', '2017-09-18 12:31:14'),
(45, '2017-12-23 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:16', '2017-09-18 12:31:16'),
(46, '2017-12-24 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:18', '2017-09-18 12:31:18'),
(47, '2017-12-29 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:19', '2017-09-18 12:31:19'),
(48, '2017-12-30 20:00:00', 1, NULL, 1, NULL, NULL, '2017-09-18 12:31:21', '2017-09-18 12:31:21'),
(49, '2017-10-31 20:00:00', 3, NULL, 0, NULL, NULL, '2017-10-25 08:45:53', '2017-10-25 12:24:33'),
(50, '2017-10-31 20:00:00', 3, NULL, 1, NULL, NULL, '2017-10-25 08:46:11', '2017-10-25 08:46:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passes_prices`
--

CREATE TABLE `passes_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `pass_seat_type_id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` int(10) UNSIGNED NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `passes_prices`
--

INSERT INTO `passes_prices` (`id`, `pass_seat_type_id`, `ticket_type_id`, `price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(37, 13, 1, 100, NULL, NULL, NULL),
(38, 13, 2, 50, NULL, NULL, NULL),
(39, 13, 3, 0, NULL, NULL, NULL),
(40, 14, 1, 70, NULL, NULL, NULL),
(41, 14, 2, 45, NULL, NULL, NULL),
(42, 14, 3, 0, NULL, NULL, NULL),
(43, 15, 1, 40, NULL, NULL, NULL),
(44, 15, 2, 20, NULL, NULL, NULL),
(45, 15, 3, 0, NULL, NULL, NULL),
(46, 16, 1, 40, NULL, NULL, NULL),
(47, 16, 2, 20, NULL, NULL, NULL),
(48, 16, 3, 0, NULL, NULL, NULL),
(49, 17, 1, 100, NULL, NULL, NULL),
(50, 17, 2, 50, NULL, NULL, NULL),
(51, 17, 3, 0, NULL, NULL, NULL),
(52, 18, 1, 50, NULL, NULL, NULL),
(53, 18, 2, 35, NULL, NULL, NULL),
(54, 18, 3, 0, NULL, NULL, NULL),
(55, 19, 1, 100, NULL, NULL, NULL),
(56, 19, 2, 50, NULL, NULL, NULL),
(57, 19, 3, 0, NULL, NULL, NULL),
(58, 20, 1, 70, NULL, NULL, NULL),
(59, 20, 2, 45, NULL, NULL, NULL),
(60, 20, 3, 0, NULL, NULL, NULL),
(61, 21, 1, 40, NULL, NULL, NULL),
(62, 21, 2, 20, NULL, NULL, NULL),
(63, 21, 3, 0, NULL, NULL, NULL),
(64, 22, 1, 40, NULL, NULL, NULL),
(65, 22, 2, 20, NULL, NULL, NULL),
(66, 22, 3, 0, NULL, NULL, NULL),
(67, 23, 1, 100, NULL, NULL, NULL),
(68, 23, 2, 50, NULL, NULL, NULL),
(69, 23, 3, 0, NULL, NULL, NULL),
(70, 24, 1, 50, NULL, NULL, NULL),
(71, 24, 2, 35, NULL, NULL, NULL),
(72, 24, 3, 0, NULL, NULL, NULL),
(73, 25, 1, 100, NULL, NULL, NULL),
(74, 25, 2, 50, NULL, NULL, NULL),
(75, 25, 3, 0, NULL, NULL, NULL),
(76, 26, 1, 70, NULL, NULL, NULL),
(77, 26, 2, 45, NULL, NULL, NULL),
(78, 26, 3, 0, NULL, NULL, NULL),
(79, 27, 1, 40, NULL, NULL, NULL),
(80, 27, 2, 20, NULL, NULL, NULL),
(81, 27, 3, 0, NULL, NULL, NULL),
(82, 28, 1, 40, NULL, NULL, NULL),
(83, 28, 2, 20, NULL, NULL, NULL),
(84, 28, 3, 0, NULL, NULL, NULL),
(85, 29, 1, 100, NULL, NULL, NULL),
(86, 29, 2, 50, NULL, NULL, NULL),
(87, 29, 3, 0, NULL, NULL, NULL),
(88, 30, 1, 50, NULL, NULL, NULL),
(89, 30, 2, 35, NULL, NULL, NULL),
(90, 30, 3, 0, NULL, NULL, NULL),
(91, 31, 1, 100, NULL, NULL, NULL),
(92, 31, 2, 50, NULL, NULL, NULL),
(93, 31, 3, 0, NULL, NULL, NULL),
(94, 32, 1, 70, NULL, NULL, NULL),
(95, 32, 2, 45, NULL, NULL, NULL),
(96, 32, 3, 0, NULL, NULL, NULL),
(97, 33, 1, 40, NULL, NULL, NULL),
(98, 33, 2, 20, NULL, NULL, NULL),
(99, 33, 3, 0, NULL, NULL, NULL),
(100, 34, 1, 40, NULL, NULL, NULL),
(101, 34, 2, 20, NULL, NULL, NULL),
(102, 34, 3, 0, NULL, NULL, NULL),
(103, 35, 1, 100, NULL, NULL, NULL),
(104, 35, 2, 50, NULL, NULL, NULL),
(105, 35, 3, 0, NULL, NULL, NULL),
(106, 36, 1, 50, NULL, NULL, NULL),
(107, 36, 2, 35, NULL, NULL, NULL),
(108, 36, 3, 0, NULL, NULL, NULL),
(109, 37, 1, 100, NULL, NULL, NULL),
(110, 37, 2, 50, NULL, NULL, NULL),
(111, 37, 3, 0, NULL, NULL, NULL),
(112, 38, 1, 70, NULL, NULL, NULL),
(113, 38, 2, 45, NULL, NULL, NULL),
(114, 38, 3, 0, NULL, NULL, NULL),
(115, 39, 1, 40, NULL, NULL, NULL),
(116, 39, 2, 20, NULL, NULL, NULL),
(117, 39, 3, 0, NULL, NULL, NULL),
(118, 40, 1, 40, NULL, NULL, NULL),
(119, 40, 2, 20, NULL, NULL, NULL),
(120, 40, 3, 0, NULL, NULL, NULL),
(121, 41, 1, 100, NULL, NULL, NULL),
(122, 41, 2, 50, NULL, NULL, NULL),
(123, 41, 3, 0, NULL, NULL, NULL),
(124, 42, 1, 50, NULL, NULL, NULL),
(125, 42, 2, 35, NULL, NULL, NULL),
(126, 42, 3, 0, NULL, NULL, NULL),
(127, 43, 1, 100, NULL, NULL, NULL),
(128, 43, 2, 50, NULL, NULL, NULL),
(129, 43, 3, 0, NULL, NULL, NULL),
(130, 44, 1, 70, NULL, NULL, NULL),
(131, 44, 2, 45, NULL, NULL, NULL),
(132, 44, 3, 0, NULL, NULL, NULL),
(133, 45, 1, 40, NULL, NULL, NULL),
(134, 45, 2, 20, NULL, NULL, NULL),
(135, 45, 3, 0, NULL, NULL, NULL),
(136, 46, 1, 40, NULL, NULL, NULL),
(137, 46, 2, 20, NULL, NULL, NULL),
(138, 46, 3, 0, NULL, NULL, NULL),
(139, 47, 1, 100, NULL, NULL, NULL),
(140, 47, 2, 50, NULL, NULL, NULL),
(141, 47, 3, 0, NULL, NULL, NULL),
(142, 48, 1, 50, NULL, NULL, NULL),
(143, 48, 2, 35, NULL, NULL, NULL),
(144, 48, 3, 0, NULL, NULL, NULL),
(145, 49, 1, 100, NULL, NULL, NULL),
(146, 49, 2, 50, NULL, NULL, NULL),
(147, 49, 3, 0, NULL, NULL, NULL),
(148, 50, 1, 70, NULL, NULL, NULL),
(149, 50, 2, 45, NULL, NULL, NULL),
(150, 50, 3, 0, NULL, NULL, NULL),
(151, 51, 1, 40, NULL, NULL, NULL),
(152, 51, 2, 20, NULL, NULL, NULL),
(153, 51, 3, 0, NULL, NULL, NULL),
(154, 52, 1, 40, NULL, NULL, NULL),
(155, 52, 2, 20, NULL, NULL, NULL),
(156, 52, 3, 0, NULL, NULL, NULL),
(157, 53, 1, 100, NULL, NULL, NULL),
(158, 53, 2, 50, NULL, NULL, NULL),
(159, 53, 3, 0, NULL, NULL, NULL),
(160, 54, 1, 50, NULL, NULL, NULL),
(161, 54, 2, 35, NULL, NULL, NULL),
(162, 54, 3, 0, NULL, NULL, NULL),
(163, 55, 1, 100, NULL, NULL, NULL),
(164, 55, 2, 50, NULL, NULL, NULL),
(165, 55, 3, 0, NULL, NULL, NULL),
(166, 56, 1, 70, NULL, NULL, NULL),
(167, 56, 2, 45, NULL, NULL, NULL),
(168, 56, 3, 0, NULL, NULL, NULL),
(169, 57, 1, 40, NULL, NULL, NULL),
(170, 57, 2, 20, NULL, NULL, NULL),
(171, 57, 3, 0, NULL, NULL, NULL),
(172, 58, 1, 40, NULL, NULL, NULL),
(173, 58, 2, 20, NULL, NULL, NULL),
(174, 58, 3, 0, NULL, NULL, NULL),
(175, 59, 1, 100, NULL, NULL, NULL),
(176, 59, 2, 50, NULL, NULL, NULL),
(177, 59, 3, 0, NULL, NULL, NULL),
(178, 60, 1, 50, NULL, NULL, NULL),
(179, 60, 2, 35, NULL, NULL, NULL),
(180, 60, 3, 0, NULL, NULL, NULL),
(181, 61, 1, 100, NULL, NULL, NULL),
(182, 61, 2, 50, NULL, NULL, NULL),
(183, 61, 3, 0, NULL, NULL, NULL),
(184, 62, 1, 70, NULL, NULL, NULL),
(185, 62, 2, 45, NULL, NULL, NULL),
(186, 62, 3, 0, NULL, NULL, NULL),
(187, 63, 1, 40, NULL, NULL, NULL),
(188, 63, 2, 20, NULL, NULL, NULL),
(189, 63, 3, 0, NULL, NULL, NULL),
(190, 64, 1, 40, NULL, NULL, NULL),
(191, 64, 2, 20, NULL, NULL, NULL),
(192, 64, 3, 0, NULL, NULL, NULL),
(193, 65, 1, 100, NULL, NULL, NULL),
(194, 65, 2, 50, NULL, NULL, NULL),
(195, 65, 3, 0, NULL, NULL, NULL),
(196, 66, 1, 50, NULL, NULL, NULL),
(197, 66, 2, 35, NULL, NULL, NULL),
(198, 66, 3, 0, NULL, NULL, NULL),
(199, 67, 1, 100, NULL, NULL, NULL),
(200, 67, 2, 50, NULL, NULL, NULL),
(201, 67, 3, 0, NULL, NULL, NULL),
(202, 68, 1, 70, NULL, NULL, NULL),
(203, 68, 2, 45, NULL, NULL, NULL),
(204, 68, 3, 0, NULL, NULL, NULL),
(205, 69, 1, 40, NULL, NULL, NULL),
(206, 69, 2, 20, NULL, NULL, NULL),
(207, 69, 3, 0, NULL, NULL, NULL),
(208, 70, 1, 40, NULL, NULL, NULL),
(209, 70, 2, 20, NULL, NULL, NULL),
(210, 70, 3, 0, NULL, NULL, NULL),
(211, 71, 1, 100, NULL, NULL, NULL),
(212, 71, 2, 50, NULL, NULL, NULL),
(213, 71, 3, 0, NULL, NULL, NULL),
(214, 72, 1, 50, NULL, NULL, NULL),
(215, 72, 2, 35, NULL, NULL, NULL),
(216, 72, 3, 0, NULL, NULL, NULL),
(217, 73, 1, 100, NULL, NULL, NULL),
(218, 73, 2, 50, NULL, NULL, NULL),
(219, 73, 3, 0, NULL, NULL, NULL),
(220, 74, 1, 70, NULL, NULL, NULL),
(221, 74, 2, 45, NULL, NULL, NULL),
(222, 74, 3, 0, NULL, NULL, NULL),
(223, 75, 1, 40, NULL, NULL, NULL),
(224, 75, 2, 20, NULL, NULL, NULL),
(225, 75, 3, 0, NULL, NULL, NULL),
(226, 76, 1, 40, NULL, NULL, NULL),
(227, 76, 2, 20, NULL, NULL, NULL),
(228, 76, 3, 0, NULL, NULL, NULL),
(229, 77, 1, 100, NULL, NULL, NULL),
(230, 77, 2, 50, NULL, NULL, NULL),
(231, 77, 3, 0, NULL, NULL, NULL),
(232, 78, 1, 50, NULL, NULL, NULL),
(233, 78, 2, 35, NULL, NULL, NULL),
(234, 78, 3, 0, NULL, NULL, NULL),
(235, 79, 1, 100, NULL, NULL, NULL),
(236, 79, 2, 50, NULL, NULL, NULL),
(237, 79, 3, 0, NULL, NULL, NULL),
(238, 80, 1, 70, NULL, NULL, NULL),
(239, 80, 2, 45, NULL, NULL, NULL),
(240, 80, 3, 0, NULL, NULL, NULL),
(241, 81, 1, 40, NULL, NULL, NULL),
(242, 81, 2, 20, NULL, NULL, NULL),
(243, 81, 3, 0, NULL, NULL, NULL),
(244, 82, 1, 40, NULL, NULL, NULL),
(245, 82, 2, 20, NULL, NULL, NULL),
(246, 82, 3, 0, NULL, NULL, NULL),
(247, 83, 1, 100, NULL, NULL, NULL),
(248, 83, 2, 50, NULL, NULL, NULL),
(249, 83, 3, 0, NULL, NULL, NULL),
(250, 84, 1, 50, NULL, NULL, NULL),
(251, 84, 2, 35, NULL, NULL, NULL),
(252, 84, 3, 0, NULL, NULL, NULL),
(253, 85, 1, 100, NULL, NULL, NULL),
(254, 85, 2, 50, NULL, NULL, NULL),
(255, 85, 3, 0, NULL, NULL, NULL),
(256, 86, 1, 70, NULL, NULL, NULL),
(257, 86, 2, 45, NULL, NULL, NULL),
(258, 86, 3, 0, NULL, NULL, NULL),
(259, 87, 1, 40, NULL, NULL, NULL),
(260, 87, 2, 20, NULL, NULL, NULL),
(261, 87, 3, 0, NULL, NULL, NULL),
(262, 88, 1, 40, NULL, NULL, NULL),
(263, 88, 2, 20, NULL, NULL, NULL),
(264, 88, 3, 0, NULL, NULL, NULL),
(265, 89, 1, 100, NULL, NULL, NULL),
(266, 89, 2, 50, NULL, NULL, NULL),
(267, 89, 3, 0, NULL, NULL, NULL),
(268, 90, 1, 50, NULL, NULL, NULL),
(269, 90, 2, 35, NULL, NULL, NULL),
(270, 90, 3, 0, NULL, NULL, NULL),
(271, 91, 1, 100, NULL, NULL, NULL),
(272, 91, 2, 50, NULL, NULL, NULL),
(273, 91, 3, 0, NULL, NULL, NULL),
(274, 92, 1, 70, NULL, NULL, NULL),
(275, 92, 2, 45, NULL, NULL, NULL),
(276, 92, 3, 0, NULL, NULL, NULL),
(277, 93, 1, 40, NULL, NULL, NULL),
(278, 93, 2, 20, NULL, NULL, NULL),
(279, 93, 3, 0, NULL, NULL, NULL),
(280, 94, 1, 40, NULL, NULL, NULL),
(281, 94, 2, 20, NULL, NULL, NULL),
(282, 94, 3, 0, NULL, NULL, NULL),
(283, 95, 1, 100, NULL, NULL, NULL),
(284, 95, 2, 50, NULL, NULL, NULL),
(285, 95, 3, 0, NULL, NULL, NULL),
(286, 96, 1, 50, NULL, NULL, NULL),
(287, 96, 2, 35, NULL, NULL, NULL),
(288, 96, 3, 0, NULL, NULL, NULL),
(289, 97, 1, 100, NULL, NULL, NULL),
(290, 97, 2, 50, NULL, NULL, NULL),
(291, 97, 3, 0, NULL, NULL, NULL),
(292, 98, 1, 70, NULL, NULL, NULL),
(293, 98, 2, 45, NULL, NULL, NULL),
(294, 98, 3, 0, NULL, NULL, NULL),
(295, 99, 1, 40, NULL, NULL, NULL),
(296, 99, 2, 20, NULL, NULL, NULL),
(297, 99, 3, 0, NULL, NULL, NULL),
(298, 100, 1, 40, NULL, NULL, NULL),
(299, 100, 2, 20, NULL, NULL, NULL),
(300, 100, 3, 0, NULL, NULL, NULL),
(301, 101, 1, 100, NULL, NULL, NULL),
(302, 101, 2, 50, NULL, NULL, NULL),
(303, 101, 3, 0, NULL, NULL, NULL),
(304, 102, 1, 50, NULL, NULL, NULL),
(305, 102, 2, 35, NULL, NULL, NULL),
(306, 102, 3, 0, NULL, NULL, NULL),
(307, 103, 1, 100, NULL, NULL, NULL),
(308, 103, 2, 50, NULL, NULL, NULL),
(309, 103, 3, 0, NULL, NULL, NULL),
(310, 104, 1, 70, NULL, NULL, NULL),
(311, 104, 2, 45, NULL, NULL, NULL),
(312, 104, 3, 0, NULL, NULL, NULL),
(313, 105, 1, 40, NULL, NULL, NULL),
(314, 105, 2, 20, NULL, NULL, NULL),
(315, 105, 3, 0, NULL, NULL, NULL),
(316, 106, 1, 40, NULL, NULL, NULL),
(317, 106, 2, 20, NULL, NULL, NULL),
(318, 106, 3, 0, NULL, NULL, NULL),
(319, 107, 1, 100, NULL, NULL, NULL),
(320, 107, 2, 50, NULL, NULL, NULL),
(321, 107, 3, 0, NULL, NULL, NULL),
(322, 108, 1, 50, NULL, NULL, NULL),
(323, 108, 2, 35, NULL, NULL, NULL),
(324, 108, 3, 0, NULL, NULL, NULL),
(325, 109, 1, 100, NULL, NULL, NULL),
(326, 109, 2, 50, NULL, NULL, NULL),
(327, 109, 3, 0, NULL, NULL, NULL),
(328, 110, 1, 70, NULL, NULL, NULL),
(329, 110, 2, 45, NULL, NULL, NULL),
(330, 110, 3, 0, NULL, NULL, NULL),
(331, 111, 1, 40, NULL, NULL, NULL),
(332, 111, 2, 20, NULL, NULL, NULL),
(333, 111, 3, 0, NULL, NULL, NULL),
(334, 112, 1, 40, NULL, NULL, NULL),
(335, 112, 2, 20, NULL, NULL, NULL),
(336, 112, 3, 0, NULL, NULL, NULL),
(337, 113, 1, 100, NULL, NULL, NULL),
(338, 113, 2, 50, NULL, NULL, NULL),
(339, 113, 3, 0, NULL, NULL, NULL),
(340, 114, 1, 50, NULL, NULL, NULL),
(341, 114, 2, 35, NULL, NULL, NULL),
(342, 114, 3, 0, NULL, NULL, NULL),
(343, 115, 1, 100, NULL, NULL, NULL),
(344, 115, 2, 50, NULL, NULL, NULL),
(345, 115, 3, 0, NULL, NULL, NULL),
(346, 116, 1, 70, NULL, NULL, NULL),
(347, 116, 2, 45, NULL, NULL, NULL),
(348, 116, 3, 0, NULL, NULL, NULL),
(349, 117, 1, 40, NULL, NULL, NULL),
(350, 117, 2, 20, NULL, NULL, NULL),
(351, 117, 3, 0, NULL, NULL, NULL),
(352, 118, 1, 40, NULL, NULL, NULL),
(353, 118, 2, 20, NULL, NULL, NULL),
(354, 118, 3, 0, NULL, NULL, NULL),
(355, 119, 1, 100, NULL, NULL, NULL),
(356, 119, 2, 50, NULL, NULL, NULL),
(357, 119, 3, 0, NULL, NULL, NULL),
(358, 120, 1, 50, NULL, NULL, NULL),
(359, 120, 2, 35, NULL, NULL, NULL),
(360, 120, 3, 0, NULL, NULL, NULL),
(361, 121, 1, 100, NULL, NULL, NULL),
(362, 121, 2, 50, NULL, NULL, NULL),
(363, 121, 3, 0, NULL, NULL, NULL),
(364, 122, 1, 70, NULL, NULL, NULL),
(365, 122, 2, 45, NULL, NULL, NULL),
(366, 122, 3, 0, NULL, NULL, NULL),
(367, 123, 1, 40, NULL, NULL, NULL),
(368, 123, 2, 20, NULL, NULL, NULL),
(369, 123, 3, 0, NULL, NULL, NULL),
(370, 124, 1, 40, NULL, NULL, NULL),
(371, 124, 2, 20, NULL, NULL, NULL),
(372, 124, 3, 0, NULL, NULL, NULL),
(373, 125, 1, 100, NULL, NULL, NULL),
(374, 125, 2, 50, NULL, NULL, NULL),
(375, 125, 3, 0, NULL, NULL, NULL),
(376, 126, 1, 50, NULL, NULL, NULL),
(377, 126, 2, 35, NULL, NULL, NULL),
(378, 126, 3, 0, NULL, NULL, NULL),
(379, 127, 1, 100, NULL, NULL, NULL),
(380, 127, 2, 50, NULL, NULL, NULL),
(381, 127, 3, 0, NULL, NULL, NULL),
(382, 128, 1, 70, NULL, NULL, NULL),
(383, 128, 2, 45, NULL, NULL, NULL),
(384, 128, 3, 0, NULL, NULL, NULL),
(385, 129, 1, 40, NULL, NULL, NULL),
(386, 129, 2, 20, NULL, NULL, NULL),
(387, 129, 3, 0, NULL, NULL, NULL),
(388, 130, 1, 40, NULL, NULL, NULL),
(389, 130, 2, 20, NULL, NULL, NULL),
(390, 130, 3, 0, NULL, NULL, NULL),
(391, 131, 1, 100, NULL, NULL, NULL),
(392, 131, 2, 50, NULL, NULL, NULL),
(393, 131, 3, 0, NULL, NULL, NULL),
(394, 132, 1, 50, NULL, NULL, NULL),
(395, 132, 2, 35, NULL, NULL, NULL),
(396, 132, 3, 0, NULL, NULL, NULL),
(397, 133, 1, 100, NULL, NULL, NULL),
(398, 133, 2, 50, NULL, NULL, NULL),
(399, 133, 3, 0, NULL, NULL, NULL),
(400, 134, 1, 70, NULL, NULL, NULL),
(401, 134, 2, 45, NULL, NULL, NULL),
(402, 134, 3, 0, NULL, NULL, NULL),
(403, 135, 1, 40, NULL, NULL, NULL),
(404, 135, 2, 20, NULL, NULL, NULL),
(405, 135, 3, 0, NULL, NULL, NULL),
(406, 136, 1, 40, NULL, NULL, NULL),
(407, 136, 2, 20, NULL, NULL, NULL),
(408, 136, 3, 0, NULL, NULL, NULL),
(409, 137, 1, 100, NULL, NULL, NULL),
(410, 137, 2, 50, NULL, NULL, NULL),
(411, 137, 3, 0, NULL, NULL, NULL),
(412, 138, 1, 50, NULL, NULL, NULL),
(413, 138, 2, 35, NULL, NULL, NULL),
(414, 138, 3, 0, NULL, NULL, NULL),
(415, 139, 1, 100, NULL, NULL, NULL),
(416, 139, 2, 50, NULL, NULL, NULL),
(417, 139, 3, 0, NULL, NULL, NULL),
(418, 140, 1, 70, NULL, NULL, NULL),
(419, 140, 2, 45, NULL, NULL, NULL),
(420, 140, 3, 0, NULL, NULL, NULL),
(421, 141, 1, 40, NULL, NULL, NULL),
(422, 141, 2, 20, NULL, NULL, NULL),
(423, 141, 3, 0, NULL, NULL, NULL),
(424, 142, 1, 40, NULL, NULL, NULL),
(425, 142, 2, 20, NULL, NULL, NULL),
(426, 142, 3, 0, NULL, NULL, NULL),
(427, 143, 1, 100, NULL, NULL, NULL),
(428, 143, 2, 50, NULL, NULL, NULL),
(429, 143, 3, 0, NULL, NULL, NULL),
(430, 144, 1, 50, NULL, NULL, NULL),
(431, 144, 2, 35, NULL, NULL, NULL),
(432, 144, 3, 0, NULL, NULL, NULL),
(433, 145, 1, 100, NULL, NULL, NULL),
(434, 145, 2, 50, NULL, NULL, NULL),
(435, 145, 3, 0, NULL, NULL, NULL),
(436, 146, 1, 70, NULL, NULL, NULL),
(437, 146, 2, 45, NULL, NULL, NULL),
(438, 146, 3, 0, NULL, NULL, NULL),
(439, 147, 1, 40, NULL, NULL, NULL),
(440, 147, 2, 20, NULL, NULL, NULL),
(441, 147, 3, 0, NULL, NULL, NULL),
(442, 148, 1, 40, NULL, NULL, NULL),
(443, 148, 2, 20, NULL, NULL, NULL),
(444, 148, 3, 0, NULL, NULL, NULL),
(445, 149, 1, 100, NULL, NULL, NULL),
(446, 149, 2, 50, NULL, NULL, NULL),
(447, 149, 3, 0, NULL, NULL, NULL),
(448, 150, 1, 50, NULL, NULL, NULL),
(449, 150, 2, 35, NULL, NULL, NULL),
(450, 150, 3, 0, NULL, NULL, NULL),
(451, 151, 1, 100, NULL, NULL, NULL),
(452, 151, 2, 50, NULL, NULL, NULL),
(453, 151, 3, 0, NULL, NULL, NULL),
(454, 152, 1, 70, NULL, NULL, NULL),
(455, 152, 2, 45, NULL, NULL, NULL),
(456, 152, 3, 0, NULL, NULL, NULL),
(457, 153, 1, 40, NULL, NULL, NULL),
(458, 153, 2, 20, NULL, NULL, NULL),
(459, 153, 3, 0, NULL, NULL, NULL),
(460, 154, 1, 40, NULL, NULL, NULL),
(461, 154, 2, 20, NULL, NULL, NULL),
(462, 154, 3, 0, NULL, NULL, NULL),
(463, 155, 1, 100, NULL, NULL, NULL),
(464, 155, 2, 50, NULL, NULL, NULL),
(465, 155, 3, 0, NULL, NULL, NULL),
(466, 156, 1, 50, NULL, NULL, NULL),
(467, 156, 2, 35, NULL, NULL, NULL),
(468, 156, 3, 0, NULL, NULL, NULL),
(469, 157, 1, 100, NULL, NULL, NULL),
(470, 157, 2, 50, NULL, NULL, NULL),
(471, 157, 3, 0, NULL, NULL, NULL),
(472, 158, 1, 70, NULL, NULL, NULL),
(473, 158, 2, 45, NULL, NULL, NULL),
(474, 158, 3, 0, NULL, NULL, NULL),
(475, 159, 1, 40, NULL, NULL, NULL),
(476, 159, 2, 20, NULL, NULL, NULL),
(477, 159, 3, 0, NULL, NULL, NULL),
(478, 160, 1, 40, NULL, NULL, NULL),
(479, 160, 2, 20, NULL, NULL, NULL),
(480, 160, 3, 0, NULL, NULL, NULL),
(481, 161, 1, 100, NULL, NULL, NULL),
(482, 161, 2, 50, NULL, NULL, NULL),
(483, 161, 3, 0, NULL, NULL, NULL),
(484, 162, 1, 50, NULL, NULL, NULL),
(485, 162, 2, 35, NULL, NULL, NULL),
(486, 162, 3, 0, NULL, NULL, NULL),
(487, 163, 1, 100, NULL, NULL, NULL),
(488, 163, 2, 50, NULL, NULL, NULL),
(489, 163, 3, 0, NULL, NULL, NULL),
(490, 164, 1, 70, NULL, NULL, NULL),
(491, 164, 2, 45, NULL, NULL, NULL),
(492, 164, 3, 0, NULL, NULL, NULL),
(493, 165, 1, 40, NULL, NULL, NULL),
(494, 165, 2, 20, NULL, NULL, NULL),
(495, 165, 3, 0, NULL, NULL, NULL),
(496, 166, 1, 40, NULL, NULL, NULL),
(497, 166, 2, 20, NULL, NULL, NULL),
(498, 166, 3, 0, NULL, NULL, NULL),
(499, 167, 1, 100, NULL, NULL, NULL),
(500, 167, 2, 50, NULL, NULL, NULL),
(501, 167, 3, 0, NULL, NULL, NULL),
(502, 168, 1, 50, NULL, NULL, NULL),
(503, 168, 2, 35, NULL, NULL, NULL),
(504, 168, 3, 0, NULL, NULL, NULL),
(505, 169, 1, 100, NULL, NULL, NULL),
(506, 169, 2, 50, NULL, NULL, NULL),
(507, 169, 3, 0, NULL, NULL, NULL),
(508, 170, 1, 70, NULL, NULL, NULL),
(509, 170, 2, 45, NULL, NULL, NULL),
(510, 170, 3, 0, NULL, NULL, NULL),
(511, 171, 1, 40, NULL, NULL, NULL),
(512, 171, 2, 20, NULL, NULL, NULL),
(513, 171, 3, 0, NULL, NULL, NULL),
(514, 172, 1, 40, NULL, NULL, NULL),
(515, 172, 2, 20, NULL, NULL, NULL),
(516, 172, 3, 0, NULL, NULL, NULL),
(517, 173, 1, 100, NULL, NULL, NULL),
(518, 173, 2, 50, NULL, NULL, NULL),
(519, 173, 3, 0, NULL, NULL, NULL),
(520, 174, 1, 50, NULL, NULL, NULL),
(521, 174, 2, 35, NULL, NULL, NULL),
(522, 174, 3, 0, NULL, NULL, NULL),
(523, 175, 1, 100, NULL, NULL, NULL),
(524, 175, 2, 50, NULL, NULL, NULL),
(525, 175, 3, 0, NULL, NULL, NULL),
(526, 176, 1, 70, NULL, NULL, NULL),
(527, 176, 2, 45, NULL, NULL, NULL),
(528, 176, 3, 0, NULL, NULL, NULL),
(529, 177, 1, 40, NULL, NULL, NULL),
(530, 177, 2, 20, NULL, NULL, NULL),
(531, 177, 3, 0, NULL, NULL, NULL),
(532, 178, 1, 40, NULL, NULL, NULL),
(533, 178, 2, 20, NULL, NULL, NULL),
(534, 178, 3, 0, NULL, NULL, NULL),
(535, 179, 1, 100, NULL, NULL, NULL),
(536, 179, 2, 50, NULL, NULL, NULL),
(537, 179, 3, 0, NULL, NULL, NULL),
(538, 180, 1, 50, NULL, NULL, NULL),
(539, 180, 2, 35, NULL, NULL, NULL),
(540, 180, 3, 0, NULL, NULL, NULL),
(541, 181, 1, 100, NULL, NULL, NULL),
(542, 181, 2, 50, NULL, NULL, NULL),
(543, 181, 3, 0, NULL, NULL, NULL),
(544, 182, 1, 70, NULL, NULL, NULL),
(545, 182, 2, 45, NULL, NULL, NULL),
(546, 182, 3, 0, NULL, NULL, NULL),
(547, 183, 1, 40, NULL, NULL, NULL),
(548, 183, 2, 20, NULL, NULL, NULL),
(549, 183, 3, 0, NULL, NULL, NULL),
(550, 184, 1, 40, NULL, NULL, NULL),
(551, 184, 2, 20, NULL, NULL, NULL),
(552, 184, 3, 0, NULL, NULL, NULL),
(553, 185, 1, 100, NULL, NULL, NULL),
(554, 185, 2, 50, NULL, NULL, NULL),
(555, 185, 3, 0, NULL, NULL, NULL),
(556, 186, 1, 50, NULL, NULL, NULL),
(557, 186, 2, 35, NULL, NULL, NULL),
(558, 186, 3, 0, NULL, NULL, NULL),
(559, 187, 1, 100, NULL, NULL, NULL),
(560, 187, 2, 50, NULL, NULL, NULL),
(561, 187, 3, 0, NULL, NULL, NULL),
(562, 188, 1, 70, NULL, NULL, NULL),
(563, 188, 2, 45, NULL, NULL, NULL),
(564, 188, 3, 0, NULL, NULL, NULL),
(565, 189, 1, 40, NULL, NULL, NULL),
(566, 189, 2, 20, NULL, NULL, NULL),
(567, 189, 3, 0, NULL, NULL, NULL),
(568, 190, 1, 40, NULL, NULL, NULL),
(569, 190, 2, 20, NULL, NULL, NULL),
(570, 190, 3, 0, NULL, NULL, NULL),
(571, 191, 1, 100, NULL, NULL, NULL),
(572, 191, 2, 50, NULL, NULL, NULL),
(573, 191, 3, 0, NULL, NULL, NULL),
(574, 192, 1, 50, NULL, NULL, NULL),
(575, 192, 2, 35, NULL, NULL, NULL),
(576, 192, 3, 0, NULL, NULL, NULL),
(577, 193, 1, 100, NULL, NULL, NULL),
(578, 193, 2, 50, NULL, NULL, NULL),
(579, 193, 3, 0, NULL, NULL, NULL),
(580, 194, 1, 70, NULL, NULL, NULL),
(581, 194, 2, 45, NULL, NULL, NULL),
(582, 194, 3, 0, NULL, NULL, NULL),
(583, 195, 1, 40, NULL, NULL, NULL),
(584, 195, 2, 20, NULL, NULL, NULL),
(585, 195, 3, 0, NULL, NULL, NULL),
(586, 196, 1, 40, NULL, NULL, NULL),
(587, 196, 2, 20, NULL, NULL, NULL),
(588, 196, 3, 0, NULL, NULL, NULL),
(589, 197, 1, 100, NULL, NULL, NULL),
(590, 197, 2, 50, NULL, NULL, NULL),
(591, 197, 3, 0, NULL, NULL, NULL),
(592, 198, 1, 50, NULL, NULL, NULL),
(593, 198, 2, 35, NULL, NULL, NULL),
(594, 198, 3, 0, NULL, NULL, NULL),
(595, 199, 1, 100, NULL, NULL, NULL),
(596, 199, 2, 50, NULL, NULL, NULL),
(597, 199, 3, 0, NULL, NULL, NULL),
(598, 200, 1, 70, NULL, NULL, NULL),
(599, 200, 2, 45, NULL, NULL, NULL),
(600, 200, 3, 0, NULL, NULL, NULL),
(601, 201, 1, 40, NULL, NULL, NULL),
(602, 201, 2, 20, NULL, NULL, NULL),
(603, 201, 3, 0, NULL, NULL, NULL),
(604, 202, 1, 40, NULL, NULL, NULL),
(605, 202, 2, 20, NULL, NULL, NULL),
(606, 202, 3, 0, NULL, NULL, NULL),
(607, 203, 1, 100, NULL, NULL, NULL),
(608, 203, 2, 50, NULL, NULL, NULL),
(609, 203, 3, 0, NULL, NULL, NULL),
(610, 204, 1, 50, NULL, NULL, NULL),
(611, 204, 2, 35, NULL, NULL, NULL),
(612, 204, 3, 0, NULL, NULL, NULL),
(613, 205, 1, 100, NULL, NULL, NULL),
(614, 205, 2, 50, NULL, NULL, NULL),
(615, 205, 3, 0, NULL, NULL, NULL),
(616, 206, 1, 70, NULL, NULL, NULL),
(617, 206, 2, 45, NULL, NULL, NULL),
(618, 206, 3, 0, NULL, NULL, NULL),
(619, 207, 1, 40, NULL, NULL, NULL),
(620, 207, 2, 20, NULL, NULL, NULL),
(621, 207, 3, 0, NULL, NULL, NULL),
(622, 208, 1, 40, NULL, NULL, NULL),
(623, 208, 2, 20, NULL, NULL, NULL),
(624, 208, 3, 0, NULL, NULL, NULL),
(625, 209, 1, 100, NULL, NULL, NULL),
(626, 209, 2, 50, NULL, NULL, NULL),
(627, 209, 3, 0, NULL, NULL, NULL),
(628, 210, 1, 50, NULL, NULL, NULL),
(629, 210, 2, 35, NULL, NULL, NULL),
(630, 210, 3, 0, NULL, NULL, NULL),
(631, 211, 1, 100, NULL, NULL, NULL),
(632, 211, 2, 50, NULL, NULL, NULL),
(633, 211, 3, 0, NULL, NULL, NULL),
(634, 212, 1, 70, NULL, NULL, NULL),
(635, 212, 2, 45, NULL, NULL, NULL),
(636, 212, 3, 0, NULL, NULL, NULL),
(637, 213, 1, 40, NULL, NULL, NULL),
(638, 213, 2, 20, NULL, NULL, NULL),
(639, 213, 3, 0, NULL, NULL, NULL),
(640, 214, 1, 40, NULL, NULL, NULL),
(641, 214, 2, 20, NULL, NULL, NULL),
(642, 214, 3, 0, NULL, NULL, NULL),
(643, 215, 1, 100, NULL, NULL, NULL),
(644, 215, 2, 50, NULL, NULL, NULL),
(645, 215, 3, 0, NULL, NULL, NULL),
(646, 216, 1, 50, NULL, NULL, NULL),
(647, 216, 2, 35, NULL, NULL, NULL),
(648, 216, 3, 0, NULL, NULL, NULL),
(649, 217, 1, 100, NULL, NULL, NULL),
(650, 217, 2, 50, NULL, NULL, NULL),
(651, 217, 3, 0, NULL, NULL, NULL),
(652, 218, 1, 70, NULL, NULL, NULL),
(653, 218, 2, 45, NULL, NULL, NULL),
(654, 218, 3, 0, NULL, NULL, NULL),
(655, 219, 1, 40, NULL, NULL, NULL),
(656, 219, 2, 20, NULL, NULL, NULL),
(657, 219, 3, 0, NULL, NULL, NULL),
(658, 220, 1, 40, NULL, NULL, NULL),
(659, 220, 2, 20, NULL, NULL, NULL),
(660, 220, 3, 0, NULL, NULL, NULL),
(661, 221, 1, 100, NULL, NULL, NULL),
(662, 221, 2, 50, NULL, NULL, NULL),
(663, 221, 3, 0, NULL, NULL, NULL),
(664, 222, 1, 50, NULL, NULL, NULL),
(665, 222, 2, 35, NULL, NULL, NULL),
(666, 222, 3, 0, NULL, NULL, NULL),
(667, 223, 1, 100, NULL, NULL, NULL),
(668, 223, 2, 50, NULL, NULL, NULL),
(669, 223, 3, 0, NULL, NULL, NULL),
(670, 224, 1, 70, NULL, NULL, NULL),
(671, 224, 2, 45, NULL, NULL, NULL),
(672, 224, 3, 0, NULL, NULL, NULL),
(673, 225, 1, 40, NULL, NULL, NULL),
(674, 225, 2, 20, NULL, NULL, NULL),
(675, 225, 3, 0, NULL, NULL, NULL),
(676, 226, 1, 40, NULL, NULL, NULL),
(677, 226, 2, 20, NULL, NULL, NULL),
(678, 226, 3, 0, NULL, NULL, NULL),
(679, 227, 1, 100, NULL, NULL, NULL),
(680, 227, 2, 50, NULL, NULL, NULL),
(681, 227, 3, 0, NULL, NULL, NULL),
(682, 228, 1, 50, NULL, NULL, NULL),
(683, 228, 2, 35, NULL, NULL, NULL),
(684, 228, 3, 0, NULL, NULL, NULL),
(685, 229, 1, 100, NULL, NULL, NULL),
(686, 229, 2, 50, NULL, NULL, NULL),
(687, 229, 3, 0, NULL, NULL, NULL),
(688, 230, 1, 70, NULL, NULL, NULL),
(689, 230, 2, 45, NULL, NULL, NULL),
(690, 230, 3, 0, NULL, NULL, NULL),
(691, 231, 1, 40, NULL, NULL, NULL),
(692, 231, 2, 20, NULL, NULL, NULL),
(693, 231, 3, 0, NULL, NULL, NULL),
(694, 232, 1, 40, NULL, NULL, NULL),
(695, 232, 2, 20, NULL, NULL, NULL),
(696, 232, 3, 0, NULL, NULL, NULL),
(697, 233, 1, 100, NULL, NULL, NULL),
(698, 233, 2, 50, NULL, NULL, NULL),
(699, 233, 3, 0, NULL, NULL, NULL),
(700, 234, 1, 50, NULL, NULL, NULL),
(701, 234, 2, 35, NULL, NULL, NULL),
(702, 234, 3, 0, NULL, NULL, NULL),
(703, 235, 1, 100, NULL, NULL, NULL),
(704, 235, 2, 50, NULL, NULL, NULL),
(705, 235, 3, 0, NULL, NULL, NULL),
(706, 236, 1, 70, NULL, NULL, NULL),
(707, 236, 2, 45, NULL, NULL, NULL),
(708, 236, 3, 0, NULL, NULL, NULL),
(709, 237, 1, 40, NULL, NULL, NULL),
(710, 237, 2, 20, NULL, NULL, NULL),
(711, 237, 3, 0, NULL, NULL, NULL),
(712, 238, 1, 40, NULL, NULL, NULL),
(713, 238, 2, 20, NULL, NULL, NULL),
(714, 238, 3, 0, NULL, NULL, NULL),
(715, 239, 1, 100, NULL, NULL, NULL),
(716, 239, 2, 50, NULL, NULL, NULL),
(717, 239, 3, 0, NULL, NULL, NULL),
(718, 240, 1, 50, NULL, NULL, NULL),
(719, 240, 2, 35, NULL, NULL, NULL),
(720, 240, 3, 0, NULL, NULL, NULL),
(721, 241, 1, 100, NULL, NULL, NULL),
(722, 241, 2, 50, NULL, NULL, NULL),
(723, 241, 3, 0, NULL, NULL, NULL),
(724, 242, 1, 70, NULL, NULL, NULL),
(725, 242, 2, 45, NULL, NULL, NULL),
(726, 242, 3, 0, NULL, NULL, NULL),
(727, 243, 1, 40, NULL, NULL, NULL),
(728, 243, 2, 20, NULL, NULL, NULL),
(729, 243, 3, 0, NULL, NULL, NULL),
(730, 244, 1, 40, NULL, NULL, NULL),
(731, 244, 2, 20, NULL, NULL, NULL),
(732, 244, 3, 0, NULL, NULL, NULL),
(733, 245, 1, 100, NULL, NULL, NULL),
(734, 245, 2, 50, NULL, NULL, NULL),
(735, 245, 3, 0, NULL, NULL, NULL),
(736, 246, 1, 50, NULL, NULL, NULL),
(737, 246, 2, 35, NULL, NULL, NULL),
(738, 246, 3, 0, NULL, NULL, NULL),
(739, 247, 1, 100, NULL, NULL, NULL),
(740, 247, 2, 50, NULL, NULL, NULL),
(741, 247, 3, 0, NULL, NULL, NULL),
(742, 248, 1, 70, NULL, NULL, NULL),
(743, 248, 2, 45, NULL, NULL, NULL),
(744, 248, 3, 0, NULL, NULL, NULL),
(745, 249, 1, 40, NULL, NULL, NULL),
(746, 249, 2, 20, NULL, NULL, NULL),
(747, 249, 3, 0, NULL, NULL, NULL),
(748, 250, 1, 40, NULL, NULL, NULL),
(749, 250, 2, 20, NULL, NULL, NULL),
(750, 250, 3, 0, NULL, NULL, NULL),
(751, 251, 1, 100, NULL, NULL, NULL),
(752, 251, 2, 50, NULL, NULL, NULL),
(753, 251, 3, 0, NULL, NULL, NULL),
(754, 252, 1, 50, NULL, NULL, NULL),
(755, 252, 2, 35, NULL, NULL, NULL),
(756, 252, 3, 0, NULL, NULL, NULL),
(757, 253, 1, 100, NULL, NULL, NULL),
(758, 253, 2, 50, NULL, NULL, NULL),
(759, 253, 3, 0, NULL, NULL, NULL),
(760, 254, 1, 70, NULL, NULL, NULL),
(761, 254, 2, 45, NULL, NULL, NULL),
(762, 254, 3, 0, NULL, NULL, NULL),
(763, 255, 1, 40, NULL, NULL, NULL),
(764, 255, 2, 20, NULL, NULL, NULL),
(765, 255, 3, 0, NULL, NULL, NULL),
(766, 256, 1, 40, NULL, NULL, NULL),
(767, 256, 2, 20, NULL, NULL, NULL),
(768, 256, 3, 0, NULL, NULL, NULL),
(769, 257, 1, 100, NULL, NULL, NULL),
(770, 257, 2, 50, NULL, NULL, NULL),
(771, 257, 3, 0, NULL, NULL, NULL),
(772, 258, 1, 50, NULL, NULL, NULL),
(773, 258, 2, 35, NULL, NULL, NULL),
(774, 258, 3, 0, NULL, NULL, NULL),
(775, 259, 1, 100, NULL, NULL, NULL),
(776, 259, 2, 50, NULL, NULL, NULL),
(777, 259, 3, 0, NULL, NULL, NULL),
(778, 260, 1, 70, NULL, NULL, NULL),
(779, 260, 2, 45, NULL, NULL, NULL),
(780, 260, 3, 0, NULL, NULL, NULL),
(781, 261, 1, 40, NULL, NULL, NULL),
(782, 261, 2, 20, NULL, NULL, NULL),
(783, 261, 3, 0, NULL, NULL, NULL),
(784, 262, 1, 40, NULL, NULL, NULL),
(785, 262, 2, 20, NULL, NULL, NULL),
(786, 262, 3, 0, NULL, NULL, NULL),
(787, 263, 1, 100, NULL, NULL, NULL),
(788, 263, 2, 50, NULL, NULL, NULL),
(789, 263, 3, 0, NULL, NULL, NULL),
(790, 264, 1, 50, NULL, NULL, NULL),
(791, 264, 2, 35, NULL, NULL, NULL),
(792, 264, 3, 0, NULL, NULL, NULL),
(793, 265, 1, 100, NULL, NULL, NULL),
(794, 265, 2, 50, NULL, NULL, NULL),
(795, 265, 3, 0, NULL, NULL, NULL),
(796, 266, 1, 70, NULL, NULL, NULL),
(797, 266, 2, 45, NULL, NULL, NULL),
(798, 266, 3, 0, NULL, NULL, NULL),
(799, 267, 1, 40, NULL, NULL, NULL),
(800, 267, 2, 20, NULL, NULL, NULL),
(801, 267, 3, 0, NULL, NULL, NULL),
(802, 268, 1, 40, NULL, NULL, NULL),
(803, 268, 2, 20, NULL, NULL, NULL),
(804, 268, 3, 0, NULL, NULL, NULL),
(805, 269, 1, 100, NULL, NULL, NULL),
(806, 269, 2, 50, NULL, NULL, NULL),
(807, 269, 3, 0, NULL, NULL, NULL),
(808, 270, 1, 50, NULL, NULL, NULL),
(809, 270, 2, 35, NULL, NULL, NULL),
(810, 270, 3, 0, NULL, NULL, NULL),
(811, 271, 1, 100, NULL, NULL, NULL),
(812, 271, 2, 50, NULL, NULL, NULL),
(813, 271, 3, 0, NULL, NULL, NULL),
(814, 272, 1, 70, NULL, NULL, NULL),
(815, 272, 2, 45, NULL, NULL, NULL),
(816, 272, 3, 0, NULL, NULL, NULL),
(817, 273, 1, 40, NULL, NULL, NULL),
(818, 273, 2, 20, NULL, NULL, NULL),
(819, 273, 3, 0, NULL, NULL, NULL),
(820, 274, 1, 40, NULL, NULL, NULL),
(821, 274, 2, 20, NULL, NULL, NULL),
(822, 274, 3, 0, NULL, NULL, NULL),
(823, 275, 1, 100, NULL, NULL, NULL),
(824, 275, 2, 50, NULL, NULL, NULL),
(825, 275, 3, 0, NULL, NULL, NULL),
(826, 276, 1, 50, NULL, NULL, NULL),
(827, 276, 2, 35, NULL, NULL, NULL),
(828, 276, 3, 0, NULL, NULL, NULL),
(829, 277, 1, 1, NULL, NULL, NULL),
(830, 277, 2, 1, NULL, NULL, NULL),
(831, 277, 3, 1, NULL, NULL, NULL),
(832, 278, 1, 1, NULL, NULL, NULL),
(833, 278, 2, 1, NULL, NULL, NULL),
(834, 278, 3, 1, NULL, NULL, NULL),
(835, 280, 1, 1, NULL, NULL, NULL),
(836, 280, 2, 1, NULL, NULL, NULL),
(837, 280, 3, 1, NULL, NULL, NULL),
(838, 281, 1, 1, NULL, NULL, NULL),
(839, 281, 2, 1, NULL, NULL, NULL),
(840, 281, 3, 1, NULL, NULL, NULL),
(841, 282, 1, 1, NULL, NULL, NULL),
(842, 282, 2, 1, NULL, NULL, NULL),
(843, 282, 3, 1, NULL, NULL, NULL),
(844, 283, 1, 1, NULL, NULL, NULL),
(845, 283, 2, 1, NULL, NULL, NULL),
(846, 283, 3, 1, NULL, NULL, NULL),
(847, 284, 1, 1, NULL, NULL, NULL),
(848, 284, 2, 1, NULL, NULL, NULL),
(849, 284, 3, 1, NULL, NULL, NULL),
(850, 285, 1, 1, NULL, NULL, NULL),
(851, 285, 2, 1, NULL, NULL, NULL),
(852, 285, 3, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passes_sellers`
--

CREATE TABLE `passes_sellers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `passes_sellers`
--

INSERT INTO `passes_sellers` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Direct', NULL, NULL, NULL),
(2, 'Touroperator', NULL, NULL, NULL),
(3, 'Agent', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pass_seat_type`
--

CREATE TABLE `pass_seat_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `pass_id` int(10) UNSIGNED NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `seats_available` int(11) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pass_seat_type`
--

INSERT INTO `pass_seat_type` (`id`, `pass_id`, `seat_type_id`, `seats_available`, `deleted_at`, `created_at`, `updated_at`) VALUES
(13, 5, 1, 350, NULL, NULL, NULL),
(14, 5, 2, 50, NULL, NULL, NULL),
(15, 5, 3, 100, NULL, NULL, NULL),
(16, 5, 4, 100, NULL, NULL, NULL),
(17, 5, 5, 100, NULL, NULL, NULL),
(18, 5, 6, 100, NULL, NULL, NULL),
(19, 6, 1, 350, NULL, NULL, NULL),
(20, 6, 2, 50, NULL, NULL, NULL),
(21, 6, 3, 100, NULL, NULL, NULL),
(22, 6, 4, 100, NULL, NULL, NULL),
(23, 6, 5, 100, NULL, NULL, NULL),
(24, 6, 6, 100, NULL, NULL, NULL),
(25, 7, 1, 350, NULL, NULL, NULL),
(26, 7, 2, 50, NULL, NULL, NULL),
(27, 7, 3, 100, NULL, NULL, NULL),
(28, 7, 4, 100, NULL, NULL, NULL),
(29, 7, 5, 100, NULL, NULL, NULL),
(30, 7, 6, 100, NULL, NULL, NULL),
(31, 8, 1, 350, NULL, NULL, NULL),
(32, 8, 2, 50, NULL, NULL, NULL),
(33, 8, 3, 100, NULL, NULL, NULL),
(34, 8, 4, 100, NULL, NULL, NULL),
(35, 8, 5, 100, NULL, NULL, NULL),
(36, 8, 6, 100, NULL, NULL, NULL),
(37, 9, 1, 350, NULL, NULL, NULL),
(38, 9, 2, 50, NULL, NULL, NULL),
(39, 9, 3, 100, NULL, NULL, NULL),
(40, 9, 4, 100, NULL, NULL, NULL),
(41, 9, 5, 100, NULL, NULL, NULL),
(42, 9, 6, 100, NULL, NULL, NULL),
(43, 10, 1, 350, NULL, NULL, NULL),
(44, 10, 2, 50, NULL, NULL, NULL),
(45, 10, 3, 100, NULL, NULL, NULL),
(46, 10, 4, 100, NULL, NULL, NULL),
(47, 10, 5, 100, NULL, NULL, NULL),
(48, 10, 6, 100, NULL, NULL, NULL),
(49, 11, 1, 350, NULL, NULL, NULL),
(50, 11, 2, 50, NULL, NULL, NULL),
(51, 11, 3, 100, NULL, NULL, NULL),
(52, 11, 4, 100, NULL, NULL, NULL),
(53, 11, 5, 100, NULL, NULL, NULL),
(54, 11, 6, 100, NULL, NULL, NULL),
(55, 12, 1, 350, NULL, NULL, NULL),
(56, 12, 2, 50, NULL, NULL, NULL),
(57, 12, 3, 100, NULL, NULL, NULL),
(58, 12, 4, 100, NULL, NULL, NULL),
(59, 12, 5, 100, NULL, NULL, NULL),
(60, 12, 6, 100, NULL, NULL, NULL),
(61, 13, 1, 350, NULL, NULL, NULL),
(62, 13, 2, 50, NULL, NULL, NULL),
(63, 13, 3, 100, NULL, NULL, NULL),
(64, 13, 4, 100, NULL, NULL, NULL),
(65, 13, 5, 100, NULL, NULL, NULL),
(66, 13, 6, 100, NULL, NULL, NULL),
(67, 14, 1, 350, NULL, NULL, NULL),
(68, 14, 2, 50, NULL, NULL, NULL),
(69, 14, 3, 100, NULL, NULL, NULL),
(70, 14, 4, 100, NULL, NULL, NULL),
(71, 14, 5, 100, NULL, NULL, NULL),
(72, 14, 6, 100, NULL, NULL, NULL),
(73, 15, 1, 350, NULL, NULL, NULL),
(74, 15, 2, 50, NULL, NULL, NULL),
(75, 15, 3, 100, NULL, NULL, NULL),
(76, 15, 4, 100, NULL, NULL, NULL),
(77, 15, 5, 100, NULL, NULL, NULL),
(78, 15, 6, 100, NULL, NULL, NULL),
(79, 16, 1, 350, NULL, NULL, NULL),
(80, 16, 2, 50, NULL, NULL, NULL),
(81, 16, 3, 100, NULL, NULL, NULL),
(82, 16, 4, 100, NULL, NULL, NULL),
(83, 16, 5, 100, NULL, NULL, NULL),
(84, 16, 6, 100, NULL, NULL, NULL),
(85, 17, 1, 350, NULL, NULL, NULL),
(86, 17, 2, 50, NULL, NULL, NULL),
(87, 17, 3, 100, NULL, NULL, NULL),
(88, 17, 4, 100, NULL, NULL, NULL),
(89, 17, 5, 100, NULL, NULL, NULL),
(90, 17, 6, 100, NULL, NULL, NULL),
(91, 18, 1, 350, NULL, NULL, NULL),
(92, 18, 2, 50, NULL, NULL, NULL),
(93, 18, 3, 100, NULL, NULL, NULL),
(94, 18, 4, 100, NULL, NULL, NULL),
(95, 18, 5, 100, NULL, NULL, NULL),
(96, 18, 6, 100, NULL, NULL, NULL),
(97, 19, 1, 350, NULL, NULL, NULL),
(98, 19, 2, 50, NULL, NULL, NULL),
(99, 19, 3, 100, NULL, NULL, NULL),
(100, 19, 4, 100, NULL, NULL, NULL),
(101, 19, 5, 100, NULL, NULL, NULL),
(102, 19, 6, 100, NULL, NULL, NULL),
(103, 20, 1, 350, NULL, NULL, NULL),
(104, 20, 2, 50, NULL, NULL, NULL),
(105, 20, 3, 100, NULL, NULL, NULL),
(106, 20, 4, 100, NULL, NULL, NULL),
(107, 20, 5, 100, NULL, NULL, NULL),
(108, 20, 6, 100, NULL, NULL, NULL),
(109, 21, 1, 350, NULL, NULL, NULL),
(110, 21, 2, 50, NULL, NULL, NULL),
(111, 21, 3, 100, NULL, NULL, NULL),
(112, 21, 4, 100, NULL, NULL, NULL),
(113, 21, 5, 100, NULL, NULL, NULL),
(114, 21, 6, 100, NULL, NULL, NULL),
(115, 22, 1, 350, NULL, NULL, NULL),
(116, 22, 2, 50, NULL, NULL, NULL),
(117, 22, 3, 100, NULL, NULL, NULL),
(118, 22, 4, 100, NULL, NULL, NULL),
(119, 22, 5, 100, NULL, NULL, NULL),
(120, 22, 6, 100, NULL, NULL, NULL),
(121, 23, 1, 350, NULL, NULL, NULL),
(122, 23, 2, 50, NULL, NULL, NULL),
(123, 23, 3, 100, NULL, NULL, NULL),
(124, 23, 4, 100, NULL, NULL, NULL),
(125, 23, 5, 100, NULL, NULL, NULL),
(126, 23, 6, 100, NULL, NULL, NULL),
(127, 24, 1, 350, NULL, NULL, NULL),
(128, 24, 2, 50, NULL, NULL, NULL),
(129, 24, 3, 100, NULL, NULL, NULL),
(130, 24, 4, 100, NULL, NULL, NULL),
(131, 24, 5, 100, NULL, NULL, NULL),
(132, 24, 6, 100, NULL, NULL, NULL),
(133, 25, 1, 350, NULL, NULL, NULL),
(134, 25, 2, 50, NULL, NULL, NULL),
(135, 25, 3, 100, NULL, NULL, NULL),
(136, 25, 4, 100, NULL, NULL, NULL),
(137, 25, 5, 100, NULL, NULL, NULL),
(138, 25, 6, 100, NULL, NULL, NULL),
(139, 26, 1, 350, NULL, NULL, NULL),
(140, 26, 2, 50, NULL, NULL, NULL),
(141, 26, 3, 100, NULL, NULL, NULL),
(142, 26, 4, 100, NULL, NULL, NULL),
(143, 26, 5, 100, NULL, NULL, NULL),
(144, 26, 6, 100, NULL, NULL, NULL),
(145, 27, 1, 350, NULL, NULL, NULL),
(146, 27, 2, 50, NULL, NULL, NULL),
(147, 27, 3, 100, NULL, NULL, NULL),
(148, 27, 4, 100, NULL, NULL, NULL),
(149, 27, 5, 100, NULL, NULL, NULL),
(150, 27, 6, 100, NULL, NULL, NULL),
(151, 28, 1, 350, NULL, NULL, NULL),
(152, 28, 2, 50, NULL, NULL, NULL),
(153, 28, 3, 100, NULL, NULL, NULL),
(154, 28, 4, 100, NULL, NULL, NULL),
(155, 28, 5, 100, NULL, NULL, NULL),
(156, 28, 6, 100, NULL, NULL, NULL),
(157, 29, 1, 350, NULL, NULL, NULL),
(158, 29, 2, 50, NULL, NULL, NULL),
(159, 29, 3, 100, NULL, NULL, NULL),
(160, 29, 4, 100, NULL, NULL, NULL),
(161, 29, 5, 100, NULL, NULL, NULL),
(162, 29, 6, 100, NULL, NULL, NULL),
(163, 30, 1, 350, NULL, NULL, NULL),
(164, 30, 2, 50, NULL, NULL, NULL),
(165, 30, 3, 100, NULL, NULL, NULL),
(166, 30, 4, 100, NULL, NULL, NULL),
(167, 30, 5, 100, NULL, NULL, NULL),
(168, 30, 6, 100, NULL, NULL, NULL),
(169, 31, 1, 350, NULL, NULL, NULL),
(170, 31, 2, 50, NULL, NULL, NULL),
(171, 31, 3, 100, NULL, NULL, NULL),
(172, 31, 4, 100, NULL, NULL, NULL),
(173, 31, 5, 100, NULL, NULL, NULL),
(174, 31, 6, 100, NULL, NULL, NULL),
(175, 32, 1, 350, NULL, NULL, NULL),
(176, 32, 2, 50, NULL, NULL, NULL),
(177, 32, 3, 100, NULL, NULL, NULL),
(178, 32, 4, 100, NULL, NULL, NULL),
(179, 32, 5, 100, NULL, NULL, NULL),
(180, 32, 6, 100, NULL, NULL, NULL),
(181, 33, 1, 350, NULL, NULL, NULL),
(182, 33, 2, 50, NULL, NULL, NULL),
(183, 33, 3, 100, NULL, NULL, NULL),
(184, 33, 4, 100, NULL, NULL, NULL),
(185, 33, 5, 100, NULL, NULL, NULL),
(186, 33, 6, 100, NULL, NULL, NULL),
(187, 34, 1, 350, NULL, NULL, NULL),
(188, 34, 2, 50, NULL, NULL, NULL),
(189, 34, 3, 100, NULL, NULL, NULL),
(190, 34, 4, 100, NULL, NULL, NULL),
(191, 34, 5, 100, NULL, NULL, NULL),
(192, 34, 6, 100, NULL, NULL, NULL),
(193, 35, 1, 350, NULL, NULL, NULL),
(194, 35, 2, 50, NULL, NULL, NULL),
(195, 35, 3, 100, NULL, NULL, NULL),
(196, 35, 4, 100, NULL, NULL, NULL),
(197, 35, 5, 100, NULL, NULL, NULL),
(198, 35, 6, 100, NULL, NULL, NULL),
(199, 36, 1, 350, NULL, NULL, NULL),
(200, 36, 2, 50, NULL, NULL, NULL),
(201, 36, 3, 100, NULL, NULL, NULL),
(202, 36, 4, 100, NULL, NULL, NULL),
(203, 36, 5, 100, NULL, NULL, NULL),
(204, 36, 6, 100, NULL, NULL, NULL),
(205, 37, 1, 350, NULL, NULL, NULL),
(206, 37, 2, 50, NULL, NULL, NULL),
(207, 37, 3, 100, NULL, NULL, NULL),
(208, 37, 4, 100, NULL, NULL, NULL),
(209, 37, 5, 100, NULL, NULL, NULL),
(210, 37, 6, 100, NULL, NULL, NULL),
(211, 38, 1, 350, NULL, NULL, NULL),
(212, 38, 2, 50, NULL, NULL, NULL),
(213, 38, 3, 100, NULL, NULL, NULL),
(214, 38, 4, 100, NULL, NULL, NULL),
(215, 38, 5, 100, NULL, NULL, NULL),
(216, 38, 6, 100, NULL, NULL, NULL),
(217, 39, 1, 350, NULL, NULL, NULL),
(218, 39, 2, 50, NULL, NULL, NULL),
(219, 39, 3, 100, NULL, NULL, NULL),
(220, 39, 4, 100, NULL, NULL, NULL),
(221, 39, 5, 100, NULL, NULL, NULL),
(222, 39, 6, 100, NULL, NULL, NULL),
(223, 40, 1, 350, NULL, NULL, NULL),
(224, 40, 2, 50, NULL, NULL, NULL),
(225, 40, 3, 100, NULL, NULL, NULL),
(226, 40, 4, 100, NULL, NULL, NULL),
(227, 40, 5, 100, NULL, NULL, NULL),
(228, 40, 6, 100, NULL, NULL, NULL),
(229, 41, 1, 350, NULL, NULL, NULL),
(230, 41, 2, 50, NULL, NULL, NULL),
(231, 41, 3, 100, NULL, NULL, NULL),
(232, 41, 4, 100, NULL, NULL, NULL),
(233, 41, 5, 100, NULL, NULL, NULL),
(234, 41, 6, 100, NULL, NULL, NULL),
(235, 42, 1, 350, NULL, NULL, NULL),
(236, 42, 2, 50, NULL, NULL, NULL),
(237, 42, 3, 100, NULL, NULL, NULL),
(238, 42, 4, 100, NULL, NULL, NULL),
(239, 42, 5, 100, NULL, NULL, NULL),
(240, 42, 6, 100, NULL, NULL, NULL),
(241, 43, 1, 350, NULL, NULL, NULL),
(242, 43, 2, 50, NULL, NULL, NULL),
(243, 43, 3, 100, NULL, NULL, NULL),
(244, 43, 4, 100, NULL, NULL, NULL),
(245, 43, 5, 100, NULL, NULL, NULL),
(246, 43, 6, 100, NULL, NULL, NULL),
(247, 44, 1, 350, NULL, NULL, NULL),
(248, 44, 2, 50, NULL, NULL, NULL),
(249, 44, 3, 100, NULL, NULL, NULL),
(250, 44, 4, 100, NULL, NULL, NULL),
(251, 44, 5, 100, NULL, NULL, NULL),
(252, 44, 6, 100, NULL, NULL, NULL),
(253, 45, 1, 350, NULL, NULL, NULL),
(254, 45, 2, 50, NULL, NULL, NULL),
(255, 45, 3, 100, NULL, NULL, NULL),
(256, 45, 4, 100, NULL, NULL, NULL),
(257, 45, 5, 100, NULL, NULL, NULL),
(258, 45, 6, 100, NULL, NULL, NULL),
(259, 46, 1, 350, NULL, NULL, NULL),
(260, 46, 2, 50, NULL, NULL, NULL),
(261, 46, 3, 100, NULL, NULL, NULL),
(262, 46, 4, 100, NULL, NULL, NULL),
(263, 46, 5, 100, NULL, NULL, NULL),
(264, 46, 6, 100, NULL, NULL, NULL),
(265, 47, 1, 350, NULL, NULL, NULL),
(266, 47, 2, 50, NULL, NULL, NULL),
(267, 47, 3, 100, NULL, NULL, NULL),
(268, 47, 4, 100, NULL, NULL, NULL),
(269, 47, 5, 100, NULL, NULL, NULL),
(270, 47, 6, 100, NULL, NULL, NULL),
(271, 48, 1, 350, NULL, NULL, NULL),
(272, 48, 2, 50, NULL, NULL, NULL),
(273, 48, 3, 100, NULL, NULL, NULL),
(274, 48, 4, 100, NULL, NULL, NULL),
(275, 48, 5, 100, NULL, NULL, NULL),
(276, 48, 6, 100, NULL, NULL, NULL),
(277, 49, 1, 350, NULL, NULL, NULL),
(278, 49, 2, 50, NULL, NULL, NULL),
(279, 49, 3, 100, NULL, NULL, NULL),
(280, 50, 1, 350, NULL, NULL, NULL),
(281, 50, 2, 50, NULL, NULL, NULL),
(282, 50, 3, 100, NULL, NULL, NULL),
(283, 50, 4, 100, NULL, NULL, NULL),
(284, 50, 5, 100, NULL, NULL, NULL),
(285, 50, 6, 100, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `image`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'cash.png', NULL, '2017-01-16 12:29:36', '2017-01-16 12:29:36'),
(2, 'Physical TPV', 'physical.png', NULL, '2017-01-16 12:29:48', '2017-01-16 12:29:48'),
(3, 'Online TPV\n', 'online.png', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'On Show', 'theater.png', NULL, '2017-03-07 07:47:17', '2017-03-07 07:47:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_method_reservations`
--

CREATE TABLE `payment_method_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `removed_by` int(10) UNSIGNED DEFAULT NULL,
  `removed_date` timestamp NULL DEFAULT NULL,
  `removed_reason` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `payment_method_reservations`
--

INSERT INTO `payment_method_reservations` (`id`, `user_id`, `payment_method_id`, `reservation_id`, `total`, `removed_by`, `removed_date`, `removed_reason`, `deleted_at`, `created_at`, `updated_at`) VALUES
(20, 1, 1, 108, '170.00', NULL, NULL, NULL, NULL, '2017-11-16 07:43:56', '2017-11-16 07:43:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint(1) NOT NULL,
  `group` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `permission`, `label`, `level`, `group`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'view_reservations', 'view_reservations', 1, 1, NULL, NULL, NULL),
(2, 'create_reservations', 'create_reservations', 1, 1, NULL, NULL, NULL),
(3, 'edit_reservations', 'edit_reservations', 1, 1, NULL, NULL, NULL),
(4, 'delete_reservations', 'delete_reservations', 1, 1, NULL, NULL, NULL),
(5, 'show_reservations', 'show_reservations', 1, 1, NULL, NULL, NULL),
(6, 'view_shows', 'view_shows', 1, 2, NULL, NULL, NULL),
(7, 'create_shows', 'create_shows', 1, 2, NULL, NULL, NULL),
(8, 'edit_shows', 'edit_shows', 1, 2, NULL, NULL, NULL),
(9, 'delete_shows', 'delete_shows', 1, 2, NULL, NULL, NULL),
(10, 'show_shows', 'show_shows', 1, 2, NULL, NULL, NULL),
(11, 'view_users', 'view_users', 1, 3, NULL, NULL, NULL),
(12, 'create_users', 'create_users', 1, 3, NULL, NULL, NULL),
(13, 'edit_users', 'edit_users', 1, 3, NULL, NULL, NULL),
(14, 'delete_users', 'delete_users', 1, 3, NULL, NULL, NULL),
(15, 'show_users', 'show_users', 1, 3, NULL, NULL, NULL),
(16, 'view_promocodes', 'view_promocodes', 1, 4, NULL, NULL, NULL),
(17, 'create_promocodes', 'create_promocodes', 1, 4, NULL, NULL, NULL),
(18, 'edit_promocodes', 'edit_promocodes', 1, 4, NULL, NULL, NULL),
(19, 'delete_promocodes', 'delete_promocodes', 1, 4, NULL, NULL, NULL),
(20, 'show_promocodes', 'show_promocodes', 1, 4, NULL, NULL, NULL),
(21, 'view_passes', 'view_passes', 1, 5, NULL, NULL, NULL),
(22, 'create_passes', 'create_passes', 1, 5, NULL, NULL, NULL),
(23, 'edit_passes', 'edit_passes', 1, 5, NULL, NULL, NULL),
(24, 'delete_passes', 'delete_passes', 1, 5, NULL, NULL, NULL),
(25, 'show_passes', 'show_passes', 1, 5, NULL, NULL, NULL),
(26, 'view_seatypes', 'view_seatypes', 1, 6, NULL, NULL, NULL),
(27, 'create_seatypes', 'create_seatypes', 1, 6, NULL, NULL, NULL),
(28, 'edit_seatypes', 'edit_seatypes', 1, 6, NULL, NULL, NULL),
(29, 'delete_seatypes', 'delete_seatypes', 1, 6, NULL, NULL, NULL),
(30, 'show_seatypes', 'show_seatypes', 1, 6, NULL, NULL, NULL),
(31, 'view_tickettype', 'view_tickettype', 1, 7, NULL, NULL, NULL),
(32, 'create_tickettype', 'create_tickettype', 1, 7, NULL, NULL, NULL),
(33, 'edit_tickettype', 'edit_tickettype', 1, 7, NULL, NULL, NULL),
(34, 'delete_tickettype', 'delete_tickettype', 1, 7, NULL, NULL, NULL),
(35, 'show_tickettype', 'show_tickettype', 1, 7, NULL, NULL, NULL),
(36, 'view_transporters', 'view_transporters', 1, 8, NULL, NULL, NULL),
(37, 'create_transporters', 'create_transporters', 1, 8, NULL, NULL, NULL),
(38, 'edit_transporters', 'edit_transporters', 1, 8, NULL, NULL, NULL),
(39, 'delete_transporters', 'delete_transporters', 1, 8, NULL, NULL, NULL),
(40, 'show_transporters', 'show_transporters', 1, 8, NULL, NULL, NULL),
(41, 'view_buses', 'view_buses', 1, 9, NULL, NULL, NULL),
(42, 'create_buses', 'create_buses', 1, 9, NULL, NULL, NULL),
(43, 'edit_buses', 'edit_buses', 1, 9, NULL, NULL, NULL),
(44, 'delete_buses', 'delete_buses', 1, 9, NULL, NULL, NULL),
(45, 'show_buses', 'show_buses', 1, 9, NULL, NULL, NULL),
(46, 'view_areas', 'view_areas', 1, 10, NULL, NULL, NULL),
(47, 'create_areas', 'create_areas', 1, 10, NULL, NULL, NULL),
(48, 'edit_areas', 'edit_areas', 1, 10, NULL, NULL, NULL),
(49, 'delete_areas', 'delete_areas', 1, 10, NULL, NULL, NULL),
(50, 'show_areas', 'show_areas', 1, 10, NULL, NULL, NULL),
(61, 'view_pickuppoints', 'view_pickuppoints', 1, 11, NULL, NULL, NULL),
(62, 'create_pickuppoints', 'create_pickuppoints', 1, 11, NULL, NULL, NULL),
(63, 'edit_pickuppoints', 'edit_pickuppoints', 1, 11, NULL, NULL, NULL),
(64, 'delete_pickuppoints', 'delete_pickuppoints', 1, 11, NULL, NULL, NULL),
(65, 'show_pickuppoints', 'show_pickuppoints', 1, 11, NULL, NULL, NULL),
(66, 'view_cities', 'view_cities', 1, 12, NULL, NULL, NULL),
(67, 'create_cities', 'create_cities', 1, 12, NULL, NULL, NULL),
(68, 'edit_cities', 'edit_cities', 1, 12, NULL, NULL, NULL),
(69, 'delete_cities', 'delete_cities', 1, 12, NULL, NULL, NULL),
(70, 'show_cities', 'show_cities', 1, 12, NULL, NULL, NULL),
(71, 'view_routes', 'view_routes', 1, 13, NULL, NULL, NULL),
(72, 'create_routes', 'create_routes', 1, 13, NULL, NULL, NULL),
(73, 'edit_routes', 'edit_routes', 1, 13, NULL, NULL, NULL),
(74, 'delete_routes', 'delete_routes', 1, 13, NULL, NULL, NULL),
(75, 'show_routes', 'show_routes', 1, 13, NULL, NULL, NULL),
(76, 'view_customers', 'view_customers', 1, 14, NULL, NULL, NULL),
(77, 'create_customers', 'create_customers', 1, 14, NULL, NULL, NULL),
(78, 'edit_customers', 'edit_customers', 1, 14, NULL, NULL, NULL),
(79, 'delete_customers', 'delete_customers', 1, 14, NULL, NULL, NULL),
(80, 'show_customers', 'show_customers', 1, 14, NULL, NULL, NULL),
(81, 'view_resellers', 'view_resellers', 1, 15, NULL, NULL, NULL),
(82, 'create_resellers', 'create_resellers', 1, 15, NULL, NULL, NULL),
(83, 'edit_resellers', 'edit_resellers', 1, 15, NULL, NULL, NULL),
(84, 'delete_resellers', 'delete_resellers', 1, 15, NULL, NULL, NULL),
(85, 'show_resellers', 'show_resellers', 1, 15, NULL, NULL, NULL),
(86, 'view_cartes', 'view_cartes', 1, 17, NULL, NULL, NULL),
(87, 'create_cartes', 'create_cartes', 1, 17, NULL, NULL, NULL),
(88, 'edit_cartes', 'edit_cartes', 1, 17, NULL, NULL, NULL),
(89, 'delete_cartes', 'delete_cartes', 1, 17, NULL, NULL, NULL),
(90, 'show_cartes', 'show_cartes', 1, 17, NULL, NULL, NULL),
(91, 'view_menus', 'view_menus', 1, 16, NULL, NULL, NULL),
(92, 'create_menus', 'create_menus', 1, 16, NULL, NULL, NULL),
(93, 'edit_menus', 'edit_menus', 1, 16, NULL, NULL, NULL),
(94, 'delete_menus', 'delete_menus', 1, 16, NULL, NULL, NULL),
(95, 'show_menus', 'show_menus', 1, 16, NULL, NULL, NULL),
(96, 'view_dishes', 'view_dishes', 1, 18, NULL, NULL, NULL),
(97, 'create_dishes', 'create_dishes', 1, 18, NULL, NULL, NULL),
(98, 'edit_dishes', 'edit_dishes', 1, 18, NULL, NULL, NULL),
(99, 'delete_dishes', 'delete_dishes', 1, 18, NULL, NULL, NULL),
(100, 'show_dishes', 'show_dishes', 1, 18, NULL, NULL, NULL),
(101, 'view_roles', 'view_roles', 1, 19, NULL, NULL, NULL),
(102, 'create_roles', 'create_roles', 1, 19, NULL, NULL, NULL),
(103, 'edit_roles', 'edit_roles', 1, 19, NULL, NULL, NULL),
(104, 'delete_roles', 'delete_roles', 1, 19, NULL, NULL, NULL),
(105, 'show_roles', 'show_roles', 1, 19, NULL, NULL, NULL),
(106, 'view-home-list', 'view-home-list', 1, 20, NULL, NULL, NULL),
(107, 'book-home-list', 'book-home-list', 1, 21, NULL, NULL, NULL),
(108, 'restore_reservations', 'restore_reservations', 1, 1, NULL, NULL, NULL),
(109, 'view_payment', 'view_payment', 1, 22, NULL, NULL, NULL),
(110, 'edit_payment', 'edit_payment', 1, 22, NULL, NULL, NULL),
(111, 'show_payment', 'show_payment', 1, 22, NULL, NULL, NULL),
(112, 'delete_payment', 'delete_payment', 1, 22, NULL, NULL, NULL),
(113, 'create_payment', 'create_payment', 1, 22, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pickup_points`
--

CREATE TABLE `pickup_points` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mapaddress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pickup_points`
--

INSERT INTO `pickup_points` (`id`, `name`, `latitude`, `longitude`, `mapaddress`, `city_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'H. Puerto Azul', '39.503759247834154', '2.53149401396513', 'Av. Notari Alemany, 3, 07181 Calvià, Illes Balears, Spain', 1, NULL, '2017-09-14 13:24:32', '2017-09-14 13:25:01'),
(2, 'Can Picaflor', '39.764741848341856', '3.1531941890716553', 'Carrer Hernán Cortés, 33, 07458 Can Picafort, Illes Balears, Spain', 1, NULL, '2017-09-14 13:25:35', '2017-09-14 13:26:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pickup_point_route`
--

CREATE TABLE `pickup_point_route` (
  `pickup_point_id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `hour` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `order` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pickup_point_route`
--

INSERT INTO `pickup_point_route` (`pickup_point_id`, `route_id`, `hour`, `price`, `order`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 1, '17:00', '10.00', '1', NULL, '2017-09-15 07:35:43', '2017-09-15 07:35:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocodes`
--

CREATE TABLE `promocodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  `for_from` datetime DEFAULT NULL,
  `for_to` datetime DEFAULT NULL,
  `discount` decimal(8,2) NOT NULL,
  `single_use` tinyint(1) NOT NULL,
  `canceled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promocodes`
--

INSERT INTO `promocodes` (`id`, `code`, `valid_from`, `valid_to`, `for_from`, `for_to`, `discount`, `single_use`, `canceled`, `created_at`, `updated_at`) VALUES
(1, 'RW2017', '2017-09-20 13:08:57', '2017-12-31 13:08:57', '2017-09-20 13:08:57', '2017-12-31 13:08:57', '15.00', 0, 0, '2017-09-20 11:08:29', '2017-09-20 11:08:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocode_show`
--

CREATE TABLE `promocode_show` (
  `promocode_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promocode_show`
--

INSERT INTO `promocode_show` (`promocode_id`, `show_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resellers`
--

CREATE TABLE `resellers` (
  `id` int(10) UNSIGNED NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_comments` text COLLATE utf8mb4_unicode_ci,
  `passes_seller_id` int(10) UNSIGNED DEFAULT NULL,
  `resellers_type_id` int(10) UNSIGNED DEFAULT NULL,
  `agent_type_id` int(10) UNSIGNED DEFAULT NULL,
  `area_id` int(10) UNSIGNED DEFAULT NULL,
  `language_id` int(10) UNSIGNED DEFAULT NULL,
  `country_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `is_enable` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `resellers`
--

INSERT INTO `resellers` (`id`, `discount`, `company`, `name`, `email`, `phone`, `fax`, `address`, `city`, `postal_code`, `internal_comments`, `passes_seller_id`, `resellers_type_id`, `agent_type_id`, `area_id`, `language_id`, `country_id`, `user_id`, `is_enable`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '10.00', 'Test (AGENT)', 'Adrian Escalada', 'aescalada@refineriaweb.com', NULL, NULL, NULL, NULL, NULL, '', 3, 3, NULL, 2, 1, NULL, 1, 1, NULL, NULL, NULL),
(2, '32.00', 'TUI (TTO)', 'Marc Ramonell Oliver', 'mramonell@refineriaweb.com', NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, 1, 1, 1, 13, 1, 1, NULL, '2017-09-20 09:24:33', '2017-09-20 11:04:15'),
(4, '15.00', 'Cruceros Cormoran', 'Natalie', 'info@cruceroscormoran.com', NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, NULL, 4, 1, 205, 1, 1, NULL, '2017-10-09 09:48:02', '2017-10-09 09:48:02'),
(5, '14.00', 'A. Aquasol', 'Aquasol A.', 'reservas@aquasol.info', NULL, NULL, NULL, NULL, NULL, NULL, 3, 3, NULL, NULL, 1, 205, 1, 1, NULL, '2017-10-09 09:51:53', '2017-10-09 09:51:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resellers_types`
--

CREATE TABLE `resellers_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `resellers_types`
--

INSERT INTO `resellers_types` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Direct', NULL, NULL, NULL),
(2, 'Touroperator', NULL, NULL, NULL),
(3, 'Agent', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `reseller_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `channel_id` int(10) UNSIGNED NOT NULL,
  `discount` int(10) UNSIGNED DEFAULT NULL,
  `promocode_id` int(10) UNSIGNED DEFAULT NULL,
  `pass_id` int(10) UNSIGNED NOT NULL,
  `reconcile` int(10) UNSIGNED DEFAULT '0',
  `identification_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `reservation_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canceled_by` int(10) UNSIGNED DEFAULT NULL,
  `canceled_date` timestamp NULL DEFAULT NULL,
  `canceled_reason` text COLLATE utf8mb4_unicode_ci,
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `finished_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `reseller_id`, `customer_id`, `channel_id`, `discount`, `promocode_id`, `pass_id`, `reconcile`, `identification_number`, `email`, `phone`, `name`, `created_by`, `reservation_number`, `reference_number`, `canceled_by`, `canceled_date`, `canceled_reason`, `finished`, `finished_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(108, NULL, 1, 6, 15, 1, 21, 0, '1', 'adrianescalada@gmail.com', '697877831', 'Adrian Escalada', 1, '5a0d41aa1758f', NULL, NULL, NULL, NULL, 1, 1, NULL, '2017-11-16 07:43:38', '2017-11-16 07:43:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_menus`
--

CREATE TABLE `reservation_menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_tickets`
--

CREATE TABLE `reservation_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` int(10) UNSIGNED NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservation_tickets`
--

INSERT INTO `reservation_tickets` (`id`, `ticket_type_id`, `seat_type_id`, `quantity`, `unit_price`, `reservation_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(97, 1, 1, 2, '100.00', 108, NULL, '2017-11-16 07:43:38', '2017-11-16 07:43:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_transports`
--

CREATE TABLE `reservation_transports` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `bus_id` int(10) UNSIGNED NOT NULL,
  `pickup_point_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `pickup_point` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hour` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `role`, `guard_name`, `layout`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'administrator', 'Administrator', 0, NULL, '2016-11-14 12:21:38', '2017-09-20 13:44:43'),
(2, 'reservas', 'Reservas', 0, NULL, '2017-04-04 12:26:15', '2017-04-04 12:30:03'),
(3, 'box office', 'Box Office', 0, NULL, '2017-04-04 12:45:05', '2017-04-04 12:45:05'),
(4, 'operaciones', 'Operaciones', 0, NULL, '2017-04-04 12:49:12', '2017-04-04 12:49:12'),
(5, 'reservas y transporte', 'Reservas y Transporte', 0, NULL, '2017-04-04 13:00:47', '2017-04-04 13:00:47'),
(6, 'marketing', 'Marketing', 0, NULL, '2017-04-04 13:33:26', '2017-04-04 13:33:26'),
(7, 'agent login', 'Agent Login', 0, NULL, '2017-04-10 08:29:48', '2017-04-10 08:30:12'),
(8, 'sales', 'Sales', 0, NULL, '2017-04-25 15:33:55', '2017-04-25 15:33:55'),
(9, 'agent edit', 'Agent Edit', 0, NULL, '2017-05-11 15:00:59', '2017-05-11 15:00:59'),
(11, 'Online', 'Online Web', 0, NULL, '2017-09-20 13:21:29', '2017-09-20 13:21:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `level` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `permission_id`, `role_id`, `level`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'all', NULL, NULL, '2017-10-18 07:28:20'),
(2, 1, 4, 'all', NULL, '2017-09-21 10:10:44', '2017-09-21 11:18:19'),
(3, 2, 4, 'all', NULL, '2017-09-21 10:10:44', '2017-09-21 11:18:19'),
(4, 3, 4, 'all', NULL, '2017-09-21 10:10:44', '2017-09-21 11:18:19'),
(5, 4, 4, 'all', NULL, '2017-09-21 10:10:44', '2017-09-21 11:18:19'),
(6, 5, 4, 'all', NULL, '2017-09-21 10:10:45', '2017-09-21 11:18:19'),
(7, 1, 3, 'team', NULL, '2017-09-21 11:06:15', '2017-09-21 11:10:26'),
(8, 3, 3, 'all', NULL, '2017-09-21 11:06:15', '2017-09-21 11:10:26'),
(9, 6, 4, 'all', NULL, '2017-09-21 11:18:19', '2017-09-21 11:18:19'),
(10, 1, 8, 'self', NULL, '2017-09-21 11:18:44', '2017-09-21 11:18:44'),
(11, 11, 1, 'all', '2017-09-21 11:40:31', NULL, '2017-09-21 11:40:31'),
(12, 2, 1, 'all', NULL, '2017-09-21 11:35:34', '2017-10-18 07:28:20'),
(13, 3, 1, 'all', NULL, '2017-09-21 11:35:34', '2017-10-18 07:28:20'),
(14, 4, 1, 'all', '2017-10-17 13:39:51', '2017-09-21 11:35:35', '2017-10-17 13:39:51'),
(15, 5, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(16, 6, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(17, 7, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(18, 8, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(19, 9, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(20, 10, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(21, 12, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(22, 13, 1, 'all', '2017-09-21 11:35:58', '2017-09-21 11:35:35', '2017-09-21 11:35:58'),
(23, 14, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(24, 15, 1, 'all', NULL, '2017-09-21 11:35:35', '2017-10-18 07:28:20'),
(25, 13, 1, 'all', '2017-09-21 12:07:18', '2017-09-21 11:36:15', '2017-09-21 12:07:18'),
(26, 16, 1, 'all', NULL, '2017-09-21 11:40:32', '2017-10-18 07:28:20'),
(27, 17, 1, 'all', NULL, '2017-09-21 11:40:32', '2017-10-18 07:28:21'),
(28, 18, 1, 'all', NULL, '2017-09-21 11:40:32', '2017-10-18 07:28:21'),
(29, 19, 1, 'all', NULL, '2017-09-21 11:40:32', '2017-10-18 07:28:21'),
(30, 20, 1, 'all', NULL, '2017-09-21 11:40:32', '2017-10-18 07:28:21'),
(31, 11, 1, 'all', NULL, '2017-09-21 11:42:47', '2017-10-18 07:28:20'),
(32, 21, 1, 'all', NULL, '2017-09-21 11:42:47', '2017-10-18 07:28:21'),
(33, 22, 1, 'all', NULL, '2017-09-21 11:42:47', '2017-10-18 07:28:21'),
(34, 23, 1, 'all', NULL, '2017-09-21 11:42:47', '2017-10-18 07:28:21'),
(35, 24, 1, 'all', NULL, '2017-09-21 11:42:48', '2017-10-18 07:28:21'),
(36, 25, 1, 'all', NULL, '2017-09-21 11:42:48', '2017-10-18 07:28:21'),
(37, 26, 1, 'all', NULL, '2017-09-21 11:48:57', '2017-10-18 07:28:21'),
(38, 27, 1, 'all', NULL, '2017-09-21 11:48:57', '2017-10-18 07:28:21'),
(39, 28, 1, 'all', NULL, '2017-09-21 11:48:57', '2017-10-18 07:28:21'),
(40, 29, 1, 'all', NULL, '2017-09-21 11:48:57', '2017-10-18 07:28:21'),
(41, 30, 1, 'all', NULL, '2017-09-21 11:48:57', '2017-10-18 07:28:21'),
(42, 31, 1, 'all', NULL, '2017-09-21 11:51:00', '2017-10-18 07:28:21'),
(43, 32, 1, 'all', NULL, '2017-09-21 11:51:00', '2017-10-18 07:28:21'),
(44, 33, 1, 'all', NULL, '2017-09-21 11:51:00', '2017-10-18 07:28:21'),
(45, 34, 1, 'all', NULL, '2017-09-21 11:51:00', '2017-10-18 07:28:22'),
(46, 35, 1, 'all', NULL, '2017-09-21 11:51:00', '2017-10-18 07:28:22'),
(47, 13, 1, 'all', NULL, '2017-09-21 12:10:27', '2017-10-18 07:28:20'),
(48, 36, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(49, 37, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(50, 38, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(51, 39, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(52, 40, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(53, 41, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(54, 42, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(55, 43, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(56, 44, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(57, 45, 1, 'all', NULL, '2017-09-21 12:10:29', '2017-10-18 07:28:22'),
(58, 46, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:22'),
(59, 47, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:22'),
(60, 48, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:22'),
(61, 49, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:22'),
(62, 50, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:22'),
(63, 61, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:23'),
(64, 62, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:23'),
(65, 63, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:23'),
(66, 64, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:23'),
(67, 65, 1, 'all', NULL, '2017-09-21 12:10:30', '2017-10-18 07:28:23'),
(68, 66, 1, 'all', NULL, '2017-09-21 12:40:26', '2017-10-18 07:28:23'),
(69, 67, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(70, 68, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(71, 69, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(72, 70, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(73, 71, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(74, 72, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(75, 73, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(76, 74, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(77, 75, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(78, 76, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:23'),
(79, 77, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(80, 78, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(81, 79, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(82, 80, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(83, 81, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(84, 82, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(85, 83, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(86, 84, 1, 'all', NULL, '2017-09-21 12:40:27', '2017-10-18 07:28:24'),
(87, 85, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(88, 86, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(89, 87, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(90, 88, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(91, 89, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(92, 90, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(93, 91, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(94, 92, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(95, 93, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:24'),
(96, 94, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(97, 95, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(98, 96, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(99, 97, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(100, 98, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(101, 99, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(102, 100, 1, 'all', NULL, '2017-09-21 12:40:28', '2017-10-18 07:28:25'),
(103, 101, 1, 'all', NULL, '2017-09-21 12:55:38', '2017-10-18 07:28:25'),
(104, 102, 1, 'all', NULL, '2017-09-21 12:55:38', '2017-10-18 07:28:25'),
(105, 103, 1, 'all', NULL, '2017-09-21 12:55:39', '2017-10-18 07:28:25'),
(106, 104, 1, 'all', NULL, '2017-09-21 12:55:39', '2017-10-18 07:28:25'),
(107, 105, 1, 'all', NULL, '2017-09-21 12:55:39', '2017-10-18 07:28:25'),
(108, 106, 1, 'all', NULL, '2017-10-11 13:23:23', '2017-10-18 07:28:25'),
(109, 107, 1, 'all', NULL, '2017-10-11 13:23:23', '2017-10-18 07:28:25'),
(110, 108, 1, 'all', '2017-10-17 13:38:59', '2017-10-17 11:16:02', '2017-10-17 13:38:59'),
(111, 4, 1, 'all', NULL, '2017-10-17 13:47:24', '2017-10-18 07:28:20'),
(112, 108, 1, 'all', NULL, '2017-10-17 13:47:30', '2017-10-18 07:28:25'),
(113, 109, 1, 'all', NULL, '2017-10-18 07:28:25', '2017-10-18 07:28:25'),
(114, 110, 1, 'all', NULL, '2017-10-18 07:28:25', '2017-10-18 07:28:25'),
(115, 111, 1, 'all', NULL, '2017-10-18 07:28:26', '2017-10-18 07:28:26'),
(116, 112, 1, 'all', NULL, '2017-10-18 07:28:26', '2017-10-18 07:28:26'),
(117, 113, 1, 'all', NULL, '2017-10-18 07:28:26', '2017-10-18 07:28:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `routes`
--

CREATE TABLE `routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `area_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `routes`
--

INSERT INTO `routes` (`id`, `area_id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'SONAMAR NORTH', NULL, '2017-09-14 13:03:37', '2017-09-14 13:04:29'),
(2, 1, 'SONAMAR STH EAST', NULL, '2017-09-14 13:03:58', '2017-09-14 13:04:39'),
(3, 3, 'SONAMAR SOUTH', NULL, '2017-09-14 13:04:15', '2017-09-14 13:04:57'),
(4, 1, 'SONOMAR NTH EAST', NULL, '2017-09-14 13:05:24', '2017-09-14 13:05:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seat_types`
--

CREATE TABLE `seat_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `acronym` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_enable` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seat_types`
--

INSERT INTO `seat_types` (`id`, `title`, `description`, `acronym`, `default_quantity`, `is_enable`, `sort`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'VIP', '{\"es\":\"Nuestro paquete VIP incluye cena con nuestro men\\u00fa especial tem\\u00e1tico e igualmente dispondr\\u00e1 de los mejores asientos y el mejor servicio con un camarero en exclusiva para usted.\",\"en\":null}', 'VIP', 350, 1, 1, NULL, '2017-09-15 08:33:27', '2017-09-18 12:15:57'),
(2, 'PLATINUM', '{\"es\":\"<p>El paquete Platinum garantiza los mejores asientos del teatro, degustando nuestra cena especial y tem&aacute;tica.<\\/p>\",\"en\":null}', 'PT', 50, 1, 2, NULL, '2017-09-15 08:33:47', '2017-09-22 13:17:14'),
(3, 'VIP SOLO SHOW', '{\"es\":\"<p>Nuestro paquete VIP se compone de los mejores asientos disponibles y el mejor servicio con un camarero para usted de manera exclusiva y personal. Incluye una copa de champ&aacute;n y bombones.<\\/p>\",\"en\":\"<p><strong>Lorem Ipsum<\\/strong>&nbsp;es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno est&aacute;ndar de las industrias desde el a&ntilde;o 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido us&oacute; una galer&iacute;a de textos y los mezcl&oacute; de tal manera que logr&oacute; hacer un libro de textos especimen. No s&oacute;lo sobrevivi&oacute; 500 a&ntilde;os, sino que tambien ingres&oacute; como texto de relleno en documentos electr&oacute;nicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creaci&oacute;n de las hojas &quot;Letraset&quot;, las cuales contenian pasajes de Lorem Ipsum, y m&aacute;s recientemente con software de autoedici&oacute;n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.<\\/p>\"}', 'VIPS', 100, 1, 4, NULL, '2017-09-15 08:34:00', '2017-09-22 13:17:34'),
(4, 'PLATINUM SOLO SHOW', '{\"es\":\"<p>El paquete Platinum le garantiza algunos de los mejores asientos del recinto, e incluye una copa de cava y bombones.<\\/p>\",\"en\":null}', 'PTS', 100, 1, 5, NULL, '2017-09-18 12:14:37', '2017-09-22 13:17:46'),
(5, 'GOLD', '{\"es\":\"<p>Nuestros asientos Gold ofrecen una incre&iacute;ble relaci&oacute;n calidad-precio. Se compone de un delicioso men&uacute; de tres platos, con vinos cl&aacute;sicos espa&ntilde;oles y localidades con buena visibilidad, que le proporcionar&aacute;n una experiencia memorable.<\\/p>\",\"en\":null}', 'GL', 100, 1, 3, NULL, '2017-09-18 12:15:08', '2017-09-22 13:17:24'),
(6, 'GOLD SOLO SHOW', '{\"es\":\"Nuestros asientos Gold ofrecen una incre\\u00edble relaci\\u00f3n calidad-precio, con una gran vista del escenario y una experiencia memorable.\",\"en\":null}', 'GLS', 100, 1, 6, NULL, '2017-09-18 12:15:37', '2017-09-18 12:15:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seat_type_show`
--

CREATE TABLE `seat_type_show` (
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL,
  `default_quantity` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seat_type_show`
--

INSERT INTO `seat_type_show` (`seat_type_id`, `show_id`, `default_quantity`) VALUES
(1, 1, 0),
(3, 1, 0),
(5, 1, 0),
(6, 1, 0),
(1, 2, 0),
(2, 2, 0),
(5, 2, 0),
(1, 3, 0),
(2, 3, 0),
(3, 3, 0),
(4, 3, 0),
(5, 3, 0),
(6, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shows`
--

CREATE TABLE `shows` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `shows`
--

INSERT INTO `shows` (`id`, `name`, `acronym`, `description`, `image`, `sort`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'VAMPIRICA', 'VAM', '{\"es\":null,\"en\":null}', NULL, 1, NULL, '2017-09-07 13:25:08', '2017-10-17 12:09:44'),
(2, 'FUSION ENCORE', 'FS', '{\"es\":\"<p>FUSI&Oacute;N ENCORE es din&aacute;mico, vibrante, colorido, divertido, sensual y con un ritmo trepidante. Escena tras escena les dejaran boquiabierto. D&eacute;jense llevar a trav&eacute;s de un mundo internacional de diferentes estilos y culturas, desde el este al oeste, pasando desde luego por nuestra querida patria Espa&ntilde;a, donde podr&aacute;n disfrutar del mejor flamenco que se puede ver en la actualidad. Acentuado con detalles de comedia visual y acrobacias incre&iacute;bles, desde los mejores artistas internacionales, este impresionante espect&aacute;culo no tiene igual, rompe las barreras del idioma y de la edad. Es apto para todos los p&uacute;blicos.LOS ARTISTAS Cantantes, Comedia, Acr&oacute;batas, Contorsionista, La Orquesta, Violinista, Caballos, Bailarines<\\/p>\",\"en\":null}', NULL, 2, NULL, '2017-09-18 12:18:20', '2017-10-17 12:09:25'),
(3, 'Halloween', 'VHA', '{\"es\":\"<p><strong>ES_HALLOWEEN<\\/strong><\\/p>\",\"en\":\"<p><strong>EN_HALLOWEEN<\\/strong><\\/p>\\r\\n\\r\\n<hr \\/>\\r\\n<p>&nbsp;<\\/p>\"}', '20171025103626-mood-halloween-emailjpg.jpg', 5, NULL, '2017-10-25 08:36:26', '2017-10-25 08:36:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `show_ticket_type`
--

CREATE TABLE `show_ticket_type` (
  `show_id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `show_ticket_type`
--

INSERT INTO `show_ticket_type` (`show_id`, `ticket_type_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_types`
--

CREATE TABLE `ticket_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `take_place` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `sort` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ticket_types`
--

INSERT INTO `ticket_types` (`id`, `title`, `acronym`, `take_place`, `sort`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adult', 'ADU', 1, 0, NULL, '2017-09-15 08:35:31', '2017-09-15 10:34:08'),
(2, 'Children', 'CHD', 1, 0, NULL, '2017-09-15 08:36:01', '2017-09-15 10:34:16'),
(3, 'Infant', 'INF', 0, 0, NULL, '2017-09-15 08:36:22', '2017-09-15 10:35:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporters`
--

CREATE TABLE `transporters` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `transporters`
--

INSERT INTO `transporters` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'AUTOCARES', NULL, '2017-09-14 13:34:08', '2017-09-15 06:09:35'),
(2, 'PRIVADO', NULL, '2017-09-14 13:46:33', '2017-09-14 13:46:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `resellers_type_id` int(10) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `resellers_type_id`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adrian Escalada', 'admin@refineriaweb.com', '$2y$10$oEX9K9FeQZn2WeS3S07zqeyPGm7C2nTYnA8mefOCqsOSh99MxsGSG', 1, 1, 'DcGsG3H7eCpleQtMkGABjqx6DUggfi8AJONjllIXkitiZvCakBOh30PO8HFP', NULL, '2017-09-07 13:24:03', '2017-10-06 12:40:42'),
(2, 'ONLINE (Sonamar)', 'info@globobalearations.com', '$2y$10$giGL5aRqDO2iKgUCkDdK6ePjEMx0xS1upfs2sdj9kYJzOAc46aNmG', 1, 1, NULL, NULL, '2017-10-20 12:27:52', '2017-11-10 12:05:13'),
(3, 'online', 'web@globobalearations.com', '', 11, 1, NULL, NULL, '2017-10-26 14:02:03', '2017-10-26 14:02:03');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `viewpayment`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `viewpayment` (
`id` int(11)
,`user_id` int(10) unsigned
,`user_name` varchar(255)
,`user_email` varchar(255)
,`customers_name` varchar(255)
,`customers_email` varchar(255)
,`customers_phone` varchar(255)
,`total` decimal(8,2)
,`reservation_number` varchar(255)
,`reference_number` varchar(255)
,`company` varchar(255)
,`channels` varchar(255)
,`reservation_id` int(10) unsigned
,`payment_method_id` int(10) unsigned
,`method` varchar(255)
,`pass_id` int(10) unsigned
,`pass_datetime` datetime
,`pass_name` varchar(279)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `viewreservations`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `viewreservations` (
`id` int(10) unsigned
,`reference_number` varchar(255)
,`phone` varchar(255)
,`reservation_number` varchar(255)
,`name` varchar(255)
,`email` varchar(255)
,`canceled_date` timestamp
,`created_by` int(10) unsigned
,`canceled_by` int(10) unsigned
,`discount` int(10) unsigned
,`identification_number` varchar(255)
,`canceled_reason` text
,`finished` tinyint(1)
,`channel_id` int(10) unsigned
,`channel` varchar(255)
,`customer_id` int(10) unsigned
,`customer` varchar(255)
,`company` varchar(255)
,`pass_id` int(10) unsigned
,`promocode_id` int(10) unsigned
,`reconcile` int(10) unsigned
,`passe` varchar(277)
,`ADU` decimal(32,0)
,`CHD` decimal(32,0)
,`INF` decimal(32,0)
,`TOT` decimal(33,0)
,`deleted_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura para la vista `viewpayment`
--
DROP TABLE IF EXISTS `viewpayment`;

CREATE ALGORITHM=UNDEFINED DEFINER=`globobalear`@`%` SQL SECURITY DEFINER VIEW `viewpayment`  AS  select `payment_method_reservations`.`id` AS `id`,`users`.`id` AS `user_id`,`users`.`name` AS `user_name`,`users`.`email` AS `user_email`,`customers`.`name` AS `customers_name`,`customers`.`email` AS `customers_email`,`customers`.`phone` AS `customers_phone`,`payment_method_reservations`.`total` AS `total`,`reservations`.`reservation_number` AS `reservation_number`,`reservations`.`reference_number` AS `reference_number`,`resellers`.`company` AS `company`,`channels`.`name` AS `channels`,`payment_method_reservations`.`reservation_id` AS `reservation_id`,`payment_method_reservations`.`payment_method_id` AS `payment_method_id`,`payment_methods`.`name` AS `method`,`reservations`.`pass_id` AS `pass_id`,`passes`.`datetime` AS `pass_datetime`,(select concat(`shows`.`name`,' | ',convert(date_format(`passes`.`datetime`,'%d/%m/%Y %H:%i') using utf8mb4)) AS `passe` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`)) AS `pass_name`,`payment_method_reservations`.`created_at` AS `created_at` from (((((((`users` left join `payment_method_reservations` on((`users`.`id` = `payment_method_reservations`.`user_id`))) left join `reservations` on((`payment_method_reservations`.`reservation_id` = `reservations`.`id`))) left join `channels` on((`channels`.`id` = `reservations`.`channel_id`))) left join `payment_methods` on((`payment_methods`.`id` = `payment_method_reservations`.`payment_method_id`))) left join `customers` on((`customers`.`id` = `reservations`.`customer_id`))) left join `passes` on((`passes`.`id` = `reservations`.`pass_id`))) left join `resellers` on((`resellers`.`id` = `reservations`.`reseller_id`))) where (isnull(`payment_method_reservations`.`deleted_at`) and (`reservations`.`finished` = 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `viewreservations`
--
DROP TABLE IF EXISTS `viewreservations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`globobalear`@`%` SQL SECURITY DEFINER VIEW `viewreservations`  AS  select `r`.`id` AS `id`,`r`.`reference_number` AS `reference_number`,`r`.`phone` AS `phone`,`r`.`reservation_number` AS `reservation_number`,`r`.`name` AS `name`,`r`.`email` AS `email`,`r`.`canceled_date` AS `canceled_date`,`r`.`created_by` AS `created_by`,`r`.`canceled_by` AS `canceled_by`,`r`.`discount` AS `discount`,`r`.`identification_number` AS `identification_number`,`r`.`canceled_reason` AS `canceled_reason`,`r`.`finished` AS `finished`,`r`.`channel_id` AS `channel_id`,`rc`.`name` AS `channel`,`r`.`customer_id` AS `customer_id`,`c`.`name` AS `customer`,`rl`.`company` AS `company`,`r`.`pass_id` AS `pass_id`,`r`.`promocode_id` AS `promocode_id`,`r`.`reconcile` AS `reconcile`,(select concat(`s`.`name`,' | ',`p`.`datetime`) AS `passe` from (`passes` `p` left join `shows` `s` on((`p`.`show_id` = `s`.`id`))) where (`p`.`id` = `r`.`pass_id`)) AS `passe`,(select sum(`rt1`.`quantity`) AS `INF` from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`) AS `ADU`,(select sum(`rt1`.`quantity`) AS `INF` from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`) AS `CHD`,(select sum(`rt1`.`quantity`) AS `INF` from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 3)) group by `rt1`.`ticket_type_id`) AS `INF`,(ifnull((select sum(`rt1`.`quantity`) AS `INF` from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`),0) + ifnull((select sum(`rt1`.`quantity`) AS `INF` from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),0)) AS `TOT`,`r`.`deleted_at` AS `deleted_at`,`r`.`created_at` AS `created_at`,`r`.`updated_at` AS `updated_at` from (((((`reservations` `r` left join `reservation_tickets` `rt` on((`r`.`id` = `rt`.`reservation_id`))) left join `resellers` `rl` on((`r`.`reseller_id` = `rl`.`id`))) left join `channels` `rc` on((`r`.`channel_id` = `rc`.`id`))) left join `customers` `c` on((`r`.`customer_id` = `c`.`id`))) left join `passes` `p` on((`r`.`pass_id` = `p`.`id`))) group by `r`.`id` order by `r`.`id` desc ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agent_types`
--
ALTER TABLE `agent_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buses_transporter_id_foreign` (`transporter_id`),
  ADD KEY `buses_route_id_foreign` (`route_id`),
  ADD KEY `buses_pass_id_foreign` (`pass_id`);

--
-- Indices de la tabla `cartes`
--
ALTER TABLE `cartes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cartes_show_id_foreign` (`show_id`),
  ADD KEY `cartes_seat_type_id_foreign` (`seat_type_id`);

--
-- Indices de la tabla `carte_menu`
--
ALTER TABLE `carte_menu`
  ADD KEY `carte_menu_carte_id_foreign` (`carte_id`),
  ADD KEY `carte_menu_menu_id_foreign` (`menu_id`);

--
-- Indices de la tabla `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channels_passes_seller_id_foreign` (`passes_seller_id`);

--
-- Indices de la tabla `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_gender_id_foreign` (`gender_id`),
  ADD KEY `customers_country_id_foreign` (`country_id`),
  ADD KEY `customers_customer_how_you_meet_us_id_foreign` (`customer_how_you_meet_us_id`),
  ADD KEY `customers_languages_id_foreign` (`languages_id`);

--
-- Indices de la tabla `customers_how_you_meet_us`
--
ALTER TABLE `customers_how_you_meet_us`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `customers_languages`
--
ALTER TABLE `customers_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dishes_dishes_type_id_foreign` (`dishes_type_id`);

--
-- Indices de la tabla `dishes_types`
--
ALTER TABLE `dishes_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dish_menu`
--
ALTER TABLE `dish_menu`
  ADD KEY `dish_menu_menu_id_foreign` (`menu_id`),
  ADD KEY `dish_menu_dish_id_foreign` (`dish_id`);

--
-- Indices de la tabla `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `global_conf`
--
ALTER TABLE `global_conf`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_seat_type_id_foreign` (`seat_type_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `passes`
--
ALTER TABLE `passes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passes_show_id_foreign` (`show_id`);

--
-- Indices de la tabla `passes_prices`
--
ALTER TABLE `passes_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passes_prices_pass_seat_type_id_foreign` (`pass_seat_type_id`),
  ADD KEY `passes_prices_ticket_type_id_foreign` (`ticket_type_id`);

--
-- Indices de la tabla `passes_sellers`
--
ALTER TABLE `passes_sellers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`(191));

--
-- Indices de la tabla `pass_seat_type`
--
ALTER TABLE `pass_seat_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pass_seat_type_pass_id_foreign` (`pass_id`),
  ADD KEY `pass_seat_type_seat_type_id_foreign` (`seat_type_id`);

--
-- Indices de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `payment_method_reservations`
--
ALTER TABLE `payment_method_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_method_reservation_payment_method_id_foreign` (`payment_method_id`),
  ADD KEY `payment_method_reservation_reservation_id_foreign` (`reservation_id`),
  ADD KEY `payment_method_reservation_user_id_foreign` (`user_id`),
  ADD KEY `payment_method_reservations_removed_by_foreign` (`removed_by`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pickup_points`
--
ALTER TABLE `pickup_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pickup_points_city_id_foreign` (`city_id`);

--
-- Indices de la tabla `pickup_point_route`
--
ALTER TABLE `pickup_point_route`
  ADD KEY `pickup_point_route_pickup_point_id_foreign` (`pickup_point_id`),
  ADD KEY `pickup_point_route_route_id_foreign` (`route_id`);

--
-- Indices de la tabla `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `promocode_show`
--
ALTER TABLE `promocode_show`
  ADD KEY `promocode_show_promocode_id_foreign` (`promocode_id`),
  ADD KEY `promocode_show_show_id_foreign` (`show_id`);

--
-- Indices de la tabla `resellers`
--
ALTER TABLE `resellers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resellers_passes_sellers_id_foreign` (`passes_seller_id`),
  ADD KEY `resellers_resellers_types_id_foreign` (`resellers_type_id`),
  ADD KEY `resellers_agent_types_id_foreign` (`agent_type_id`),
  ADD KEY `resellers_areas_id_foreign` (`area_id`),
  ADD KEY `resellers_languages_id_foreign` (`language_id`),
  ADD KEY `resellers_countries_id_foreign` (`country_id`),
  ADD KEY `resellers_users_id_foreign` (`user_id`);

--
-- Indices de la tabla `resellers_types`
--
ALTER TABLE `resellers_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_created_by_foreign` (`created_by`),
  ADD KEY `reservations_pass_id_foreign` (`pass_id`),
  ADD KEY `reservations_promocode_id_foreign` (`promocode_id`),
  ADD KEY `reservations_channel_id_foreign` (`channel_id`),
  ADD KEY `canceled_by_users` (`canceled_by`),
  ADD KEY `reservations_customer_id_foreign` (`customer_id`),
  ADD KEY `reservations_reseller_id_foreign` (`reseller_id`),
  ADD KEY `	reservations_finished_id_foreign` (`finished_by`);

--
-- Indices de la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_menu_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_menu_menu_id_foreign` (`menu_id`);

--
-- Indices de la tabla `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_tickets_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_tickets_seat_type_id_foreign` (`seat_type_id`),
  ADD KEY `reservation_tickets_ticket_type_id_foreign` (`ticket_type_id`);

--
-- Indices de la tabla `reservation_transports`
--
ALTER TABLE `reservation_transports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_transports_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_transports_bus_id_foreign` (`bus_id`),
  ADD KEY `reservation_transports_pickup_point_id_foreign` (`pickup_point_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routes_area_id_foreign` (`area_id`);

--
-- Indices de la tabla `seat_types`
--
ALTER TABLE `seat_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `seat_type_show`
--
ALTER TABLE `seat_type_show`
  ADD KEY `seat_type_show_seat_type_id_foreign` (`seat_type_id`),
  ADD KEY `seat_type_show_show_id_foreign` (`show_id`);

--
-- Indices de la tabla `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `show_ticket_type`
--
ALTER TABLE `show_ticket_type`
  ADD KEY `show_ticket_type_show_id_foreign` (`show_id`),
  ADD KEY `show_ticket_type_ticket_type_id_foreign` (`ticket_type_id`);

--
-- Indices de la tabla `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `transporters`
--
ALTER TABLE `transporters`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_resellers_type_id_foreign` (`resellers_type_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agent_types`
--
ALTER TABLE `agent_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT de la tabla `cartes`
--
ALTER TABLE `cartes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `channels`
--
ALTER TABLE `channels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;
--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `customers_how_you_meet_us`
--
ALTER TABLE `customers_how_you_meet_us`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `customers_languages`
--
ALTER TABLE `customers_languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `dishes`
--
ALTER TABLE `dishes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `dishes_types`
--
ALTER TABLE `dishes_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `genders`
--
ALTER TABLE `genders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `global_conf`
--
ALTER TABLE `global_conf`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `passes`
--
ALTER TABLE `passes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT de la tabla `passes_prices`
--
ALTER TABLE `passes_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=853;
--
-- AUTO_INCREMENT de la tabla `passes_sellers`
--
ALTER TABLE `passes_sellers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `pass_seat_type`
--
ALTER TABLE `pass_seat_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;
--
-- AUTO_INCREMENT de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `payment_method_reservations`
--
ALTER TABLE `payment_method_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;
--
-- AUTO_INCREMENT de la tabla `pickup_points`
--
ALTER TABLE `pickup_points`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `resellers`
--
ALTER TABLE `resellers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `resellers_types`
--
ALTER TABLE `resellers_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT de la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT de la tabla `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT de la tabla `reservation_transports`
--
ALTER TABLE `reservation_transports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT de la tabla `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `seat_types`
--
ALTER TABLE `seat_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `ticket_types`
--
ALTER TABLE `ticket_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `transporters`
--
ALTER TABLE `transporters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `buses`
--
ALTER TABLE `buses`
  ADD CONSTRAINT `buses_pass_id_foreign` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `buses_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `buses_transporter_id_foreign` FOREIGN KEY (`transporter_id`) REFERENCES `transporters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cartes`
--
ALTER TABLE `cartes`
  ADD CONSTRAINT `cartes_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cartes_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carte_menu`
--
ALTER TABLE `carte_menu`
  ADD CONSTRAINT `carte_menu_carte_id_foreign` FOREIGN KEY (`carte_id`) REFERENCES `cartes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carte_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `channels_passes_seller_id_foreign` FOREIGN KEY (`passes_seller_id`) REFERENCES `passes_sellers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customers_customer_how_you_meet_us_id_foreign` FOREIGN KEY (`customer_how_you_meet_us_id`) REFERENCES `customers_how_you_meet_us` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customers_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customers_languages_id_foreign` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `dishes`
--
ALTER TABLE `dishes`
  ADD CONSTRAINT `dishes_dishes_type_id_foreign` FOREIGN KEY (`dishes_type_id`) REFERENCES `dishes_types` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `dish_menu`
--
ALTER TABLE `dish_menu`
  ADD CONSTRAINT `dish_menu_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dish_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `passes`
--
ALTER TABLE `passes`
  ADD CONSTRAINT `passes_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `passes_prices`
--
ALTER TABLE `passes_prices`
  ADD CONSTRAINT `passes_prices_pass_seat_type_id_foreign` FOREIGN KEY (`pass_seat_type_id`) REFERENCES `pass_seat_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `passes_prices_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pass_seat_type`
--
ALTER TABLE `pass_seat_type`
  ADD CONSTRAINT `pass_seat_type_pass_id_foreign` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pass_seat_type_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `payment_method_reservations`
--
ALTER TABLE `payment_method_reservations`
  ADD CONSTRAINT `payment_method_reservation_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_method_reservation_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_method_reservation_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payment_method_reservations_removed_by_foreign` FOREIGN KEY (`removed_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pickup_points`
--
ALTER TABLE `pickup_points`
  ADD CONSTRAINT `pickup_points_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pickup_point_route`
--
ALTER TABLE `pickup_point_route`
  ADD CONSTRAINT `pickup_point_route_pickup_point_id_foreign` FOREIGN KEY (`pickup_point_id`) REFERENCES `pickup_points` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pickup_point_route_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `promocode_show`
--
ALTER TABLE `promocode_show`
  ADD CONSTRAINT `promocode_show_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `promocode_show_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `resellers`
--
ALTER TABLE `resellers`
  ADD CONSTRAINT `resellers_agent_types_id_foreign` FOREIGN KEY (`agent_type_id`) REFERENCES `agent_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_areas_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_countries_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_languages_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_passes_sellers_id_foreign` FOREIGN KEY (`passes_seller_id`) REFERENCES `passes_sellers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_resellers_types_id_foreign` FOREIGN KEY (`resellers_type_id`) REFERENCES `resellers_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resellers_users_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `canceled_by_users` FOREIGN KEY (`canceled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  ADD CONSTRAINT `reservations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `reservations_finished_id_foreign` FOREIGN KEY (`finished_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_pass_id_foreign` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_reseller_id_foreign` FOREIGN KEY (`reseller_id`) REFERENCES `resellers` (`id`);

--
-- Filtros para la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  ADD CONSTRAINT `reservation_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_menu_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
  ADD CONSTRAINT `reservation_tickets_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_tickets_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_tickets_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservation_transports`
--
ALTER TABLE `reservation_transports`
  ADD CONSTRAINT `reservation_transports_bus_id_foreign` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_transports_pickup_point_id_foreign` FOREIGN KEY (`pickup_point_id`) REFERENCES `pickup_points` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_transports_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `seat_type_show`
--
ALTER TABLE `seat_type_show`
  ADD CONSTRAINT `seat_type_show_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `seat_type_show_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `show_ticket_type`
--
ALTER TABLE `show_ticket_type`
  ADD CONSTRAINT `show_ticket_type_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_ticket_type_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_resellers_type_id_foreign` FOREIGN KEY (`resellers_type_id`) REFERENCES `resellers_types` (`id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
