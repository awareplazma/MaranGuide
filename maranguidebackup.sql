-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2025 at 09:32 AM
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
-- Database: `maranguidebackup`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlist`
--

CREATE TABLE `adminlist` (
  `admin_id` int(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_phone_number` varchar(255) DEFAULT NULL,
  `admin_profile_picture` varchar(255) DEFAULT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `admin_role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlist`
--

INSERT INTO `adminlist` (`admin_id`, `admin_password`, `admin_name`, `admin_phone_number`, `admin_profile_picture`, `admin_email`, `admin_role`) VALUES
(2021, '1', 'Afiq', NULL, NULL, NULL, 'superadmin'),
(2025, '1', 'Anakanda', '011-1435 5742', '/media/owner_pfp5.jpeg', 'afiqmazli550@gmail.com', 'owner'),
(2026, '1', 'Attraction ID 2', '011-1435', '/media/owner_pfp/1.jpeg', 'afiqmazli550@gmail.com', 'owner'),
(2030, '123', 'AFIQ MAZLI', '011-1435 5742', '/media/owner_pfp/1.jpeg', 'afiqmazli550@gmail.com', 'owner');

-- --------------------------------------------------------

--
-- Table structure for table `attraction`
--

CREATE TABLE `attraction` (
  `attraction_id` int(255) NOT NULL,
  `attraction_name` varchar(255) NOT NULL,
  `attraction_description` varchar(255) NOT NULL,
  `attraction_created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `attraction_address` varchar(255) NOT NULL,
  `attraction_operating_days` varchar(255) NOT NULL,
  `attraction_opening_hours` time(4) NOT NULL,
  `attraction_closing_hours` time(4) DEFAULT NULL,
  `attraction_status` varchar(255) NOT NULL,
  `attraction_thumbnails` varchar(255) NOT NULL,
  `attraction_latitude` varchar(255) NOT NULL,
  `attraction_longitude` varchar(255) NOT NULL,
  `admin_id` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attraction`
--

INSERT INTO `attraction` (`attraction_id`, `attraction_name`, `attraction_description`, `attraction_created_at`, `attraction_address`, `attraction_operating_days`, `attraction_opening_hours`, `attraction_closing_hours`, `attraction_status`, `attraction_thumbnails`, `attraction_latitude`, `attraction_longitude`, `admin_id`) VALUES
(29, 'Hutan Lipur Teladas', 'Hutan Lipur Teladas', '2024-12-17 04:51:35.438308', 'Hutan Lipur Teladas', 'Monday', '00:49:00.0000', '02:49:00.0000', 'aktif', '/media/attraction/Hutan Lipur Teladas/thumbnail/29_thumbnail.jpg', '3.413812', '102.502441', 2025),
(33, 'Bukit Kertau', 'Destinasi yang dikunjungi kerana keindahan alamnya serta sejarahnya yang tersendiri.', '2024-12-27 02:17:58.072309', 'Bukit Kertau, 28100 Chenor', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '06:00:00.0000', '12:05:00.0000', 'aktif', '/media/attraction/Bukit Kertau/thumbnail/33_thumbnail.jpg', '3.455437', '102.617674', 2026),
(34, 'Maran Hill Golf Resort', 'Maran Hill Golf Resort- Areno, Maran', '2024-12-27 02:17:46.930065', 'Maran Hill Golf Resort- Areno', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '08:09:00.0000', '19:14:00.0000', 'aktif', '/media/attraction/Maran Hill Golf Resort/thumbnail/34_thumbnail.jpg', '3.580961', '102.783498', 2025),
(35, 'Testing', 'Tempat Tarikan', '2025-01-02 07:42:40.322824', 'Present 1 2 3', 'Monday,Tuesday', '20:38:00.0000', '22:38:00.0000', 'aktif', '/media/attraction/Testing/thumbnail/35_thumbnail.jpg', '2.247323', '103.149821', 2030);

-- --------------------------------------------------------

--
-- Table structure for table `attraction_media`
--

CREATE TABLE `attraction_media` (
  `media_id` int(255) NOT NULL,
  `attraction_id` int(255) NOT NULL,
  `media_path` varchar(255) NOT NULL,
  `media_title` varchar(255) NOT NULL,
  `media_description` varchar(255) NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attraction_media`
--

INSERT INTO `attraction_media` (`media_id`, `attraction_id`, `media_path`, `media_title`, `media_description`, `media_type`, `created_at`) VALUES
(35, 29, 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/pictures/676f59292afe6_1735350569.jpg', 'Air Terjun', 'Air Terjun sejuk', 'image', '2024-12-28 01:49:29.000000'),
(36, 29, 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/pictures/676f593bf01cc_1735350587.jpg', 'Temat menarik 2', '123', 'image', '2024-12-28 01:49:47.000000'),
(37, 29, 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/videos/676f595df2f50_1735350621.mp4', 'Menarik', '123', 'video', '2024-12-28 01:50:21.000000'),
(38, 29, 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/pictures/6776441fe89c6_1735803935.jpg', 'Kamera', 'Rakam Video', 'image', '2025-01-02 07:45:35.000000');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(255) NOT NULL,
  `attraction_id` int(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `rating` float NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `approval_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `attraction_id`, `user`, `content`, `rating`, `created_at`, `approval_status`) VALUES
(11, 29, '@anonymous123', 'Cantik!', 3, '2024-12-20 04:08:27.789274', 'Lulus'),
(22, 29, 'pelawatyangbenar', 'Terlalu ramai orang!', 2, '2024-12-27 07:43:51.234144', 'Tidak Lulus'),
(23, 29, '@pencintaalam', 'Sungai dia sejuk. Seronok mandi time panas!', 5, '2025-01-02 07:51:12.181337', 'Tidak Lulus'),
(24, 29, 'alifbata', 'Ada tempat khemah sini. Tapak buat dia jaga elok2', 5, '2025-01-02 07:51:30.222187', 'Tidak Lulus'),
(25, 29, 'Terlalu banyak monyet', 'Terlalu banyak monyet berkelriaran', 1, '2024-12-28 01:46:25.049543', 'Lulus'),
(26, 29, 'Lalala', 'Lalala', 1, '2024-12-27 07:42:00.852166', 'Tidak Lulus'),
(27, 29, 'Test 123', '125152', 5, '2025-01-02 08:00:19.616820', 'Lulus');

-- --------------------------------------------------------

--
-- Table structure for table `eventlist`
--

CREATE TABLE `eventlist` (
  `event_id` int(255) NOT NULL,
  `attraction_id` int(255) NOT NULL,
  `event_created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `event_name` varchar(255) NOT NULL,
  `event_description` varchar(255) NOT NULL,
  `event_thumbnails` varchar(255) NOT NULL,
  `event_start_date` datetime(6) NOT NULL,
  `event_end_date` datetime(6) NOT NULL,
  `event_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventlist`
--

INSERT INTO `eventlist` (`event_id`, `attraction_id`, `event_created_at`, `event_name`, `event_description`, `event_thumbnails`, `event_start_date`, `event_end_date`, `event_status`) VALUES
(26, 29, '2024-12-27 02:22:13.031207', 'Mandi Sungai', 'Air Sungai yang sejuk', '/media/attraction/Hutan Lipur Teladas/Mandi Sungai/thumbnail/26_thumbnail.jpg', '2024-12-27 10:27:00.000000', '2024-12-31 16:21:00.000000', 'active'),
(27, 29, '2025-01-02 07:46:31.761793', 'Test 1', 'Test 1', '/media/attraction/Hutan Lipur Teladas/Test 1/thumbnail/27_thumbnail.jpg', '2025-01-02 15:49:00.000000', '2025-01-28 15:46:00.000000', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `event_media`
--

CREATE TABLE `event_media` (
  `media_id` int(255) NOT NULL,
  `event_id` int(255) NOT NULL,
  `media_title` varchar(255) NOT NULL,
  `media_description` varchar(255) NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `media_type` varchar(255) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_media`
--

INSERT INTO `event_media` (`media_id`, `event_id`, `media_title`, `media_description`, `media_path`, `media_type`, `created_at`) VALUES
(9, 27, 'Index', 'Index', 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/Test 1/pictures/67764472c22dd_1735804018.png', 'image', '2025-01-02 07:46:58.000000'),
(10, 27, 'Test', 'Test', 'C:/xampp/htdocs/MARANGUIDE/media/attraction/Hutan Lipur Teladas/Test 1/pictures/6776474748a8e_1735804743.jpg', 'image', '2025-01-02 07:59:03.000000');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `feedback_content` varchar(255) NOT NULL,
  `feedback_created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `read_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `title`, `feedback_content`, `feedback_created_at`, `read_status`) VALUES
(4, 'Butang tidak berfungsi', 'Ada sesetengah butang tidak boleh ditekan', '2024-12-27 07:29:52.000000', 'unread'),
(5, 'Gambar tidak jelas', 'Ada sesetengah tempat gambar tidak jelas', '2024-12-27 07:30:33.000000', 'unread'),
(6, 'Butang Tidak', 'AA', '2025-01-02 08:06:32.000000', 'unread');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlist`
--
ALTER TABLE `adminlist`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `attraction`
--
ALTER TABLE `attraction`
  ADD PRIMARY KEY (`attraction_id`),
  ADD UNIQUE KEY `attraction_id` (`attraction_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `attraction_id_2` (`attraction_id`);

--
-- Indexes for table `attraction_media`
--
ALTER TABLE `attraction_media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `attraction_id` (`attraction_id`),
  ADD KEY `attraction_id_2` (`attraction_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `attraction_id` (`attraction_id`);

--
-- Indexes for table `eventlist`
--
ALTER TABLE `eventlist`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `attraction_id_2` (`attraction_id`),
  ADD KEY `attraction_id_3` (`attraction_id`),
  ADD KEY `attraction_id` (`attraction_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event_media`
--
ALTER TABLE `event_media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `event_id_2` (`event_id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `event_id_3` (`event_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlist`
--
ALTER TABLE `adminlist`
  MODIFY `admin_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2031;

--
-- AUTO_INCREMENT for table `attraction`
--
ALTER TABLE `attraction`
  MODIFY `attraction_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `attraction_media`
--
ALTER TABLE `attraction_media`
  MODIFY `media_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `eventlist`
--
ALTER TABLE `eventlist`
  MODIFY `event_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `event_media`
--
ALTER TABLE `event_media`
  MODIFY `media_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attraction`
--
ALTER TABLE `attraction`
  ADD CONSTRAINT `attraction_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `adminlist` (`admin_id`);

--
-- Constraints for table `attraction_media`
--
ALTER TABLE `attraction_media`
  ADD CONSTRAINT `attraction_media_ibfk_1` FOREIGN KEY (`attraction_id`) REFERENCES `attraction` (`attraction_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_constraint_name` FOREIGN KEY (`attraction_id`) REFERENCES `attraction` (`attraction_id`);

--
-- Constraints for table `eventlist`
--
ALTER TABLE `eventlist`
  ADD CONSTRAINT `eventlist_ibfk_1` FOREIGN KEY (`attraction_id`) REFERENCES `attraction` (`attraction_id`);

--
-- Constraints for table `event_media`
--
ALTER TABLE `event_media`
  ADD CONSTRAINT `event_media_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `eventlist` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
