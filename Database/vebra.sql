-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2026 at 08:04 AM
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
-- Database: `vebra`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `name`, `slug`, `description`, `cover`, `release_year`, `genre`, `created_at`) VALUES
(1, 'Nightform', 'nightform', 'A collection of atmospheric sounds and unforgettable melodies.', 'assets/images/album-1.jpg', '2026', 'Pop', '2026-11-05 04:05:55'),
(2, 'Static Bloom', 'static-bloom', 'A collection of intimate melodies and modern Urdu pop.', 'assets/images/album-2.jpg', '2024', 'Pop', '2026-11-05 04:05:55'),
(3, 'Halogen', 'halogen', 'A collection of global sounds and alternative rhythms.', 'assets/images/album-3.jpg', '2021', 'Mixed', '2026-11-05 04:05:55');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `bio`, `image`, `created_at`) VALUES
(8, 'Atif Aslam', 'Pakistani singer known for his distinctive vocals, romantic melodies and performances across pop and film music. His music spans soulful ballads, acoustic pop and energetic soundtracks.', 'assets/media/artists/img_images_32bfcca7bd3a.jpg', '2026-10-29 08:10:48'),
(9, 'Ali Sethi', 'Pakistani singer and songwriter known for blending traditional South Asian music, ghazal influences and contemporary pop.', 'assets/media/artists/img_images__2__7cd52bf6cb6b.jpg', '2026-10-29 08:12:31'),
(10, 'Shae Gill', 'Pakistani singer known for her expressive vocals and contemporary pop-fusion sound.', 'assets/media/artists/img_images__3__11b83e02982b.jpg', '2026-10-29 08:14:32'),
(11, 'Momina Mustehsan', 'Pakistani singer and songwriter recognized for her melodic vocals and work across pop and contemporary South Asian music.', 'assets/media/artists/img_images__4__d1e40d41ed68.jpg', '2026-10-29 08:15:22'),
(12, 'Asim Azhar', 'Pakistani singer and songwriter whose music blends contemporary pop, romantic melodies and modern production.', 'assets/media/artists/img_images__5__965512aaf5dd.jpg', '2026-10-29 08:15:57'),
(13, 'Rahat Fateh Ali Khan', 'Pakistani vocalist known for qawwali, Sufi music and powerful performances in South Asian film soundtracks.', 'assets/media/artists/img_images__6__ba5f7318c268.jpg', '2026-10-29 08:16:31'),
(14, 'Abdul Hannan', 'Pakistani singer and songwriter associated with contemporary indie pop, acoustic melodies and collaborations with emerging artists.', 'assets/media/artists/img_images__7__8b48cc7f8c9a.jpg', '2026-10-29 08:17:10'),
(15, 'Talwiinder', 'Punjabi singer and songwriter known for atmospheric production, contemporary Punjabi music and melodic vocals.', 'assets/media/artists/img_images__8__a08d45771a96.jpg', '2026-10-29 08:18:04'),
(16, 'Sidhu Moose Wala', 'Punjabi singer, rapper and songwriter known for his influential Punjabi hip-hop sound and storytelling.', 'assets/media/artists/img_images__9__82f3e72a5201.jpg', '2026-10-29 08:18:42'),
(17, 'Arijit Singh', 'Indian playback singer known for expressive vocals, romantic ballads and emotionally driven Hindi film songs.', 'assets/media/artists/img_images__10__415738bdcf88.jpg', '2026-10-29 08:19:18'),
(18, 'Badshah', 'Indian rapper and music producer known for commercial hip-hop, Punjabi pop and dance-oriented tracks.', 'assets/media/artists/img_images__11__36fefb7680db.jpg', '2026-10-29 08:19:48'),
(19, 'Diljit Dosanjh', 'Punjabi singer and actor known for Punjabi pop, contemporary bhangra and international music collaborations.', 'assets/media/artists/img_images__12__6acbc555880f.jpg', '2026-10-29 08:20:30'),
(20, 'Ed Sheeran', 'English singer-songwriter known for melodic pop, acoustic guitar arrangements and romantic ballads.', 'assets/media/artists/img_images__13__400566953430.jpg', '2026-10-29 08:21:09'),
(21, 'Eminem', 'American rapper known for intricate wordplay, rapid delivery and influential hip-hop recordings.', 'assets/media/artists/img_images__14__e6d8317341da.jpg', '2026-10-29 08:21:50'),
(22, 'Sabrina Carpenter', 'American singer and songwriter known for contemporary pop, disco-pop influences and catchy melodies.', 'assets/media/artists/img_images__15__aabbdf7273bc.jpg', '2026-10-29 08:23:53'),
(23, 'Ellie Goulding', 'English singer-songwriter known for electronic pop, synth-pop and dance-oriented music.', 'assets/media/artists/img_images__16__4c22d78f7ff6.jpg', '2026-10-29 08:24:34'),
(24, 'Britney Spears', 'American pop singer known for dance-pop, electropop and influential mainstream pop recordings.', 'assets/media/artists/img_images__17__cc064d0d0afd.jpg', '2026-10-29 08:25:28'),
(25, 'Armaan Malik', 'Indian singer known for his smooth vocals, romantic ballads, and contemporary Hindi and English pop music.', 'assets/media/artists/img_images__18__11a5b05bfe63.jpg', '2026-10-29 08:27:42'),
(26, 'Jasmine Sandlas', 'Punjabi-American singer and songwriter known for her distinctive vocals, Punjabi pop, and energetic urban music.', 'assets/media/artists/img_images__19__813f5b8254ba.jpg', '2026-10-29 08:30:14'),
(27, 'Kendrick Lamar', 'American rapper and songwriter acclaimed for intricate lyricism, storytelling, and innovative contemporary hip-hop.', 'assets/media/artists/img_images__20__24a09376e9a7.jpg', '2026-10-29 08:31:01'),
(28, 'Lana Del Rey', 'American singer-songwriter known for cinematic alternative pop, atmospheric production, and nostalgic musical themes.', 'assets/media/artists/img_images__21__d6777f89287b.jpg', '2026-10-29 08:31:34'),
(29, 'G-Eazy', 'American rapper and songwriter known for contemporary hip-hop, melodic rap, and polished pop-rap production.', 'assets/media/artists/img_images__25__4eef34eca118.jpg', '2026-10-29 08:51:56'),
(30, 'Asfar Hussain', 'Pakistani singer and songwriter known for expressive vocals, contemporary indie music, and his work with the band Bayaan.', 'assets/media/artists/img_images__26__a71c6795f195.jpg', '2026-10-29 08:52:29'),
(31, 'Halsey', 'American singer and songwriter known for alternative pop, electropop, and emotionally expressive music.', 'assets/media/artists/img_images__27__eb5da1fc8678.jpg', '2026-10-29 08:53:06'),
(32, 'Cris MJ', 'Chilean singer and songwriter known for Latin urban music, reggaeton, and contemporary Latin hits.', 'assets/media/artists/img_images__28__b23f72f1a441.jpg', '2026-10-29 08:53:35'),
(33, 'FloyyMenor', 'Chilean singer and songwriter associated with Latin urban music, reggaeton, and modern Latin trap sounds.', 'assets/media/artists/img_images__29__0923628c0913.jpg', '2026-10-29 08:54:26'),
(34, 'The PropheC', 'Punjabi-Canadian singer, songwriter, and producer known for blending Punjabi melodies with contemporary R&B and urban production.', 'assets/media/artists/img_images__30__6c2c8e741982.jpg', '2026-10-29 08:55:03'),
(35, 'Ravyn Lenae', 'American singer and songwriter known for alternative R&B, atmospheric production, and distinctive vocals.', 'assets/media/artists/img_images__31__4e6761f76600.jpg', '2026-10-29 08:55:43'),
(36, 'AP Dhillon', 'Indo-Canadian singer and rapper known for blending Punjabi music with contemporary hip-hop, R&B, and melodic pop.', 'assets/media/artists/img_images__36__ea55c729c6e8.jpg', '2026-10-29 09:30:40'),
(37, 'Billie Eilish', 'American singer-songwriter known for alternative pop, intimate vocals, and atmospheric music production.', 'assets/media/artists/img_images__37__58a904736257.jpg', '2026-10-29 09:31:28'),
(38, 'Dua Lipa', 'English-Albanian pop singer known for dance-pop hits, disco influences, and electronic production.', 'assets/media/artists/img_images__38__0f5bb006c837.jpg', '2026-10-29 09:32:37'),
(39, 'Drake', 'Canadian rapper and singer known for melodic hip-hop, contemporary R&B, and influential mainstream releases.', 'assets/media/artists/img_images__39__60bc1eaeb6e4.jpg', '2026-10-29 09:33:11'),
(40, 'Rovalio', 'Music producer and collaborator associated with contemporary South Asian indie-pop releases and melodic production.', 'assets/media/artists/img_images__40__b0182c3f5b2e.jpg', '2026-10-29 09:33:47'),
(41, 'Hasan Raheem', 'Pakistani singer-songwriter known for blending contemporary pop, indie music and R&B with distinctive melodies.', 'assets/media/artists/img_images__49__4ae30f8ec7d4.jpg', '2026-10-29 15:19:54'),
(42, 'Kaifi Khalil', 'Pakistani singer-songwriter recognized for emotional vocals and music influenced by Balochi language and culture.', 'assets/media/artists/img_images__50__2c68ed80c97f.jpg', '2026-10-29 15:20:36'),
(43, 'Young Stunners', 'Pakistani hip-hop duo of Talha Anjum and Talhah Yunus, known for Urdu rap, storytelling and contemporary South Asian hip-hop.', 'assets/media/artists/img_images__51__c56d7cb61f99.jpg', '2026-10-29 15:21:30'),
(44, 'Faris Shafi', 'Pakistani rapper and songwriter known for sharp wordplay, expressive delivery and Urdu-language hip-hop.', 'assets/media/artists/img_images__52__b81cadebd631.jpg', '2026-10-29 15:22:21'),
(45, 'Aima Baig', 'Pakistani vocalist known for contemporary pop songs, film soundtracks and performances across Urdu and Punjabi music.', 'assets/media/artists/img_images__53__21ca50b18e2e.jpg', '2026-10-29 15:23:24'),
(46, 'Maanu', 'Pakistani singer-songwriter associated with contemporary indie pop, atmospheric production and modern Urdu music.', 'assets/media/artists/img_images__54__5f1caa752fd6.jpg', '2026-10-29 15:24:08'),
(47, 'Natasha Noorani', 'Pakistani singer, songwriter and music researcher known for alternative pop and independent music projects.', 'assets/media/artists/img_images__55__434e74a6f69b.jpg', '2026-10-29 15:25:06'),
(48, 'Shreya Ghoshal', 'Indian playback singer celebrated for expressive vocals across Hindi cinema and multiple Indian-language soundtracks.', 'assets/media/artists/img_images__56__96e8748280ea.jpg', '2026-10-29 15:26:04'),
(49, 'Karan Aujla', 'Punjabi singer, rapper and lyricist known for contemporary Punjabi hits, melodic rap and songwriting.', 'assets/media/artists/img_images__57__bb78780f61eb.jpg', '2026-10-29 15:26:42'),
(50, 'Anirudh Ravichander', 'Indian composer, singer and music producer known for energetic film soundtracks and modern electronic-pop arrangements.', 'assets/media/artists/img_images__58__c839b85045b0.jpg', '2026-10-29 15:27:17'),
(51, 'The Weeknd', 'Canadian singer and songwriter known for atmospheric R&B, synth-driven pop and cinematic production.', 'assets/media/artists/img_images__59__f9ce3765f01d.jpg', '2026-10-29 15:28:04'),
(52, 'Bad Bunny', 'Puerto Rican singer and rapper known for Spanish-language Latin trap, reggaeton and contemporary Latin pop.', 'assets/media/artists/img_images__60__a2ef6e957d85.jpg', '2026-10-29 15:28:59'),
(53, 'Taylor Swift', 'American singer-songwriter known for narrative songwriting, emotional lyrics and successful pop and country music releases.', 'assets/media/artists/img_images__61__b734ca9a484b.jpg', '2026-10-29 15:32:36'),
(54, 'Ariana Grande', 'American singer and songwriter known for powerful vocals, contemporary pop and R&B influences.', 'assets/media/artists/img_images__62__3248116c708c.jpg', '2026-10-29 15:34:25'),
(55, 'Bruno Mars', 'American singer, songwriter and performer known for pop, funk, soul and dance music.', 'assets/media/artists/img_images__63__a780251a1948.jpg', '2026-10-29 15:35:15'),
(56, 'Justin Bieber', 'Canadian singer known for contemporary pop, R&B influences and internationally popular hit songs.', 'assets/media/artists/img_images__64__8d6ce0248921.jpg', '2026-10-29 15:36:02'),
(57, 'Post Malone', 'American singer, rapper and songwriter who blends hip-hop, pop, rock and melodic hooks.', 'assets/media/artists/img_images__65__eebddd96ef23.jpg', '2026-10-29 15:40:55'),
(58, 'Travis Scott', 'American rapper and producer known for atmospheric trap, experimental production and energetic performances.', 'assets/media/artists/img_images__66__0bd8d43c9c14.jpg', '2026-10-29 15:41:32'),
(59, 'Savage', 'British-born American rapper known for minimalist trap production and contemporary hip-hop.', 'assets/media/artists/img_images__66__ab0414b9cb51.jpg', '2026-10-29 15:42:42'),
(60, 'Jubin Nautiyal', 'Indian playback singer known for Hindi film songs, romantic ballads and soulful vocals.', 'assets/media/artists/img_images__68__cb601b265ecc.jpg', '2026-10-29 15:44:23'),
(61, 'Vishal Mishra', 'Indian singer, songwriter and composer known for emotional Hindi film songs and contemporary pop ballads.', 'assets/media/artists/img_images__69__4d1c8c16b89c.jpg', '2026-10-29 15:44:57'),
(62, 'Sonu Nigam', 'Indian playback singer known for expressive vocals across Hindi film music and several other Indian languages.', 'assets/media/artists/img_images__70__72d618564532.jpg', '2026-10-29 15:45:29'),
(63, 'Vishal Dadlani', 'Indian singer, composer and performer known for Bollywood music, rock influences and the duo Vishal–Shekhar.\r\nCopy biography', 'assets/media/artists/img_images__71__8d4d49fc4651.jpg', '2026-10-29 15:46:17'),
(64, 'Pritam', 'Indian composer and music producer known for popular Hindi film soundtracks and contemporary Bollywood melodies.', 'assets/media/artists/img_images__72__024526a34d2e.jpg', '2026-10-29 15:46:48'),
(65, 'Tahsan Khan', 'Bangladeshi singer, songwriter and actor known for Bengali pop, romantic songs and contemporary Bangla music.', 'assets/media/artists/img_images__73__bd0c9235840b.jpg', '2026-10-29 15:47:27'),
(66, 'Habib Wahid', 'Bangladeshi singer and composer known for modern Bengali pop and electronic influences in Bangla music.', 'assets/media/artists/img_images__74__2b884ee7aab0.jpg', '2026-10-29 15:48:05'),
(67, 'Anupam Roy', 'Indian singer-songwriter and composer known for Bengali music and Hindi film soundtracks.', 'assets/media/artists/img_images_1c308aa82c15.jpg', '2026-10-29 15:48:48'),
(68, 'SEVENTEEN', 'South Korean boy group known for K-pop, synchronized performances and self-produced music.', 'assets/media/artists/img_images__76__98dd75fcf28a.jpg', '2026-10-29 15:49:36'),
(69, 'EXO', 'South Korean boy group known for vocal-driven K-pop, R&B influences and dance-pop releases.', 'assets/media/artists/img_images__77__63173dcfc72d.jpg', '2026-10-29 15:50:08'),
(70, 'Taeyang', 'South Korean singer known for R&B, K-pop and soulful solo performances.', 'assets/media/artists/img_images__78__a5d2c6213b66.jpg', '2026-10-29 15:50:53'),
(74, 'Ali Zafar', 'Pakistani singer, songwriter and actor known for pop hits, romantic melodies and contributions to Pakistani and Indian film music.', 'assets/media/artists/img_images_3e89a80a725a.jpg', '2026-11-04 15:11:19'),
(75, 'Bilal Saeed', 'Pakistani singer, songwriter and music producer known for Punjabi and Urdu pop, romantic songs and contemporary R&B influences.', 'assets/media/artists/img_images__1__a610595be8b4.jpg', '2026-11-04 15:12:45'),
(76, 'Farhan Saeed', 'Pakistani singer, songwriter and actor, formerly associated with the band Jal, known for pop-rock music and romantic vocals.', 'assets/media/artists/img_images__2__ddd3f8ffc2b5.jpg', '2026-11-04 15:13:31'),
(77, 'Quratulain Balouch', 'Pakistani singer known for her powerful vocals, contemporary Sufi music, soulful performances and popular television soundtrack songs.', 'assets/media/artists/img_images__3__a163b70eea95.jpg', '2026-11-04 15:14:32'),
(78, 'Zeb Bangash', 'Pakistani singer and songwriter known for folk-inspired melodies, alternative music and her work with Zeb and Haniya.', 'assets/media/artists/img_images__4__6e0e12ca2871.jpg', '2026-11-04 15:15:30'),
(79, 'Shuja Haider', 'Pakistani singer, songwriter and music composer known for contemporary pop, soulful vocals and television soundtracks.', 'assets/media/artists/img_images__5__6432857954be.jpg', '2026-11-04 15:16:10'),
(80, 'Junoon', 'Legendary Pakistani rock band known for combining Sufi poetry with rock music and creating influential Urdu rock songs.', 'assets/media/artists/img_images__6__2b12eec91858.jpg', '2026-11-04 15:17:58'),
(81, 'Strings', 'Pakistani pop-rock band known for memorable melodies, guitar-driven arrangements and influential songs in South Asian music.', 'assets/media/artists/img_images__8__c0edc34a33d4.jpg', '2026-11-04 15:18:52'),
(82, 'Umair Jaswal', 'Pakistani singer, songwriter and actor known for rock music, energetic performances and powerful vocals.', 'assets/media/artists/img_images__9__63b4b38425f0.jpg', '2026-11-04 15:19:29'),
(83, 'Asrar Shah', 'Pakistani singer known for Sufi-inspired music, expressive vocals and blending traditional influences with contemporary sounds.', 'assets/media/artists/img_images__10__28094969d66c.jpg', '2026-11-04 15:20:10'),
(84, 'Ali Azmat', 'Pakistani rock singer known for his powerful vocals, solo music and role as the lead vocalist of Junoon.', 'assets/media/artists/img_images__11__5b2b2eb578e0.jpg', '2026-11-04 15:21:26'),
(85, 'Bilal Khan', 'Pakistani singer-songwriter known for acoustic pop, romantic melodies and contemporary Urdu music.', 'assets/media/artists/img_images__12__7213178c4288.jpg', '2026-11-04 15:22:01'),
(86, 'Arooj Aftab', 'Pakistani-American singer and composer known for her distinctive blend of jazz, classical influences and contemporary music.', 'assets/media/artists/img_images__13__f6a4aa364e1b.jpg', '2026-11-04 15:22:45'),
(87, 'Sonu Kakkar', 'Indian playback singer known for energetic Bollywood songs and contemporary Indian pop music.', 'assets/media/artists/img_images__14__502f8f22939a.jpg', '2026-11-04 15:23:27'),
(88, 'Neha Kakkar', 'Indian singer known for popular Bollywood hits, dance tracks and contemporary pop music.', 'assets/media/artists/img_images__15__dd357b3725ee.jpg', '2026-11-04 15:24:42'),
(89, 'Vishal–Shekhar', 'Indian music composer duo known for Bollywood soundtracks, pop-rock arrangements and hit film songs.', 'assets/media/artists/img_images__16__85762ff55672.jpg', '2026-11-04 15:25:21'),
(90, 'Mohit Chauhan', 'Indian playback singer known for soulful vocals, romantic melodies and popular Hindi film songs.', 'assets/media/artists/img_images__17__56762925f9aa.jpg', '2026-11-04 15:26:01'),
(91, 'Arnob', 'Bangladeshi singer-songwriter and composer known for alternative music, folk influences and Bengali melodies.', 'assets/media/artists/img_images__18__6eee1cf366ce.jpg', '2026-11-04 15:26:33'),
(92, 'Lady Gaga', 'American singer-songwriter known for theatrical pop, dance music and powerful vocal performances.', 'assets/media/artists/img_images__19__a4f3b3570bde.jpg', '2026-11-04 15:27:50'),
(93, 'Coldplay', 'British rock band known for atmospheric sound, emotional melodies and stadium-scale performances.', 'assets/media/artists/img_images__20__2577f83627b0.jpg', '2026-11-04 15:28:33'),
(94, 'Imagine Dragons', 'American pop-rock band known for energetic anthems, alternative rock and electronic influences.', 'assets/media/artists/img_images__21__581223ce75c3.jpg', '2026-11-04 15:29:19'),
(95, 'BLACKPINK', 'South Korean girl group known for global K-pop hits, dance-pop and hip-hop influences.', 'assets/media/artists/img_images__23__b981dff908d9.jpg', '2026-11-04 15:32:32'),
(96, 'BTS', 'South Korean boy group known for global pop success, hip-hop influences and genre-diverse music.', 'assets/media/artists/img_images__24__b5a272928e13.jpg', '2026-11-04 15:33:23'),
(97, 'Jungkook', 'South Korean singer known for contemporary pop, R&B influences and international solo releases.', 'assets/media/artists/img_images__25__a554ad22aff8.jpg', '2026-11-04 15:33:59'),
(98, 'Aditya Rikhari', 'Indian singer-songwriter known for emotional indie-pop, romantic melodies and contemporary Hindi music.', 'assets/media/artists/img_images__26__308c50b94d74.jpg', '2026-11-04 15:37:47'),
(99, 'Kushagra', 'Indian singer-songwriter known for soft romantic pop, melodic vocals and contemporary independent music.', 'assets/media/artists/img_images__27__5dd157ab85e0.jpg', '2026-11-04 15:38:25'),
(101, 'Mitraz', 'Indian independent music duo known for electronic indie-pop, romantic melodies and modern Hindi music.', 'assets/media/artists/img_images__28__9241443ffa3e.jpg', '2026-11-04 15:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `artist_applications`
--

CREATE TABLE `artist_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `artist_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artist_applications`
--

INSERT INTO `artist_applications` (`id`, `name`, `bio`, `image`, `status`, `artist_id`, `created_at`, `reviewed_at`) VALUES
(1, 'Test Artist 001', '', 'assets/images/team-1.jpg', 'approved', 73, '2026-11-04 13:37:27', '2026-11-04 18:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_key` varchar(80) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `size` varchar(16) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_key`, `quantity`, `size`, `created_at`) VALUES
(1, 1, 'nightform-vinyl', 1, '', '2026-11-02 18:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Ali', 'ali@gmail.com', 'Add 5 Atif Songs', 'Add 5 Atif Aslam Popular Songs', '2026-10-31 19:10:19'),
(2, 'Umar Ali', 'mumarali6202@gmail.com', 'Add 5 Atif Songs', 'Add 5 Atif Aslam Popular Songs', '2026-10-31 20:19:20'),
(3, 'Muhammad Umar Ali', 'mumarali6202@gmail.com', 'Add 5 Atif Songs', 'Add More Popular Songs', '2026-10-31 20:24:38'),
(4, 'Muhammad Umar Ali', 'mumarali6202@gmail.com', 'Add 5 Atif Songs', 'Add More Popular Songs', '2026-10-31 20:32:20'),
(5, 'Muhammad Umar Ali', 'mumarali6202@gmail.com', 'Add 5 Atif Songs', 'Add More Popular Songs', '2026-10-31 20:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(3, 'Ambient'),
(7, 'Classical'),
(1, 'Electronic'),
(4, 'Hip-Hop'),
(8, 'Other'),
(5, 'Pop'),
(6, 'Rock'),
(2, 'Synthwave');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `song_id`, `rating`, `review`, `status`, `created_at`) VALUES
(4, 1, 6, 5, 'Afreen Afreen is a beautiful composition. The vocals and music create an unforgettable listening experience.', 'approved', '2026-11-04 15:02:15'),
(5, 5, 12, 5, 'Pasoori has an amazing vibe. The vocals, rhythm and overall production make it a track worth replaying.', 'approved', '2026-11-04 15:02:15'),
(6, 8, 7, 4, 'Mockingbird is a classic track with meaningful lyrics and a memorable melody. Great addition to the collection.', 'approved', '2026-11-04 15:02:15'),
(7, 2, 9, 5, 'Perfect is a lovely song with a timeless melody. I really enjoy listening to it.', 'approved', '2026-11-04 15:02:15'),
(8, 5, 17, 4, 'Iraaday has a relaxing sound and beautiful vocals. Definitely one of my favourite tracks here.', 'approved', '2026-11-04 15:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `language` varchar(80) DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `artwork` varchar(255) DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `album_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist_id`, `genre_id`, `language`, `release_year`, `artwork`, `audio_path`, `status`, `created_at`, `album_id`) VALUES
(6, 'Afreen Afreen', 13, 8, 'Urdu', '2026', 'assets/media/album-art/img_1900x1900-000000-80-0-0_35240fa6dbb4.jpg', 'assets/media/music/track_Coke_Studio_Season_9-_Af_f1ec08ba6830.mp3', 'approved', '2026-10-29 08:40:57', 3),
(7, 'Mockingbird', 21, 4, 'English', '2004', 'assets/media/album-art/img_images__22__94baade6963d.jpg', 'assets/media/music/track_Eminem_-_Mockingbird__Ly_a09b19304c86.mp3', 'approved', '2026-10-29 08:48:24', 3),
(9, 'Perfect', 20, 5, 'English', '2017', 'assets/media/album-art/img_images__23__9c7e7bcae815.jpg', 'assets/media/music/track_Ed_Sheeran_-_Perfect__Ly_819cc23180c6.mp3', 'approved', '2026-10-29 08:56:16', 3),
(10, 'Him & I', 29, 4, 'English', '2017', 'assets/media/album-art/img_images__24__9e12b54f9e90.jpg', 'assets/media/music/track_G-Eazy___Halsey_-_Him____dd78be59dab6.mp3', 'approved', '2026-10-29 08:57:02', 3),
(11, 'Ae Dil Hai Mushkil', 17, 5, 'Hindhi', '2016', 'assets/media/album-art/img_images__32__e0445751f2b4.jpg', 'assets/media/music/track_Ae_Dil_Hai_Mushkil__Titl_a59ecabb76cc.mp3', 'approved', '2026-10-29 09:21:38', 3),
(12, 'Pasoori', 9, 5, 'Panjabi', '2022', 'assets/media/album-art/img_images__33__3347bad3342f.jpg', 'assets/media/music/track_Coke_Studio_-_Season_14__b842c36609b4.mp3', 'approved', '2026-10-29 09:23:24', 3),
(13, 'Hawayein', 17, 5, 'Hindi', '2017', 'assets/media/album-art/img_images__34__2e26317b4a1d.jpg', 'assets/media/music/track_Hawayein_-_Lyrical_Video_b0a3e21bbdf2.mp3', 'approved', '2026-10-29 09:25:12', 3),
(14, 'Mehram', 30, 5, 'Urdu', '2022', 'assets/media/album-art/img_images__35__bfd52e1ab026.jpg', 'assets/media/music/track_Coke_Studio_-_Season_14__139e81f40887.mp3', 'approved', '2026-10-29 09:26:35', 3),
(15, 'Tajdar-e-Haram', 8, 8, 'Urdu', '2015', 'assets/media/album-art/img_images__41__0d565cd6677d.jpg', 'assets/media/music/track_Coke_Studio_Season_8-_Ta_80c3f02ee1f9.mp3', 'approved', '2026-10-29 09:44:19', 3),
(16, 'Tera Woh Pyar', 11, 5, 'Urdu', '2016', 'assets/media/album-art/img_images__42__ad591179bf1e.jpg', 'assets/media/music/track_Coke_Studio_Season_9-_Te_64277192bd4d.mp3', 'approved', '2026-10-29 09:46:04', 3),
(17, 'Iraaday', 14, 5, 'Urdu', '2022', 'assets/media/album-art/img_images__43__4b1c0c3b2e94.jpg', 'assets/media/music/track_Abdul_Hannan___Rovalio_-_fcc83f841e8b.mp3', 'approved', '2026-10-29 09:47:39', 3),
(23, 'Pheli Dafa', 8, 5, 'Urdu', '2017', 'assets/media/album-art/img_images__90__ea22a784307e.jpg', 'assets/media/music/track_Atif_Aslam-_Pehli_Dafa_S_829a647fca2e.mp3', 'approved', '2026-11-03 04:58:54', 2),
(24, 'Hona Tha Pyar', 8, 5, 'Urdu', '2018', 'assets/media/album-art/img_images__91__6ed921c04295.jpg', 'assets/media/music/track_Atif_Aslam_s_Hona_Tha_Py_51c5982f8e3d.mp3', 'approved', '2026-11-03 05:02:43', 2),
(25, 'Rafta Rafta', 8, 5, 'Urdu', '2021', 'assets/media/album-art/img_images__92__578b7aacba2f.jpg', 'assets/media/music/track_Rafta_Rafta_-_Official_M_bfd07e77a444.mp3', 'approved', '2026-11-03 05:04:04', 2),
(26, 'Woh Lamha Woh Baatein', 8, 5, 'Urdu', '2005', 'assets/media/album-art/img_images__93__6c542f59b389.jpg', 'assets/media/music/track_Woh_Lamhe_Woh_Baatein__L_a56724affd0c.mp3', 'approved', '2026-11-03 05:06:00', 2),
(27, 'Jab Koi Baat', 8, 5, 'Urdu', '2018', 'assets/media/album-art/img_images__94__62ebfcd55fbb.jpg', 'assets/media/music/track_Jab_Koi_Baat_-_DJ_Chetas_a2721874122a.mp3', 'approved', '2026-11-03 05:07:09', 2),
(28, 'Gulon Main Rang', 9, 8, 'Urdu', '2019', 'assets/media/album-art/img_images__89__596512e96bd8.jpg', 'assets/media/music/track_Coke_Studio_Season_12_-__1bd101aaa8a2.mp3', 'approved', '2026-11-03 08:10:36', 2),
(29, 'Tere Hawale', 17, 5, 'Hindhi', '2022', 'assets/media/album-art/img_images__90__bce1dafc1b8e.jpg', 'assets/media/music/track_Tere_Hawale_-_Arijit-Shr_d1bac089fa8c.mp3', 'approved', '2026-11-03 08:12:16', 2),
(30, 'Chandni Raat', 9, 8, 'Urdu', '2019', 'assets/media/album-art/img_images__91__750d01fb237e.jpg', 'assets/media/music/track_Chandni_Raat_-_Ali_Sethi_7c7f62e39488.mp3', 'approved', '2026-11-03 08:13:10', 2),
(31, 'Dil Ki Khair', 9, 8, 'Urdu', '2019', 'assets/media/album-art/img_a0845462309_5_b30d22dee117.jpg', 'assets/media/music/track_Dil_Ki_Khair_-_Ali_Sethi_a024b0307873.mp3', 'approved', '2026-11-03 08:14:25', 2),
(32, 'Sahiba', 98, 5, 'Hindhi', '2023', 'assets/media/album-art/img_images__29__59f5ca37f927.jpg', 'assets/media/music/track_Sahiba__Official_Music_V_279623675d5d.mp3', 'approved', '2026-11-04 15:48:30', 2),
(33, 'Samjo Na', 98, 5, 'Hindhi', '2022', 'assets/media/album-art/img_images__32__663cf242570b.jpg', 'assets/media/music/track_Aditya_Rikhari_-_SAMJHO__21c4c19f9d4d.mp3', 'approved', '2026-11-04 15:49:08', 2),
(34, 'Paro', 98, NULL, 'Hindhi', '2024', 'assets/media/album-art/img_images__31__eb06ad53ab94.jpg', 'assets/media/music/track_Aditya_Rikhari_-_Paaro___87cf333c7eb4.mp3', 'approved', '2026-11-04 15:49:46', 2),
(35, 'Faasle', 98, 5, 'Hindhi', '2021', 'assets/media/album-art/img_images__26__e9ebaf9ad92f.jpg', 'assets/media/music/track_Aditya_Rikhari_-_FAASLE_da15b491dc13.mp3', 'approved', '2026-11-04 15:50:20', 1),
(36, 'Teri Yaad', 98, 5, 'Hindhi', '2023', 'assets/media/album-art/img_images__40__c1aa89b8ea6d.jpg', 'assets/media/music/track_Aditya_Rikhari_____TERI__132ae8c65776.mp3', 'approved', '2026-11-04 15:50:52', 1),
(37, 'Finding Her', 99, 5, 'Hindhi', '2025', 'assets/media/album-art/img_images__38__8060bb07508b.jpg', 'assets/media/music/track_Finding_Her__Jana_Mere_S_d9ee0e1a3a7f.mp3', 'approved', '2026-11-04 15:58:47', 1),
(39, 'Dard', 99, 5, 'Hindhi', '2023', 'assets/media/album-art/img_images__37__81bf8fa8bff2.jpg', 'assets/media/music/track_Dard__Official_Video__-__dc13edb5f59e.mp3', 'approved', '2026-11-04 16:00:17', 1),
(40, 'Baawra', 99, 5, 'Hindhi', '2024', 'assets/media/album-art/img_images__36__a8efb5fe6546.jpg', 'assets/media/music/track_Baawra__Official_Video___a6d809f8d982.mp3', 'approved', '2026-11-04 16:01:03', 1),
(41, 'Main Kho Gaya', 99, 5, 'Hindhi', '2024', 'assets/media/album-art/img_images__35__df44e1aaf7d3.jpg', 'assets/media/music/track_Main_Kho_Gaya__Visualise_2178851f35af.mp3', 'approved', '2026-11-04 16:02:01', 1),
(42, 'Rozaana', 99, 5, 'Hindhi', '2024', 'assets/media/album-art/img_images__34__fe5783294f45.jpg', 'assets/media/music/track_Rozaana__Visualiser__-_K_236d905de25e.mp3', 'approved', '2026-11-04 16:02:41', 1),
(43, 'Akhiyaan Gulaab', 101, 5, 'Hindhi', '2024', 'assets/media/album-art/img_images__43__d655ac1f87ee.jpg', 'assets/media/music/track_MITRAZ_-_Gulaab__Officia_15cf8c1383cb.mp3', 'approved', '2026-11-04 16:16:19', 1),
(44, 'Akhiyaan', 101, 5, 'Hindhi', '2022', 'assets/media/album-art/img_images__42__c081e92a9464.jpg', 'assets/media/music/track_MITRAZ_-_Akhiyaan__Offic_cfc2a955be54.mp3', 'approved', '2026-11-04 16:17:24', 1),
(45, 'Alfaazo', 101, 5, 'Hindhi', '2023', 'assets/media/album-art/img_images__41__80f84ab22822.jpg', 'assets/media/music/track_Alfaazo_-__MITRAZ_-_Tren_30cc2f24aacf.mp3', 'approved', '2026-11-04 16:18:11', 1),
(46, 'Junoon', 101, 5, 'Hindhi', '2022', 'assets/media/album-art/img_images__40__e894d5adb1e7.jpg', 'assets/media/music/track_MITRAZ_-_Junoon_song_1a6b2f8e5cfa.mp3', 'approved', '2026-11-04 16:18:52', 1),
(47, 'Muskurahat', 101, 5, 'Hindhi', '2022', 'assets/media/album-art/img_images__39__e1c777e12d73.jpg', 'assets/media/music/track_MITRAZ_-_Muskurahat__Off_507fa473a912.mp3', 'approved', '2026-11-04 16:19:31', 1),
(48, 'Aadat', 8, 6, 'Urdu', '2004', 'assets/media/album-art/img_images__39__24a325f4ec2a.jpg', 'assets/media/music/track_Aadat_-_Atif_Aslam__Lyri_7f3731280825.mp3', 'approved', '2026-11-05 04:53:48', NULL),
(49, 'Tera Hona Laga Hon', 8, 5, 'English', '2009', 'assets/media/album-art/img_images__1__11bf073e68c5.jpg', 'assets/media/music/track_Tera_Hone_Laga_Hoon_-_At_9411a51781ef.mp3', 'approved', '2026-11-05 04:55:14', NULL),
(50, 'Dil Diyan Gallan', 8, 8, 'Urdu', '2017', 'assets/media/album-art/img_images__2__cc2ebef3d3b7.jpg', 'assets/media/music/track_Dil_Diyan_Gallan_Song_-__b9a7b54c9caf.mp3', 'approved', '2026-11-05 04:56:10', NULL),
(51, 'O Re Piya', 13, 7, 'Urdu', '2007', 'assets/media/album-art/img_images__41__d7227736d8f5.jpg', 'assets/media/music/track_O_Re_Piya_-_Full_Song_-__c8382d94e0ce.mp3', 'approved', '2026-11-05 05:19:16', NULL),
(52, 'Dhanni', 81, 6, 'Urdu', '2003', 'assets/media/album-art/img_images__5__5d492e08e258.jpg', 'assets/media/music/track_Dhaani_-_Strings_-_2003__dd4c9949f467.mp3', 'approved', '2026-11-05 05:20:32', NULL),
(53, 'Sajni', 81, 6, 'Urdu', '2003', 'assets/media/album-art/img_images__4__195901f0a3a3.jpg', 'assets/media/music/track_Sajni_-_Strings_-_Faisal_7cb29a3ca5fa.mp3', 'approved', '2026-11-05 05:21:14', NULL),
(54, 'Humsafar', 77, 5, 'Urdu', '2011', 'assets/media/album-art/img_images__6__1ad0e3d914eb.jpg', 'assets/media/music/track_Humsafar__Woh_Humsafar_T_8ccd5f7dab51.mp3', 'approved', '2026-11-05 05:52:46', NULL),
(55, 'Saaiyaan', 77, 6, 'Panjabi', '2016', 'assets/media/album-art/img_images__7__c9c43abb9098.jpg', 'assets/media/music/track_Saaiyaan__Official_Video_712cb0ce9453.mp3', 'approved', '2026-11-05 05:53:54', NULL),
(56, 'Roiyaan', 76, 5, 'Urdu', '2014', 'assets/media/album-art/img_images__42__954739d06df9.jpg', 'assets/media/music/track_Roiyaan_-_Farhan_Saeed___cb76041bc183.mp3', 'approved', '2026-11-05 05:54:54', NULL),
(57, 'Adhi Adhi Raat', 75, 5, 'Panjabi', '2012', 'assets/media/album-art/img_images__9__a403f3eae72c.jpg', 'assets/media/music/track_ADHI_ADHI_RAAT_-_BILAL_S_70d08b9c858b.mp3', 'approved', '2026-11-05 05:56:03', NULL),
(58, '12 Saal', 75, 5, 'Panjabi', '2011', 'assets/media/album-art/img_images__10__0c111d6e39a6.jpg', 'assets/media/music/track_12_SAAL_-_BILAL_SAEED_-__36d794772088.mp3', 'approved', '2026-11-05 05:56:44', NULL),
(59, 'Jhoom', 74, 6, 'Urdu', '2011', 'assets/media/album-art/img_images__11__4e189c584449.jpg', 'assets/media/music/track_Ali_Zafar_-_Jhoom_-_Offi_5b0798be4a46.mp3', 'approved', '2026-11-05 05:57:34', NULL),
(60, 'Channo', 74, 5, 'Urdu', '2003', 'assets/media/album-art/img_images__12__a161aa9800c8.jpg', 'assets/media/music/track_Ali_Zafar-_Channo__Offic_b4fcb4e21948.mp3', 'approved', '2026-11-05 05:58:30', NULL),
(61, 'Jhol', 46, 5, 'Panjabi', '2024', 'assets/media/album-art/img_images__13__13ad3219398e.jpg', 'assets/media/music/track_Jhol_-_Coke_Studio_Pakis_17c2c0607ff7.mp3', 'approved', '2026-11-05 05:59:16', NULL),
(62, 'Nazar', 44, 4, 'Urdu', '2022', 'assets/media/album-art/img_images__14__dfc0e623b61f.jpg', 'assets/media/music/track_Gravero___faizal_-_Nazre_04765fdb9179.mp3', 'approved', '2026-11-05 06:00:00', NULL),
(63, 'Introduction', 44, 4, 'Urdu', '2021', 'assets/media/album-art/img_images__15__e3f5d12960c0.jpg', 'assets/media/music/track_INTRODUCTION_-_FARIS_SHA_d012dde1e279.mp3', 'approved', '2026-11-05 06:00:47', NULL),
(64, 'Quarantine', 43, 4, 'Urdu', '2020', 'assets/media/album-art/img_images__16__bca15676b46a.jpg', 'assets/media/music/track_QUARANTINE_-_Young_Stunn_343c2a3044c0.mp3', 'approved', '2026-11-05 06:01:30', NULL),
(65, 'Gumaan', 43, 4, 'Urdu', '2020', 'assets/media/album-art/img_images__17__75b0239a8b0f.jpg', 'assets/media/music/track_GUMAAN_-_Young_Stunners__33df7f1c8f59.mp3', 'approved', '2026-11-05 06:02:09', NULL),
(66, 'Dhundhala', 15, 4, 'Hindhi', '2023', 'assets/media/album-art/img_images__18__28769f08aec9.jpg', 'assets/media/music/track_Yashraj__Talwiinder__Dro_6c8f1011691d.mp3', 'approved', '2026-11-05 06:03:16', NULL),
(67, 'Khayaal', 15, 5, 'Panjabi', '2023', 'assets/media/album-art/img_images__19__d9920f4d78ad.jpg', 'assets/media/music/track__Talwiinder_____Khayaal__909beeb43a18.mp3', 'approved', '2026-11-05 06:03:59', NULL),
(68, 'Wishes', 41, 8, 'Urdu', '2022', 'assets/media/album-art/img_images__20__5222c768f850.jpg', 'assets/media/music/track_Hasan_Raheem_-_Wishes_ft_8cfc1adb74ab.mp3', 'approved', '2026-11-05 06:05:18', NULL),
(71, 'Bol Do Na Zara', 25, 8, 'Hindhi', '2016', 'assets/media/album-art/img_images__27__5a12f4feeb22.jpg', 'assets/media/music/track_BOL_DO_NA_ZARA_Full_Vide_a404730380bd.mp3', 'approved', '2026-11-05 06:21:13', NULL),
(73, '295', 16, 4, 'Panjabi', '2021', 'assets/media/album-art/img_images__29__5b3fde95dc7a.jpg', 'assets/media/music/track_295__Official_Audio__-_S_aab1eb59702f.mp3', 'approved', '2026-11-05 06:25:38', NULL),
(74, 'Ghost', 56, 5, 'English', '2021', 'assets/media/album-art/img_images__30__ffbffd057da5.jpg', 'assets/media/music/track_Justin_Bieber_-_Ghost_bb28ca308443.mp3', 'approved', '2026-11-05 06:28:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_orders`
--

CREATE TABLE `store_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(24) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_email` varchar(254) NOT NULL,
  `customer_phone` varchar(40) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(120) NOT NULL,
  `region` varchar(120) DEFAULT NULL,
  `postal_code` varchar(32) DEFAULT NULL,
  `country` varchar(120) NOT NULL,
  `customer_note` text DEFAULT NULL,
  `payment_method` varchar(32) NOT NULL DEFAULT 'cash_on_delivery',
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_orders`
--

INSERT INTO `store_orders` (`id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `address_line1`, `address_line2`, `city`, `region`, `postal_code`, `country`, `customer_note`, `payment_method`, `status`, `subtotal`, `shipping_amount`, `total_amount`, `created_at`, `updated_at`) VALUES
(6, 'VEB-55E155158D', 'hamza', 'hamza@gmail.com', '03119734797', 'aptech north karachi', NULL, 'karachi', NULL, NULL, 'pakistan', 'xtz', 'cash_on_delivery', 'pending', 88.00, NULL, 88.00, '2026-10-31 12:14:32', '2026-10-31 12:14:32'),
(7, 'VEB-2E945496E1', 'Admad  Khan', 'admad@gmail.com', '0213200000', 'Lahore', '', 'lahore', '', '123', 'pakistan', '', 'cash_on_delivery', 'pending', 22.00, NULL, 22.00, '2026-11-04 14:22:02', '2026-11-04 14:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `store_order_items`
--

CREATE TABLE `store_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_key` varchar(80) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_size` varchar(16) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_order_items`
--

INSERT INTO `store_order_items` (`id`, `order_id`, `product_key`, `sku`, `product_name`, `product_size`, `unit_price`, `quantity`, `line_total`) VALUES
(6, 6, 'halogen-single', 'VEB-HLG-12-002', 'Halogen — 12\" Single', NULL, 22.00, 4, 88.00),
(7, 7, 'halogen-single', 'VEB-HLG-12-002', 'Halogen — 12\" Single', '', 22.00, 1, 22.00);

-- --------------------------------------------------------

--
-- Table structure for table `store_products`
--

CREATE TABLE `store_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_key` varchar(80) NOT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(200) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `category` varchar(120) NOT NULL,
  `category_key` varchar(40) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `reviews_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `shipping` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image1` varchar(255) NOT NULL,
  `image2` varchar(255) NOT NULL,
  `image3` varchar(255) NOT NULL,
  `badge` varchar(60) DEFAULT NULL,
  `badge_style` varchar(30) NOT NULL DEFAULT '',
  `sizes` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_products`
--

INSERT INTO `store_products` (`id`, `product_key`, `title`, `subtitle`, `sku`, `category`, `category_key`, `price`, `old_price`, `rating`, `reviews_count`, `shipping`, `description`, `image1`, `image2`, `image3`, `badge`, `badge_style`, `sizes`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'nightform-vinyl', 'Nightform — Double Vinyl', 'Vinyl · Limited to 1,000', 'VEB-NF-2LP-001', 'Vinyl · Albums', 'vinyl', 38.00, 46.00, 5, 42, 'Free over $75 · Ships in 3 days', 'The 2026 album on 180g double vinyl, cut at 45rpm from the original tape transfers. Foil-blocked gatefold sleeve, twelve-page insert of Catskills session photographs and a lossless download card tucked behind side D.', 'assets/images/product-1.jpg', 'assets/images/product-2.jpg', 'assets/images/product-3.jpg', 'Limited', 'pill--accent', '', 1, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01'),
(2, 'halogen-single', 'Halogen — 12\" Single', 'Vinyl · Limited Pressing', 'VEB-HLG-12-002', 'Vinyl · Singles', 'vinyl', 22.00, 28.00, 4, 18, 'Free over $75 · Ships in 3 days', 'Limited edition 12-inch single featuring the extended club mix of \"Halogen\" on side A, and an unreleased ambient instrumental rework on side B. Cut at 45rpm for supreme punch and clarity.', 'assets/images/product-2.jpg', 'assets/images/product-3.jpg', 'assets/images/Product-4.jpg', 'Sale', '', '', 2, 1, '2026-10-31 09:49:01', '2026-10-31 09:50:32'),
(3, 'tour-tee', 'Tour Tee 2026', 'Apparel · Heavyweight Organic Cotton', 'VEB-TEE-2026-BLK', 'Apparel · T-Shirts', 'apparel', 34.00, NULL, 5, 64, 'Ships worldwide in 2-4 business days', 'Custom heavyweight 240gsm combed organic cotton t-shirt. Features typographic Nightform chest embroidery and high-density screenprinted 2026 tour routing across the reverse.', 'assets/images/product-3.jpg', 'assets/images/Product-4.jpg', 'assets/images/Product-5.jpg', 'New', 'pill--accent', 'S,M,L,XL', 3, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01'),
(4, 'work-jacket', 'Rivington Work Jacket', 'Apparel · Limited Edition Cut', 'VEB-JKT-RIV-001', 'Apparel · Outerwear', 'apparel', 120.00, NULL, 4, 11, 'Free worldwide priority courier', 'Durable 12oz duck canvas jacket lined with warm quilted flannel. Custom bronze hardware, interior stash pocket for cassette tapes or audio interfaces, and tonal VEBRA collar embroidery.', 'assets/images/Product-4.jpg', 'assets/images/Product-5.jpg', 'assets/images/Product-6.jpg', '2 left', '', 'S,M,L,XL', 4, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01'),
(5, 'enamel-pins', 'Nightform Enamel Pin Set', 'Accessories · 3-Piece Set', 'VEB-PIN-SET-001', 'Accessories · Pins', 'accessories', 14.00, 18.00, 4, 27, 'Ships in padded recyclable mailer', 'Hard enamel pin set depicting the waveform sigil, analogue tape reel, and classic VEBRA wordmark with dual butterfly clasps on commemorative card backing.', 'assets/images/Product-5.jpg', 'assets/images/Product-6.jpg', 'assets/images/product-1.jpg', 'Sale', '', '', 5, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01'),
(6, 'screen-print', 'Screen Print — Hand Numbered', 'Artwork · Limited Edition of 300', 'VEB-PRT-SET-2026', 'Artwork · Prints', 'accessories', 45.00, NULL, 5, 9, 'Shipped in heavy duty cardboard tube', 'Hand-pulled 4-color silkscreen print on 300gsm Somerset archival cotton paper. Measures 18\" x 24\", signed and hand-numbered in pencil by the band.', 'assets/images/Product-6.jpg', 'assets/images/product-1.jpg', 'assets/images/product-2.jpg', 'Signed', 'pill--accent', '', 6, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01'),
(7, 'deluxe-box', 'Nightform Deluxe Box Set', 'Collector Edition · 500 Copies Made', 'VEB-BOX-DLX-001', 'Vinyl · Box Sets', 'vinyl', 89.00, 110.00, 5, 58, 'Free tracked express worldwide', 'The ultimate collector package: Double 180g colored vinyl, 48-page hardcover photo book from Catskills recording sessions, numbered art print, enamel pin, and download voucher.', 'assets/images/product-1.jpg', 'assets/images/product-2.jpg', 'assets/images/product-3.jpg', 'Bundle · Limited', 'pill--accent', '', 0, 1, '2026-10-31 09:49:01', '2026-10-31 09:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `address`, `phone`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'VEBRA Administrator', 'VEBRA Studios, Brooklyn, NY', '+1-555-0100', 'admin@vebra.com', '$2y$10$HH9c7ZJzfGxYRPHB4fHONO/97H.qYuPxBjq.9OgjcGDPihJdqBjPS', 'admin', '2026-10-26 19:14:45'),
(2, 'demouser', 'Demo Listener', '42 Music Avenue, New York, NY', '+1-555-0188', 'user@vebra.com', '$2y$10$cwS9OShEEN.70QX5nQVIzexrF0cpy8X3lgZ.BqkyzMgfjVtft6jfm', 'user', '2026-10-27 16:55:35'),
(5, 'Ali', 'ali khan', 'karachi', '03118967452', 'ali@gmail.com', '$2y$10$r0SiaFmbsDOCua4pwiPGR.dH6ilpKa3JQynDvn8r./tNJW0Adza1W', 'user', '2026-10-31 12:17:54'),
(8, 'Ahmad', 'Khan', 'Lahore', '02132******', 'admad@gmail.com', '$2y$10$MmVRPvFF1d0SQ3p9U6vRo.Q5viPTD0.D3TffTV5jOkcd9lj61nYSS', 'user', '2026-11-04 13:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `language` varchar(80) DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `artist_id`, `genre_id`, `language`, `release_year`, `thumbnail`, `video_path`, `video_url`, `status`, `created_at`) VALUES
(3, 'Hun Ishq Main', 8, 5, 'Urdu', '2026', 'assets/media/videos/img_images__46__b68f24c08f36.jpg', 'assets/videos/1.mp4', 'https://youtu.be/WRcIwPwlxbw', 'approved', '2026-10-29 10:45:43'),
(4, 'Aarzu', 12, 5, 'Urdu', '2026', 'assets/media/videos/img_images__45__bc316b4c3bda.jpg', 'assets/videos/1.mp4', 'https://youtu.be/M5OCLifZK1w', 'approved', '2026-10-29 10:47:20'),
(5, 'I Wait', 9, 8, 'English', '2026', 'assets/media/videos/img_images__47__b5ec6c1d14fd.jpg', 'assets/videos/1.mp4', 'https://youtu.be/jwSl1kaWIIs', 'approved', '2026-10-29 10:50:23'),
(6, 'AKHIYAN', 15, 5, 'Panjabi', '2026', 'assets/media/videos/img_images__48__b8fcee3b8e70.jpg', 'assets/videos/1.mp4', 'https://youtu.be/Rjt-xAAe2KM', 'approved', '2026-10-29 10:52:32'),
(7, 'Lut Gaye', 60, 5, 'Hindhi', '2021', 'assets/images/video-cover-1.jpg', 'assets/videos/1.mp4', 'https://youtu.be/858YA9sSWqg', 'approved', '2026-10-29 15:56:19'),
(8, 'SOFTLY', 49, 5, 'Panjabi', '2023', 'assets/media/videos/img_images__80__ad504d94b609.jpg', 'assets/videos/1.mp4', 'https://youtu.be/cWMxCE2HTag', 'approved', '2026-10-29 15:58:26'),
(9, 'Kahani Suno 2.0', 42, 5, 'Urdu', '2022', 'assets/media/videos/img_h0R5Gvwdc8-rbaz-VkS6JgkZ_c239226f1a71.jpg', 'assets/videos/1.mp4', 'https://youtu.be/_XBVWlI8TsQ', 'approved', '2026-10-29 16:04:25'),
(10, 'Dance The Night', 38, 5, 'English', '2023', 'assets/media/videos/img_ZJe08tE6qMpUdQDLlmfzL_vJ_8b05a11c9d54.jpg', 'assets/videos/1.mp4', 'https://youtu.be/suAR1PYFNYA', 'approved', '2026-10-29 16:06:14'),
(11, 'BIRDS OF A FEATHER', 37, 5, 'English', '2024', 'assets/media/videos/img_snx3VOUv4ggRrLZLnKGtedzH_d30ff9c0a622.jpg', 'assets/videos/1.mp4', 'https://youtu.be/V9PVRfjEBTI', 'approved', '2026-10-29 16:07:21'),
(12, 'Mansoob', 42, 5, 'Urdu', '2023', 'assets/media/videos/img_images__82__5158486e6a42.jpg', 'assets/videos/1.mp4', 'https://youtu.be/KA4APfVz5I8', 'approved', '2026-10-29 16:09:07'),
(13, 'Love Again', 38, 5, 'English', '2021', 'assets/media/videos/img_images__83__adcff26aed3a.jpg', 'assets/videos/1.mp4', 'https://youtu.be/BC19kwABFwc', 'approved', '2026-10-29 16:10:11'),
(14, 'GHOSTFACE KILLAH', 16, 4, 'Panjabi', '2026', 'assets/media/videos/img_P-bkDNQbruB1Z0WhWzDDmz8E_15fdc3a262f5.jpg', 'assets/videos/1.mp4', 'https://youtu.be/Pq7UxbWeRU0', 'approved', '2026-10-29 16:14:19'),
(15, 'HUMBLE', 27, 4, 'English', '2026', 'assets/media/videos/img_mPipJ4sLId8j-kYpXEIg98Pk_8a73ac3793d1.jpg', 'assets/videos/1.mp4', 'https://youtu.be/tvTRZJ-4EyI', 'approved', '2026-10-29 16:15:55'),
(16, 'Blinding Lights', 51, 4, 'English', '2020', 'assets/media/videos/img_images__84__1aac13c5f0be.jpg', 'assets/videos/1.mp4', 'https://youtu.be/4NRXx6U8ABQ', 'approved', '2026-10-29 16:17:30'),
(17, 'bad guy', 37, 5, 'English', '2019', 'assets/media/videos/img_images__85__743e326cc5bc.jpg', 'assets/videos/1.mp4', 'https://youtu.be/DyDfgMOUjCI', 'approved', '2026-10-29 16:19:09'),
(18, 'Lose Yourself', 21, 4, 'English', '2002', 'assets/media/videos/img_UhaEiC4BRmz_sm_1MVR-17Gg_eb81bce8c248.jpg', 'assets/videos/1.mp4', 'https://youtu.be/xFYQQPAOz7Y', 'approved', '2026-10-29 16:20:52'),
(19, 'Zaroori Tha', 13, 8, 'Urdu', '2014', 'assets/media/videos/img_images__87__8db8b6a79c70.jpg', 'assets/videos/1.mp4', 'https://youtu.be/YiP8l60YjKc', 'approved', '2026-10-29 16:22:29'),
(20, 'Channa Mereya', 17, 8, 'Hindhi', '2016', 'assets/media/videos/img_images__88__68f0aad5eb5c.jpg', 'assets/videos/1.mp4', 'https://youtu.be/284Ov7ysmfA', 'approved', '2026-10-29 16:24:20'),
(27, 'Akhiyaan', 101, 5, 'Hindhi', '2023', 'assets/images/video-cover-1.jpg', '', 'https://youtu.be/xEALTVLxrDw', 'approved', '2026-11-04 16:25:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artist_applications`
--
ALTER TABLE `artist_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_artist_id` (`artist_id`),
  ADD KEY `idx_application_status` (`status`),
  ADD KEY `idx_application_created` (`created_at`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_size` (`user_id`,`product_key`,`size`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_song` (`user_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `store_orders`
--
ALTER TABLE `store_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_store_orders_order_number` (`order_number`),
  ADD KEY `idx_store_orders_status_created` (`status`,`created_at`);

--
-- Indexes for table `store_order_items`
--
ALTER TABLE `store_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_store_order_items_order` (`order_id`);

--
-- Indexes for table `store_products`
--
ALTER TABLE `store_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_store_products_product_key` (`product_key`),
  ADD UNIQUE KEY `uq_store_products_sku` (`sku`),
  ADD KEY `idx_store_products_active_sort` (`is_active`,`sort_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `artist_applications`
--
ALTER TABLE `artist_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `store_orders`
--
ALTER TABLE `store_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `store_order_items`
--
ALTER TABLE `store_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `store_products`
--
ALTER TABLE `store_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `songs_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `store_order_items`
--
ALTER TABLE `store_order_items`
  ADD CONSTRAINT `fk_store_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `store_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
