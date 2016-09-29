<?php

// Load the libraries
require_once ("Util.php");
require_once ("template.inc");
require_once ("Formulaire.class.php");

// Instanciate a template object
$tpl = new Template (".");

// Connect to the database
$db = new BDMySQL (NAME, PASS, BASE, SERVER);

// Load the required files and assign them to variables
$tpl->set_file ( array ("cours" => TPLDIR . "ShowCours.tpl") );

$tpl->set_var("TITLE", "Cours");

$id_master = $_REQUEST['id_master'];
$master = GetMaster($id_master, $db);
$tpl->set_var("MASTER_NOM", $master->nom);

$id_contenu = $_REQUEST['id_contenu'];
$cours = GetCours ($id_master, $id_contenu, $db, "object"); 

if (!is_object($cours))
     echo "ERREUR<br>";
     else
     InstanciateCoursVars ($cours, $tpl, $db);

if (!isSet($_REQUEST['feuille']))
 $tpl->pparse("RESULT", "cours");
else {
  ListeEtudiants ($id_master, $id_contenu, $db, $tpl, "feuille.tpl");
  $tpl->pparse("RESULT", "BODY");
}
?>
