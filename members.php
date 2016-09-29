<?php
session_start(); 

// Load the libraries
require_once ("Util.php");
require_once ("template.inc");
require_once ("Formulaire.class.php");

// Instanciate a template object
$tpl = new Template (".");

// Connect to the database
$db = new BDMySQL (NAME, PASS, BASE, SERVER);

// Load the required files and assign them to variables
$tpl->set_file ( array ("members" => TPLDIR . "Members.tpl"));

if (isSet($_REQUEST['id_person']))
{
  // To do: present the lab. member
  $tpl->set_var("members", "TODO");
}
else
{
  $tpl->set_block("members", "MEMBER", "MEMBERS");
  $tpl->set_var("MEMBERS", "");
  $query = "SELECT * FROM Person p "
    . "WHERE roles LIKE '%M%' ORDER BY last_name ";
  $res = $db->execRequete($query);
  while ($p = $db->objetSuivant($res))
    {
      InstanciatePersonVars ($p, &$tpl, $db);
      $tpl->parse ("MEMBERS", "MEMBER", true);
    }
}

$tpl->pparse("RESULT", "members");



?>
