<?php
session_start();

// Load the libraries
require_once ("Util.php");
require_once ("template.inc");
require_once ("Formulaire.class.php");
require_once ("IhmCandidature.class.php");

// Instanciate a template object
$tpl = new Template (".");

// Connect to the database
$db = new BDMySQL (NAME, PASS, BASE, SERVER);

// Load the required files and assign them to variables
$tpl->set_file ( array ("menu" => TPLDIR . "menu.tpl", 
			"intranet" => TPLDIR . "intranet.tpl",
			"etudiant" => TPLDIR . "etudiant.tpl",
			"page" => TPLDIR . "Page.tpl"));

$tpl->set_var("TITLE", "Intranet");

// Logout required?
if (isSet($_GET['logout']))
{
  // Delete the current session
  $q = "DELETE FROM Session WHERE id_session='" . session_id() . "'";
  $db->execRequete($q);
}

// First check for access rights
$session = CheckAccess ("intranet.php", $_POST, session_id(), $db, $tpl);

if (is_object($session))
{
	/*
	$res = $db->execRequete ("SELECT * FROM Personne WHERE annee_master=2008 and roles='S'");
	while ($cand = $db->objetSUivant($res)) {
       $p = trim(addSlashes(UCName($cand->prenom)));
	   $n = trim(addSlashes (UCName($cand->nom)));
	   $pass = strtolower($cand->prenom);
	   $db->execRequete ("UPDATE Personne SET prenom='$p', nom='$n', password='$pass' WHERE id='$cand->id' ");
	   echo "Update $p $n<br>";
	}
*/
      // Création de la liste des cours
      $q_cours = "SELECT id_master, id_contenu, m.nom, o.nom AS nom_contenu "
	. "FROM Master m, Cours c, Contenu o WHERE c.id_contenu=o.id "
	. " AND m.id=c.id_master ORDER BY id_master, nom";
      $r_cours = $db->execRequete($q_cours);
      $listeCours = array();
      while ($cours = $db->objetSuivant ($r_cours)) {
	$listeCours["$cours->id_master;$cours->id_contenu"]
	  = $cours->nom . " - " . $cours->nom_contenu;
      }
      $tpl->set_var ("LISTE_DES_COURS",
		     SelectField ("id_cours", $listeCours, 0));

  if (isSet($_REQUEST['option'])) {
      $option = $_REQUEST['option'];

	  	// Attention aux options réservées à l'admin
	if ($session->last_name != "admin" 
		AND ($option==1 OR $option==2 OR $option==3 OR $option==4 
		       OR $option==10 OR $option==11)) {
		  $tpl->set_var("BODY", "Accès refusé");
          $tpl->pparse("RESULT", "page");
		  exit;
	}

	 // Interdit de modifier les coord. si poleinfo3
	if ($session->last_name == "poleinfo3"  and $option==7) {
		  $tpl->set_var("BODY", "Action impossible: merci de contacter sans délai les responsables du site.");
          $tpl->pparse("RESULT", "page");
		  exit;
	}

      switch ($option)
	{
	case 1:
	  // Creation de l'interface sur la table Master
	  $ihm = new IhmBD ("Master", $db, "intranet.php?option=1");
	  $tpl->set_var("BODY", $ihm->genererIHM($_REQUEST));
	  break;

	case 2:
	  // Creation de l'interface sur la table Contenu
	  $ihm = new IhmBD ("Contenu", $db, "intranet.php?option=2");
	  $tpl->set_var("BODY", $ihm->genererIHM($_REQUEST));
	  break;

	case 3:
	  // Creation de l'interface sur la table Personne
	  $ihm = new IhmBD ("Personne", $db, "intranet.php?option=3");
	  $ihm->setWhereClause (" WHERE roles LIKE '%M%' ");
	  $ihm->setFormField("vacataire", BOOLEAN_FIELD, 
			     array());
	  $ihm->setFormField("roles", SELECT_FIELD, 
			     array("tb_name" => "Role", "id_name" => "code",
				   "name" => "intitule"));
	  $tpl->set_var("BODY", $ihm->genererIHM($_REQUEST));
	  break;

	case 4:
	  // Creation de l'interface sur la table Cours
	  $ihm = new IhmBD ("Cours", $db, "intranet.php?option=4");
	  $ihm->setEntete ("id_contenu", "Contenu");
	  $ihm->setEntete ("id_enseignant", "Enseignant");
	  $ihm->setEntete ("id_master", "Master");
	  $ihm->setFormField("id_master", SELECT_FIELD, 
			     array("tb_name" => "Master", "id_name" => "id",
				   "name" => "nom"));
	  $ihm->setFormField("id_contenu", SELECT_FIELD, 
			     array("tb_name" => "Contenu", "id_name" => "id",
				   "name" => "nom"));
	  $ihm->setFormField("id_enseignant", SELECT_FIELD, 
			     array("tb_name" => "Personne", 
				   "id_name" => "id",
				   "name" => 
				   "CONCAT('<a href=\'mailto:', email, '\'>', 
                                   nom, ' ', 
                                   prenom, '</a>')"),
			     " WHERE roles LIKE '%M%' ");
	  $ihm->setFormField("obligatoire", CHECKBOX_FIELD, $YESNO);
	  $tpl->set_var("BODY", $ihm->genererIHM($_REQUEST));
	  break;

	case 5:
	  // Liste des cours
	  $id_master = $_GET['id_master'];
	  ListeCoursMaster ($id_master, 0, $db, $tpl);
	  break;

	case 6:
	  // Formulaire de saisie des donn?es personnelles
	  $ihm = new IhmPerson ("Personne", $db, "intranet.php?option=6");
	  $tpl->set_var("BODY", $ihm->GUIStudent( $_REQUEST));
	  break;
	  
	case 7:
	  // Formulaire de mise ? jour des donn?es personnelles
	  $ihm = new IhmPerson ("Personne", $db, "intranet.php?option=6");
	  $person = $ihm->chercheLigne(array("id" => $session->id_person));
	  $tpl->set_var("BODY", $ihm->formulaire(MAJ_BD, $person));
	  break;
	  
	case 8:
	  // Liste des ?tudiants
	  if (isSet ($_REQUEST['id_master'])) {
	    $id_master = $_REQUEST['id_master'];
	    $id_contenu = 0;
	  }
	  else if (isSet ($_REQUEST['id_cours'])) {
	    list ($id_master, $id_contenu) = 
	      explode (";", $_REQUEST['id_cours']);
	  }
	  if ($session->last_name == "admin")
		   $template = "ListeAdminEtudiants.tpl";
	  else
		   $template = "ListeEtudiants.tpl";

	  // echo "Id master = $id_master, id_contenu=$id_contenu<br>";
	  ListeEtudiants ($id_master, $id_contenu, $db, $tpl, $template);
	  break;

	case 9:
	  // Liste des cours pour un ?tudiant donn?
	  ListeCoursMaster (0, $_REQUEST['id_etudiant'], $db, $tpl, 
			    "ListeCoursEtudiant.tpl");
	  break;

	case 10:
	  // Liste des enseignants
          if (isSet($_REQUEST['vacataire']))
           $vacataire = true;
          else
            $vacataire=false;
	  ListeEnseignants ($db, $tpl, "ListeEnseignants.tpl", $vacataire);
	  break;

	  // Gestion des candidatures
	case 11:
		$ok = true;
   	  $ihm = new IhmCandidature ("Candidat", $db, "intranet.php?option=11");
	  // Insertion d'un nouveau candidat
	   if (isSet($_REQUEST['nouveau_candidat'])) {
		  if ($_REQUEST['action'] == INS_BD)
        	  $ok = $ihm->insertion($_REQUEST);
		  else {
			  echo "MAJ";
        	  $ok = $ihm->maj($_REQUEST);
		  }
	  }
      else if (isSet($_REQUEST['modifier'])) {
		  $ancre = "<p><a href='intranet.php?option=11'>Retour à la liste</a></p>";
		$cand = $ihm->chercheLigne ($_REQUEST);

        $tpl->set_var("BODY", $ancre . $ihm->formulaire_simple(MAJ_BD, $cand));
		break;
  	  }
	  if (isSet($_REQUEST['saisie']) or !$ok) {
		  $ancre = "<p><a href='intranet.php?option=11'>Retour à la liste</a></p>";
        $tpl->set_var("BODY", $ancre . $ihm->formulaire_simple(INS_BD, $_REQUEST));
		break;
	  }
   
   //print_r($_REQUEST);
   //echo "<br>";

		  // Liste des cours pour un étudiant donné
      if (isSet($_REQUEST['exporter'])) 
        $template = "ExcelCandidats.tpl";
        else $template = "ListeCandidats.tpl";
	  ListeCandidats ($db, $tpl,  $template);
	  
	   if (isSet($_REQUEST['exporter'])) {
//		   echo "Export YES<br>";
	    Export ("candidats.xls", $tpl->get_var("BODY"));
	    exit;
	   }
	  break;
	}
  }
  else {
    if ($session->last_name == "admin") {
      $tpl->parse("BODY", "menu");
    }
    else     if ($session->last_name == "poleinfo3")
      $tpl->parse("BODY", "intranet");
    else {
      ListeCoursMaster (0, $session->id_person, $db, $tpl, 
			"ListeCoursEtudiant.tpl",
			"LISTE_COURS");
      $tpl->parse("BODY", "etudiant");
    }
  }
}
$tpl->pparse("RESULT", "page");

?>
