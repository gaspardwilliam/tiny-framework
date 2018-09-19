-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 19 Septembre 2018 à 13:48
-- Version du serveur :  5.7.11
-- Version de PHP :  7.2.7RC1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `alto`
--

-- --------------------------------------------------------

--
-- Structure de la table `alto_categories`
--

CREATE TABLE `alto_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `alto_images`
--

CREATE TABLE `alto_images` (
  `image_id` int(11) NOT NULL,
  `image_key` varchar(255) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `alto_postmeta`
--

CREATE TABLE `alto_postmeta` (
  `meta_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `alto_posts`
--

CREATE TABLE `alto_posts` (
  `post_id` int(11) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `post_content` longtext NOT NULL,
  `post_created_date` datetime NOT NULL,
  `post_updated_date` datetime NOT NULL,
  `post_slug` varchar(255) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `alto_users`
--

CREATE TABLE `alto_users` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_role` varchar(255) NOT NULL,
  `user_password` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `alto_users`
--

INSERT INTO `alto_users` (`user_id`, `user_email`, `user_role`, `user_password`) VALUES
(1, 'admin@gmail.com', 'admin', '$2y$10$GYE21RvGcR.5TldqnhCWRe/H4u/d0lwkCywGsiYqcFnzqueDLrAJ2');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `alto_categories`
--
ALTER TABLE `alto_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Index pour la table `alto_images`
--
ALTER TABLE `alto_images`
  ADD PRIMARY KEY (`image_id`);

--
-- Index pour la table `alto_postmeta`
--
ALTER TABLE `alto_postmeta`
  ADD PRIMARY KEY (`meta_id`);

--
-- Index pour la table `alto_posts`
--
ALTER TABLE `alto_posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Index pour la table `alto_users`
--
ALTER TABLE `alto_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `alto_categories`
--
ALTER TABLE `alto_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `alto_images`
--
ALTER TABLE `alto_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;
--
-- AUTO_INCREMENT pour la table `alto_postmeta`
--
ALTER TABLE `alto_postmeta`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
--
-- AUTO_INCREMENT pour la table `alto_posts`
--
ALTER TABLE `alto_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT pour la table `alto_users`
--
ALTER TABLE `alto_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
