<?php

function ListeCoursMaster ($id_master, 
			   $id_etudiant, 
			   $db, &$tpl, 
			   $template="ListeCours.tpl",
			   $entity="BODY")
{
  $tpl->set_file ("ListeCours",TPLDIR . $template);
  $tpl->set_block ("ListeCours", "COURS", "LES_COURS");
  $tpl->set_var("LES_COURS", "");

  $nb_ects = 0;
  $nb_heures = 0;
  $nb_heures_projet = 0;
  
  if ($id_etudiant == 0)
    $query = "SELECT o.*, c.obligatoire, c.id_enseignant, c.id_contenu,  "
     .  " c.id_master, c.notes FROM Cours c, Contenu o"
      . " WHERE c.id_master='$id_master' AND c.id_contenu=o.id order by obligatoire DESC, o.nom";
  else {
    $person = GetPersonne ($id_etudiant, $db);
    InstanciatePersonVars ($person, $tpl, $db);
    $annee_master = ANNEE_MASTER;
    $id_master = $person->id_master;
    $query = "SELECT o.*, a.*, 0 as id_enseignant, 'xx' as notes FROM  Contenu o,  "
      . " Affectation a WHERE a.id_personne='$person->id' AND "
      . " a.id_contenu=o.id "
      . " AND annee='$annee_master' order by o.nom ";
  }

  $master = GetMaster ($id_master, $db);
  $tpl->set_var("MASTER_NOM", $master->nom);
  $res = $db->execRequete ($query);
  $liste_emails = ""; $comma='';
  $css_class="even";
  while ($pub = $db->objetSuivant($res))
    {
    // Recherche de l'enseignant qui assure le cours pour ces masters
	// A REVOIR
    if ($id_master == 1 or $id_master==3 or $id_master==5 or $id_master==7)
     $r_ens = $db->execRequete ("SELECT * FROM Cours "
     . " WHERE id_master IN (1, 3, 5, 7) AND id_contenu='$pub->id_contenu' ");
    else {
     $r_ens = $db->execRequete ("SELECT * FROM Cours "
     . " WHERE id_master IN (2, 4, 6) AND id_contenu='$pub->id_contenu' ");
}

      $cours = $db->objetSuivant ($r_ens);
     $pub->id_enseignant = $cours->id_enseignant;

      if ($css_class=="even") $css_class="odd";
      else $css_class="even";

      $enseignant = GetPersonne ($pub->id_enseignant, $db);
      if (!strstr($liste_emails, "$comma$enseignant->email")) {
	$liste_emails .= "$comma$enseignant->email";
	$comma=",";
      }
      $nb_ects += $pub->ects;
      $nb_heures += $pub->volume_horaire;
      $nb_heures_projet += $pub->volume_projet;
      InstanciateCoursVars ($pub, $tpl, $db);
      $tpl->set_var("CSS_CLASS", $css_class);
      $tpl->set_var("COURS_EFFECTIF", 
	CountInscrits ($pub->id_master, $pub->id_contenu, $db)	);
      $tpl->parse ("LES_COURS", "COURS", true);
    }
  $tpl->set_var("LISTE_EMAILS", $liste_emails);
  $tpl->set_var("TOTAL_ECTS", $nb_ects);
  $tpl->set_var("TOTAL_HEURES", $nb_heures);
  $tpl->set_var("TOTAL_HEURES_PROJET", $nb_heures_projet);
  $tpl->parse ($entity, "ListeCours");

}

function ListeEtudiants ($id_master, $id_contenu, $db, &$tpl, 
			 $template="ListeEtudiants.tpl")
{
  $tpl->set_file ("ListeEtudiants",TPLDIR . $template);
  $tpl->set_block ("ListeEtudiants", "ETUDIANT", "LISTE_ETUDIANTS");
  $tpl->set_var("LISTE_ETUDIANTS", "");

  if ($id_master == 0)
    $tpl->set_var("TITRE_LISTE", "Pôle Info 3");
  else if ($id_contenu == 0) {
    $master = GetMaster ($id_master, $db);
    $tpl->set_var("TITRE_LISTE", "Master " . $master->nom);
  }
  else {
    $master = GetMaster ($id_master, $db);
    $cours = GetCours ($id_master, $id_contenu, $db, "object");
    $tpl->set_var("TITRE_LISTE", "Cours $master->nom - $cours->nom");
  }

  $annee = ANNEE_MASTER;
  $student_role = STUDENT_ROLE;

  if ($id_contenu == 0) 
    $query = "SELECT * FROM Personne "
      . " WHERE roles LIKE '%$student_role%' AND annee_master='$annee' "
      . " AND (id_master='$id_master' OR $id_master=0) ORDER BY nom, prenom ";
  else
    $query = "SELECT p.* FROM Personne p, Affectation a"
      . " WHERE roles LIKE '%$student_role%' AND annee_master='$annee' AND a.annee='$annee' "
      . " AND id_contenu='$id_contenu' "
      . " AND p.id=a.id_personne "
      . " AND (  ($id_master IN (1, 3, 5, 8) AND p.id_master IN (1, 3, 5, 8)) "
      . "    OR  ($id_master IN (2,4,6) AND p.id_master IN (2,4,6)) )  "
      . " ORDER BY nom, prenom ";
    
  $res = $db->execRequete ($query);
  $liste_emails = ""; $comma='';
  $css_class="even";
  while ($et = $db->objetSuivant($res))    {
      if ($css_class=="even") $css_class="odd";
      else $css_class="even";
      
      $master = GetMaster ($et->id_master, $db);
      $tpl->set_var("MASTER_NOM", $master->nom);

      if (!strstr($liste_emails, "$comma$et->email")) {
	$liste_emails .= "$comma$et->email";
	$comma=",";
      }
      InstanciatePersonVars ($et, $tpl, $db);
      $tpl->set_var("CSS_CLASS", $css_class);
      $tpl->parse ("LISTE_ETUDIANTS", "ETUDIANT", true);
    }
  $tpl->set_var("LISTE_EMAILS", $liste_emails);
  $tpl->parse ("BODY", "ListeEtudiants");
}


//  Liste des candidats
function ListeCandidats (&$db, &$tpl, $template="ListeCandidats.tpl")
{
	define ("NULL_CRITERIA", "-1");

	// Create the list of anchors
  $res = $db->execRequete ( "SELECT * FROM Candidat ORDER BY nom");
      $first = ""; $list_letters = array("" => "Toute lettre");
  while ($cand = $db->objetSuivant($res))    {
    if ($first != substr (ucfirst($cand->nom), 0, 1))
 	  {
 	  $first = substr (trim($cand->nom), 0, 1);
	  $list_letters[$first] = $first;
	  }
  }

  // Gestion des critères de sélection
  if (isSet($_REQUEST['statut']))  $def_statut = $_REQUEST['statut'];
  else $def_statut = NULL_CRITERIA;
 if (isSet($_REQUEST['cand_master']))  $def_master = $_REQUEST['cand_master'];
  else $def_master = NULL_CRITERIA;
 if (isSet($_REQUEST['letter']))  $def_letter = $_REQUEST['letter'];
  else $def_letter = "A"; 

 $tpl->set_var("DEF_MASTER", $def_master);
 $tpl->set_var("DEF_STATUT", $def_statut);
 $tpl->set_var("DEF_LETTER", $def_letter);
  
  if (isSet($_REQUEST['choix_affectation'])) {
    // Mise à jour des affectations
    foreach ($_REQUEST['choix_affectation'] as $id_cand => $id_master) {
       $genre =  $_REQUEST['genre'][$id_cand];
      $db->execRequete ("UPDATE Candidat SET affectation='$id_master', genre='$genre' WHERE id='$id_cand'");
    }
  }
  
  if (isSet($_REQUEST['choix'])) {
  	$choix = $_REQUEST['choix'];
    $id_cand = $_REQUEST['candidat_id'];
  	if ($choix == "supprimer") {
       $db->execRequete ("DELETE FROM Candidat WHERE id='$id_cand'");
    }
  }
  
  $tpl->set_file ("ListeCandidats", TPLDIR . $template);
  $tpl->set_block ("ListeCandidats", "CANDIDAT", "LISTE_CANDIDATS");
  $tpl->set_var("LISTE_CANDIDATS", "");

   // alter table candidat add affectation INT
  $statuts = GetCodeList ("Master", $db, "id", "nom", 
 	    		array(0 => "Non affecté", 99 => "Attente SITN",101 => "Refusé")
					);
  $masters = GetCodeList ("Master", $db, "id", "nom", array(NULL_CRITERIA => "Tous"));
   $statuts_crit =  $statuts;
   $statuts_crit[-1] = "Tous";
   	                   
  $liste_genres = array("F" => "Madame", "M" => "Monsieur");
  
  $tpl->set_var("LISTE_LETTERS", SelectField ("letter", $list_letters, $def_letter));    	
  $tpl->set_var("LISTE_STATUTS", SelectField ("statut", $statuts_crit, $def_statut));

  $tpl->set_var("ANNEE_COURANTE", ANNEE_MASTER);
  $student_role = STUDENT_ROLE;

  // Création de la requête
  $where_clause = "WHERE 1=1 ";
  if ($def_master != NULL_CRITERIA) $where_clause .= " AND masters LIKE '%$def_master%'";
 if ($def_statut != NULL_CRITERIA) $where_clause .= " AND affectation= '$def_statut'";
 if ($def_letter != NULL_CRITERIA) $where_clause .= " AND nom LIKE '$def_letter%' ";
  $query = "SELECT c.* FROM Candidat c $where_clause " 
      . " ORDER BY nom, prenom ";
    // echo "Query = $query<br>";
  $res = $db->execRequete ($query);
  $liste_emails = ""; $comma='';
  $css_class="even"; $no=0;
  while ($cand = $db->objetSuivant($res))    {
  	  $no++;
  	  $tpl->set_var("CAND_NO", $no);
      if ($css_class=="even") $css_class="odd";
      else $css_class="even";
      
       if (!strstr($liste_emails, "$comma$cand->email")) {
	    $liste_emails .= "$comma$cand->email";
	    $comma=",";
      }
      
	  $tpl->set_var("LISTE_EMAILS", $liste_emails);

 	  $tpl->set_var("LISTE_CHOIX_MASTERS",
 	    SelectField ("choix_affectation[$cand->id]", $statuts, 
 	                   $cand->affectation));
 
     $tpl->set_var("LISTE_GENRES", SelectField ("genre[$cand->id]", 
		                     $liste_genres, $cand->genre));    	
  	                   
      InstantiateCandidateVars ($cand, $tpl, $db);
      $tpl->set_var("CSS_CLASS", $css_class);
      $tpl->parse ("LISTE_CANDIDATS", "CANDIDAT", true);
    }
  $tpl->set_var("LISTE_EMAILS", $liste_emails);
  $tpl->parse ("BODY", "ListeCandidats");
}

function ListeEnseignants ($db, &$tpl, 
			 $template="ListeEnseignants.tpl",
                          $vacataire=false)
{
  $tpl->set_file ("ListeEnseignants",TPLDIR . $template);
  $tpl->set_block ("ListeEnseignants", "ENSEIGNANT", "LISTE_ENSEIGNANTS");
  $tpl->set_var("LISTE_ENSEIGNANTS", "");

  $tpl->set_var("TITRE_LISTE", "Pï¿½le Info 3");

  if ($vacataire == false)
  $query = "SELECT * FROM Personne p WHERE roles LIKE '%M%' ORDER BY nom";
 else
  $query = "SELECT * FROM Personne p "
    . " WHERE roles LIKE '%M%' AND vacataire='Y' ORDER BY nom";
  $res = $db->execRequete ($query);
  $liste_emails = ""; $comma='';
  $css_class="even";
  while ($et = $db->objetSuivant($res))    {
      if ($css_class=="even") $css_class="odd";
      else $css_class="even";
      
      //      $master = GetMaster ($et->id_master, $db);
      // $tpl->set_var("MASTER_NOM", $master->nom);

      if (!strstr($liste_emails, "$comma$et->email")) {
	$liste_emails .= "$comma$et->email";
	$comma=",";
      }
      InstanciatePersonVars ($et, $tpl, $db);
      $tpl->set_var("CSS_CLASS", $css_class);
      $tpl->parse ("LISTE_ENSEIGNANTS", "ENSEIGNANT", true);
    }
  $tpl->set_var("LISTE_EMAILS", $liste_emails);
  $tpl->parse ("BODY", "ListeEnseignants");
}
?>
