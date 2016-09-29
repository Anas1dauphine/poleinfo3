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
$tpl->set_file ( array ("menu" => TPLDIR . "menu.tpl", 
			"page" => TPLDIR . "Page.tpl"));

$tpl->set_var("TITLE", "Intranet");

if (isSet($_REQUEST['id_master']))
{
  $id_master = $_REQUEST['id_master'];
  if (isSet($_REQUEST['ascii']))
    ListeCoursMaster ($id_master, 0, $db, $tpl, "LesCoursAscii.tpl");
  else
    ListeCoursMaster ($id_master, 0, $db, $tpl, "LesCours.tpl");
}
else
{
  $tpl->parse("BODY", "menu");
}
$tpl->pparse("RESULT", "page");

?>