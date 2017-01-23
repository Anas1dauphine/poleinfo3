SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE `Affectation` (
  `id_master` int(11) NOT NULL default '0',
  `id_contenu` int(11) NOT NULL default '0',
  `id_personne` int(11) NOT NULL default '0',
  `annee` int(11) NOT NULL default '0',
  `note` decimal(6,2) default NULL,
  PRIMARY KEY  (`id_master`,`id_contenu`,`id_personne`,`annee`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `affect_bis` (
  `id_master` int(11) NOT NULL default '0',
  `id_contenu` int(11) NOT NULL default '0',
  `id_personne` int(11) NOT NULL default '0',
  `annee` int(11) NOT NULL default '0',
  `note` decimal(6,2) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Candidat` (
  `id` int(11) NOT NULL auto_increment,
  `prenom` varchar(60) NOT NULL,
  `nom` varchar(60) NOT NULL,
  `email` varchar(60) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text,
  `ville` varchar(60) default NULL,
  `code_postal` varchar(10) default NULL,
  `pays` varchar(50) default NULL,
  `masters` varchar(20) default NULL,
  `annee_master` int(11) default NULL,
  `diplome` varchar(60) default NULL,
  `etablissement` varchar(60) default NULL,
  `ville_diplome` varchar(60) default NULL,
  `pays_diplome` varchar(50) default NULL,
  `mois_obtention` int(11) default NULL,
  `annee_obtention` int(11) default NULL,
  `affectation` int(11) default NULL,
  `genre` char(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2252 ;

CREATE TABLE `Contenu` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(200) NOT NULL default '',
  `description` text,
  `apprentissage` char(1) NOT NULL default 'N',
  `volume_horaire` int(11) NOT NULL default '0',
  `ects` decimal(4,2) default NULL,
  `volume_projet` int(11) default NULL,
  `objectives` text,
  `contents` text,
  `biblio` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=108 ;

CREATE TABLE `Cours` (
  `id_master` int(11) NOT NULL default '0',
  `id_contenu` int(11) NOT NULL default '0',
  `id_enseignant` int(11) NOT NULL default '0',
  `periode` varchar(200) collate latin1_general_ci default NULL,
  `obligatoire` char(1) collate latin1_general_ci NOT NULL default 'O',
  `notes` text collate latin1_general_ci,
  PRIMARY KEY  (`id_master`,`id_contenu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `Master` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL default '',
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

CREATE TABLE `Personne` (
  `id` int(11) NOT NULL auto_increment,
  `prenom` varchar(60) NOT NULL default '',
  `nom` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `telephone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `home_page` varchar(100) default NULL,
  `cv` text,
  `notes` text,
  `password` varchar(32) default NULL,
  `roles` varchar(10) default NULL,
  `id_master` int(11) default NULL,
  `annee_master` int(11) default NULL,
  `adresse` text,
  `mobile` varchar(20) default NULL,
  `vacataire` char(1) default 'N',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `prenom` (`prenom`,`nom`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2673 ;

CREATE TABLE `Preference` (
  `id_master` int(11) NOT NULL default '0',
  `id_contenu` int(11) NOT NULL default '0',
  `id_personne` int(11) NOT NULL default '0',
  `niveau` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_master`,`id_contenu`,`id_personne`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Role` (
  `code` char(1) NOT NULL default '',
  `intitule` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Session` (
  `id_session` varchar(40) NOT NULL default '',
  `id_person` varchar(60) NOT NULL default '',
  `first_name` varchar(60) NOT NULL default '',
  `last_name` varchar(60) NOT NULL default '',
  `end_session` decimal(10,0) NOT NULL default '0',
  `roles` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id_session`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `sv_affect` (
  `id_master` int(11) NOT NULL default '0',
  `id_contenu` int(11) NOT NULL default '0',
  `id_personne` int(11) NOT NULL default '0',
  `annee` int(11) NOT NULL default '0',
  `note` decimal(6,2) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO Personne (id, prenom, nom, email, telephone, fax,
                      password, roles)
	VALUES (999,'poleinfo3','poleinfo3', 'poleinfo3', '', '', 
                'poleinfo3', 'M');
INSERT INTO Personne (id, prenom, nom, email, telephone, fax,
                      password, roles)
	VALUES (1000,'admin','admin', 'admin', '', '', 
                'admin', 'M, A');

INSERT INTO `Role` (`code`, `intitule`) VALUES
('M', 'Enseignant'),
('S', 'Etudiant');


