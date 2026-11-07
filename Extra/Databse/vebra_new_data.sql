-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2026 at 03:35 PM
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
-- Database: `vebra_new_data`
--

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
(3, 'Elias Ward', 'Guitars. Angular chords and wall of sound tone shaping. Elias runs a vintage pedal chain into custom stereo tube amps.', 'assets/images/team-2.jpg', '2026-10-27 16:55:35'),
(5, 'Junie Okafor', 'Drums & Percussion. Dynamic polyrhythms and visceral live energy. Junie drives the tempo across all live tours.', 'assets/images/team-4.jpg', '2026-10-27 16:55:35'),
(7, 'Ada Reyes', 'Live Strings. Cinematic violin and viola arrangements on tour, blending classical counterpoint with electronic delay.', 'assets/images/team-6.jpg', '2026-10-27 16:55:35'),
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
(71, 'VEBRA Demo Artist', 'Demo artist for the existing VEBRA media library.', '', '2026-10-31 09:55:14'),
(72, 'ali', 'xyz', 'assets/media/artists/img_qHivQU_-_Copy__2__cfd456c49e72.png', '2026-10-31 12:35:55');

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
(1, 'Ali', 'ali@gmail.com', 'Add 5 Atif Songs', 'Add 5 Atif Aslam Popular Songs', '2026-10-31 19:10:19');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist_id`, `genre_id`, `language`, `release_year`, `artwork`, `audio_path`, `status`, `created_at`) VALUES
(1, 'Nightform', NULL, 1, 'English', '2026', 'assets/images/album-1.jpg', 'assets/musics/1.mp3', 'approved', '2026-10-25 16:56:51'),
(2, 'Halogen Kids', NULL, 1, 'English', '2026', 'assets/images/album-2.jpg', 'assets/musics/2.mp3', 'approved', '2026-10-25 16:56:51'),
(3, 'Amber Room', NULL, 1, 'English', '2026', 'assets/images/album-3.jpg', 'assets/musics/3.mp3', 'approved', '2026-10-25 16:56:51'),
(4, 'Static Bloom', NULL, 1, 'English', '2026', 'assets/images/album-4.jpg', 'assets/musics/4.mp3', 'approved', '2026-10-25 16:56:52'),
(5, 'Sirens, Slowly', NULL, 1, 'English', '2026', 'assets/images/album-5.jpg', 'assets/musics/5.mp3', 'approved', '2026-10-25 16:56:52'),
(6, 'Afreen Afreen', 13, 8, 'Urdu', '2026', 'assets/media/album-art/img_1900x1900-000000-80-0-0_35240fa6dbb4.jpg', 'assets/media/music/track_Coke_Studio_Season_9-_Af_f1ec08ba6830.mp3', 'approved', '2026-10-29 08:40:57'),
(7, 'Mockingbird', 21, 4, 'English', '2004', 'assets/media/album-art/img_images__22__94baade6963d.jpg', 'assets/media/music/track_Eminem_-_Mockingbird__Ly_a09b19304c86.mp3', 'approved', '2026-10-29 08:48:24'),
(9, 'Perfect', 20, 5, 'English', '2017', 'assets/media/album-art/img_images__23__9c7e7bcae815.jpg', 'assets/media/music/track_Ed_Sheeran_-_Perfect__Ly_819cc23180c6.mp3', 'approved', '2026-10-29 08:56:16'),
(10, 'Him & I', 29, 4, 'English', '2017', 'assets/media/album-art/img_images__24__9e12b54f9e90.jpg', 'assets/media/music/track_G-Eazy___Halsey_-_Him____dd78be59dab6.mp3', 'approved', '2026-10-29 08:57:02'),
(11, 'Ae Dil Hai Mushkil', 17, 5, 'Hindhi', '2016', 'assets/media/album-art/img_images__32__e0445751f2b4.jpg', 'assets/media/music/track_Ae_Dil_Hai_Mushkil__Titl_a59ecabb76cc.mp3', 'approved', '2026-10-29 09:21:38'),
(12, 'Pasoori', 9, 5, 'Panjabi', '2022', 'assets/media/album-art/img_images__33__3347bad3342f.jpg', 'assets/media/music/track_Coke_Studio_-_Season_14__b842c36609b4.mp3', 'approved', '2026-10-29 09:23:24'),
(13, 'Hawayein', 17, 5, 'Hindi', '2017', 'assets/media/album-art/img_images__34__2e26317b4a1d.jpg', 'assets/media/music/track_Hawayein_-_Lyrical_Video_b0a3e21bbdf2.mp3', 'approved', '2026-10-29 09:25:12'),
(14, 'Mehram', 30, 5, 'Urdu', '2022', 'assets/media/album-art/img_images__35__bfd52e1ab026.jpg', 'assets/media/music/track_Coke_Studio_-_Season_14__139e81f40887.mp3', 'approved', '2026-10-29 09:26:35'),
(15, 'Tajdar-e-Haram', 8, 8, 'Urdu', '2015', 'assets/media/album-art/img_images__41__0d565cd6677d.jpg', 'assets/media/music/track_Coke_Studio_Season_8-_Ta_80c3f02ee1f9.mp3', 'approved', '2026-10-29 09:44:19'),
(16, 'Tera Woh Pyar', 11, 5, 'Urdu', '2016', 'assets/media/album-art/img_images__42__ad591179bf1e.jpg', 'assets/media/music/track_Coke_Studio_Season_9-_Te_64277192bd4d.mp3', 'approved', '2026-10-29 09:46:04'),
(17, 'Iraaday', 14, 5, 'Urdu', '2022', 'assets/media/album-art/img_images__43__4b1c0c3b2e94.jpg', 'assets/media/music/track_Abdul_Hannan___Rovalio_-_fcc83f841e8b.mp3', 'approved', '2026-10-29 09:47:39'),
(18, 'VEBRA Track 1', 71, 1, 'English', '2026', 'assets/images/album-1.jpg', 'assets/musics/1.mp3', 'approved', '2026-10-31 09:55:14'),
(19, 'VEBRA Track 2', 71, 1, 'English', '2026', 'assets/images/album-2.jpg', 'assets/musics/2.mp3', 'approved', '2026-10-31 09:55:14'),
(20, 'VEBRA Track 3', 71, 1, 'English', '2026', 'assets/images/album-3.jpg', 'assets/musics/3.mp3', 'approved', '2026-10-31 09:55:14'),
(21, 'VEBRA Track 4', 71, 1, 'English', '2026', 'assets/images/album-4.jpg', 'assets/musics/4.mp3', 'approved', '2026-10-31 09:55:14'),
(22, 'VEBRA Track 5', 71, 1, 'English', '2026', 'assets/images/album-5.jpg', 'assets/musics/5.mp3', 'approved', '2026-10-31 09:55:14');

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
(6, 'VEB-55E155158D', 'hamza', 'hamza@gmail.com', '03119734797', 'aptech north karachi', NULL, 'karachi', NULL, NULL, 'pakistan', 'xtz', 'cash_on_delivery', 'pending', 88.00, NULL, 88.00, '2026-10-31 12:14:32', '2026-10-31 12:14:32');

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
(6, 6, 'halogen-single', 'VEB-HLG-12-002', 'Halogen — 12\" Single', NULL, 22.00, 4, 88.00);

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
(5, 'Ali', 'ali khan', 'karachi', '03118967452', 'ali@gmail.com', '$2y$10$r0SiaFmbsDOCua4pwiPGR.dH6ilpKa3JQynDvn8r./tNJW0Adza1W', 'user', '2026-10-31 12:17:54');

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
(1, 'Nightform — Official Video', NULL, 1, 'English', '2026', 'assets/images/video-cover-1.jpg', 'assets/videos/1.mp4', '', 'approved', '2026-10-25 16:56:52'),
(3, 'Hun Ishq Main', 8, 5, 'Urdu', '2026', 'assets/media/videos/img_images__46__b68f24c08f36.jpg', 'assets/videos/1.mp4', 'https://youtu.be/WRcIwPwlxbw', 'approved', '2026-10-29 10:45:43'),
(4, 'Aarzu', 12, 5, 'Urdu', '2026', 'assets/media/videos/img_images__45__bc316b4c3bda.jpg', 'assets/videos/1.mp4', 'https://youtu.be/M5OCLifZK1w', 'approved', '2026-10-29 10:47:20'),
(5, 'I Wait', 9, 8, 'English', '2026', 'assets/media/videos/img_images__47__b5ec6c1d14fd.jpg', 'assets/videos/1.mp4', 'https://youtu.be/jwSl1kaWIIs', 'approved', '2026-10-29 10:50:23'),
(6, 'AKHIYAN', 15, 5, 'Panjabi', '2026', 'assets/media/videos/img_images__48__b8fcee3b8e70.jpg', 'assets/videos/1.mp4', 'https://youtu.be/Rjt-xAAe2KM', 'approved', '2026-10-29 10:52:32'),
(7, 'Lut Gaye', 60, 5, 'Hindhi', '2021', 'assets/images/video-cover-1.jpg', 'assets/videos/1.mp4', 'https://youtu.be/858YA9sSWqg', 'approved', '2026-10-29 15:56:19'),
(8, 'SOFTLY', 49, 5, 'Panjabi', '2023', 'assets/media/videos/img_images__80__ad504d94b609.jpg', 'assets/videos/1.mp4', 'https://youtu.be/cWMxCE2HTag', 'approved', '2026-10-29 15:58:26'),
(9, 'Kahani Suno 2.0', 42, 5, 'Urdu', '2022', 'assets/media/videos/img_h0R5Gvwdc8-rbaz-VkS6JgkZ_c239226f1a71.jpg', 'assets/videos/1.mp4', 'https://youtu.be/_XBVWlI8TsQ', 'approved', '2026-10-29 16:04:25'),
(10, 'https://youtu.be/suAR1PYFNYA', 38, 5, 'English', '2023', 'assets/media/videos/img_ZJe08tE6qMpUdQDLlmfzL_vJ_8b05a11c9d54.jpg', 'assets/videos/1.mp4', 'https://youtu.be/suAR1PYFNYA', 'approved', '2026-10-29 16:06:14'),
(11, 'BIRDS OF A FEATHER', 37, 5, 'English', '2024', 'assets/media/videos/img_snx3VOUv4ggRrLZLnKGtedzH_d30ff9c0a622.jpg', 'assets/videos/1.mp4', 'https://youtu.be/V9PVRfjEBTI', 'approved', '2026-10-29 16:07:21'),
(12, 'Mansoob', 42, 5, 'Urdu', '2023', 'assets/media/videos/img_images__82__5158486e6a42.jpg', 'assets/videos/1.mp4', 'https://youtu.be/KA4APfVz5I8', 'approved', '2026-10-29 16:09:07'),
(13, 'Love Again', 38, NULL, 'English', '2021', 'assets/media/videos/img_images__83__adcff26aed3a.jpg', 'assets/videos/1.mp4', 'https://youtu.be/BC19kwABFwc', 'approved', '2026-10-29 16:10:11'),
(14, 'GHOSTFACE KILLAH', 16, 4, 'Panjabi', '2026', 'assets/media/videos/img_P-bkDNQbruB1Z0WhWzDDmz8E_15fdc3a262f5.jpg', 'assets/videos/1.mp4', 'https://youtu.be/Pq7UxbWeRU0', 'approved', '2026-10-29 16:14:19'),
(15, 'HUMBLE', 27, 4, 'English', '2026', 'assets/media/videos/img_mPipJ4sLId8j-kYpXEIg98Pk_8a73ac3793d1.jpg', 'assets/videos/1.mp4', 'https://youtu.be/tvTRZJ-4EyI', 'approved', '2026-10-29 16:15:55'),
(16, 'Blinding Lights', 51, 4, 'English', '2020', 'assets/media/videos/img_images__84__1aac13c5f0be.jpg', 'assets/videos/1.mp4', 'https://youtu.be/4NRXx6U8ABQ', 'approved', '2026-10-29 16:17:30'),
(17, 'bad guy', 37, 5, 'English', '2019', 'assets/media/videos/img_images__85__743e326cc5bc.jpg', 'assets/videos/1.mp4', 'https://youtu.be/DyDfgMOUjCI', 'approved', '2026-10-29 16:19:09'),
(18, 'Lose Yourself', 21, 4, 'English', '2002', 'assets/media/videos/img_UhaEiC4BRmz_sm_1MVR-17Gg_eb81bce8c248.jpg', 'assets/videos/1.mp4', 'https://youtu.be/xFYQQPAOz7Y', 'approved', '2026-10-29 16:20:52'),
(19, 'Zaroori Tha', 13, 8, 'Urdu', '2014', 'assets/media/videos/img_images__87__8db8b6a79c70.jpg', 'assets/videos/1.mp4', 'https://youtu.be/YiP8l60YjKc', 'approved', '2026-10-29 16:22:29'),
(20, 'Channa Mereya', 17, 8, 'Hindhi', '2016', 'assets/media/videos/img_images__88__68f0aad5eb5c.jpg', 'assets/videos/1.mp4', 'https://youtu.be/284Ov7ysmfA', 'approved', '2026-10-29 16:24:20'),
(21, 'VEBRA Video 1', 71, 1, 'English', '2026', 'assets/images/video-cover-1.jpg', 'assets/videos/1.mp4', '', 'approved', '2026-10-31 09:55:14'),
(22, 'me at the zoo', 72, 4, 'urdu', '2026', 'assets/media/videos/img_qHivQU_-_Copy__2__597db4412d7b.png', 'assets/videos/1.mp4', 'https://youtu.be/jNQXAC9IVRw?si=pIGLJKCeXrYpezZk', 'approved', '2026-10-31 12:40:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `store_orders`
--
ALTER TABLE `store_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `store_order_items`
--
ALTER TABLE `store_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `store_products`
--
ALTER TABLE `store_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

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
