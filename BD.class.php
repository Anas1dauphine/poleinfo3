<?php
// Classe abstraite définissant une interface générique d'accès
// à une base de données. Version complète

 class BD
{
  // ----   Partie privée : les propriétés
  var $sgbd, $connexion, $nom_base;
  var $code_erreur, $message_erreur;

  // Constructeur de la classe
  function BD ($login, $mot_de_passe, $base, $serveur)
  {
    // Initialisations
    $this->nom_base = $base;
    $this->code_erreur = 0;
    $this->message_erreur = "";
    $this->sgbd = "Inconnu?";

    // Connexion au serveur par appel à une méthode privée
    $this->connexion = $this->connect($login, $mot_de_passe, 
				      $base, $serveur);

    // Lancé d'exception en cas d'erreur
    if ($this->connexion == 0) 
      echo "Erreur de connexion au SGBD" . 
			   $this->sgbd . ": " . $this->code_erreur;

    // Fin du constructeur
  }

  // Méthodes privées
    function connect ($login, $mot_de_passe, $base, $serveur) {}
    function exec ($requete) {}

  // Méthodes publiques

  // Méthode d'exécution d'une requête
   function execRequete ($requete)
  {
    if (!$resultat = $this->exec ($requete)) 
      echo "Problème dans l'exécution de la requête : $requete.<br> "
	. $this->messageSGBD();

    return $resultat;
  }

    // Get the id of the last inserted row
    function idDerniereLigne ()
    {  return  mysql_insert_id(); }
  // Méthodes abstraites
  // Accès à la ligne suivante, sous forme d'objet
    function objetSuivant ($resultat) {}
  // Accès à la ligne suivante, sous forme de tableau associatif
    function ligneSuivante ($resultat) {}
  // Accès à la ligne suivante, sous forme de tableau indicé
    function tableauSuivant ($resultat) {}

  // Echappement des apostrophes et autres préparations à l'insertion
    function prepareChaine($chaine) {}

  // Génération d'un identifiant
    function genereID($nom_sequence) {}

  // Méthode indiquant le nombre d'attributs dans le résultat
   function nbAttributs ($res) {}

  // Méthode donnant le nom d'un attribut dans un résultat
   function nomAttribut ($res, $position) {}

  // Retour du message d'erreur
    function messageSGBD () {}

  // Nom du SGBD
   function getSGBD()
  {    return $this->sgbd;  }
   function setSGBD($sgbd)
  { $this->sgbd = $sgbd;  }

  // Fin de la classe
}
?>
