-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 21 Mai 2019 à 13:37
-- Version du serveur :  10.1.13-MariaDB
-- Version de PHP :  5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `membres`
--

-- --------------------------------------------------------

--
-- Structure de la table `barre_matchs`
--

CREATE TABLE `barre_matchs` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `val_barre` int(11) NOT NULL,
  `time_barre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `bloquer`
--

CREATE TABLE `bloquer` (
  `id` int(11) NOT NULL,
  `id_bloquer` int(11) NOT NULL,
  `id_bloqueur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `connected`
--

CREATE TABLE `connected` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `co` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id` int(11) NOT NULL,
  `id_visiter` int(11) NOT NULL,
  `id_visiteur` int(11) NOT NULL,
  `vu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `insta`
--

CREATE TABLE `insta` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nb_followers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `local`
--

CREATE TABLE `local` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `lon` text NOT NULL,
  `lat` text NOT NULL,
  `city` text NOT NULL,
  `region` text NOT NULL,
  `pays` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `id` int(11) NOT NULL,
  `id_p1` int(11) NOT NULL,
  `id_p2` int(11) NOT NULL,
  `match_p1` int(11) NOT NULL,
  `match_p2` int(11) NOT NULL,
  `vu_p1` int(11) NOT NULL,
  `vu_p2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `prenom` text NOT NULL,
  `mail` text NOT NULL,
  `mdp` text NOT NULL,
  `sexe` text NOT NULL,
  `attirance` text NOT NULL,
  `description` text NOT NULL,
  `photos` text NOT NULL,
  `photos_pro` text NOT NULL,
  `age` int(11) NOT NULL,
  `verif` text NOT NULL,
  `nom_utilisteur` text NOT NULL,
  `interets` text NOT NULL,
  `oauth` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `id_p1` int(11) NOT NULL,
  `id_p2` int(11) NOT NULL,
  `message` text NOT NULL,
  `vu` int(11) NOT NULL,
  `id_envoyeur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `musique`
--

CREATE TABLE `musique` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_musique` text NOT NULL,
  `name_musique` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `played`
--

CREATE TABLE `played` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `game` varchar(255) NOT NULL,
  `time_played` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `signaler`
--

CREATE TABLE `signaler` (
  `id` int(11) NOT NULL,
  `id_signaler` int(11) NOT NULL,
  `id_signaleur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `song`
--

CREATE TABLE `song` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_musique` text NOT NULL,
  `nb_fois` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suppr_m`
--

CREATE TABLE `suppr_m` (
  `id` int(11) NOT NULL,
  `id_p1` int(11) NOT NULL,
  `id_p2` int(11) NOT NULL,
  `vu_p1` int(11) NOT NULL,
  `vu_p2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `barre_matchs`
--
ALTER TABLE `barre_matchs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `bloquer`
--
ALTER TABLE `bloquer`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `connected`
--
ALTER TABLE `connected`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `insta`
--
ALTER TABLE `insta`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `local`
--
ALTER TABLE `local`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `musique`
--
ALTER TABLE `musique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `played`
--
ALTER TABLE `played`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `signaler`
--
ALTER TABLE `signaler`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `song`
--
ALTER TABLE `song`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `suppr_m`
--
ALTER TABLE `suppr_m`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `barre_matchs`
--
ALTER TABLE `barre_matchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;
--
-- AUTO_INCREMENT pour la table `bloquer`
--
ALTER TABLE `bloquer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `connected`
--
ALTER TABLE `connected`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;
--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pour la table `insta`
--
ALTER TABLE `insta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT pour la table `local`
--
ALTER TABLE `local`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;
--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;
--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT pour la table `musique`
--
ALTER TABLE `musique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT pour la table `played`
--
ALTER TABLE `played`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;
--
-- AUTO_INCREMENT pour la table `signaler`
--
ALTER TABLE `signaler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `song`
--
ALTER TABLE `song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2272;
--
-- AUTO_INCREMENT pour la table `suppr_m`
--
ALTER TABLE `suppr_m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
