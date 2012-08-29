-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost:3306
-- Généré le : Mer 29 Août 2012 à 17:08
-- Version du serveur: 5.5.9
-- Version de PHP: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `rmbdev`
--

-- --------------------------------------------------------

--
-- Structure de la table `remboursemoi_pp_ipn_log`
--

CREATE TABLE `remboursemoi_pp_ipn_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `custom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blob` text COLLATE utf8_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8_unicode_ci NOT NULL,
  `curl_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Structure de la table `remboursemoi_request`
--

CREATE TABLE `remboursemoi_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rmb_name` varchar(255) DEFAULT NULL,
  `rmb_email` varchar(255) DEFAULT NULL,
  `rmb_amount` double DEFAULT NULL,
  `rmb_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rmb_dest` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=296 ;

-- --------------------------------------------------------

--
-- Structure de la table `remboursemoi_transaction`
--

CREATE TABLE `remboursemoi_transaction` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `bnf_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email du bénéficiaire',
  `bnf_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nom du bénéficiaire',
  `tx_qte` int(11) NOT NULL COMMENT 'quantité de la transaction',
  `tx_unit_price` float NOT NULL COMMENT 'montant unitaire de la transaction',
  `ht_firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'prénom de l''acheteur',
  `ht_lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nom de l''acheteur',
  `ht_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email de l''acheteur',
  `pp_ipn_blob` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'sérialisation des infos envoyé par l''IPN (PayPal)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date de la création de la transaction',
  `pp_txn_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ID de la transation PayPal',
  `public_balance_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'hash utiliser pour accéder à la page balance',
  `tr_id` int(11) DEFAULT NULL COMMENT 'Id de la transaction request lié à cette ',
  PRIMARY KEY (`ID`),
  KEY `bnf_email` (`bnf_email`),
  KEY `pp_txn_id` (`pp_txn_id`),
  KEY `public_balance_hash` (`public_balance_hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Structure de la table `remboursemoi_transfert_request`
--

CREATE TABLE `remboursemoi_transfert_request` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la demande de virement',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `code_banque` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `code_guichet` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `num_compte` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `cle_rib` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `tr_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tr_complete_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
