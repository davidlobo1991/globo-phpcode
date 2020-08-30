-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-01-2018 a las 11:16:52
-- Versión del servidor: 10.1.29-MariaDB
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `globoreserv_globo_pre`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`globoreserv_crs`@`%` PROCEDURE `pass_price` (IN `pass` INT, IN `ticket_type` INT, IN `seat_type` INT)  NO SQL
SELECT id, pass_seat_type_id, ticket_type_id, price FROM passes_prices as pp
              WHERE pp.ticket_type_id = ticket_type 
              AND pp.pass_seat_type_id = (
                SELECT pst.id as pass_seat_type from pass_seat_type as pst 
                WHERE pst.seat_type_id = seat_type AND pst.pass_id = pass
              )$$

CREATE DEFINER=`globoreserv_crs`@`%` PROCEDURE `sp_reservations_availability` (IN `in_passe` INT UNSIGNED, IN `in_seller` INT UNSIGNED)  NO SQL
SELECT
        st.title,
        st.acronym, 
        st.default_quantity,
        IFNULL(IFNULL(seats_sold,0) + IFNULL(seats_sold_pack,0),0) as seats_sold,
        ABS(
            IFNULL(IFNULL(IFNULL(seats_sold,0) + IFNULL(seats_sold_pack,0),0), 0) - IFNULL(total_seats, 0)
        ) AS total_solded

        FROM
        seat_types AS st
        LEFT JOIN(
        SELECT st.id,
            (SUM(rt.quantity)) AS seats_sold,
            (SUM(rp.quantity)) AS seats_sold_pack
        FROM
        seat_types st
        LEFT JOIN reservation_tickets rt ON
        st.id = rt.seat_type_id
        LEFT JOIN reservation_packs rp ON
        st.id = rp.seat_type_id
        LEFT JOIN reservations r ON
        r.id = rt.reservation_id or r.id = rp.reservation_id
        LEFT JOIN channels rc ON
        r.channel_id = rc.id
        LEFT JOIN ticket_types t ON
        t.id = rt.ticket_type_id or t.id = rp.ticket_type_id 
        WHERE
        r.finished = 1 AND r.canceled_date IS NULL AND t.take_place != 0 AND rc.passes_seller_id = in_seller AND (r.pass_id = in_passe or rp.pass_id = in_passe)
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
        WHERE st.is_enable = 1
        ORDER BY
        st.sort$$

CREATE DEFINER=`globoreserv_crs`@`%` PROCEDURE `sp_reservations_total_pack` (IN `in_passe` INT, IN `in_reservation` INT)  NO SQL
SELECT
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`) as ADU ,
            
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`) as CHD,
                        
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id` )        as INF,   
            
        (
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0)) +
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0))
              
        ) as TOT
        FROM
        `reservations` `r`
        LEFT JOIN reservation_packs rp ON
         r.id = rp.reservation_id    
        
        LEFT JOIN `resellers` `rl`
        ON
            (`r`.`reseller_id` = `rl`.`id`)
        
        LEFT JOIN `channels` `rc`
        ON
            (`r`.`channel_id` = `rc`.`id`)
        
        LEFT JOIN `customers` `c`
        ON
            (`r`.`customer_id` = `c`.`id`)
            
        LEFT JOIN `reservation_types` `rtp`
        ON
            (`r`.`reservation_type_id` = `rtp`.`id`)
        
        LEFT JOIN `packs` `pa`
        ON
            (`r`.`pack_id` = `pa`.`id`)
        
        LEFT JOIN `passes` `p`
        ON
            (`r`.`pass_id` = `p`.`id`)

     where rp.pass_id = in_passe
     and r.id = in_reservation
        GROUP BY
        `r`.`id`
        ORDER BY
        `r`.`id`
        DESC$$

CREATE DEFINER=`globoreserv_crs`@`%` PROCEDURE `sp_reservations_total_passe` (IN `in_passe` INT)  NO SQL
SELECT

        
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 1 GROUP BY `rt1`.`ticket_type_id` )
        ,
        (SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id` )          
        )

        as ADU,

            
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 2 GROUP BY `rt1`.`ticket_type_id` )          
        ) as CHD,

            
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 3 GROUP BY `rt1`.`ticket_type_id` )          
        ) as INF,

        (
        (IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 1 GROUP BY `rt1`.`ticket_type_id`),0)) + 
        (IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 2 GROUP BY `rt1`.`ticket_type_id`), 0))
        ) 
        +
        (
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0)) +
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0))
        +
        (
        (IFNULL((SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id`),0)))


        ) as TOT

        FROM

        `reservations` `r`
        LEFT JOIN `reservation_tickets` `rt`
        ON
            (`r`.`id` = `rt`.`reservation_id`)

        LEFT JOIN reservation_packs rp ON
        r.id = rp.reservation_id    

        LEFT JOIN `resellers` `rl`
        ON
            (`r`.`reseller_id` = `rl`.`id`)

        LEFT JOIN `channels` `rc`
        ON
            (`r`.`channel_id` = `rc`.`id`)

        LEFT JOIN `customers` `c`
        ON
            (`r`.`customer_id` = `c`.`id`)
            
        LEFT JOIN `reservation_types` `rtp`
        ON
            (`r`.`reservation_type_id` = `rtp`.`id`)

        LEFT JOIN `packs` `pa`
        ON
            (`r`.`pack_id` = `pa`.`id`)

        LEFT JOIN `passes` `p`
        ON
            (`r`.`pass_id` = `p`.`id`)

        where r.pass_id = in_passe or rp.pass_id = in_passe
        GROUP BY
        `r`.`id`
        ORDER BY
        `r`.`id`
        DESC$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agent_types`
--

CREATE TABLE `agent_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Mallorca North East', 'NE', NULL, '2017-09-14 11:01:47', '2017-09-14 11:01:47'),
(2, 'Mallorca North', 'NO', NULL, '2017-09-14 11:02:03', '2017-09-14 11:02:03'),
(3, 'Mallorca South', 'SO', NULL, '2017-09-14 11:02:23', '2017-09-14 11:02:23'),
(4, 'Mallorca South East', 'SE', NULL, '2017-09-14 11:02:32', '2017-09-14 11:02:32');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_es` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `acronym`, `description_es`, `description_en`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'CRUISE', 'CR', '', '', NULL, NULL, NULL),
(2, 'BAR', 'BR', '', '', NULL, NULL, NULL),
(3, 'FEST', 'FS', '', '', NULL, NULL, NULL),
(4, 'BEACHCLUB', 'BC', '', '', NULL, NULL, NULL),
(5, 'CLUB', 'CL', '', '', NULL, NULL, NULL);

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
(1, 'Tour Ops', 2, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09'),
(2, 'Agents', 3, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09'),
(3, 'Telesales', 1, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09'),
(4, 'Box Office', 1, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09'),
(5, 'Internal', 1, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09'),
(6, 'Online', 1, 1, NULL, '2016-10-05 03:03:09', '2016-10-05 03:03:09');

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
(248, 'ZW', 'Zimbabwe', NULL, NULL, NULL),
(249, '-', '-', NULL, NULL, NULL);

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
(1, 'Adrian Escalada', '1', '1976-09-08 14:46:51', '697877831', 'beatriz de pinos', 'Palma de Mallorca', '00075', 'adrianescalada@gmail.com', NULL, 1, NULL, 1, NULL, 1, 1, 1, NULL, '2017-09-19 10:46:51', '2017-09-20 09:16:45'),
(5, 'Fernanda/Globo', NULL, NULL, '66666', NULL, NULL, NULL, 'fcoringrato@globobalear.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, '2017-12-11 14:56:08', '2017-12-11 14:56:08');

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
  `paypal` int(11) NOT NULL COMMENT 'Comission Paypal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `global_conf`
--

INSERT INTO `global_conf` (`id`, `amber_trigger`, `family_discount`, `gold_discount`, `booking_fee`, `pound_exchange`, `paypal`, `created_at`, `updated_at`) VALUES
(1, 10, '0.00', '0.00', '5.00', '1.18643462', 10, NULL, '2017-12-17 13:51:07');

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
(44, '2017_10_20_095326_add_user_id_to_payment_method_reservation_table', 16),
(45, '2017_10_20_151647_add_finished_by_to_reservations_table', 17),
(47, '2017_10_24_151002_add_remove_reason_remove_date_to_payment_method_reservations_table', 18),
(48, '2017_10_26_131837_create_ViewPayment', 19),
(53, '2017_11_16_101408_create_packs_table', 20),
(56, '2017_11_16_130736_create_wristbands_table', 21),
(57, '2017_11_16_130851_create_wristband_passes_table', 21),
(58, '2017_11_17_083140_create_show_wristband_passes_table', 21),
(59, '2017_11_17_129322_create_categories_table', 21),
(60, '2017_11_17_129666_alter_show_add_category', 21),
(61, '2017_11_16_112700_create_pack_show_table', 22),
(62, '2017_11_21_151013_add_comissions_to_shows', 23),
(63, '2017_08_09_129322_create_packs_module_tables', 24),
(65, '2017_11_23_141749_add_pack_id_wristband_id_to_reservations_table', 26),
(66, '2017_11_27_115037_create_reservations_packs__table', 27),
(67, '2017_11_24_100424_create_reservations_types', 28),
(68, '2017_11_23_141747_add_pack_id_wristband_id_to_reservations_table', 29),
(69, '2017_11_24_084133_create_reservation_wristband_pass_table', 30),
(70, '2017_11_27_144832_create_promocode_wristband_pass_table', 31),
(71, '2017_11_28_104417_create_promocode_wristband_table', 32),
(72, '2017_11_28_104839_create_promocode_pack_table', 32),
(73, '2017_12_03_185154_add_comments_to_reservations_table', 33),
(74, '2017_12_08_142052_create_sp_reservations_total_passe_table', 34),
(75, '2017_12_09_105748_create_sp_reservations_total_pack_table', 34),
(76, '2017_12_17_120206_add_paypal_to_global_conf_table', 35),
(77, '2017_12_17_192708_add_booking_fee_to_reservations_table', 36),
(78, '2017_12_18_145120_create_pack_wristband_table', 37),
(79, '2017_12_14_124737_create_pass_price_procedure', 38),
(80, '2017_12_19_103142_create_reservation_pack_wristbands_table', 38),
(81, '2017_12_21_102151_add_paypal__to_reservations_table', 39);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packs`
--

CREATE TABLE `packs` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `packs`
--

INSERT INTO `packs` (`id`, `title`, `acronym`, `date_start`, `date_end`, `deleted_at`, `created_at`, `updated_at`) VALUES
(16, 'pack Mayo', 'pkm', '2018-05-01 00:00:00', '2018-05-31 00:00:00', NULL, '2018-01-03 14:18:40', '2018-01-03 14:18:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pack_shows`
--

CREATE TABLE `pack_shows` (
  `pack_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` int(10) UNSIGNED NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pack_shows`
--

INSERT INTO `pack_shows` (`pack_id`, `show_id`, `seat_type_id`, `ticket_type_id`, `price`) VALUES
(16, 16, 1, 1, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pack_wristbands`
--

CREATE TABLE `pack_wristbands` (
  `pack_id` int(10) UNSIGNED NOT NULL,
  `wristband_passes_id` int(10) UNSIGNED NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pack_wristbands`
--

INSERT INTO `pack_wristbands` (`pack_id`, `wristband_passes_id`, `price`) VALUES
(16, 4, 0);

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
(613, '2018-01-06 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(614, '2018-01-13 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(615, '2018-01-20 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(616, '2018-01-27 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(617, '2018-02-03 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(618, '2018-02-10 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(619, '2018-02-17 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(620, '2018-02-24 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(621, '2018-03-03 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(622, '2018-03-10 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(623, '2018-03-17 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(624, '2018-03-24 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(625, '2018-03-31 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(626, '2018-04-07 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(627, '2018-04-14 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(628, '2018-04-21 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(629, '2018-04-28 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(630, '2018-05-05 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(631, '2018-05-12 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(632, '2018-05-19 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(633, '2018-05-26 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(634, '2018-06-02 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(635, '2018-06-09 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(636, '2018-06-16 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(637, '2018-06-23 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(638, '2018-06-30 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(639, '2018-07-07 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(640, '2018-07-14 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(641, '2018-07-21 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(642, '2018-07-28 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(643, '2018-08-04 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(644, '2018-08-11 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(645, '2018-08-18 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(646, '2018-08-25 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(647, '2018-09-01 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(648, '2018-09-08 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(649, '2018-09-15 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(650, '2018-09-22 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(651, '2018-09-29 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(652, '2018-10-06 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(653, '2018-10-13 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(654, '2018-10-20 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(655, '2018-10-27 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(656, '2018-11-03 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(657, '2018-11-10 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(658, '2018-11-17 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(659, '2018-11-24 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(660, '2018-12-01 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(661, '2018-12-08 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(662, '2018-12-15 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(663, '2018-12-22 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(664, '2018-12-29 15:10:00', 16, NULL, 1, NULL, NULL, '2018-01-03 14:14:27', '2018-01-03 14:14:27'),
(665, '2018-01-06 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(666, '2018-01-13 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(667, '2018-01-20 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(668, '2018-01-27 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(669, '2018-02-03 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(670, '2018-02-10 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(671, '2018-02-17 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(672, '2018-02-24 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(673, '2018-03-03 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(674, '2018-03-10 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(675, '2018-03-17 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(676, '2018-03-24 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(677, '2018-03-31 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:39', '2018-01-04 08:09:39'),
(678, '2018-04-07 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(679, '2018-04-14 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(680, '2018-04-21 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(681, '2018-04-28 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(682, '2018-05-05 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(683, '2018-05-12 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(684, '2018-05-19 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(685, '2018-05-26 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(686, '2018-06-02 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(687, '2018-06-09 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(688, '2018-06-16 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(689, '2018-06-23 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(690, '2018-06-30 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(691, '2018-07-07 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(692, '2018-07-14 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(693, '2018-07-21 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(694, '2018-07-28 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(695, '2018-08-04 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(696, '2018-08-11 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(697, '2018-08-18 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(698, '2018-08-25 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(699, '2018-09-01 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(700, '2018-09-08 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(701, '2018-09-15 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(702, '2018-09-22 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(703, '2018-09-29 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(704, '2018-10-06 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(705, '2018-10-13 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(706, '2018-10-20 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(707, '2018-10-27 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(708, '2018-11-03 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(709, '2018-11-10 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(710, '2018-11-17 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(711, '2018-11-24 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(712, '2018-12-01 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(713, '2018-12-08 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(714, '2018-12-15 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(715, '2018-12-22 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40'),
(716, '2018-12-29 19:00:00', 17, NULL, 1, NULL, NULL, '2018-01-04 08:09:40', '2018-01-04 08:09:40');

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
(1553, 852, 1, 100, NULL, NULL, NULL),
(1554, 853, 1, 100, NULL, NULL, NULL),
(1555, 854, 1, 100, NULL, NULL, NULL),
(1556, 855, 1, 100, NULL, NULL, NULL),
(1557, 856, 1, 100, NULL, NULL, NULL),
(1558, 857, 1, 100, NULL, NULL, NULL),
(1559, 858, 1, 100, NULL, NULL, NULL),
(1560, 859, 1, 100, NULL, NULL, NULL),
(1561, 860, 1, 100, NULL, NULL, NULL),
(1562, 861, 1, 100, NULL, NULL, NULL),
(1563, 862, 1, 100, NULL, NULL, NULL),
(1564, 863, 1, 100, NULL, NULL, NULL),
(1565, 864, 1, 100, NULL, NULL, NULL),
(1566, 865, 1, 100, NULL, NULL, NULL),
(1567, 866, 1, 100, NULL, NULL, NULL),
(1568, 867, 1, 100, NULL, NULL, NULL),
(1569, 868, 1, 100, NULL, NULL, NULL),
(1570, 869, 1, 100, NULL, NULL, NULL),
(1571, 870, 1, 100, NULL, NULL, NULL),
(1572, 871, 1, 100, NULL, NULL, NULL),
(1573, 872, 1, 100, NULL, NULL, NULL),
(1574, 873, 1, 100, NULL, NULL, NULL),
(1575, 874, 1, 100, NULL, NULL, NULL),
(1576, 875, 1, 100, NULL, NULL, NULL),
(1577, 876, 1, 100, NULL, NULL, NULL),
(1578, 877, 1, 100, NULL, NULL, NULL),
(1579, 878, 1, 100, NULL, NULL, NULL),
(1580, 879, 1, 100, NULL, NULL, NULL),
(1581, 880, 1, 100, NULL, NULL, NULL),
(1582, 881, 1, 100, NULL, NULL, NULL),
(1583, 882, 1, 100, NULL, NULL, NULL),
(1584, 883, 1, 100, NULL, NULL, NULL),
(1585, 884, 1, 100, NULL, NULL, NULL),
(1586, 885, 1, 100, NULL, NULL, NULL),
(1587, 886, 1, 100, NULL, NULL, NULL),
(1588, 887, 1, 100, NULL, NULL, NULL),
(1589, 888, 1, 100, NULL, NULL, NULL),
(1590, 889, 1, 100, NULL, NULL, NULL),
(1591, 890, 1, 100, NULL, NULL, NULL),
(1592, 891, 1, 100, NULL, NULL, NULL),
(1593, 892, 1, 100, NULL, NULL, NULL),
(1594, 893, 1, 100, NULL, NULL, NULL),
(1595, 894, 1, 100, NULL, NULL, NULL),
(1596, 895, 1, 100, NULL, NULL, NULL),
(1597, 896, 1, 100, NULL, NULL, NULL),
(1598, 897, 1, 100, NULL, NULL, NULL),
(1599, 898, 1, 100, NULL, NULL, NULL),
(1600, 899, 1, 100, NULL, NULL, NULL),
(1601, 900, 1, 100, NULL, NULL, NULL),
(1602, 901, 1, 100, NULL, NULL, NULL),
(1603, 902, 1, 100, NULL, NULL, NULL),
(1604, 903, 1, 100, NULL, NULL, NULL),
(1605, 904, 1, 250, NULL, NULL, NULL),
(1606, 905, 1, 250, NULL, NULL, NULL),
(1607, 906, 1, 250, NULL, NULL, NULL),
(1608, 907, 1, 250, NULL, NULL, NULL),
(1609, 908, 1, 250, NULL, NULL, NULL),
(1610, 909, 1, 250, NULL, NULL, NULL),
(1611, 910, 1, 250, NULL, NULL, NULL),
(1612, 911, 1, 250, NULL, NULL, NULL),
(1613, 912, 1, 250, NULL, NULL, NULL),
(1614, 913, 1, 250, NULL, NULL, NULL),
(1615, 914, 1, 250, NULL, NULL, NULL),
(1616, 915, 1, 250, NULL, NULL, NULL),
(1617, 916, 1, 250, NULL, NULL, NULL),
(1618, 917, 1, 250, NULL, NULL, NULL),
(1619, 918, 1, 250, NULL, NULL, NULL),
(1620, 919, 1, 250, NULL, NULL, NULL),
(1621, 920, 1, 250, NULL, NULL, NULL),
(1622, 921, 1, 250, NULL, NULL, NULL),
(1623, 922, 1, 250, NULL, NULL, NULL),
(1624, 923, 1, 250, NULL, NULL, NULL),
(1625, 924, 1, 250, NULL, NULL, NULL),
(1626, 925, 1, 250, NULL, NULL, NULL),
(1627, 926, 1, 250, NULL, NULL, NULL),
(1628, 927, 1, 250, NULL, NULL, NULL),
(1629, 928, 1, 250, NULL, NULL, NULL),
(1630, 929, 1, 250, NULL, NULL, NULL),
(1631, 930, 1, 250, NULL, NULL, NULL),
(1632, 931, 1, 250, NULL, NULL, NULL),
(1633, 932, 1, 250, NULL, NULL, NULL),
(1634, 933, 1, 250, NULL, NULL, NULL),
(1635, 934, 1, 250, NULL, NULL, NULL),
(1636, 935, 1, 250, NULL, NULL, NULL),
(1637, 936, 1, 250, NULL, NULL, NULL),
(1638, 937, 1, 250, NULL, NULL, NULL),
(1639, 938, 1, 250, NULL, NULL, NULL),
(1640, 939, 1, 250, NULL, NULL, NULL),
(1641, 940, 1, 250, NULL, NULL, NULL),
(1642, 941, 1, 250, NULL, NULL, NULL),
(1643, 942, 1, 250, NULL, NULL, NULL),
(1644, 943, 1, 250, NULL, NULL, NULL),
(1645, 944, 1, 250, NULL, NULL, NULL),
(1646, 945, 1, 250, NULL, NULL, NULL),
(1647, 946, 1, 250, NULL, NULL, NULL),
(1648, 947, 1, 250, NULL, NULL, NULL),
(1649, 948, 1, 250, NULL, NULL, NULL),
(1650, 949, 1, 250, NULL, NULL, NULL),
(1651, 950, 1, 250, NULL, NULL, NULL),
(1652, 951, 1, 250, NULL, NULL, NULL),
(1653, 952, 1, 250, NULL, NULL, NULL),
(1654, 953, 1, 250, NULL, NULL, NULL),
(1655, 954, 1, 250, NULL, NULL, NULL),
(1656, 955, 1, 250, NULL, NULL, NULL);

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
(2, 'Touroperator', '2017-11-15 22:00:00', NULL, NULL),
(3, 'Agent', '2017-11-20 22:00:00', NULL, NULL);

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
(852, 613, 1, 100, NULL, NULL, NULL),
(853, 614, 1, 100, NULL, NULL, NULL),
(854, 615, 1, 100, NULL, NULL, NULL),
(855, 616, 1, 100, NULL, NULL, NULL),
(856, 617, 1, 100, NULL, NULL, NULL),
(857, 618, 1, 100, NULL, NULL, NULL),
(858, 619, 1, 100, NULL, NULL, NULL),
(859, 620, 1, 100, NULL, NULL, NULL),
(860, 621, 1, 100, NULL, NULL, NULL),
(861, 622, 1, 100, NULL, NULL, NULL),
(862, 623, 1, 100, NULL, NULL, NULL),
(863, 624, 1, 100, NULL, NULL, NULL),
(864, 625, 1, 100, NULL, NULL, NULL),
(865, 626, 1, 100, NULL, NULL, NULL),
(866, 627, 1, 100, NULL, NULL, NULL),
(867, 628, 1, 100, NULL, NULL, NULL),
(868, 629, 1, 100, NULL, NULL, NULL),
(869, 630, 1, 100, NULL, NULL, NULL),
(870, 631, 1, 100, NULL, NULL, NULL),
(871, 632, 1, 100, NULL, NULL, NULL),
(872, 633, 1, 100, NULL, NULL, NULL),
(873, 634, 1, 100, NULL, NULL, NULL),
(874, 635, 1, 100, NULL, NULL, NULL),
(875, 636, 1, 100, NULL, NULL, NULL),
(876, 637, 1, 100, NULL, NULL, NULL),
(877, 638, 1, 100, NULL, NULL, NULL),
(878, 639, 1, 100, NULL, NULL, NULL),
(879, 640, 1, 100, NULL, NULL, NULL),
(880, 641, 1, 100, NULL, NULL, NULL),
(881, 642, 1, 100, NULL, NULL, NULL),
(882, 643, 1, 100, NULL, NULL, NULL),
(883, 644, 1, 100, NULL, NULL, NULL),
(884, 645, 1, 100, NULL, NULL, NULL),
(885, 646, 1, 100, NULL, NULL, NULL),
(886, 647, 1, 100, NULL, NULL, NULL),
(887, 648, 1, 100, NULL, NULL, NULL),
(888, 649, 1, 100, NULL, NULL, NULL),
(889, 650, 1, 100, NULL, NULL, NULL),
(890, 651, 1, 100, NULL, NULL, NULL),
(891, 652, 1, 100, NULL, NULL, NULL),
(892, 653, 1, 100, NULL, NULL, NULL),
(893, 654, 1, 100, NULL, NULL, NULL),
(894, 655, 1, 100, NULL, NULL, NULL),
(895, 656, 1, 100, NULL, NULL, NULL),
(896, 657, 1, 100, NULL, NULL, NULL),
(897, 658, 1, 100, NULL, NULL, NULL),
(898, 659, 1, 100, NULL, NULL, NULL),
(899, 660, 1, 100, NULL, NULL, NULL),
(900, 661, 1, 100, NULL, NULL, NULL),
(901, 662, 1, 100, NULL, NULL, NULL),
(902, 663, 1, 100, NULL, NULL, NULL),
(903, 664, 1, 100, NULL, NULL, NULL),
(904, 665, 1, 100, NULL, NULL, NULL),
(905, 666, 1, 100, NULL, NULL, NULL),
(906, 667, 1, 100, NULL, NULL, NULL),
(907, 668, 1, 100, NULL, NULL, NULL),
(908, 669, 1, 100, NULL, NULL, NULL),
(909, 670, 1, 100, NULL, NULL, NULL),
(910, 671, 1, 100, NULL, NULL, NULL),
(911, 672, 1, 100, NULL, NULL, NULL),
(912, 673, 1, 100, NULL, NULL, NULL),
(913, 674, 1, 100, NULL, NULL, NULL),
(914, 675, 1, 100, NULL, NULL, NULL),
(915, 676, 1, 100, NULL, NULL, NULL),
(916, 677, 1, 100, NULL, NULL, NULL),
(917, 678, 1, 100, NULL, NULL, NULL),
(918, 679, 1, 100, NULL, NULL, NULL),
(919, 680, 1, 100, NULL, NULL, NULL),
(920, 681, 1, 100, NULL, NULL, NULL),
(921, 682, 1, 100, NULL, NULL, NULL),
(922, 683, 1, 100, NULL, NULL, NULL),
(923, 684, 1, 100, NULL, NULL, NULL),
(924, 685, 1, 100, NULL, NULL, NULL),
(925, 686, 1, 100, NULL, NULL, NULL),
(926, 687, 1, 100, NULL, NULL, NULL),
(927, 688, 1, 100, NULL, NULL, NULL),
(928, 689, 1, 100, NULL, NULL, NULL),
(929, 690, 1, 100, NULL, NULL, NULL),
(930, 691, 1, 100, NULL, NULL, NULL),
(931, 692, 1, 100, NULL, NULL, NULL),
(932, 693, 1, 100, NULL, NULL, NULL),
(933, 694, 1, 100, NULL, NULL, NULL),
(934, 695, 1, 100, NULL, NULL, NULL),
(935, 696, 1, 100, NULL, NULL, NULL),
(936, 697, 1, 100, NULL, NULL, NULL),
(937, 698, 1, 100, NULL, NULL, NULL),
(938, 699, 1, 100, NULL, NULL, NULL),
(939, 700, 1, 100, NULL, NULL, NULL),
(940, 701, 1, 100, NULL, NULL, NULL),
(941, 702, 1, 100, NULL, NULL, NULL),
(942, 703, 1, 100, NULL, NULL, NULL),
(943, 704, 1, 100, NULL, NULL, NULL),
(944, 705, 1, 100, NULL, NULL, NULL),
(945, 706, 1, 100, NULL, NULL, NULL),
(946, 707, 1, 100, NULL, NULL, NULL),
(947, 708, 1, 100, NULL, NULL, NULL),
(948, 709, 1, 100, NULL, NULL, NULL),
(949, 710, 1, 100, NULL, NULL, NULL),
(950, 711, 1, 100, NULL, NULL, NULL),
(951, 712, 1, 100, NULL, NULL, NULL),
(952, 713, 1, 100, NULL, NULL, NULL),
(953, 714, 1, 100, NULL, NULL, NULL),
(954, 715, 1, 100, NULL, NULL, NULL),
(955, 716, 1, 100, NULL, NULL, NULL);

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
(1, 'Cash', 'cash.png', NULL, '2017-01-16 11:29:36', '2017-01-16 11:29:36'),
(2, 'Physical TPV', 'physical.png', NULL, '2017-01-16 11:29:48', '2017-01-16 11:29:48'),
(3, 'Online TPV\n', 'online.png', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'On Show', 'theater.png', '2017-12-16 22:00:00', '2017-03-07 06:47:17', '2017-03-07 06:47:17'),
(6, 'Paypal', NULL, '2017-12-19 22:00:00', NULL, NULL);

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
(49, 1, 1, 145, '1005.00', NULL, NULL, NULL, NULL, '2018-01-03 14:22:11', '2018-01-03 14:22:11'),
(50, 1, 1, 146, '205.00', NULL, NULL, NULL, NULL, '2018-01-04 09:08:08', '2018-01-04 09:08:08');

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
(113, 'create_payment', 'create_payment', 1, 22, NULL, NULL, NULL),
(114, 'create_wristbands', 'create_wristbands', 1, 23, NULL, NULL, NULL),
(115, 'show_wristbands', 'show_wristbands', 1, 23, NULL, NULL, NULL),
(116, 'delete_wristbands', 'delete_wristbands', 1, 23, NULL, NULL, NULL),
(117, 'edit_wristbands', 'edit_wristbands', 1, 23, NULL, NULL, NULL),
(118, 'create_wristband_passes', 'create_wristband_passes', 1, 24, NULL, NULL, NULL),
(119, 'edit_wristband_passes', 'edit_wristband_passes', 1, 24, NULL, NULL, NULL),
(120, 'show_wristband_passes', 'show_wristband_passes', 1, 24, NULL, NULL, NULL),
(121, 'delete_wristband_passes', 'delete_wristband_passes', 1, 24, NULL, NULL, NULL),
(122, 'create_pack', 'create_pack', 1, 25, NULL, NULL, NULL),
(123, 'show_pack', 'show_pack', 1, 25, NULL, NULL, NULL),
(124, 'edit_pack', 'edit_pack', 1, 25, NULL, NULL, NULL),
(125, 'delete_pack', 'delete_pack', 1, 25, NULL, NULL, NULL),
(126, 'download_payment', 'download_payment', 1, 22, NULL, NULL, NULL),
(127, 'view_global', 'view_global', 1, 26, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, 'edit_global', 'edit_global', 1, 26, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, 'show_global', 'show_global', 1, 26, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, 'delete_global', 'delete_global', 1, 26, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, 'view_pack', 'view_pack', 1, 25, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, 'view_wristbands', 'view_wristbands', 1, 24, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 'view_wristband_passes', 'view_wristband_passes', 1, 24, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, 'cancel_passes', 'cancel_passes', 1, 5, NULL, NULL, NULL),
(135, 'reactivate_passes', 'reactivate_passes', 1, 5, NULL, NULL, NULL),
(136, 'cancel_promocodes', 'cancel_promocodes', 1, 4, NULL, NULL, NULL),
(137, 'reactivate_promocodes', 'reactivate_promocodes', 1, 4, NULL, NULL, NULL),
(138, 'manage_users', 'manage_users', 1, 3, NULL, NULL, NULL),
(139, 'manage_roles', 'manage_roles', 1, 19, NULL, NULL, NULL),
(140, 'view_system_logs', 'view_system_logs', 1, 19, NULL, NULL, NULL),
(141, 'cancel_reservations', 'cancel_reservations', 1, 1, NULL, NULL, NULL);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocode_pack`
--

CREATE TABLE `promocode_pack` (
  `id` int(10) UNSIGNED NOT NULL,
  `promocode_id` int(10) UNSIGNED NOT NULL,
  `pack_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocode_show`
--

CREATE TABLE `promocode_show` (
  `promocode_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocode_wristband`
--

CREATE TABLE `promocode_wristband` (
  `id` int(10) UNSIGNED NOT NULL,
  `promocode_id` int(10) UNSIGNED NOT NULL,
  `wristband_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, 'Touroperator', '2017-11-15 22:00:00', NULL, NULL),
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
  `pass_id` int(10) UNSIGNED DEFAULT NULL,
  `pack_id` int(10) UNSIGNED DEFAULT NULL,
  `reservation_type_id` int(10) UNSIGNED DEFAULT NULL,
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
  `booking_fee` decimal(10,2) NOT NULL,
  `paypal` int(11) NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `reseller_id`, `customer_id`, `channel_id`, `discount`, `promocode_id`, `pass_id`, `pack_id`, `reservation_type_id`, `reconcile`, `identification_number`, `email`, `phone`, `name`, `created_by`, `reservation_number`, `reference_number`, `canceled_by`, `canceled_date`, `canceled_reason`, `finished`, `finished_by`, `booking_fee`, `paypal`, `comments`, `deleted_at`, `created_at`, `updated_at`) VALUES
(145, NULL, 1, 3, NULL, NULL, 613, NULL, 1, 0, '1', 'adrianescalada@gmail.com', '697877831', 'Adrian Escalada', 1, '5a4ce6f73b8ae', NULL, NULL, NULL, NULL, 1, 1, '5.00', 0, '<p>Test de reserva con Victor!</p>', NULL, '2018-01-03 14:21:43', '2018-01-03 14:22:11'),
(146, NULL, 1, 3, NULL, NULL, NULL, 16, 2, 0, '1', 'adrianescalada@gmail.com', '697877831', 'Adrian Escalada', 1, '5a4dedbc2192e', NULL, NULL, NULL, NULL, 1, 1, '5.00', 0, '<p>Test de reserva de un Pack con Victor</p>', NULL, '2018-01-04 09:02:52', '2018-01-04 09:08:08');

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
-- Estructura de tabla para la tabla `reservation_packs`
--

CREATE TABLE `reservation_packs` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `pack_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL,
  `seat_type_id` int(10) UNSIGNED NOT NULL,
  `ticket_type_id` int(10) UNSIGNED NOT NULL,
  `pass_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservation_packs`
--

INSERT INTO `reservation_packs` (`id`, `reservation_id`, `pack_id`, `show_id`, `seat_type_id`, `ticket_type_id`, `pass_id`, `quantity`, `unit_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(10, 146, 16, 16, 1, 1, 630, 1, '100.00', NULL, '2018-01-04 09:02:52', '2018-01-04 09:02:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_pack_wristbands`
--

CREATE TABLE `reservation_pack_wristbands` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `pack_id` int(10) UNSIGNED NOT NULL,
  `wristband_passes_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservation_pack_wristbands`
--

INSERT INTO `reservation_pack_wristbands` (`id`, `reservation_id`, `pack_id`, `wristband_passes_id`, `quantity`, `unit_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(5, 146, 16, 4, 1, '0.00', NULL, '2018-01-04 09:02:52', '2018-01-04 09:02:52');

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
(121, 1, 1, 10, '100.00', 145, NULL, '2018-01-03 14:21:43', '2018-01-03 14:21:43');

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
-- Estructura de tabla para la tabla `reservation_types`
--

CREATE TABLE `reservation_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservation_types`
--

INSERT INTO `reservation_types` (`id`, `name`, `route`, `acronym`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Products', 'reservations.create', 'pr', NULL, NULL, NULL),
(2, 'Packs', 'reservationspacks.create', 'pk', NULL, NULL, NULL),
(3, 'Wristbands', 'reservationsWristbands.create', 'wr', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_wristband_pass`
--

CREATE TABLE `reservation_wristband_pass` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `wristband_pass_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
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
(1, 'administrator', 'Administrator', 0, NULL, '2016-11-14 11:21:38', '2017-09-20 11:44:43'),
(2, 'reservas', 'Reservas', 0, NULL, '2017-04-04 10:26:15', '2017-04-04 10:30:03'),
(3, 'box office', 'Box Office', 0, NULL, '2017-04-04 10:45:05', '2017-04-04 10:45:05'),
(4, 'operaciones', 'Operaciones', 0, NULL, '2017-04-04 10:49:12', '2017-04-04 10:49:12'),
(5, 'reservas y transporte', 'Reservas y Transporte', 0, NULL, '2017-04-04 11:00:47', '2017-04-04 11:00:47'),
(6, 'marketing', 'Marketing', 0, NULL, '2017-04-04 11:33:26', '2017-04-04 11:33:26'),
(7, 'agent login', 'Agent Login', 0, NULL, '2017-04-10 06:29:48', '2017-04-10 06:30:12'),
(8, 'sales', 'Sales', 0, NULL, '2017-04-25 13:33:55', '2017-04-25 13:33:55'),
(9, 'agent edit', 'Agent Edit', 0, NULL, '2017-05-11 13:00:59', '2017-05-11 13:00:59'),
(11, 'Online', 'Online Web', 0, NULL, '2017-09-20 11:21:29', '2017-09-20 11:21:42');

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
(1, 1, 1, 'all', NULL, NULL, '2017-12-22 09:48:56'),
(2, 1, 4, 'all', NULL, '2017-09-21 08:10:44', '2017-09-21 09:18:19'),
(3, 2, 4, 'all', NULL, '2017-09-21 08:10:44', '2017-09-21 09:18:19'),
(4, 3, 4, 'all', NULL, '2017-09-21 08:10:44', '2017-09-21 09:18:19'),
(5, 4, 4, 'all', NULL, '2017-09-21 08:10:44', '2017-09-21 09:18:19'),
(6, 5, 4, 'all', NULL, '2017-09-21 08:10:45', '2017-09-21 09:18:19'),
(7, 1, 3, 'team', NULL, '2017-09-21 09:06:15', '2017-09-21 09:10:26'),
(8, 3, 3, 'all', NULL, '2017-09-21 09:06:15', '2017-09-21 09:10:26'),
(9, 6, 4, 'all', NULL, '2017-09-21 09:18:19', '2017-09-21 09:18:19'),
(10, 1, 8, 'self', NULL, '2017-09-21 09:18:44', '2017-09-21 09:18:44'),
(11, 11, 1, 'all', '2017-09-21 09:40:31', NULL, '2017-09-21 09:40:31'),
(12, 2, 1, 'all', NULL, '2017-09-21 09:35:34', '2017-12-22 09:48:56'),
(13, 3, 1, 'all', NULL, '2017-09-21 09:35:34', '2017-12-22 09:48:56'),
(14, 4, 1, 'all', '2017-10-17 11:39:51', '2017-09-21 09:35:35', '2017-10-17 11:39:51'),
(15, 5, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(16, 6, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(17, 7, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(18, 8, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(19, 9, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(20, 10, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(21, 12, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(22, 13, 1, 'all', '2017-09-21 09:35:58', '2017-09-21 09:35:35', '2017-09-21 09:35:58'),
(23, 14, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(24, 15, 1, 'all', NULL, '2017-09-21 09:35:35', '2017-12-22 09:48:56'),
(25, 13, 1, 'all', '2017-09-21 10:07:18', '2017-09-21 09:36:15', '2017-09-21 10:07:18'),
(26, 16, 1, 'all', NULL, '2017-09-21 09:40:32', '2017-12-22 09:48:56'),
(27, 17, 1, 'all', NULL, '2017-09-21 09:40:32', '2017-12-22 09:48:56'),
(28, 18, 1, 'all', NULL, '2017-09-21 09:40:32', '2017-12-22 09:48:56'),
(29, 19, 1, 'all', NULL, '2017-09-21 09:40:32', '2017-12-22 09:48:56'),
(30, 20, 1, 'all', NULL, '2017-09-21 09:40:32', '2017-12-22 09:48:56'),
(31, 11, 1, 'all', NULL, '2017-09-21 09:42:47', '2017-12-22 09:48:56'),
(32, 21, 1, 'all', NULL, '2017-09-21 09:42:47', '2017-12-22 09:48:56'),
(33, 22, 1, 'all', NULL, '2017-09-21 09:42:47', '2017-12-22 09:48:56'),
(34, 23, 1, 'all', NULL, '2017-09-21 09:42:47', '2017-12-22 09:48:56'),
(35, 24, 1, 'all', NULL, '2017-09-21 09:42:48', '2017-12-22 09:48:56'),
(36, 25, 1, 'all', NULL, '2017-09-21 09:42:48', '2017-12-22 09:48:56'),
(37, 26, 1, 'all', NULL, '2017-09-21 09:48:57', '2017-12-22 09:48:56'),
(38, 27, 1, 'all', NULL, '2017-09-21 09:48:57', '2017-12-22 09:48:56'),
(39, 28, 1, 'all', NULL, '2017-09-21 09:48:57', '2017-12-22 09:48:56'),
(40, 29, 1, 'all', NULL, '2017-09-21 09:48:57', '2017-12-22 09:48:56'),
(41, 30, 1, 'all', NULL, '2017-09-21 09:48:57', '2017-12-22 09:48:56'),
(42, 31, 1, 'all', NULL, '2017-09-21 09:51:00', '2017-12-22 09:48:56'),
(43, 32, 1, 'all', NULL, '2017-09-21 09:51:00', '2017-12-22 09:48:56'),
(44, 33, 1, 'all', NULL, '2017-09-21 09:51:00', '2017-12-22 09:48:56'),
(45, 34, 1, 'all', NULL, '2017-09-21 09:51:00', '2017-12-22 09:48:56'),
(46, 35, 1, 'all', NULL, '2017-09-21 09:51:00', '2017-12-22 09:48:56'),
(47, 13, 1, 'all', NULL, '2017-09-21 10:10:27', '2017-12-22 09:48:56'),
(48, 36, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(49, 37, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(50, 38, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(51, 39, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(52, 40, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(53, 41, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(54, 42, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(55, 43, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(56, 44, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(57, 45, 1, 'all', NULL, '2017-09-21 10:10:29', '2017-12-22 09:48:56'),
(58, 46, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(59, 47, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(60, 48, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(61, 49, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(62, 50, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(63, 61, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(64, 62, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(65, 63, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(66, 64, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(67, 65, 1, 'all', NULL, '2017-09-21 10:10:30', '2017-12-22 09:48:56'),
(68, 66, 1, 'all', NULL, '2017-09-21 10:40:26', '2017-12-22 09:48:56'),
(69, 67, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(70, 68, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(71, 69, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(72, 70, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(73, 71, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(74, 72, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(75, 73, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(76, 74, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(77, 75, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(78, 76, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(79, 77, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(80, 78, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(81, 79, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(82, 80, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(83, 81, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(84, 82, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(85, 83, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(86, 84, 1, 'all', NULL, '2017-09-21 10:40:27', '2017-12-22 09:48:56'),
(87, 85, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(88, 86, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(89, 87, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(90, 88, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(91, 89, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(92, 90, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(93, 91, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(94, 92, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(95, 93, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(96, 94, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(97, 95, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(98, 96, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(99, 97, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(100, 98, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(101, 99, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(102, 100, 1, 'all', NULL, '2017-09-21 10:40:28', '2017-12-22 09:48:56'),
(103, 101, 1, 'all', NULL, '2017-09-21 10:55:38', '2017-12-22 09:48:56'),
(104, 102, 1, 'all', NULL, '2017-09-21 10:55:38', '2017-12-22 09:48:56'),
(105, 103, 1, 'all', NULL, '2017-09-21 10:55:39', '2017-12-22 09:48:56'),
(106, 104, 1, 'all', NULL, '2017-09-21 10:55:39', '2017-12-22 09:48:56'),
(107, 105, 1, 'all', NULL, '2017-09-21 10:55:39', '2017-12-22 09:48:56'),
(108, 106, 1, 'all', NULL, '2017-10-11 11:23:23', '2017-12-22 09:48:56'),
(109, 107, 1, 'all', NULL, '2017-10-11 11:23:23', '2017-12-22 09:48:56'),
(110, 108, 1, 'all', '2017-10-17 11:38:59', '2017-10-17 09:16:02', '2017-10-17 11:38:59'),
(111, 4, 1, 'all', NULL, '2017-10-17 11:47:24', '2017-12-22 09:48:56'),
(112, 108, 1, 'all', NULL, '2017-10-17 11:47:30', '2017-12-22 09:48:56'),
(113, 109, 1, 'all', NULL, '2017-10-18 05:28:25', '2017-12-22 09:48:56'),
(114, 110, 1, 'all', NULL, '2017-10-18 05:28:25', '2017-12-22 09:48:56'),
(115, 111, 1, 'all', NULL, '2017-10-18 05:28:26', '2017-12-22 09:48:56'),
(116, 112, 1, 'all', NULL, '2017-10-18 05:28:26', '2017-12-22 09:48:56'),
(117, 113, 1, 'all', NULL, '2017-10-18 05:28:26', '2017-12-22 09:48:56'),
(118, 114, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(119, 115, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(120, 116, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(121, 117, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(122, 118, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(123, 119, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(124, 120, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(125, 121, 1, 'all', NULL, '2017-12-04 08:00:23', '2017-12-22 09:48:56'),
(126, 122, 1, 'all', NULL, '2017-12-07 11:16:01', '2017-12-22 09:48:56'),
(127, 123, 1, 'all', NULL, '2017-12-07 11:16:01', '2017-12-22 09:48:56'),
(128, 124, 1, 'all', NULL, '2017-12-07 11:16:01', '2017-12-22 09:48:56'),
(129, 125, 1, 'all', NULL, '2017-12-07 11:16:01', '2017-12-22 09:48:56'),
(130, 126, 1, 'all', NULL, '2017-12-07 11:16:01', '2017-12-22 09:48:56'),
(131, 127, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(132, 128, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(133, 129, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(134, 130, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(135, 131, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(136, 132, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(137, 133, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(138, 134, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(139, 135, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(140, 136, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(141, 137, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(142, 138, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(143, 139, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(144, 141, 1, 'all', NULL, '2017-12-22 09:48:56', '2017-12-22 09:48:56'),
(145, 1, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(146, 2, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(147, 3, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(148, 4, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(149, 5, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(150, 108, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(151, 141, 2, 'all', NULL, '2017-12-22 09:56:33', '2017-12-22 09:58:59'),
(152, 106, 2, 'all', NULL, '2017-12-22 09:58:59', '2017-12-22 09:58:59'),
(153, 107, 2, 'all', NULL, '2017-12-22 09:58:59', '2017-12-22 09:58:59'),
(154, 109, 2, 'all', NULL, '2017-12-22 09:58:59', '2017-12-22 09:58:59'),
(155, 111, 2, 'all', NULL, '2017-12-22 09:58:59', '2017-12-22 09:58:59'),
(156, 113, 2, 'all', NULL, '2017-12-22 09:58:59', '2017-12-22 09:58:59');

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
(1, 'Generic', '{\"es\":null,\"en\":null}', 'GN', 100, 1, 1, NULL, '2017-09-15 06:33:27', '2018-01-03 14:01:50');

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
(1, 17, 0),
(1, 16, 0);

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `commission` double(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `shows`
--

INSERT INTO `shows` (`id`, `name`, `acronym`, `description`, `image`, `sort`, `deleted_at`, `created_at`, `updated_at`, `category_id`, `commission`) VALUES
(16, 'Reloaded Pool Party', 'RPP', '{\"es\":\"<p>La mejor fiesta de piscina en Maga!<\\/p>\",\"en\":\"<p>The best Pool Party in Maga!<\\/p>\"}', NULL, 2, NULL, '2017-12-18 16:18:59', '2018-01-03 14:12:15', 3, 5.00),
(17, 'Reloaded BOAT Party', 'RBP', '{\"es\":\"<p>La mejor fiesta en barco de Maga!<\\/p>\",\"en\":\"<p>The best Boat Party in Maga!<\\/p>\"}', NULL, 1, NULL, '2017-12-18 16:20:11', '2017-12-22 11:32:33', 1, 54.54);

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
(17, 1),
(16, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `show_wristband_pass`
--

CREATE TABLE `show_wristband_pass` (
  `id` int(10) UNSIGNED NOT NULL,
  `wristband_pass_id` int(10) UNSIGNED NOT NULL,
  `show_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Adult', 'ADU', 1, 1, NULL, '2018-01-03 14:10:39', '2018-01-03 14:10:39'),
(2, 'Children', 'CHD', 1, 2, NULL, '2017-09-15 06:36:01', '2018-01-03 14:09:48'),
(3, 'Infant', 'INF', 0, 3, NULL, '2017-09-15 06:36:22', '2018-01-03 14:09:39');

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
(1, 'Adrian Escalada', 'admin@refineriaweb.com', '$2y$10$oEX9K9FeQZn2WeS3S07zqeyPGm7C2nTYnA8mefOCqsOSh99MxsGSG', 1, 1, 'raMRYf77iTGLi6VbyAStU6ChyACrp3wS5wrRHK7TN7YTtqPunVKaKyX7G0Hl', NULL, '2017-09-07 11:24:03', '2017-10-06 10:40:42'),
(2, 'Fernanda Coringrato', 'fcoringrato@globobalear.com', '$2y$10$3yzOB9CqIAihHOhgExgDMOrAJSPP.uu/djFdpj5u0JHtEeB7SWOdC', 1, 1, 'Bb9FpgT3lxCAksj4CBYfGg91qe2RpQ3KEG6KAZgcnEk11DFnE2TTJtZFDy7E', NULL, '2017-10-20 10:27:52', '2017-11-29 08:01:22'),
(3, 'online', 'web@globoreservations.com', '', 11, 1, NULL, NULL, '2017-10-26 12:02:03', '2017-10-26 12:02:03'),
(4, 'Richard Metola', 'rmetola@globobalear.com', '$2y$10$oQ/tgddKRcb7DvIzo0c/EuE7hrpzelypqaXlZXH300uvtNqEvAPj2', 1, 1, NULL, NULL, '2017-11-29 08:03:18', '2017-11-29 08:03:18'),
(5, 'Adrian Escalada', 'aescalada@refineriaweb.com', '$2y$10$iO4kGekeLaKyOjmmvbc1FeE57U4B2wUlzk6JXfQilk1KnsoLVzBPa', 2, 1, NULL, NULL, '2018-01-03 14:15:45', '2018-01-03 14:15:45');

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
,`commision_wristaband` double(19,2)
,`totalmenoscomision` decimal(40,2)
,`comisionpulseraspirates` double(19,2)
,`commission` double(19,2)
,`totalcomision` double(19,2)
,`totalcomisionpulsera` double(19,2)
,`comisionpirates` double(19,2)
,`booking_fee` decimal(10,2)
,`paypal` int(11)
,`reservation_number` varchar(255)
,`reference_number` varchar(255)
,`company` varchar(255)
,`channels` varchar(255)
,`reservation_id` int(10) unsigned
,`payment_method_id` int(10) unsigned
,`method` varchar(255)
,`pass_id` int(10) unsigned
,`pack_id` int(10) unsigned
,`pass_datetime` datetime
,`reservation_type_id` int(10) unsigned
,`type` varchar(255)
,`show_id` bigint(10) unsigned
,`wristaband_id` bigint(10) unsigned
,`name_reservation` varchar(279)
,`deleted_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `viewreservations`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `viewreservations` (
`id` int(10) unsigned
,`type` varchar(255)
,`acronym` varchar(10)
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
,`pack_id` int(10) unsigned
,`reservation_type_id` int(10) unsigned
,`promocode_id` int(10) unsigned
,`reconcile` int(10) unsigned
,`pack` varchar(255)
,`name_reservation` varchar(277)
,`ADU` decimal(33,0)
,`CHD` decimal(33,0)
,`INF` decimal(33,0)
,`TOT` decimal(38,0)
,`booking_fee` decimal(10,2)
,`paypal` int(11)
,`comments` text
,`deleted_at` timestamp
,`created_at` timestamp
,`fecha` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wristbands`
--

CREATE TABLE `wristbands` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `wristbands`
--

INSERT INTO `wristbands` (`id`, `title`, `acronym`, `deleted_at`, `created_at`, `updated_at`) VALUES
(4, 'Azul Libre bebidas', 'ab', NULL, '2018-01-03 13:45:13', '2018-01-03 13:45:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wristband_passes`
--

CREATE TABLE `wristband_passes` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `price` double(6,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `wristband_id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `wristband_passes`
--

INSERT INTO `wristband_passes` (`id`, `title`, `date_start`, `date_end`, `price`, `quantity`, `wristband_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(4, 'Azul Libre bebidas (mayo)', '2018-04-30 22:00:00', '2018-05-30 22:00:00', 0.00, 50, 4, NULL, '2018-01-03 13:45:34', '2018-01-03 13:45:34');

-- --------------------------------------------------------

--
-- Estructura para la vista `viewpayment`
--
DROP TABLE IF EXISTS `viewpayment`;

CREATE ALGORITHM=UNDEFINED DEFINER=`globoreserv_crs`@`%` SQL SECURITY DEFINER VIEW `viewpayment`  AS  select `payment_method_reservations`.`id` AS `id`,`users`.`id` AS `user_id`,`users`.`name` AS `user_name`,`users`.`email` AS `user_email`,`customers`.`name` AS `customers_name`,`customers`.`email` AS `customers_email`,`customers`.`phone` AS `customers_phone`,`payment_method_reservations`.`total` AS `total`,(select sum(`shows`.`commission`) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `wristband_passes` on((`wristband_passes`.`wristband_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`)) AS `commision_wristaband`,(select (sum(`reservation_packs`.`unit_price`) * `reservation_packs`.`quantity`) AS `total` from (`reservation_packs` join `shows` on((`reservation_packs`.`show_id` = `shows`.`id`))) where (`reservation_packs`.`reservation_id` = `reservations`.`id`)) AS `totalmenoscomision`,abs((`payment_method_reservations`.`total` - (select round(((`payment_method_reservations`.`total` * sum(`shows`.`commission`)) / 100),2) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) join `payment_method_reservations` on((`payment_method_reservations`.`reservation_id` = `reservation_wristband_pass`.`reservation_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`) group by `payment_method_reservations`.`id`))) AS `comisionpulseraspirates`,coalesce((select `shows`.`commission` AS `passes` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`)),(select sum(`shows`.`commission`) from ((`packs` join `pack_shows` on((`packs`.`id` = `pack_shows`.`pack_id`))) join `shows` on((`pack_shows`.`show_id` = `shows`.`id`))) where (`packs`.`id` = `reservations`.`pack_id`) group by `packs`.`id`),(select sum(`shows`.`commission`) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `wristband_passes` on((`wristband_passes`.`wristband_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`))) AS `commission`,coalesce(round(((`payment_method_reservations`.`total` * (select `shows`.`commission` AS `passes` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`))) / 100),2),round((((select (sum(`reservation_packs`.`unit_price`) * `reservation_packs`.`quantity`) AS `total` from (`reservation_packs` join `shows` on((`reservation_packs`.`show_id` = `shows`.`id`))) where (`reservation_packs`.`reservation_id` = `reservations`.`id`)) * (select sum(`shows`.`commission`) from ((`packs` join `pack_shows` on((`packs`.`id` = `pack_shows`.`pack_id`))) join `shows` on((`pack_shows`.`show_id` = `shows`.`id`))) where (`packs`.`id` = `reservations`.`pack_id`) group by `packs`.`id`)) / 100),2),(select round(((`payment_method_reservations`.`total` * sum(`shows`.`commission`)) / 100),2) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) join `payment_method_reservations` on((`payment_method_reservations`.`reservation_id` = `reservation_wristband_pass`.`reservation_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`) group by `payment_method_reservations`.`id`)) AS `totalcomision`,(select round(((`payment_method_reservations`.`total` * sum(`shows`.`commission`)) / 100),2) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) join `payment_method_reservations` on((`payment_method_reservations`.`reservation_id` = `reservation_wristband_pass`.`reservation_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`) group by `payment_method_reservations`.`id`) AS `totalcomisionpulsera`,coalesce(abs((round(((`payment_method_reservations`.`total` * (select `shows`.`commission` AS `passes` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`))) / 100),2) - round(`payment_method_reservations`.`total`,0))),abs((round((((select (sum(`reservation_packs`.`unit_price`) * `reservation_packs`.`quantity`) AS `total` from (`reservation_packs` join `shows` on((`reservation_packs`.`show_id` = `shows`.`id`))) where (`reservation_packs`.`reservation_id` = `reservations`.`id`)) * (select sum(`shows`.`commission`) from ((`packs` join `pack_shows` on((`packs`.`id` = `pack_shows`.`pack_id`))) join `shows` on((`pack_shows`.`show_id` = `shows`.`id`))) where (`packs`.`id` = `reservations`.`pack_id`) group by `packs`.`id`)) / 100),2) - round((select (sum(`reservation_packs`.`unit_price`) * `reservation_packs`.`quantity`) AS `total` from (`reservation_packs` join `shows` on((`reservation_packs`.`show_id` = `shows`.`id`))) where (`reservation_packs`.`reservation_id` = `reservations`.`id`)),0))),abs((`payment_method_reservations`.`total` - (select round(((`payment_method_reservations`.`total` * sum(`shows`.`commission`)) / 100),2) from (((`reservation_wristband_pass` join `show_wristband_pass` on((`reservation_wristband_pass`.`wristband_pass_id` = `show_wristband_pass`.`wristband_pass_id`))) join `shows` on((`shows`.`id` = `show_wristband_pass`.`show_id`))) join `payment_method_reservations` on((`payment_method_reservations`.`reservation_id` = `reservation_wristband_pass`.`reservation_id`))) where (`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`) group by `payment_method_reservations`.`id`)))) AS `comisionpirates`,`reservations`.`booking_fee` AS `booking_fee`,`reservations`.`paypal` AS `paypal`,`reservations`.`reservation_number` AS `reservation_number`,`reservations`.`reference_number` AS `reference_number`,`resellers`.`company` AS `company`,`channels`.`name` AS `channels`,`payment_method_reservations`.`reservation_id` AS `reservation_id`,`payment_method_reservations`.`payment_method_id` AS `payment_method_id`,`payment_methods`.`name` AS `method`,`reservations`.`pass_id` AS `pass_id`,`reservations`.`pack_id` AS `pack_id`,`passes`.`datetime` AS `pass_datetime`,`reservations`.`reservation_type_id` AS `reservation_type_id`,`reservation_types`.`name` AS `type`,(select `shows`.`id` AS `passes` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`)) AS `show_id`,(select `wp`.`id` from (`reservation_wristband_pass` `rwp` left join `wristband_passes` `wp` on((`rwp`.`wristband_pass_id` = `wp`.`id`))) where (`rwp`.`reservation_id` = `reservations`.`id`)) AS `wristaband_id`,coalesce((select concat(`shows`.`name`,' | ',date_format(`passes`.`datetime`,'%d/%m/%Y %H:%i')) AS `passe` from (`passes` left join `shows` on((`passes`.`show_id` = `shows`.`id`))) where (`passes`.`id` = `reservations`.`pass_id`)),(select `packs`.`title` from `packs` where (`packs`.`id` = `reservations`.`pack_id`)),(select `wp`.`title` from (`reservation_wristband_pass` `rwp` left join `wristband_passes` `wp` on((`rwp`.`wristband_pass_id` = `wp`.`id`))) where (`rwp`.`reservation_id` = `reservations`.`id`))) AS `name_reservation`,`payment_method_reservations`.`deleted_at` AS `deleted_at`,`payment_method_reservations`.`created_at` AS `created_at`,`payment_method_reservations`.`updated_at` AS `updated_at` from (((((((((`users` left join `payment_method_reservations` on((`users`.`id` = `payment_method_reservations`.`user_id`))) left join `reservations` on((`payment_method_reservations`.`reservation_id` = `reservations`.`id`))) left join `reservation_wristband_pass` on((`reservation_wristband_pass`.`reservation_id` = `reservations`.`id`))) left join `reservation_types` on((`reservations`.`reservation_type_id` = `reservation_types`.`id`))) left join `channels` on((`channels`.`id` = `reservations`.`channel_id`))) left join `payment_methods` on((`payment_methods`.`id` = `payment_method_reservations`.`payment_method_id`))) left join `customers` on((`customers`.`id` = `reservations`.`customer_id`))) left join `passes` on((`passes`.`id` = `reservations`.`pass_id`))) left join `resellers` on((`resellers`.`id` = `reservations`.`reseller_id`))) where (isnull(`payment_method_reservations`.`deleted_at`) and (`reservations`.`finished` = 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `viewreservations`
--
DROP TABLE IF EXISTS `viewreservations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`globoreserv_crs`@`%` SQL SECURITY DEFINER VIEW `viewreservations`  AS  select `r`.`id` AS `id`,`rtp`.`name` AS `type`,`rtp`.`acronym` AS `acronym`,`r`.`reference_number` AS `reference_number`,`r`.`phone` AS `phone`,`r`.`reservation_number` AS `reservation_number`,`r`.`name` AS `name`,`r`.`email` AS `email`,`r`.`canceled_date` AS `canceled_date`,`r`.`created_by` AS `created_by`,`r`.`canceled_by` AS `canceled_by`,`r`.`discount` AS `discount`,`r`.`identification_number` AS `identification_number`,`r`.`canceled_reason` AS `canceled_reason`,`r`.`finished` AS `finished`,`r`.`channel_id` AS `channel_id`,`rc`.`name` AS `channel`,`r`.`customer_id` AS `customer_id`,`c`.`name` AS `customer`,`rl`.`company` AS `company`,`r`.`pass_id` AS `pass_id`,`r`.`pack_id` AS `pack_id`,`r`.`reservation_type_id` AS `reservation_type_id`,`r`.`promocode_id` AS `promocode_id`,`r`.`reconcile` AS `reconcile`,`pa`.`title` AS `pack`,coalesce((select concat(`s`.`name`,' | ',`p`.`datetime`) AS `passe` from (`passes` `p` left join `shows` `s` on((`p`.`show_id` = `s`.`id`))) where (`p`.`id` = `r`.`pass_id`)),`pa`.`title`,(select `wp`.`title` from (`reservation_wristband_pass` `rwp` left join `wristband_passes` `wp` on((`rwp`.`wristband_pass_id` = `wp`.`id`))) where (`rwp`.`reservation_id` = `r`.`id`))) AS `name_reservation`,coalesce(((select sum(`rt2`.`quantity`) from `pre_globoreserv_crs3`.`reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `ADU`,coalesce(((select sum(`rt2`.`quantity`) from `pre_globoreserv_crs3`.`reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `CHD`,coalesce(((select sum(`rt2`.`quantity`) from `pre_globoreserv_crs3`.`reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 3)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `INF`,((((((ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `pre_globoreserv_crs3`.`reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`)),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),0)) AS `TOT`,`r`.`booking_fee` AS `booking_fee`,`r`.`paypal` AS `paypal`,`r`.`comments` AS `comments`,`r`.`deleted_at` AS `deleted_at`,`r`.`created_at` AS `created_at`,`r`.`created_at` AS `fecha`,`r`.`updated_at` AS `updated_at` from (((((((`reservations` `r` left join `reservation_tickets` `rt` on((`r`.`id` = `rt`.`reservation_id`))) left join `resellers` `rl` on((`r`.`reseller_id` = `rl`.`id`))) left join `channels` `rc` on((`r`.`channel_id` = `rc`.`id`))) left join `customers` `c` on((`r`.`customer_id` = `c`.`id`))) left join `reservation_types` `rtp` on((`r`.`reservation_type_id` = `rtp`.`id`))) left join `packs` `pa` on((`r`.`pack_id` = `pa`.`id`))) left join `passes` `p` on((`r`.`pass_id` = `p`.`id`))) group by `r`.`id` order by `r`.`id` desc ;

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
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `packs`
--
ALTER TABLE `packs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pack_shows`
--
ALTER TABLE `pack_shows`
  ADD KEY `pack_shows_pack_id_foreign` (`pack_id`),
  ADD KEY `pack_shows_show_id_foreign` (`show_id`),
  ADD KEY `pack_shows_seat_type_id_foreign` (`seat_type_id`),
  ADD KEY `pack_shows_ticket_type_id_foreign` (`ticket_type_id`);

--
-- Indices de la tabla `pack_wristbands`
--
ALTER TABLE `pack_wristbands`
  ADD KEY `pack_wristbands_pack_id_foreign` (`pack_id`),
  ADD KEY `pack_wristbands_wristband_passes_id_foreign` (`wristband_passes_id`);

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
-- Indices de la tabla `promocode_pack`
--
ALTER TABLE `promocode_pack`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promocode_pack_promocode_id_foreign` (`promocode_id`),
  ADD KEY `promocode_pack_pack_id_foreign` (`pack_id`);

--
-- Indices de la tabla `promocode_show`
--
ALTER TABLE `promocode_show`
  ADD KEY `promocode_show_promocode_id_foreign` (`promocode_id`),
  ADD KEY `promocode_show_show_id_foreign` (`show_id`);

--
-- Indices de la tabla `promocode_wristband`
--
ALTER TABLE `promocode_wristband`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promocode_wristband_promocode_id_foreign` (`promocode_id`),
  ADD KEY `promocode_wristband_wristband_id_foreign` (`wristband_id`);

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
  ADD KEY `	reservations_finished_id_foreign` (`finished_by`),
  ADD KEY `reservations_pack_id_foreign` (`pack_id`),
  ADD KEY `reservations_reservation_type_id_foreign` (`reservation_type_id`);

--
-- Indices de la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_menu_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_menu_menu_id_foreign` (`menu_id`);

--
-- Indices de la tabla `reservation_packs`
--
ALTER TABLE `reservation_packs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_packs_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_packs_pack_id_foreign` (`pack_id`),
  ADD KEY `reservation_packs_show_id_foreign` (`show_id`),
  ADD KEY `reservation_packs_seat_type_id_foreign` (`seat_type_id`),
  ADD KEY `reservation_packs_ticket_type_id_foreign` (`ticket_type_id`),
  ADD KEY `reservation_packs_pass_id_foreign` (`pass_id`);

--
-- Indices de la tabla `reservation_pack_wristbands`
--
ALTER TABLE `reservation_pack_wristbands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_pack_wristbands_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_pack_wristbands_pack_id_foreign` (`pack_id`),
  ADD KEY `reservation_pack_wristbands_wristband_passes_id_foreign` (`wristband_passes_id`);

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
-- Indices de la tabla `reservation_types`
--
ALTER TABLE `reservation_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservation_wristband_pass`
--
ALTER TABLE `reservation_wristband_pass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_wristband_pass_reservation_id_foreign` (`reservation_id`),
  ADD KEY `reservation_wristband_pass_wristband_pass_id_foreign` (`wristband_pass_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `shows_category_id_foreign` (`category_id`);

--
-- Indices de la tabla `show_ticket_type`
--
ALTER TABLE `show_ticket_type`
  ADD KEY `show_ticket_type_show_id_foreign` (`show_id`),
  ADD KEY `show_ticket_type_ticket_type_id_foreign` (`ticket_type_id`);

--
-- Indices de la tabla `show_wristband_pass`
--
ALTER TABLE `show_wristband_pass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `show_wristband_pass_wristband_pass_id_foreign` (`wristband_pass_id`),
  ADD KEY `show_wristband_pass_show_id_foreign` (`show_id`);

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
-- Indices de la tabla `wristbands`
--
ALTER TABLE `wristbands`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wristband_passes`
--
ALTER TABLE `wristband_passes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wristband_passes_wristband_id_foreign` (`wristband_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cartes`
--
ALTER TABLE `cartes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=495;
--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
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
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT de la tabla `packs`
--
ALTER TABLE `packs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT de la tabla `passes`
--
ALTER TABLE `passes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=717;
--
-- AUTO_INCREMENT de la tabla `passes_prices`
--
ALTER TABLE `passes_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1657;
--
-- AUTO_INCREMENT de la tabla `passes_sellers`
--
ALTER TABLE `passes_sellers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `pass_seat_type`
--
ALTER TABLE `pass_seat_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=956;
--
-- AUTO_INCREMENT de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `payment_method_reservations`
--
ALTER TABLE `payment_method_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
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
-- AUTO_INCREMENT de la tabla `promocode_pack`
--
ALTER TABLE `promocode_pack`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `promocode_wristband`
--
ALTER TABLE `promocode_wristband`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT de la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `reservation_packs`
--
ALTER TABLE `reservation_packs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `reservation_pack_wristbands`
--
ALTER TABLE `reservation_pack_wristbands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;
--
-- AUTO_INCREMENT de la tabla `reservation_transports`
--
ALTER TABLE `reservation_transports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `reservation_types`
--
ALTER TABLE `reservation_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `reservation_wristband_pass`
--
ALTER TABLE `reservation_wristband_pass`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;
--
-- AUTO_INCREMENT de la tabla `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `seat_types`
--
ALTER TABLE `seat_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT de la tabla `show_wristband_pass`
--
ALTER TABLE `show_wristband_pass`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `ticket_types`
--
ALTER TABLE `ticket_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `transporters`
--
ALTER TABLE `transporters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `wristbands`
--
ALTER TABLE `wristbands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `wristband_passes`
--
ALTER TABLE `wristband_passes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
-- Filtros para la tabla `pack_shows`
--
ALTER TABLE `pack_shows`
  ADD CONSTRAINT `pack_shows_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_shows_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_shows_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_shows_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pack_wristbands`
--
ALTER TABLE `pack_wristbands`
  ADD CONSTRAINT `pack_wristbands_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_wristbands_wristband_passes_id_foreign` FOREIGN KEY (`wristband_passes_id`) REFERENCES `wristband_passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `passes`
--
ALTER TABLE `passes`
  ADD CONSTRAINT `passes_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `passes_prices`
--
ALTER TABLE `passes_prices`
  ADD CONSTRAINT `passes_prices_pass_seat_type_id_foreign` FOREIGN KEY (`pass_seat_type_id`) REFERENCES `pass_seat_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `passes_prices_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON UPDATE CASCADE;

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
-- Filtros para la tabla `promocode_pack`
--
ALTER TABLE `promocode_pack`
  ADD CONSTRAINT `promocode_pack_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `promocode_pack_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `promocode_show`
--
ALTER TABLE `promocode_show`
  ADD CONSTRAINT `promocode_show_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `promocode_show_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `promocode_wristband`
--
ALTER TABLE `promocode_wristband`
  ADD CONSTRAINT `promocode_wristband_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `promocode_wristband_wristband_id_foreign` FOREIGN KEY (`wristband_id`) REFERENCES `wristbands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `reservations_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_pass_id_foreign` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_promocode_id_foreign` FOREIGN KEY (`promocode_id`) REFERENCES `promocodes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_reseller_id_foreign` FOREIGN KEY (`reseller_id`) REFERENCES `resellers` (`id`),
  ADD CONSTRAINT `reservations_reservation_type_id_foreign` FOREIGN KEY (`reservation_type_id`) REFERENCES `reservation_types` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservation_menus`
--
ALTER TABLE `reservation_menus`
  ADD CONSTRAINT `reservation_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_menu_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservation_packs`
--
ALTER TABLE `reservation_packs`
  ADD CONSTRAINT `reservation_packs_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_packs_pass_id_foreign` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_packs_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_packs_seat_type_id_foreign` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_packs_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_packs_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservation_pack_wristbands`
--
ALTER TABLE `reservation_pack_wristbands`
  ADD CONSTRAINT `reservation_pack_wristbands_pack_id_foreign` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_pack_wristbands_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_pack_wristbands_wristband_passes_id_foreign` FOREIGN KEY (`wristband_passes_id`) REFERENCES `wristband_passes` (`id`) ON UPDATE CASCADE;

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
-- Filtros para la tabla `reservation_wristband_pass`
--
ALTER TABLE `reservation_wristband_pass`
  ADD CONSTRAINT `reservation_wristband_pass_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_wristband_pass_wristband_pass_id_foreign` FOREIGN KEY (`wristband_pass_id`) REFERENCES `wristband_passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `show_ticket_type`
--
ALTER TABLE `show_ticket_type`
  ADD CONSTRAINT `show_ticket_type_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_ticket_type_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `show_wristband_pass`
--
ALTER TABLE `show_wristband_pass`
  ADD CONSTRAINT `show_wristband_pass_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_wristband_pass_wristband_pass_id_foreign` FOREIGN KEY (`wristband_pass_id`) REFERENCES `wristband_passes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_resellers_type_id_foreign` FOREIGN KEY (`resellers_type_id`) REFERENCES `resellers_types` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `wristband_passes`
--
ALTER TABLE `wristband_passes`
  ADD CONSTRAINT `wristband_passes_wristband_id_foreign` FOREIGN KEY (`wristband_id`) REFERENCES `wristbands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
