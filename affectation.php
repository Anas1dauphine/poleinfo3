<?php
 
 session_start(); 

// Load the libraries

require_once ("Util.php");
require_once("template.inc");

define ("UNKNOWN_RATING", "-1"); 

// Instanciate a template object
$tpl = new Template (".");

// Connect to the database
$db = new BDMySQL (NAME, PASS, BASE, SERVER);
// Set the standard conf. infos

// Load the required files and assign them to variables
$tpl->set_file ( array ("Page" => TPLDIR . "Page.tpl",
			"affectation" => TPLDIR . "affectation.tpl"));


$session = CheckAccess ("preferences.php", $_POST, session_id(), $db, $tpl);

$annee_master = ANNEE_MASTER;

if (is_object($session))
{
  // Mise a jour des choix d'inscription
  if (isSet($_POST['changeAssignment'])) { 
    // On commence par tout virer
    $assignments = $_POST['assignments']; 
    if (is_array($assignments)) { 
      foreach ($assignments as $id_etudiant => $tabCours) {
	foreach ($tabCours as $id_cours => $assign) {
	  list ($id_master, $id_contenu) = explode (";", $id_cours);
	  
	  // On vire l'inscription
	  $db->execRequete 
	    ("DELETE FROM Affectation WHERE id_personne='$id_etudiant' "
	     . " AND id_contenu='$id_contenu' "
	     . " AND annee='$annee_master'");

	  if ($assign == 1) {
	    // On inscrit
	  $db->execRequete 
	    ("INSERT INTO Affectation (id_personne, id_master, id_contenu, "
	     . " annee) VALUES ('$id_etudiant', 1, '$id_contenu', "
	     . " '$annee_master')");
	  }
	}
      } 
    } 
  }

  /* Récupération des valeurs par défaut */
  if (isSet($_REQUEST['master_etudiant'])
      and $_REQUEST['master_etudiant'] != 0) {
      $prefMasterEtudiant = $_REQUEST['master_etudiant'] ; 
    }
  else 
    $prefMasterEtudiant= "%"; 
 
  if (isSet($_REQUEST['etudiant'])
      and $_REQUEST['etudiant'] != 0)
    $prefEtudiant = $_REQUEST['etudiant'] ; 
  else 
    $prefEtudiant= "%"; 

  if ($prefEtudiant == "%" and $prefMasterEtudiant == '%') {
	  // Aucun choix: on ne montre rien
	  $prefEtudiant = $prefMasterEtudiant = '';
  }

    //echo "$prefEtudiant<br>";

  $tpl->set_var ("MASTER_ETUDIANT", $prefMasterEtudiant);
  // $tpl->set_var ("MASTER_COURS", $prefMasterCours);
  $tpl->set_var ("ETUDIANT", $prefEtudiant);

  // Decompose the blocks of the template 
  $tpl->set_block("affectation", "ETUDIANT_DETAIL", "ETUDIANTS"); 
  $tpl->set_block("affectation", "COURS_DETAIL", "COURS"); 
  $tpl->set_block("ETUDIANT_DETAIL", "ASSIGNMENT_DETAIL", "ASSIGNMENTS"); 
 
  $tpl->set_var("ASSIGNMENTS", ""); 
  $tpl->set_var("ETUDIANTS", ""); 
  $tpl->set_var("COURS", ""); 
 
  // Recherche de la liste des Masters
  $qMaster = "SELECT * FROM Master WHERE id IN (1, 2, 3, 4, 5, 6, 8)"; 
  $rMaster = $db->execRequete($qMaster); 
  $masters = array(0 => "Tous"); 
  $fMasters = new Formulaire ("",""); 
  while ($master = $db->objetSuivant($rMaster)) 
    $masters[$master->id] = $master->nom; 
 
  // Recherche de la liste des étudiants
  $qEtudiant = "SELECT * FROM Personne WHERE id_master IN (1, 2, 3, 4, 5, 6)"
  . " AND roles LIKE '%s' AND annee_master = " . ANNEE_MASTER 
	  . " ORDER BY id_master, nom"; 
  $rEtudiant = $db->execRequete($qEtudiant); 
  $etudiants = array(0 => "Tous"); 
  while ($etudiant = $db->objetSuivant($rEtudiant)) {
     $nom_master = $masters[$etudiant->id_master];
      $etudiants[$etudiant->id] = 
    "$etudiant->nom $etudiant->prenom (Master $nom_master) "; 
  }

  // Affichage des listes de selection
  $tpl->set_var ("LIST_MASTER_ETUDIANT", 
		 $fMasters->champSELECT ("master_etudiant", $masters, 
				       $prefMasterEtudiant, 1)); 
  $tpl->set_var ("LIST_ETUDIANTS", 
		 $fMasters->champSELECT ("etudiant", $etudiants, 
				       $prefEtudiant, 1)); 
 
 // On regarde quelle est la liste des cours à afficher
 $cours_apprentissage = false;
   if ($prefMasterEtudiant != '%') {
	   if ($prefMasterEtudiant == 2 
		   or $prefMasterEtudiant == 4  
		   or $prefMasterEtudiant == 6)
		   $cours_apprentissage = true;
   }
  if ($prefEtudiant != '%' and !empty($prefEtudiant)) {
      $etudiant  = GetPersonne ($prefEtudiant, $db);
	  if ($etudiant->id_master == 2 
		  or $etudiant->id_master == 4 
		  or $etudiant->id_master ==  6)
		  $cours_apprentissage = true;
  }

  /* Stockage de la liste des cours dans un tableau */
  if ($cours_apprentissage)
	  $liste_cours = " (2, 4, 6) ";
  else
	  $liste_cours = " (1, 3, 5) ";
  $query = "SELECT o.*,c.obligatoire,c.id_enseignant,c.id_contenu,o.ects, "
    .  " c.id_master, c.notes, o.volume_horaire, o.volume_projet "
    . " FROM Cours c, Contenu o "
    . " WHERE c.id_contenu=o.id AND id_master IN $liste_cours order by nom";
 
  $tabcours = array(); 
  $rCours = $db->execRequete($query); 
  $nb_cours = 0; 
  while ($un_cours = $db->objetSuivant($rCours)) {
    $nb_cours++; 
    $tabcours[$un_cours->id_contenu] = $un_cours->id_master; 
  } 
 
  // Pareil pour les étudiants
  $student_role = STUDENT_ROLE;
  $query =  "SELECT * FROM Personne p "
  . "   WHERE roles LIKE '%$student_role%' "
    .   " AND id_master LIKE '$prefMasterEtudiant'  AND annee_master = " . ANNEE_MASTER 
    .   " AND id LIKE '$prefEtudiant' ORDER BY nom, prenom "; 

  $etudiants=array(); 
  $result = $db->execRequete ($query); 
  $nb_etudiants = 0; 
  while ($etudiant = $db->objetSuivant($result)) { 
    $nb_etudiants++; 
    $etudiants[$etudiant->id] = "$etudiant->nom";
  } 
  
  // Allons-y pour créer la table. D'abord les entêtes de colonne
  foreach ($tabcours as $id_contenu => $id_master) {
    $cours = GetCours ($id_master, $id_contenu, $db, "object") ;
    InstanciateCoursVars ($cours, $tpl, $db); 
    $tpl->set_var("COURS_NB_INSCRITS", 
		  CountInscrits ($id_master, $id_contenu, $db)) ;
    $tpl->parse("COURS", "COURS_DETAIL", true); 
  } 
 
  // Puis chaque ligne
  $i = 0;
  foreach ($etudiants as $id_etudiant => $nom_etudiant)  { 
    $i++;
    // Choose the CSS class 
    if ($i%2 == 0) 
      $tpl->set_var("CSS_CLASS", "even"); 
    else 
	$tpl->set_var("CSS_CLASS", "odd"); 
    
    $etudiant  = GetPersonne ($id_etudiant, $db);
    InstanciatePersonVars ($etudiant, $tpl, $db); 
    $nb_ects = CountECTS ($id_etudiant, $db) ;
    $tpl->set_var ("ETUDIANT_NB_ECTS", $nb_ects);

    // On recherche les préférences pour chaque cours
    reset ($tabcours);
    foreach ($tabcours as $id_contenu => $id_master) { 
      $pref = GetPref ($id_master, $id_contenu, $id_etudiant, $db);
        $tpl->set_var ("COURS_ID", "$id_master;$id_contenu");
        $tpl->set_var("BG_COLOR", "white"); 
	  if (is_object($pref)) {
        $tpl->set_var("PREF_COURS", $pref->niveau);
	  }
	  else {
        $tpl->set_var("PREF_COURS", "");
	  }
      $tpl->set_var("CHECKED_YES", ""); 
      $tpl->set_var("CHECKED_NO", "checked"); 
      
      // On regarde si l'étudiant est inscrit
      $inscrit = GetInscrit ($id_master, $id_contenu, $id_etudiant, $db);
      if ($inscrit) { 
	$tpl->set_var("BG_COLOR", "yellow"); 
	$tpl->set_var("CHECKED_YES", "checked"); 
	$tpl->set_var("CHECKED_NO", ""); 
      } 
      
      // Add to the assignment line 
      $tpl->parse("ASSIGNMENTS", "ASSIGNMENT_DETAIL", true); 
    } 
    // Add to the list of papers 
    $tpl->parse("ETUDIANTS", "ETUDIANT_DETAIL", true); 
    $tpl->set_var("ASSIGNMENTS", ""); 
  } 
  $tpl->parse ("BODY", "affectation");
}

$tpl->pparse ("RESULT", "Page");

?>
