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
$tpl->set_file ( array ("candidature" => TPLDIR . "candidature.tpl", 
                        "page_dossier" => TPLDIR . "page_dossier.tpl",
			"page" => TPLDIR . "Page.tpl"));

$tpl->set_var("TITLE", "Dossier d'amission");

// Logout required?
if (isSet($_GET['logout']))
{
  // Delete the current session
  $q = "DELETE FROM Session WHERE id_session='" . session_id() . "'";
  $db->execRequete($q);
}

// First check for access rights
// $session = CheckAccess ("intranet.php", $_POST, session_id(), $db, $tpl);

// if (is_object($session)) {

$ihm = new IhmCandidature ("Candidat", $db, "candidature.php");

$ihm->GUICandidat($_REQUEST, $tpl);

/*
$tpl->set_var("BODY", "Les candidatures ouvriront le premier mars");
*/

$tpl->pparse("RESULT", "page");

?>
