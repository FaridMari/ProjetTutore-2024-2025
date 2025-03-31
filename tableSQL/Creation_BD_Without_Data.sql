-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 mars 2025 à 04:27
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

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
-- Structure de la table `affectations_historisees`
--

CREATE TABLE `affectations_historisees` (
  `id_affectation` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `heures_affectees` double NOT NULL,
  `type_heure` varchar(255) NOT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configurationplanningdetaille`
--

CREATE TABLE `configurationplanningdetaille` (
  `id` int(11) NOT NULL,
  `semestre` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `dateDebut` date DEFAULT NULL,
  `dateFin` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `nbSemaines` int(11) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT '#FFFFFF',
  `modifiable` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configurationplanningdetaille_historisees`
--

CREATE TABLE `configurationplanningdetaille_historisees` (
  `id` int(11) NOT NULL,
  `semestre` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `dateDebut` date DEFAULT NULL,
  `dateFin` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `nbSemaines` int(11) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT '#FFFFFF',
  `modifiable` tinyint(1) DEFAULT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `cours_samedi` varchar(20) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'en attente',
  `commentaire` text DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contraintes_historisees`
--

CREATE TABLE `contraintes_historisees` (
  `id_contrainte` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `jour` varchar(255) NOT NULL,
  `heure_debut` int(11) NOT NULL,
  `heure_fin` int(11) NOT NULL,
  `creneau_preference` varchar(20) DEFAULT NULL,
  `cours_samedi` varchar(20) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'en attente',
  `commentaire` text DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `cours_historisees`
--

CREATE TABLE `cours_historisees` (
  `id_cours` int(11) NOT NULL,
  `formation` varchar(255) NOT NULL,
  `semestre` varchar(255) NOT NULL,
  `nom_cours` varchar(255) NOT NULL,
  `code_cours` varchar(255) NOT NULL,
  `nb_heures_total` double DEFAULT 0,
  `nb_heures_cm` double DEFAULT 0,
  `nb_heures_td` double DEFAULT 0,
  `nb_heures_tp` double DEFAULT 0,
  `nb_heures_ei` double DEFAULT 0,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `details` text NOT NULL,
  `statut` varchar(20) DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `details_cours_historisees`
--

CREATE TABLE `details_cours_historisees` (
  `id_ressource` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_responsable_module` int(11) NOT NULL,
  `type_salle` varchar(255) NOT NULL,
  `equipements_specifiques` text NOT NULL,
  `details` text NOT NULL,
  `statut` varchar(20) DEFAULT 'en attente',
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id_enseignant` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `heures_affectees` double DEFAULT 0,
  `statut` varchar(255) NOT NULL,
  `total_hetd` double DEFAULT 0,
  `nb_contrainte` int(11) DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE `groupes` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(255) NOT NULL,
  `niveau` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupes_historisees`
--

CREATE TABLE `groupes_historisees` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(255) NOT NULL,
  `niveau` varchar(255) NOT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `repartition_heures_historisees`
--

CREATE TABLE `repartition_heures_historisees` (
  `id_repartition` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `semaine_debut` int(11) NOT NULL,
  `semaine_fin` int(11) NOT NULL,
  `type_heure` varchar(20) NOT NULL,
  `nb_heures_par_semaine` int(11) NOT NULL,
  `semestre` varchar(255) DEFAULT NULL,
  `annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `supprimer` tinyint(1) NOT NULL DEFAULT 0,
  `responsable` varchar(20) DEFAULT 'oui',
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `remarques` text NOT NULL,
  `statut` varchar(20) DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `voeux_historisees`
--

CREATE TABLE `voeux_historisees` (
  `id_voeu` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `semestre` varchar(255) NOT NULL,
  `nb_CM` double NOT NULL,
  `nb_TD` double NOT NULL,
  `nb_TP` double NOT NULL,
  `nb_EI` double NOT NULL,
  `remarques` text NOT NULL,
  `statut` varchar(20) DEFAULT 'en attente',
  `annee` int(11) NOT NULL
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

-- --------------------------------------------------------

--
-- Structure de la table `voeux_hors_iut_historisees`
--

CREATE TABLE `voeux_hors_iut_historisees` (
  `id_voeu_hi` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `composant` varchar(255) DEFAULT NULL,
  `formation` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `nb_heures_cm` double DEFAULT NULL,
  `nb_heures_td` double DEFAULT NULL,
  `nb_heures_tp` double DEFAULT NULL,
  `nb_heures_ei` double DEFAULT NULL,
  `nb_total` double DEFAULT NULL,
  `annee` int(11) NOT NULL
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
-- Index pour la table `affectations_historisees`
--
ALTER TABLE `affectations_historisees`
  ADD PRIMARY KEY (`id_affectation`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_enseignant` (`id_enseignant`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Index pour la table `configurationplanningdetaille`
--
ALTER TABLE `configurationplanningdetaille`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `configurationplanningdetaille_historisees`
--
ALTER TABLE `configurationplanningdetaille_historisees`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contraintes`
--
ALTER TABLE `contraintes`
  ADD PRIMARY KEY (`id_contrainte`),
  ADD KEY `id_enseignant` (`id_utilisateur`);

--
-- Index pour la table `contraintes_historisees`
--
ALTER TABLE `contraintes_historisees`
  ADD PRIMARY KEY (`id_contrainte`),
  ADD KEY `id_enseignant` (`id_utilisateur`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id_cours`);

--
-- Index pour la table `cours_historisees`
--
ALTER TABLE `cours_historisees`
  ADD PRIMARY KEY (`id_cours`);

--
-- Index pour la table `details_cours`
--
ALTER TABLE `details_cours`
  ADD PRIMARY KEY (`id_ressource`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `id_responsable_module` (`id_responsable_module`);

--
-- Index pour la table `details_cours_historisees`
--
ALTER TABLE `details_cours_historisees`
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
-- Index pour la table `groupes_historisees`
--
ALTER TABLE `groupes_historisees`
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
-- Index pour la table `repartition_heures_historisees`
--
ALTER TABLE `repartition_heures_historisees`
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
-- Index pour la table `voeux_historisees`
--
ALTER TABLE `voeux_historisees`
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
-- Index pour la table `voeux_hors_iut_historisees`
--
ALTER TABLE `voeux_hors_iut_historisees`
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
-- AUTO_INCREMENT pour la table `affectations_historisees`
--
ALTER TABLE `affectations_historisees`
  MODIFY `id_affectation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `configurationplanningdetaille`
--
ALTER TABLE `configurationplanningdetaille`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `configurationplanningdetaille_historisees`
--
ALTER TABLE `configurationplanningdetaille_historisees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contraintes`
--
ALTER TABLE `contraintes`
  MODIFY `id_contrainte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contraintes_historisees`
--
ALTER TABLE `contraintes_historisees`
  MODIFY `id_contrainte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id_cours` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cours_historisees`
--
ALTER TABLE `cours_historisees`
  MODIFY `id_cours` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `details_cours`
--
ALTER TABLE `details_cours`
  MODIFY `id_ressource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `details_cours_historisees`
--
ALTER TABLE `details_cours_historisees`
  MODIFY `id_ressource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id_enseignant` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id_groupe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groupes_historisees`
--
ALTER TABLE `groupes_historisees`
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
  MODIFY `id_repartition` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `repartition_heures_historisees`
--
ALTER TABLE `repartition_heures_historisees`
  MODIFY `id_repartition` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `voeux`
--
ALTER TABLE `voeux`
  MODIFY `id_voeu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `voeux_historisees`
--
ALTER TABLE `voeux_historisees`
  MODIFY `id_voeu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `voeux_hors_iut`
--
ALTER TABLE `voeux_hors_iut`
  MODIFY `id_voeu_hi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `voeux_hors_iut_historisees`
--
ALTER TABLE `voeux_hors_iut_historisees`
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
-- Contraintes pour la table `affectations_historisees`
--
ALTER TABLE `affectations_historisees`
  ADD CONSTRAINT `affectations_historisees_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `affectations_historisees_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `affectations_historisees_ibfk_3` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`);

--
-- Contraintes pour la table `contraintes`
--
ALTER TABLE `contraintes`
  ADD CONSTRAINT `contraintes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `contraintes_historisees`
--
ALTER TABLE `contraintes_historisees`
  ADD CONSTRAINT `contraintes_historisees_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `details_cours`
--
ALTER TABLE `details_cours`
  ADD CONSTRAINT `detailscours_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `detailscours_ibfk_2` FOREIGN KEY (`id_responsable_module`) REFERENCES `enseignants` (`id_enseignant`);

--
-- Contraintes pour la table `details_cours_historisees`
--
ALTER TABLE `details_cours_historisees`
  ADD CONSTRAINT `detailscours_historisees_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`),
  ADD CONSTRAINT `detailscours_historisees_ibfk_2` FOREIGN KEY (`id_responsable_module`) REFERENCES `enseignants` (`id_enseignant`);

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
-- Contraintes pour la table `repartition_heures_historisees`
--
ALTER TABLE `repartition_heures_historisees`
  ADD CONSTRAINT `repartition_heures_historisees_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`);

--
-- Contraintes pour la table `voeux`
--
ALTER TABLE `voeux`
  ADD CONSTRAINT `voeux_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `voeux_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`);

--
-- Contraintes pour la table `voeux_historisees`
--
ALTER TABLE `voeux_historisees`
  ADD CONSTRAINT `voeux_historisees_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `voeux_historisees_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id_cours`);

--
-- Contraintes pour la table `voeux_hors_iut`
--
ALTER TABLE `voeux_hors_iut`
  ADD CONSTRAINT `voeux_hors_iut_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`);

--
-- Contraintes pour la table `voeux_hors_iut_historisees`
--
ALTER TABLE `voeux_hors_iut_historisees`
  ADD CONSTRAINT `voeux_hors_iut_historisees_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
