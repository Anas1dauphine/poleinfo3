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
			"preferences" => TPLDIR . "preferences.tpl"));

$tpl->set_block("preferences", "RATING_MESSAGE", "HELP_MESSAGE");
$tpl->set_block("preferences", "ACK_RATING_MESSAGE", "ACK_MESSAGE");
// Extract the 'block' describing a line from the template
$tpl->set_block("preferences", "PREF_DETAIL", "PREFS");
$tpl->set_var("PREFS", "");

$session = CheckAccess ("preferences.php", $_POST, session_id(), $db, $tpl);

if (is_object($session))
{
  $person = GetPersonne ($session->id_person, $db);

  // Préférences soumises? On insère 
  if (isSet($_POST['rates'])) {
    while (list ($id_cours, $rate) = each ($_POST['rates'])) {
      list ($id_master, $id_contenu) = explode (";", $id_cours);
      if ($rate != UNKNOWN_RATING) {
	// Insert or update a rate on a paper
	// Get the rate, if exists
	$rating = GetPref ($id_master, $id_contenu, $session->id_person, $db);
	if (!is_object($rating)) {
	    // Insert
	    $query = "INSERT INTO Preference (id_master, id_contenu, "
	      . " id_personne, niveau)"
	      . "VALUES ('$id_master', '$id_contenu', '$session->id_person', "
	      . " '$rate') ";
	}
	else  {
	    // Update
	    $query = "UPDATE Preference SET niveau='$rate' "
	  . " WHERE id_master = '$id_master' and id_contenu='$id_contenu' "
	  . " AND id_personne = '$session->id_person' "; 
	}
	$db->execRequete ($query);
      }
      $tpl->parse("ACK_MESSAGE", "ACK_RATING_MESSAGE");
      $tpl->set_var("HELP_MESSAGE", "");
    }
  }
  else {
    // Print the main message
    $tpl->set_var("ACK_MESSAGE", "");
    $tpl->parse("HELP_MESSAGE", "RATING_MESSAGE");
  }
  
  // Liste des niveaux
  $rates[0] = "Je n'en veux pas";
  $rates[1] = "Je pr&eacute;f&egrave;re ne pas avoir ce cours";
  $rates[2] = "Pourquoi pas";
  $rates[3] = "Je suis int&eacute;ress&eacute;(e)";
  $rates[4] = "Je veux ce cours";
  
  $form = new Formulaire ( "POST", "preferences.php");

  /* Liste des cours   */
  $query = "SELECT * FROM Cours c, Contenu c2 WHERE id_contenu=c2.id "
    . " AND id_master IN (1, 3, 5) " 
    . " ORDER BY id_master, obligatoire desc, c2.nom ";
  
  $i = 0;
  $result = $db->execRequete ($query);
  while ($cours = $db->objetSuivant($result))    {
      $i++;

      // Instanciate paper variables
      InstanciateCoursVars ($cours, $tpl, $db);
      
      // Choose the CSS class
      if ($i % 2 == 0)
	$tpl->set_var("CSS_CLASS", "even");
      else
	$tpl->set_var("CSS_CLASS", "odd");
      
      $tpl->set_var("SESSION_ID", session_id());
      
      $pref = GetPref ($cours->id_master, $cours->id_contenu, 
		       $session->id_person, $db);
      if (is_object($pref))
	$niveau = $pref->niveau;
      else if ($cours->id_master == $person->id_master
	       and $cours->obligatoire == 'O')
	$niveau = 4;
      else if ($cours->id_contenu=='15')
           $niveau = 4;
      else if ($cours->id_master == '1' and $person->id_master < 5 and $cours->id_contenu=='1')
        $niveau = 4;
      else if ($cours->id_master == '1' and $person->id_master < 5 and $cours->id_contenu=='3')
        $niveau = 4;
       else if ($cours->id_master == '3' and $person->id_master < 5 and $cours->id_contenu=='1')
        $niveau = 4;
      else if ($cours->id_master == '3' and $person->id_master < 5 and $cours->id_contenu=='3')
        $niveau = 4;
      else
	$niveau = UNKNOWN_RATING;
      
      $tpl->set_var("COURS_NIVEAU", 
		    $form->champSELECT 
		    ("rates[$cours->id_master;$cours->id_contenu]", 
		     $rates, $niveau, 1));
      
      /* Instanciate the entities in PAPER_DETAIL. Put the
               result in PAPERS   */
      $tpl->parse("PREFS", "PREF_DETAIL", true);
  }
  $tpl->parse("BODY", "preferences");
}
else
$tpl->set_var("BODY", "Accès non autorisé");

$tpl->pparse ("RESULT", "Page");

?>