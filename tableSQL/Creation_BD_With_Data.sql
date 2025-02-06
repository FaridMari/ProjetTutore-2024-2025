-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 05 fév. 2025 à 09:09
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projettutore`
--

-- --------------------------------------------------------

--
-- Structure de la table `affectations`
--

CREATE TABLE `affectations` (
  `id_affectation` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `heures_affectees` double NOT NULL,
  `type_heure` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contraintes`
--

CREATE TABLE `contraintes` (
  `id_contrainte` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `jour` varchar(255) NOT NULL,
  `heure_debut` int(11) NOT NULL,
  `heure_fin` int(11) NOT NULL,
  `creneau_preference` varchar(20) DEFAULT NULL,
  `cours_samedi` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `contraintes`
--

INSERT INTO `contraintes` (`id_contrainte`, `id_utilisateur`, `jour`, `heure_debut`, `heure_fin`, `creneau_preference`, `cours_samedi`) VALUES
(61, 19, 'lundi', 8, 10, '8h-10h', 'oui'),
(62, 19, 'mardi', 10, 12, '8h-10h', 'oui'),
(64, 20, 'lundi', 8, 10, '8h-10h', 'oui'),
(65, 20, 'mardi', 10, 12, '8h-10h', 'oui');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id_cours` int(11) NOT NULL,
  `formation` varchar(255) NOT NULL,
  `semestre` varchar(255) NOT NULL,
  `nom_cours` varchar(255) NOT NULL,
  `code_cours` varchar(255) NOT NULL,
  `nb_heures_total` double DEFAULT 0,
  `nb_heures_cm` double DEFAULT 0,
  `nb_heures_td` double DEFAULT 0,
  `nb_heures_tp` double DEFAULT 0,
  `nb_heures_ei` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id_cours`, `formation`, `semestre`, `nom_cours`, `code_cours`, `nb_heures_total`, `nb_heures_cm`, `nb_heures_td`, `nb_heures_tp`, `nb_heures_ei`) VALUES
(183, 'Autre', '1', 'Autre (préciser dans les remarques)', '', 0, 0, 0, 0, 0),
(184, 'Autre', '1', 'Forfait suivi de stage', '', 0, 0, 0, 0, 0),
(185, 'Autre', '5', 'Portfolio', 'P5-RA-DWM-01', 0, 0, 0, 0, 0),
(186, 'BUT S1', '1', 'Introduction à l\'algorithmique', 'R1-01A', 32, 0, 32, 0, 0),
(187, 'BUT S1', '1', 'Bases de la programmation', 'R1-01B', 40, 0, 24, 16, 0),
(188, 'BUT S1', '1', 'Structure de données et programmation', 'R1-01C', 32, 0, 24, 8, 0),
(189, 'BUT S1', '1', 'Développement d\'interfaces web', 'R1-02', 24, 0, 24, 0, 0),
(190, 'BUT S1', '1', 'Introduction à l\'architecture des ordinateurs', 'R1-03', 24, 0, 16, 8, 0),
(191, 'BUT S1', '1', 'Introduction aux SE et à leur fonctionnement', 'R1-04', 24, 0, 8, 16, 0),
(192, 'BUT S1', '1', 'Introduction aux BD et SQL', 'R1-05', 48, 0, 48, 0, 0),
(193, 'BUT S1', '1', 'Maths discrètes', 'R1-06', 40, 16, 24, 0, 0),
(194, 'BUT S1', '1', 'Outils mathématiques fondamentaux', 'R1-07', 24, 8, 16, 0, 0),
(195, 'BUT S1', '1', 'Introduction à la gestion des organisations', 'R1-08', 36, 0, 36, 0, 0),
(196, 'BUT S1', '1', 'Introduction à l\'économie durable et numérique', 'R1-09', 24, 0, 24, 0, 0),
(197, 'BUT S1', '1', 'Anglais', 'R1-10', 32, 0, 32, 0, 0),
(198, 'BUT S1', '1', 'Bases de la communication', 'R1-11', 28, 0, 28, 0, 0),
(199, 'BUT S1', '1', 'Projet professionnel et personnel', 'R1-12', 16, 0, 13, 3, 0),
(200, 'BUT S3', '3', 'Développement web', 'R3-01', 40, 0, 40, 0, 0),
(201, 'BUT S3', '3', 'Développement efficace', 'R3-02', 28, 0, 28, 0, 0),
(202, 'BUT S3', '3', 'Analyse', 'R3-03', 28, 0, 28, 0, 0),
(203, 'BUT S3', '3', 'Qualité de développement', 'R3-04', 46, 0, 46, 0, 0),
(204, 'BUT S3', '3', 'Programmation système', 'R3-05', 36, 0, 36, 0, 0),
(205, 'BUT S3', '3', 'Architecture des réseaux', 'R3-06', 36, 0, 36, 0, 0),
(206, 'BUT S3', '3', 'SQL dans un langage de programmation', 'R3-07', 32, 0, 32, 0, 0),
(207, 'BUT S3', '3', 'Probabilités', 'R3-08', 24, 12, 12, 0, 0),
(208, 'BUT S3', '3', 'Cryptographie et sécurité', 'R3-09', 28, 0, 12, 16, 0),
(209, 'BUT S3', '3', 'Management des SI', 'R3-10', 38, 0, 38, 0, 0),
(210, 'BUT S3', '3', 'Droit des contrats et du numérique', 'R3-11', 24, 0, 24, 0, 0),
(211, 'BUT S3', '3', 'Anglais', 'R3-12', 28, 0, 28, 0, 0),
(212, 'BUT S3', '3', 'Communication professionnelle', 'R3-13', 24, 0, 24, 0, 0),
(213, 'BUT S3', '3', 'Projet Personnel et Professionnel', 'R3-14', 12, 0, 12, 0, 0),
(214, 'BUT S5 DACS', '5', 'Initiation au management d\'une équipe', 'R5-DACS-01', 0, 0, 0, 0, 0),
(215, 'BUT S5 DACS', '5', 'PPP', 'R5-DACS-02', 0, 0, 0, 0, 0),
(216, 'BUT S5 DACS', '5', 'Politiques de communication', 'R5-DACS-03', 0, 0, 0, 0, 0),
(217, 'BUT S5 DACS', '5', 'Programmation avancée en système', 'R5-DACS-04', 0, 0, 0, 0, 0),
(218, 'BUT S5 DACS', '5', 'Automatisation de la chaîne de production', 'R5-DACS-05', 0, 0, 0, 0, 0),
(219, 'BUT S5 DACS', '5', 'Installation et config. de services complexes', 'R5-DACS-06', 0, 0, 0, 0, 0),
(220, 'BUT S5 DACS', '5', 'Virtualisation avancée', 'R5-DACS-07', 0, 0, 0, 0, 0),
(221, 'BUT S5 DACS', '5', 'Continuité de service', 'R5-DACS-08', 0, 0, 0, 0, 0),
(222, 'BUT S5 DACS', '5', 'Cybersécurité', 'R5-DACS-09', 0, 0, 0, 0, 0),
(223, 'BUT S5 DACS', '5', 'Modélisation mathématiques', 'R5-DACS-10', 0, 0, 0, 0, 0),
(224, 'BUT S5 DACS', '5', 'Économie durable et numérique', 'R5-DACS-11', 0, 0, 0, 0, 0),
(225, 'BUT S5 DACS', '5', 'Anglais', 'R5-DACS-12', 0, 0, 0, 0, 0),
(226, 'BUT S5 RA-DWM', '5', 'Initiation au management d\'une équipe', 'R5-RA-DWM-01', 37, 0, 37, 0, 0),
(227, 'BUT S5 RA-DWM', '5', 'PPP', 'R5-RA-DWM-02', 24, 0, 24, 0, 0),
(228, 'BUT S5 RA-DWM', '5', 'Politiques de communication', 'R5-RA-DWM-03', 24, 0, 24, 0, 0),
(229, 'BUT S5 RA-DWM', '5', 'Qualité algorithmique', 'R5-RA-DWM-04', 60, 0, 60, 0, 0),
(230, 'BUT S5 RA-DWM', '5', 'Programmation avancée', 'R5-RA-DWM-05', 46, 0, 46, 0, 0),
(231, 'BUT S5 RA-DWM', '5', 'Programmation multimédia', 'R5-RA-DWM-06', 48, 0, 48, 0, 0),
(232, 'BUT S5 RA-DWM', '5', 'Automatisation de la production', 'R5-RA-DWM-07', 28, 0, 28, 0, 0),
(233, 'BUT S5 RA-DWM', '5', 'Qualité de développement', 'R5-RA-DWM-08', 60, 0, 60, 0, 0),
(234, 'BUT S5 RA-DWM', '5', 'Virtualisation avancée', 'R5-RA-DWM-09', 34, 0, 34, 0, 0),
(235, 'BUT S5 RA-DWM', '5', 'Nouveaux paradigmes BD', 'R5-RA-DWM-10', 28, 0, 28, 0, 0),
(236, 'BUT S5 RA-DWM', '5', 'Optimisation pour l\'aide à la décision', 'R5-RA-DWM-11', 34, 0, 34, 0, 0),
(237, 'BUT S5 RA-DWM', '5', 'Modélisations mathématiques', 'R5-RA-DWM-12', 34, 0, 34, 0, 0),
(238, 'BUT S5 RA-DWM', '5', 'Économie numérique et durable', 'R5-RA-DWM-13', 20, 0, 20, 0, 0),
(239, 'BUT S5 RA-DWM', '5', 'Anglais', 'R5-RA-DWM-14', 24, 0, 24, 0, 0),
(240, 'BUT S5 RA-IL', '5', 'Initiation au management d\'une équipe', 'R5-RA-IL-01', 12, 0, 12, 0, 0),
(241, 'BUT S5 RA-IL', '5', 'PPP', 'R5-RA-IL-02', 6, 0, 6, 0, 0),
(242, 'BUT S5 RA-IL', '5', 'Politiques de communication', 'R5-RA-IL-03', 6, 0, 6, 0, 0),
(243, 'BUT S5 RA-IL', '5', 'Qualité algorithmique', 'R5-RA-IL-04', 24, 0, 24, 0, 0),
(244, 'BUT S5 RA-IL', '5', 'Programmation avancée', 'R5-RA-IL-05', 24, 0, 24, 0, 0),
(245, 'BUT S5 RA-IL', '5', 'Programmation multimédia', 'R5-RA-IL-06', 18, 0, 18, 0, 0),
(246, 'BUT S5 RA-IL', '5', 'Automatisation de la production', 'R5-RA-IL-07', 12, 0, 12, 0, 0),
(247, 'BUT S5 RA-IL', '5', 'Qualité de développement', 'R5-RA-IL-08', 24, 0, 24, 0, 0),
(248, 'BUT S5 RA-IL', '5', 'Virtualisation avancée', 'R5-RA-IL-09', 12, 0, 12, 0, 0),
(249, 'BUT S5 RA-IL', '5', 'Nouveaux paradigmes BD', 'R5-RA-IL-10', 26, 0, 26, 0, 0),
(250, 'BUT S5 RA-IL', '5', 'Optimisation pour l\'aide à la décision', 'R5-RA-IL-11', 12, 0, 12, 0, 0),
(251, 'BUT S5 RA-IL', '5', 'Modélisations mathématiques', 'R5-RA-IL-12', 36, 0, 36, 0, 0),
(252, 'BUT S5 RA-IL', '5', 'Économie numérique et durable', 'R5-RA-IL-13', 12, 0, 12, 0, 0),
(253, 'BUT S5 RA-IL', '5', 'Anglais', 'R5-RA-IL-14', 24, 0, 24, 0, 0),
(254, 'BUT S5 RA-IL', '5', 'Logique', 'R5-RA-IL-15', 32, 0, 32, 0, 0),
(255, 'BUT S1', '1', 'Implémentation d\'un besoin client', 'S1-01', 12, 0, 12, 0, 0),
(256, 'BUT S1', '1', 'Comparaison d\'approches algorithmique', 'S1-02', 12, 0, 12, 0, 0),
(257, 'BUT S1', '1', 'Installation d\'un poste développement', 'S1-03', 12, 0, 12, 0, 0),
(258, 'BUT S1', '1', 'Création d\'une BD', 'S1-04', 12, 0, 12, 0, 0),
(259, 'BUT S1', '1', 'Recueil de besoins', 'S1-05', 12, 0, 12, 0, 0),
(260, 'BUT S1', '1', 'Découverte de l\'environnement éco et écolo', 'S1-06', 12, 0, 12, 0, 0),
(261, 'BUT S3', '3', 'Développement d\'une application java', 'S3-01', 62, 0, 62, 0, 0),
(262, 'BUT S3', '3', 'Développement d\'une appli web sécurisée', 'S3-02', 36, 0, 36, 0, 0),
(263, 'BUT S3', '3', 'Réseau et application serveur', 'S3-03', 24, 0, 24, 0, 0),
(264, 'BUT S5 DACS', '5', 'Projet tuteuré', 'S5-DACS-01-1', 0, 0, 0, 0, 0),
(265, 'BUT S5 DACS', '5', 'Administrer un serveur Web', 'S5-DACS-01-2', 0, 0, 0, 0, 0),
(266, 'BUT S5 RA-DWM', '5', 'Projet application web et mobile', 'S5-RA-DWM-01-1', 0, 0, 0, 0, 0),
(267, 'BUT S5 RA-DWM', '5', 'Atelier-projet développement-intégration', 'S5-RA-DWM-01-2', 0, 0, 0, 0, 0),
(268, 'BUT S5 RA-DWM', '5', 'Services Web et interopérabilité', 'S5-RA-DWM-01-3', 32, 0, 32, 0, 0),
(269, 'BUT S5 RA-IL', '5', 'Projet tuteuré', 'S5-RA-IL-01-1', 160, 0, 160, 0, 0),
(270, 'BUT S5 RA-IL', '5', 'Initiation à l\'intelligence artificielle', 'S5-RA-IL-01-2', 60, 0, 60, 0, 0),
(271, 'BUT S5 RA-IL', '5', 'Compilation', 'S5-RA-IL-01-3', 32, 0, 32, 0, 0),
(272, 'Autre', '5', 'Portfolio', 'P5-RA-DWM-01', 0, 0, 0, 0, 0),
(273, 'BUT S2', '2', 'Portfolio', 'P2-01', 0, 0, 0, 0, 0),
(274, 'BUT S6 DACS', '6', 'Portfolio', 'P6-DACS-01', 0, 0, 0, 0, 0),
(275, 'BUT S6 RA-IL', '6', 'Portfolio', 'P6-RA-IL-01', 0, 0, 0, 0, 0),
(276, 'BUT S2', '2', 'Initiation à la programmation objet', 'R2-01A', 32, 0, 32, 0, 0),
(277, 'BUT S2', '2', 'Initiation à la conception objet', 'R2-01B', 28, 0, 20, 8, 0),
(278, 'BUT S2', '2', 'Développement d\'applications avec IHM', 'R2-02', 44, 0, 36, 8, 0),
(279, 'BUT S2', '2', 'Qualité de développement', 'R2-03', 24, 0, 16, 8, 0),
(280, 'BUT S2', '2', 'Communication et fonctionnement bas niveau', 'R2-04', 28, 0, 20, 8, 0),
(281, 'BUT S2', '2', 'Introduction aux services réseaux', 'R2-05', 20, 0, 12, 8, 0),
(282, 'BUT S2', '2', 'Exploitation d\'une BD', 'R2-06', 40, 0, 40, 0, 0),
(283, 'BUT S2', '2', 'Graphes', 'R2-07', 32, 8, 24, 0, 0),
(284, 'BUT S2', '2', 'Outils numériques pour les statistiques descript', 'R2-08', 16, 0, 16, 0, 0),
(285, 'BUT S2', '2', 'Méthodes numériques', 'R2-09', 16, 8, 8, 0, 0),
(286, 'BUT S2', '2', 'Introduction à la gestion des systèmes d\'information', 'R2-10', 40, 0, 40, 0, 0),
(287, 'BUT S2', '2', 'Introduction au droit', 'R2-11', 24, 0, 24, 0, 0),
(288, 'BUT S2', '2', 'Anglais', 'R2-12', 28, 0, 28, 0, 0),
(289, 'BUT S2', '2', 'Communication technique', 'R2-13', 32, 0, 32, 0, 0),
(290, 'BUT S2', '2', 'Projet professionnel et personnel', 'R2-14', 20, 0, 20, 0, 0),
(291, 'BUT S4 DACS', '4', 'Architecture logicielle', 'R4-DACS-01', 32, 0, 32, 0, 0),
(292, 'BUT S4 DACS', '4', 'Qualité développement', 'R4-DACS-02', 16, 0, 16, 0, 0),
(293, 'BUT S4 DACS', '4', 'Qualité et au delà du relationnel', 'R4-DACS-03', 16, 0, 16, 0, 0),
(294, 'BUT S4 DACS', '4', 'Méthode d\'optimisation', 'R4-DACS-04', 12, 0, 12, 0, 0),
(295, 'BUT S4 DACS', '4', 'Anglais', 'R4-DACS-05', 24, 0, 24, 0, 0),
(296, 'BUT S4 DACS', '4', 'Communication interne', 'R4-DACS-06', 22, 0, 22, 0, 0),
(297, 'BUT S4 DACS', '4', 'Projet personnel et professionnel', 'R4-DACS-07', 8, 0, 8, 0, 0),
(298, 'BUT S4 DACS', '4', 'Virtualisation', 'R4-DACS-08', 24, 0, 24, 0, 0),
(299, 'BUT S4 DACS', '4', 'Management avancé des SI', 'R4-DACS-09', 20, 0, 20, 0, 0),
(300, 'BUT S4 DACS', '4', 'Cryptographie et sécurité', 'R4-DACS-10', 12, 0, 12, 0, 0),
(301, 'BUT S4 DACS', '4', 'Réseau avancé', 'R4-DACS-11', 32, 0, 32, 0, 0),
(302, 'BUT S4 DACS', '4', 'Sécurité système et réseaux', 'R4-DACS-12', 20, 0, 20, 0, 0),
(303, 'BUT S4 DACS', '4', 'Administration Unix', 'R4-DACS-13', 24, 0, 24, 0, 0),
(304, 'BUT S4 RA-DWM', '4', 'Architecture logicielle', 'R4-RA-DWM-01', 42, 0, 42, 0, 0),
(305, 'BUT S4 RA-DWM', '4', 'Qualité développement', 'R4-RA-DWM-02', 16, 0, 16, 0, 0),
(306, 'BUT S4 RA-DWM', '4', 'Qualité et au delà du relationnel', 'R4-RA-DWM-03', 24, 0, 240, 0, 0),
(307, 'BUT S4 RA-DWM', '4', 'Méthode d\'optimisation', 'R4-RA-DWM-04', 12, 0, 12, 0, 0),
(308, 'BUT S4 RA-DWM', '4', 'Anglais', 'R4-RA-DWM-05', 24, 0, 24, 0, 0),
(309, 'BUT S4 RA-DWM', '4', 'Communication interne', 'R4-RA-DWM-06', 22, 0, 22, 0, 0),
(310, 'BUT S4 RA-DWM', '4', 'Projet personnel et professionnel', 'R4-RA-DWM-07', 8, 0, 8, 0, 0),
(311, 'BUT S4 RA-DWM', '4', 'Virtualisation', 'R4-RA-DWM-08', 18, 0, 18, 0, 0),
(312, 'BUT S4 RA-DWM', '4', 'Management avancé des SI', 'R4-RA-DWM-09', 20, 0, 20, 0, 0),
(313, 'BUT S4 RA-DWM', '4', 'Complément web', 'R4-RA-DWM-10', 32, 0, 32, 0, 0),
(314, 'BUT S4 RA-DWM', '4', 'Développement pour les app. mobiles', 'R4-RA-DWM-11', 32, 0, 32, 0, 0),
(315, 'BUT S4 RA-DWM', '4', 'Automates et langages', 'R4-RA-DWM-12', 12, 0, 12, 0, 0),
(316, 'BUT S4 RA-IL', '4', 'Architecture logicielle', 'R4-RA-IL-01', 24, 0, 24, 0, 0),
(317, 'BUT S4 RA-IL', '4', 'Qualité de développement', 'R4-RA-IL-02', 16, 0, 16, 0, 0),
(318, 'BUT S4 RA-IL', '4', 'Qualité et au delà du relationnel', 'R4-RA-IL-03', 24, 0, 24, 0, 0),
(319, 'BUT S4 RA-IL', '4', 'Méthode d\'optimisation', 'R4-RA-IL-04', 16, 0, 16, 0, 0),
(320, 'BUT S4 RA-IL', '4', 'Anglais', 'R4-RA-IL-05', 24, 0, 24, 0, 0),
(321, 'BUT S4 RA-IL', '4', 'Communication interne', 'R4-RA-IL-06', 20, 0, 20, 0, 0),
(322, 'BUT S4 RA-IL', '4', 'Projet personnel et professionnel', 'R4-RA-IL-07', 8, 0, 8, 0, 0),
(323, 'BUT S4 RA-IL', '4', 'Virtualisation', 'R4-RA-IL-08', 18, 0, 18, 0, 0),
(324, 'BUT S4 RA-IL', '4', 'Management avancé des SI', 'R4-RA-IL-09', 20, 0, 20, 0, 0),
(325, 'BUT S4 RA-IL', '4', 'Complément web', 'R4-RA-IL-10', 30, 0, 30, 0, 0),
(326, 'BUT S4 RA-IL', '4', 'Développement pour les app. mobiles', 'R4-RA-IL-11', 30, 0, 30, 0, 0),
(327, 'BUT S4 RA-IL', '4', 'Automates et langages', 'R4-RA-IL-12', 20, 0, 20, 0, 0),
(328, 'BUT S6 DACS', '6', 'Initiation entrepreneuriat', 'R6-DACS-01', 0, 0, 0, 0, 0),
(329, 'BUT S6 DACS', '6', 'Droit du numérique et de la prop. industrielle', 'R6-DACS-02', 0, 0, 0, 0, 0),
(330, 'BUT S6 DACS', '6', 'Com. : organisation et diffusion de l\'info', 'R6-DACS-03', 0, 0, 0, 0, 0),
(331, 'BUT S6 DACS', '6', 'PPP', 'R6-DACS-04', 0, 0, 0, 0, 0),
(332, 'BUT S6 DACS', '6', 'Optimisation des services complexes', 'R6-DACS-05', 0, 0, 0, 0, 0),
(333, 'BUT S6 DACS', '6', 'Cloud computing', 'R6-DACS-06', 0, 0, 0, 0, 0),
(334, 'BUT S6 RA', '6', 'Approfondissement dév. mobile ', 'R6-RA-05-1', 0, 0, 0, 0, 0),
(335, 'BUT S6 RA-DWM', '6', 'Initiation à l\'entrepreneuriat', 'R6-RA-DWM-01', 20, 0, 20, 0, 0),
(336, 'BUT S6 RA-DWM', '6', 'Droit numérique et P.I.', 'R6-RA-DWM-02', 18, 0, 18, 0, 0),
(337, 'BUT S6 RA-DWM', '6', 'Com : organisation et diff. de l\'info.', 'R6-RA-DWM-03', 16, 0, 16, 0, 0),
(338, 'BUT S6 RA-DWM', '6', 'PPP', 'R6-RA-DWM-04', 16, 0, 16, 0, 0),
(339, 'BUT S6 RA-DWM', '6', 'Initiation au développement mobile', 'R6-RA-DWM-05-2', 0, 0, 0, 0, 0),
(340, 'BUT S6 RA-DWM', '6', 'Développement côté serveur en JS', 'R6-RA-DWM-05-3', 30, 0, 30, 0, 0),
(341, 'BUT S6 RA-DWM', '6', 'Maintenance applicative', 'R6-RA-DWM-06', 28, 0, 28, 0, 0),
(342, 'BUT S6 RA-IL', '6', 'Initiation à l\'entrepreneuriat', 'R6-RA-IL-01', 8, 0, 8, 0, 0),
(343, 'BUT S6 RA-IL', '6', 'Droit numérique et P.I.', 'R6-RA-IL-02', 18, 0, 18, 0, 0),
(344, 'BUT S6 RA-IL', '6', 'Com : organisation et diff. de l\'info.', 'R6-RA-IL-03', 6, 0, 6, 0, 0),
(345, 'BUT S6 RA-IL', '6', 'PPP', 'R6-RA-IL-04', 6, 0, 6, 0, 0),
(346, 'BUT S6 RA-IL', '6', 'Maintenance applicative', 'R6-RA-IL-06', 14, 0, 14, 0, 0),
(347, 'BUT S2', '2', 'Développement d\'une application', 'S2-01', 16, 0, 16, 0, 0),
(348, 'BUT S2', '2', 'Exploration algorithmique d\'un problème', 'S2-02', 16, 0, 16, 0, 0),
(349, 'BUT S2', '2', 'Installation de services réseaux', 'S2-03', 16, 0, 16, 0, 0),
(350, 'BUT S2', '2', 'Exploitation d\'une BD', 'S2-04', 16, 0, 16, 0, 0),
(351, 'BUT S2', '2', 'Gestion d\'un projet', 'S2-05', 16, 0, 16, 0, 0),
(352, 'BUT S2', '2', 'Organisation d\'un travail d\'équipe', 'S2-06', 16, 0, 16, 0, 0),
(353, 'BUT S4 DACS', '4', 'Déploiement de solution', 'S4-DACS-01', 10, 0, 10, 0, 0),
(354, 'BUT S4 DACS', '4', 'Déploiement avancé', 'S4-DACS-02', 22, 0, 22, 0, 0),
(355, 'BUT S4 DACS', '4', 'Jeu entreprise / Anglais', 'S4-DACS-03', 15, 0, 15, 0, 0),
(356, 'BUT S4 RA-DWM', '4', 'Ateliers projet dvpt web', 'S4-RA-DWM-01', 32, 0, 32, 0, 0),
(357, 'BUT S4 RA-DWM', '4', 'Jeu d\'entreprise / Anglais', 'S4-RA-DWM-02', 15, 0, 15, 0, 0),
(358, 'BUT S4 RA-IL', '4', 'Projet IA', 'S4-RA-IL-01', 10, 0, 10, 0, 0),
(359, 'BUT S4 RA-IL', '4', 'Projet application répartie', 'S4-RA-IL-02', 22, 0, 22, 0, 0),
(360, 'BUT S4 RA-IL', '4', 'Jeu d\'entreprise / Anglais', 'S4-RA-IL-03', 15, 0, 15, 0, 0),
(361, 'BUT S6 DACS', '6', 'Optimisation des services', 'S6-DACS-01', 0, 0, 0, 0, 0),
(362, 'BUT S6 RA-DWM', '6', 'Projet application web et mobile', 'S6-RA-DWM-01-1', 110, 0, 110, 0, 0),
(363, 'BUT S6 RA-DWM', '6', 'Atelier-projet développement-intégration', 'S6-RA-DWM-01-2', 140, 0, 140, 0, 0),
(364, 'BUT S6 RA-IL', '6', 'Projet tuteuré', 'S6-RA-IL-01-1', 50, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `details_cours`
--

CREATE TABLE `details_cours` (
  `id_ressource` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_responsable_module` int(11) NOT NULL,
  `type_salle` varchar(255) NOT NULL,
  `equipements_specifiques` text NOT NULL,
  `details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `details_cours`
--

INSERT INTO `details_cours` (`id_ressource`, `id_cours`, `id_responsable_module`, `type_salle`, `equipements_specifiques`, `details`) VALUES
(6, 186, 55, 'Inconnu', 'Intervention en salle 016 : Oui, de préférence\n', 'DS : Bonjour'),
(7, 187, 55, 'Inconnu', 'Intervention en salle 016 : Indifférent\n', 'DS : Bonjour'),
(8, 186, 55, 'Inconnu', 'Intervention en salle 016 : Oui, de préférence\n', 'DS : t');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id_enseignant` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `heures_affectees` double DEFAULT 0,
  `statut` varchar(255) NOT NULL,
  `total_hetd` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id_enseignant`, `id_utilisateur`, `heures_affectees`, `statut`, `total_hetd`) VALUES
(1, 2, 0, 'vacataire', 0),
(55, 1, 0, 'vacataire', 0),
(64, 19, 0, 'enseignant-chercheur', 0),
(65, 20, 0, 'enseignant-chercheur', 0);

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE `groupes` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(255) NOT NULL,
  `niveau` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `groupes` (`nom_groupe`, `niveau`) 
VALUES 
('GR A', 'BUT 1'),
('GR B', 'BUT 1'),
('GR C', 'BUT 1'),
('GR D', 'BUT 1'),
('GR E', 'BUT 1'),
('GR A', 'BUT 2'),
('GR B', 'BUT 2'),
('GR C', 'BUT 2'),
('GR D', 'BUT 2'),
('GR E', 'BUT 2');

-- --------------------------------------------------------

--
-- Structure de la table `historisation`
--

CREATE TABLE `historisation` (
  `id_historique` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `repartition_heures`
--

CREATE TABLE `repartition_heures` (
  `id_repartition` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `semaine_debut` int(11) NOT NULL,
  `semaine_fin` int(11) NOT NULL,
  `type_heure` varchar(20) NOT NULL,
  `nb_heures_par_semaine` int(11) NOT NULL,
  `semestre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `repartition_heures`
--

INSERT INTO `repartition_heures` (`id_repartition`, `id_cours`, `semaine_debut`, `semaine_fin`, `type_heure`, `nb_heures_par_semaine`, `semestre`) VALUES
(20253, 186, 37, 38, 'TD', 1, 'S1'),
(20254, 255, 1, 6, 'TP', 0, 'S1'),
(20255, 255, 36, 42, 'TP', 0, 'S1'),
(20256, 255, 44, 50, 'TP', 0, 'S1'),
(20257, 257, 1, 1, 'CM', 0, 'S1'),
(20258, 257, 2, 2, 'CM', 0, 'S1'),
(20259, 257, 3, 3, 'CM', 0, 'S1'),
(20260, 257, 4, 4, 'CM', 0, 'S1'),
(20261, 257, 5, 5, 'CM', 0, 'S1'),
(20262, 257, 6, 6, 'CM', 0, 'S1'),
(20263, 257, 7, 7, 'CM', 0, 'S1'),
(20264, 257, 36, 36, 'CM', 0, 'S1'),
(20265, 257, 37, 37, 'CM', 0, 'S1'),
(20266, 257, 38, 38, 'CM', 0, 'S1'),
(20267, 257, 39, 39, 'CM', 0, 'S1'),
(20268, 257, 40, 40, 'CM', 0, 'S1'),
(20269, 257, 41, 41, 'CM', 0, 'S1'),
(20270, 257, 42, 42, 'CM', 0, 'S1'),
(20271, 257, 43, 43, 'CM', 0, 'S1'),
(20272, 257, 44, 44, 'CM', 0, 'S1'),
(20273, 257, 45, 45, 'CM', 0, 'S1'),
(20274, 257, 46, 46, 'CM', 0, 'S1'),
(20275, 257, 47, 47, 'CM', 0, 'S1'),
(20276, 257, 48, 48, 'CM', 0, 'S1'),
(20277, 257, 49, 49, 'CM', 0, 'S1'),
(20278, 257, 50, 50, 'CM', 0, 'S1'),
(20279, 257, 51, 51, 'CM', 0, 'S1'),
(20280, 257, 52, 52, 'CM', 0, 'S1'),
(20307, 277, 44, 45, 'TP', 2, 'S2'),
(20308, 348, 1, 1, 'TD', 0, 'S2'),
(20309, 348, 2, 2, 'TD', 0, 'S2'),
(20310, 348, 3, 3, 'TD', 0, 'S2'),
(20311, 348, 4, 4, 'TD', 0, 'S2'),
(20312, 348, 5, 5, 'TD', 0, 'S2'),
(20313, 348, 6, 6, 'TD', 0, 'S2'),
(20314, 348, 7, 7, 'TD', 0, 'S2'),
(20315, 348, 36, 36, 'TD', 0, 'S2'),
(20316, 348, 37, 37, 'TD', 0, 'S2'),
(20317, 348, 38, 38, 'TD', 0, 'S2'),
(20318, 348, 39, 39, 'TD', 0, 'S2'),
(20319, 348, 40, 40, 'TD', 0, 'S2'),
(20320, 348, 41, 41, 'TD', 0, 'S2'),
(20321, 348, 42, 42, 'TD', 0, 'S2'),
(20322, 348, 43, 43, 'TD', 0, 'S2'),
(20323, 348, 44, 44, 'TD', 0, 'S2'),
(20324, 348, 45, 45, 'TD', 0, 'S2'),
(20325, 348, 46, 46, 'TD', 0, 'S2'),
(20326, 348, 47, 47, 'TD', 0, 'S2'),
(20327, 348, 48, 48, 'TD', 0, 'S2'),
(20328, 348, 49, 49, 'TD', 0, 'S2'),
(20329, 348, 50, 50, 'TD', 0, 'S2'),
(20330, 348, 51, 51, 'TD', 0, 'S2'),
(20331, 348, 52, 52, 'TD', 0, 'S2');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `statut` varchar(255) DEFAULT NULL,
  `nombre_heures` int(11) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiration` datetime DEFAULT NULL,
  `supprimer` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `statut`, `nombre_heures`, `reset_token`, `reset_token_expiration`, `supprimer`) VALUES
(1, 'test2', 'gestionnaire', 'test2@gmail.com', 'azertyuiop', 'gestionnaire', NULL, NULL, NULL, NULL, 0),
(2, 'test', 'enseignant', 'test@gmail.com', 'azertyuiop', 'enseignant', NULL, NULL, NULL, NULL, 0),
(19, 'Test', 'Prenom', 'tp@gmail.com', '$2y$10$AH8akmk4QoUyHHKyDH1ei.58VXQC70ESENNRdiQMgfDnhJ1.ui9wO', 'enseignant', 'enseignant-chercheur', 123, NULL, NULL, 0),
(20, 'lundi', 'lundi', 'lundi@gmail.com', '$2y$10$uSs/EOKo.8D4uyr/nGVqguDi4wvU.MFMOo7KHV9CPudYalaR1KS3e', 'enseignant', 'enseignant-chercheur', 132, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `voeux`
--

CREATE TABLE `voeux` (
  `id_voeu` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `semestre` varchar(255) NOT NULL,
  `nb_CM` double NOT NULL,
  `nb_TD` double NOT NULL,
  `nb_TP` double NOT NULL,
  `nb_EI` double NOT NULL,
  `remarques` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



-- --------------------------------------------------------

--
-- Structure de la table `voeux_hors_iut`
--

CREATE TABLE `voeux_hors_iut` (
  `id_voeu_hi` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `composant` varchar(255) DEFAULT NULL,
  `formation` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `nb_heures_cm` double DEFAULT NULL,
  `nb_heures_td` double DEFAULT NULL,
  `nb_heures_tp` double DEFAULT NULL,
  `nb_heures_ei` double DEFAULT NULL,
  `nb_total` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `affectations`
--
ALTER TABLE `affectations`
  ADD PRIMARY KEY (`id_affectation`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_enseignant` (`id_enseignant`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Index pour la table `contraintes`
--
ALTER TABLE `contraintes`
  ADD PRIMARY KEY (`id_contrainte`),
  ADD KEY `id_enseignant` (`id_utilisateur`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id_cours`);

--
-- Index pour la table `details_cours`
--
ALTER TABLE `details_cours`
  ADD PRIMARY KEY (`id_ressource`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_responsable_module` (`id_responsable_module`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id_enseignant`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id_groupe`);

--
-- Index pour la table `historisation`
--
ALTER TABLE `historisation`
  ADD PRIMARY KEY (`id_historique`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_enseignant` (`id_enseignant`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Index pour la table `repartition_heures`
--
ALTER TABLE `repartition_heures`
  ADD PRIMARY KEY (`id_repartition`),
  ADD KEY `id_cours` (`id_cours`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `voeux`
--
ALTER TABLE `voeux`
  ADD PRIMARY KEY (`id_voeu`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_enseignant` (`id_enseignant`);

--
-- Index pour la table `voeux_hors_iut`
--
ALTER TABLE `voeux_hors_iut`
  ADD PRIMARY KEY (`id_voeu_hi`),
  ADD KEY `id_enseignant` (`id_enseignant`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `affectations`
--
ALTER TABLE `affectations`
  MODIFY `id_affectation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contraintes`
--
ALTER TABLE `contraintes`
  MODIFY `id_contrainte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id_cours` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- AUTO_INCREMENT pour la table `details_cours`
--
ALTER TABLE `details_cours`
  MODIFY `id_ressource` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id_enseignant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT pour la table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id_groupe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historisation`
--
ALTER TABLE `historisation`
  MODIFY `id_historique` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `repartition_heures`
--
ALTER TABLE `repartition_heures`
  MODIFY `id_repartition` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20332;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `voeux`
--
ALTER TABLE `voeux`
  MODIFY `id_voeu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `voeux_hors_iut`
--
ALTER TABLE `voeux_hors_iut`
  MODIFY `id_voeu_hi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectations`
--
ALTER TABLE `affectations`
  ADD CONSTRAINT `affectations_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `affectations_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `affectations_ibfk_3` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`);

--
-- Contraintes pour la table `contraintes`
--
ALTER TABLE `contraintes`
  ADD CONSTRAINT `contraintes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `details_cours`
--
ALTER TABLE `details_cours`
  ADD CONSTRAINT `detailscours_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `detailscours_ibfk_2` FOREIGN KEY (`id_responsable_module`) REFERENCES `enseignants` (`id_enseignant`);

--
-- Contraintes pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD CONSTRAINT `enseignants_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `historisation`
--
ALTER TABLE `historisation`
  ADD CONSTRAINT `historisation_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `historisation_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `historisation_ibfk_3` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`);

--
-- Contraintes pour la table `repartition_heures`
--
ALTER TABLE `repartition_heures`
  ADD CONSTRAINT `repartition_heures_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`);

--
-- Contraintes pour la table `voeux`
--
ALTER TABLE `voeux`
  ADD CONSTRAINT `voeux_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `voeux_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`);

--
-- Contraintes pour la table `voeux_hors_iut`
--
ALTER TABLE `voeux_hors_iut`
  ADD CONSTRAINT `voeux_hors_iut_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
