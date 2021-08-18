
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_allowances`
--

CREATE TABLE IF NOT EXISTS `0_kv_allowances` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `percentage` int(3) NOT NULL,
  `basic` int(11) DEFAULT NULL,
  `Tax` int(2) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_country`
--

CREATE TABLE IF NOT EXISTS `0_kv_country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iso` varchar(50) DEFAULT NULL,
  `local_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=247 ;

--
-- Dumping data for table `0_kv_country`
--

INSERT INTO `0_kv_country` (`id`, `iso`, `local_name`) VALUES
(1, 'AD', 'Andorra'),
(2, 'AE', 'United Arab Emirates'),
(3, 'AF', 'Afghanistan'),
(4, 'AG', 'Antigua and Barbuda'),
(5, 'AI', 'Anguilla'),
(6, 'AL', 'Albania'),
(7, 'AM', 'Armenia\r\n'),
(8, 'AN', 'Netherlands Antilles\r\n'),
(9, 'AO', 'Angola\r\n'),
(10, 'AQ', 'Antarctica\r\n'),
(11, 'AR', 'Argentina\r\n'),
(12, 'AS', 'American Samoa\r\n'),
(13, 'AT', 'Austria\r\n'),
(14, 'AU', 'Australia\r\n'),
(15, 'AW', 'Aruba\r\n'),
(16, 'AX', 'Aland Islands'),
(17, 'AZ', 'Azerbaijan\r\n'),
(18, 'BA', 'Bosnia and Herzegovina\r\n'),
(19, 'BB', 'Barbados\r\n'),
(20, 'BD', 'Bangladesh\r\n'),
(21, 'BE', 'Belgium\r\n'),
(22, 'BF', 'Burkina Faso\r\n'),
(23, 'BG', 'Bulgaria\r\n'),
(24, 'BH', 'Bahrain\r\n'),
(25, 'BI', 'Burundi\r\n'),
(26, 'BJ', 'Benin\r\n'),
(27, 'BL', 'Saint Barthlemy'),
(28, 'BM', 'Bermuda\r\n'),
(29, 'BN', 'Brunei Darussalam\r\n'),
(30, 'BO', 'Bolivia\r\nBolivia, Plurinational state of'),
(31, 'BR', 'Brazil\r\n'),
(32, 'BS', 'Bahamas\r\n'),
(33, 'BT', 'Bhutan\r\n'),
(34, 'BV', 'Bouvet Island\r\n'),
(35, 'BW', 'Botswana\r\n'),
(36, 'BY', 'Belarus\r\n'),
(37, 'BZ', 'Belize\r\n'),
(38, 'CA', 'Canada\r\n'),
(39, 'CC', 'Cocos (Keeling) Islands\r\n'),
(40, 'CD', 'Congo, The Democratic Republic of the\r\n'),
(41, 'CF', 'Central African Republic\r\n'),
(42, 'CG', 'Congo\r\n'),
(43, 'CH', 'Switzerland\r\n'),
(45, 'CK', 'Cook Islands\r\n'),
(46, 'CL', 'Chile'),
(47, 'CM', 'Cameroon\r\n'),
(48, 'CN', 'China\r\n'),
(49, 'CO', 'Colombia\r\n'),
(50, 'CR', 'Costa Rica\r\n'),
(51, 'CU', 'Cuba\r\n'),
(52, 'CV', 'Cape Verde\r\n'),
(53, 'CX', 'Christmas Island\r\n'),
(54, 'CY', 'Cyprus\r\n'),
(55, 'CZ', 'Czech Republic\r\n'),
(56, 'DE', 'Germany\r\n'),
(57, 'DJ', 'Djibouti\r\n'),
(58, 'DK', 'Denmark\r\n'),
(59, 'DM', 'Dominica\r\n'),
(60, 'DO', 'Dominican Republic\r\n'),
(61, 'DZ', 'Algeria\r\n'),
(62, 'EC', 'Ecuador\r\n'),
(63, 'EE', 'Estonia\r\n'),
(64, 'EG', 'Egypt\r\n'),
(65, 'EH', 'Western Sahara\r\n'),
(66, 'ER', 'Eritrea\r\n'),
(67, 'ES', 'Spain\r\n'),
(68, 'ET', 'Ethiopia\r\n'),
(69, 'FI', 'Finland\r\n'),
(70, 'FJ', 'Fiji\r\n'),
(71, 'FK', 'Falkland Islands (Malvinas)\r\n'),
(72, 'FM', 'Micronesia, Federated States of\r\n'),
(73, 'FO', 'Faroe Islands\r\n'),
(74, 'FR', 'France\r\n'),
(75, 'GA', 'Gabon'),
(76, 'GB', 'United Kingdom'),
(77, 'GD', 'Grenada'),
(78, 'GE', 'Georgia'),
(79, 'GF', 'French Guiana'),
(80, 'GG', 'Guernsey'),
(81, 'GH', 'Ghana\r\n'),
(82, 'GI', 'Gibraltar\r\n'),
(83, 'GL', 'Greenland\r\n'),
(84, 'GM', 'Gambia\r\n'),
(85, 'GN', 'Guinea\r\n'),
(86, 'GP', 'Guadeloupe\r\n'),
(87, 'GQ', 'Equatorial Guinea\r\n'),
(88, 'GR', 'Greece\r\n'),
(89, 'GS', 'South Georgia and the South Sandwich Islands\r\n'),
(90, 'GT', 'Guatemala\r\n'),
(91, 'GU', 'Guam\r\n'),
(92, 'GW', 'Guinea-Bissau\r\n'),
(93, 'GY', 'Guyana\r\n'),
(94, 'HK', 'Hong Kong\r\n'),
(95, 'HM', 'Heard Island and McDonald Islands\r\n'),
(96, 'HN', 'Honduras\r\n'),
(97, 'HR', 'Croatia\r\n'),
(98, 'HT', 'Haiti\r\n'),
(99, 'HU', 'Hungary\r\n'),
(100, 'ID', 'Indonesia\r\n'),
(101, 'IE', 'Ireland\r\n'),
(102, 'IL', 'Israel\r\n'),
(103, 'IM', 'Isle of Man\r\n'),
(104, 'IN', 'India\r\n'),
(105, 'IO', 'British Indian Ocean Territory\r\n'),
(106, 'IQ', 'Iraq\r\n'),
(107, 'IR', 'Iran, Islamic Republic of\r\n'),
(108, 'IS', 'Iceland\r\n'),
(109, 'IT', 'Italy'),
(110, 'JE', 'Jersey\r\n'),
(111, 'JM', 'Jamaica\r\n'),
(112, 'JO', 'Jordan\r\n'),
(113, 'JP', 'Japan\r\n'),
(114, 'KE', 'Kenya\r\n'),
(115, 'KG', 'Kyrgyzstan\r\n'),
(116, 'KH', 'Cambodia\r\n'),
(117, 'KI', 'Kiribati\r\n'),
(118, 'KM', 'Comoros\r\n'),
(119, 'KN', 'Saint Kitts and Nevis\r\n'),
(120, 'KP', 'Korea, Democratic People&#39;s Republic of\r\n'),
(121, 'KR', 'Korea, Republic of\r\n'),
(122, 'KW', 'Kuwait\r\n'),
(123, 'KY', 'Cayman Islands\r\n'),
(124, 'KZ', 'Kazakhstan\r\n'),
(125, 'LA', 'Lao People&#39;s Democratic Republic\r\n'),
(126, 'LB', 'Lebanon\r\n'),
(127, 'LC', 'Saint Lucia\r\n'),
(128, 'LI', 'Liechtenstein\r\n'),
(129, 'LK', 'Sri Lanka\r\n'),
(130, 'LR', 'Liberia\r\n'),
(131, 'LS', 'Lesotho\r\n'),
(132, 'LT', 'Lithuania\r\n'),
(133, 'LU', 'Luxembourg\r\n'),
(134, 'LV', 'Latvia\r\n'),
(135, 'LY', 'Libyan Arab Jamahiriya\r\n'),
(136, 'MA', 'Morocco\r\n'),
(137, 'MC', 'Monaco\r\n'),
(138, 'MD', 'Moldova, Republic of\r\n'),
(139, 'ME', 'Montenegro\r\n'),
(140, 'MF', 'Saint Martin'),
(141, 'MG', 'Madagascar\r\n'),
(142, 'MH', 'Marshall Islands\r\n'),
(143, 'MK', 'Macedonia\r\n'),
(144, 'ML', 'Mali\r\n'),
(145, 'MM', 'Myanmar\r\n'),
(146, 'MN', 'Mongolia\r\n'),
(147, 'MO', 'Macao\r\n'),
(148, 'MP', 'Northern Mariana Islands\r\n'),
(149, 'MQ', 'Martinique\r\n'),
(150, 'MR', 'Mauritania\r\n'),
(151, 'MS', 'Montserrat\r\n'),
(152, 'MT', 'Malta\r\n'),
(153, 'MU', 'Mauritius\r\n'),
(154, 'MV', 'Maldives\r\n'),
(155, 'MW', 'Malawi\r\n'),
(156, 'MX', 'Mexico\r\n'),
(157, 'MY', 'Malaysia\r\n'),
(158, 'MZ', 'Mozambique\r\n'),
(159, 'NA', 'Namibia\r\n'),
(160, 'NC', 'New Caledonia\r\n'),
(161, 'NE', 'Niger\r\n'),
(162, 'NF', 'Norfolk Island\r\n'),
(163, 'NG', 'Nigeria\r\n'),
(164, 'NI', 'Nicaragua\r\n'),
(165, 'NL', 'Netherlands\r\n'),
(166, 'NO', 'Norway'),
(167, 'NP', 'Nepal\r\n'),
(168, 'NR', 'Nauru\r\n'),
(169, 'NU', 'Niue\r\n'),
(170, 'NZ', 'New Zealand\r\n'),
(171, 'OM', 'Oman\r\n'),
(172, 'PA', 'Panama\r\n'),
(173, 'PE', 'Peru\r\n'),
(174, 'PF', 'French Polynesia\r\n'),
(175, 'PG', 'Papua New Guinea\r\n'),
(176, 'PH', 'Philippines\r\n'),
(177, 'PK', 'Pakistan\r\n'),
(178, 'PL', 'Poland\r\n'),
(179, 'PM', 'Saint Pierre and Miquelon\r\n'),
(180, 'PN', 'Pitcairn\r\n'),
(181, 'PR', 'Puerto Rico\r\n'),
(182, 'PS', 'Palestinian Territory, Occupied'),
(183, 'PT', 'Portugal\r\n'),
(184, 'PW', 'Palau\r\n'),
(185, 'PY', 'Paraguay\r\n'),
(186, 'QA', 'Qatar\r\n'),
(188, 'RO', 'Romania\r\n'),
(189, 'RS', 'Serbia\r\n'),
(190, 'RU', 'Russian Federation\r\n'),
(191, 'RW', 'Rwanda\r\n'),
(192, 'SA', 'Saudi Arabia\r\n'),
(193, 'SB', 'Solomon Islands\r\n'),
(194, 'SC', 'Seychelles\r\n'),
(195, 'SD', 'Sudan\r\n'),
(196, 'SE', 'Sweden\r\n'),
(197, 'SG', 'Singapore\r\n'),
(198, 'SH', 'Saint Helena\r\n'),
(199, 'SI', 'Slovenia\r\n'),
(200, 'SJ', 'Svalbard and Jan Mayen\r\n'),
(201, 'SK', 'Slovakia\r\n'),
(202, 'SL', 'Sierra Leone\r\n'),
(203, 'SM', 'San Marino\r\n'),
(204, 'SN', 'Senegal\r\n'),
(205, 'SO', 'Somalia\r\n'),
(206, 'SR', 'Suriname\r\n'),
(207, 'ST', 'Sao Tome and Principe\r\n'),
(208, 'SV', 'El Salvador\r\n'),
(209, 'SY', 'Syrian Arab Republic\r\n'),
(210, 'SZ', 'Swaziland\r\n'),
(211, 'TC', 'Turks and Caicos Islands\r\n'),
(212, 'TD', 'Chad'),
(213, 'TF', 'French Southern Territories'),
(214, 'TG', 'Togo'),
(215, 'TH', 'Thailand'),
(216, 'TJ', 'Tajikistan'),
(217, 'TK', 'Tokelau'),
(218, 'TL', 'Timor-Leste'),
(219, 'TM', 'Turkmenistan\r\n'),
(220, 'TN', 'Tunisia\r\n'),
(221, 'TO', 'Tonga\r\n'),
(222, 'TR', 'Turkey'),
(223, 'TT', 'Trinidad and Tobago\r\n'),
(224, 'TV', 'Tuvalu\r\n'),
(225, 'TW', 'Taiwan\r\n'),
(226, 'TZ', 'Tanzania, United Republic of\r\n'),
(227, 'UA', 'Ukraine\r\n'),
(228, 'UG', 'Uganda\r\n'),
(229, 'UM', 'United States Minor Outlying Islands\r\n'),
(230, 'US', 'United States\r\n'),
(231, 'UY', 'Uruguay\r\n'),
(232, 'UZ', 'Uzbekistan\r\n'),
(233, 'VA', 'Holy See (Vatican City State)\r\n'),
(234, 'VC', 'Saint Vincent and the Grenadines\r\n'),
(235, 'VE', 'Venezuela, Bolivarian Republic of'),
(236, 'VG', 'Virgin Islands, British\r\n'),
(237, 'VI', 'Virgin Islands, U.S.\r\n'),
(238, 'VN', 'Viet Nam'),
(239, 'VU', 'Vanuatu\r\n'),
(240, 'WF', 'Wallis and Futuna\r\n'),
(241, 'WS', 'Samoa\r\n'),
(242, 'YE', 'Yemen\r\n'),
(243, 'YT', 'Mayotte\r\n'),
(244, 'ZA', 'South Africa\r\n'),
(245, 'ZM', 'Zambia\r\n'),
(246, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_departments`
--

CREATE TABLE IF NOT EXISTS `0_kv_departments` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_attendancee`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_attendancee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `year` int(2) DEFAULT NULL,
  `dept_id` int(10) NOT NULL,
  `empl_id` varchar(30) DEFAULT NULL,
  `1` varchar(2) NOT NULL,
  `2` varchar(2) NOT NULL,
  `3` varchar(2) NOT NULL,
  `4` varchar(2) NOT NULL,
  `5` varchar(2) NOT NULL,
  `6` varchar(2) NOT NULL,
  `7` varchar(2) NOT NULL,
  `8` varchar(2) NOT NULL,
  `9` varchar(2) NOT NULL,
  `10` varchar(2) NOT NULL,
  `11` varchar(2) NOT NULL,
  `12` varchar(2) NOT NULL,
  `13` varchar(2) NOT NULL,
  `14` varchar(2) NOT NULL,
  `15` varchar(2) NOT NULL,
  `16` varchar(2) NOT NULL,
  `17` varchar(2) NOT NULL,
  `18` varchar(2) NOT NULL,
  `19` varchar(2) NOT NULL,
  `20` varchar(2) NOT NULL,
  `21` varchar(2) NOT NULL,
  `22` varchar(2) NOT NULL,
  `23` varchar(2) NOT NULL,
  `24` varchar(2) NOT NULL,
  `25` varchar(2) NOT NULL,
  `26` varchar(2) NOT NULL,
  `27` varchar(2) NOT NULL,
  `28` varchar(2) NOT NULL,
  `29` varchar(2) NOT NULL,
  `30` varchar(2) NOT NULL,
  `31` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_cv`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_cv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `empl_firstname` varchar(60) NOT NULL,
  `cv_title` varchar(60) NOT NULL,
  `filename` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_degree`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_degree` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `degree` varchar(20) NOT NULL,
  `major` varchar(20) NOT NULL,
  `university` varchar(80) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `year` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_experience`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_experience` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `organization` varchar(60) NOT NULL,
  `job_role` varchar(60) NOT NULL,
  `s_date` date NOT NULL,
  `e_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_info`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_info` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `empl_salutation` varchar(9) NOT NULL,
  `empl_firstname` varchar(120) NOT NULL,
  `empl_lastname` varchar(50) NOT NULL,
  `addr_line1` varchar(200) NOT NULL,
  `addr_line2` varchar(200) NOT NULL,
  `empl_city` varchar(60) NOT NULL,
  `empl_state` varchar(100) NOT NULL,
  `country` int(5) NOT NULL,
  `gender` int(2) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(3) NOT NULL,
  `marital_status` int(2) NOT NULL,
  `office_phone` varchar(15) NOT NULL,
  `home_phone` varchar(15) NOT NULL,
  `mobile_phone` varchar(15) NOT NULL,
  `email` varchar(120) NOT NULL,
  `status` int(2) NOT NULL,
  `empl_pic` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_loan`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_loan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `loan_type_id` int(5) NOT NULL,
  `periods` int(5) NOT NULL,
  `monthly_pay` decimal(15,2) NOT NULL,
  `periods_paid` int(5) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_option`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_option` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(150) NOT NULL,
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `0_kv_empl_option`
--

INSERT INTO `0_kv_empl_option` (`id`, `option_name`, `option_value`) VALUES
(1, 'weekly_off', 'Sun'),
(2, 'empl_ref_type', '0'),
(3, 'salary_account', '5410'),
(4, 'paid_from_account', '1060'),
(5, 'expd_percentage_amt', '30'),
(6, 'weekly_off', 'Sun'),
(7, 'empl_ref_type', '0'),
(8, 'next_empl_id', '1');

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_empl_training`
--

CREATE TABLE IF NOT EXISTS `0_kv_empl_training` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `empl_id` varchar(10) NOT NULL,
  `training_desc` varchar(60) NOT NULL,
  `course` varchar(50) NOT NULL,
  `cost` int(8) NOT NULL,
  `institute` varchar(60) NOT NULL,
  `s_date` date NOT NULL,
  `e_date` date NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_hrm_tax`
--

CREATE TABLE IF NOT EXISTS `0_kv_hrm_tax` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `min_sal` int(10) NOT NULL,
  `max_sal` int(10) NOT NULL,
  `percentage` int(10) NOT NULL,
  `offset` int(10) NOT NULL,
  `year` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_kv_loan_types`
--

CREATE TABLE IF NOT EXISTS `0_kv_loan_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_name` varchar(200) NOT NULL,
  `interest_rate` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
