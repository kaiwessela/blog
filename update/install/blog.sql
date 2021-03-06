-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 16. Mrz 2021 um 18:07
-- Server-Version: 10.5.9-MariaDB-1
-- PHP-Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `blog_new`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `columns`
--

CREATE TABLE `columns` (
  `column_id` varchar(8) NOT NULL,
  `column_longid` varchar(60) NOT NULL,
  `column_name` varchar(30) NOT NULL,
  `column_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE `events` (
  `event_id` varchar(8) NOT NULL,
  `event_longid` varchar(60) NOT NULL,
  `event_title` varchar(50) NOT NULL,
  `event_organisation` varchar(40) NOT NULL,
  `event_timestamp` datetime NOT NULL,
  `event_location` varchar(60) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `event_cancelled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groups`
--

CREATE TABLE `groups` (
  `group_id` varchar(8) NOT NULL,
  `group_longid` varchar(60) NOT NULL,
  `group_name` varchar(30) NOT NULL,
  `group_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `media`
--

CREATE TABLE `media` (
  `medium_id` varchar(8) NOT NULL,
  `medium_longid` varchar(60) NOT NULL,
  `medium_class` set('application','audio','image','video') NOT NULL,
  `medium_type` varchar(80) NOT NULL,
  `medium_extension` varchar(10) NOT NULL,
  `medium_title` varchar(60) DEFAULT NULL,
  `medium_description` varchar(250) DEFAULT NULL,
  `medium_copyright` varchar(250) DEFAULT NULL,
  `medium_alternative` varchar(250) DEFAULT NULL,
  `medium_variants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medium_variants`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mediafiles`
--

CREATE TABLE `mediafiles` (
  `mediafile_medium_id` varchar(8) NOT NULL,
  `mediafile_variant` varchar(60) DEFAULT NULL,
  `mediafile_type` varchar(80) NOT NULL,
  `mediafile_extension` varchar(10) NOT NULL,
  `mediafile_data` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `motions`
--

CREATE TABLE `motions` (
  `motion_id` varchar(8) NOT NULL,
  `motion_longid` varchar(60) NOT NULL,
  `motion_title` varchar(80) NOT NULL,
  `motion_description` text DEFAULT NULL,
  `motion_timestamp` datetime NOT NULL,
  `motion_status` varchar(20) NOT NULL,
  `motion_votes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `motion_document_id` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE `pages` (
  `page_id` varchar(8) NOT NULL,
  `page_longid` varchar(60) NOT NULL,
  `page_title` varchar(60) NOT NULL,
  `page_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `persongrouprelations`
--

CREATE TABLE `persongrouprelations` (
  `persongrouprelation_id` varchar(8) NOT NULL,
  `persongrouprelation_person_id` varchar(8) NOT NULL,
  `persongrouprelation_group_id` varchar(8) NOT NULL,
  `persongrouprelation_number` int(11) DEFAULT NULL,
  `persongrouprelation_role` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `persons`
--

CREATE TABLE `persons` (
  `person_id` varchar(8) NOT NULL,
  `person_longid` varchar(60) NOT NULL,
  `person_name` varchar(50) NOT NULL,
  `person_image_id` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `postcolumnrelations`
--

CREATE TABLE `postcolumnrelations` (
  `postcolumnrelation_id` varchar(8) NOT NULL,
  `postcolumnrelation_post_id` varchar(8) NOT NULL,
  `postcolumnrelation_column_id` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts`
--

CREATE TABLE `posts` (
  `post_id` varchar(8) NOT NULL,
  `post_longid` varchar(60) NOT NULL,
  `post_overline` varchar(25) DEFAULT NULL,
  `post_headline` varchar(60) NOT NULL,
  `post_subline` varchar(40) DEFAULT NULL,
  `post_teaser` text DEFAULT NULL,
  `post_author` varchar(50) NOT NULL,
  `post_timestamp` datetime NOT NULL,
  `post_image_id` varchar(8) DEFAULT NULL,
  `post_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `columns`
--
ALTER TABLE `columns`
  ADD PRIMARY KEY (`column_id`),
  ADD UNIQUE KEY `column_longid` (`column_longid`);

--
-- Indizes für die Tabelle `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD UNIQUE KEY `event_longid` (`event_longid`);

--
-- Indizes für die Tabelle `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_longid` (`group_longid`);

--
-- Indizes für die Tabelle `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`medium_id`),
  ADD UNIQUE KEY `medium_longid` (`medium_longid`);

--
-- Indizes für die Tabelle `mediafiles`
--
ALTER TABLE `mediafiles`
  ADD UNIQUE KEY `mediafile_variant` (`mediafile_variant`,`mediafile_medium_id`);

--
-- Indizes für die Tabelle `motions`
--
ALTER TABLE `motions`
  ADD PRIMARY KEY (`motion_id`),
  ADD UNIQUE KEY `proposal_longid` (`motion_longid`),
  ADD KEY `motion_document_id` (`motion_document_id`);

--
-- Indizes für die Tabelle `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `page_longid` (`page_longid`);

--
-- Indizes für die Tabelle `persongrouprelations`
--
ALTER TABLE `persongrouprelations`
  ADD PRIMARY KEY (`persongrouprelation_id`),
  ADD KEY `persongrouprelation_person_id` (`persongrouprelation_person_id`),
  ADD KEY `persongrouprelation_group_id` (`persongrouprelation_group_id`);

--
-- Indizes für die Tabelle `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `person_longid` (`person_longid`),
  ADD KEY `person_image_id` (`person_image_id`);

--
-- Indizes für die Tabelle `postcolumnrelations`
--
ALTER TABLE `postcolumnrelations`
  ADD PRIMARY KEY (`postcolumnrelation_id`),
  ADD KEY `postcolumnrelation_post_id` (`postcolumnrelation_post_id`),
  ADD KEY `postcolumnrelation_column_id` (`postcolumnrelation_column_id`);

--
-- Indizes für die Tabelle `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD UNIQUE KEY `post_longid` (`post_longid`),
  ADD KEY `post_image_id` (`post_image_id`);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `mediafiles`
--
ALTER TABLE `mediafiles`
  ADD CONSTRAINT `mediafiles_ibfk_1` FOREIGN KEY (`mediafile_medium_id`) REFERENCES `media` (`medium_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `motions`
--
ALTER TABLE `motions`
  ADD CONSTRAINT `motion_document` FOREIGN KEY (`motion_document_id`) REFERENCES `media` (`medium_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `persongrouprelations`
--
ALTER TABLE `persongrouprelations`
  ADD CONSTRAINT `persongrouprelations_ibfk_1` FOREIGN KEY (`persongrouprelation_person_id`) REFERENCES `persons` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `persongrouprelations_ibfk_2` FOREIGN KEY (`persongrouprelation_group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `persons`
--
ALTER TABLE `persons`
  ADD CONSTRAINT `persons_ibfk_1` FOREIGN KEY (`person_image_id`) REFERENCES `media` (`medium_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `postcolumnrelations`
--
ALTER TABLE `postcolumnrelations`
  ADD CONSTRAINT `postcolumnrelations_ibfk_1` FOREIGN KEY (`postcolumnrelation_post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postcolumnrelations_ibfk_2` FOREIGN KEY (`postcolumnrelation_column_id`) REFERENCES `columns` (`column_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`post_image_id`) REFERENCES `media` (`medium_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
