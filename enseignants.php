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
$tpl->set_file ( array ("members" => TPLDIR . "Enseignants.tpl"));

$tpl->set_block("members", "MEMBER", "MEMBERS");
$tpl->set_block("MEMBER", "FC_ANCHOR", "ANCHOR");
$tpl->set_var("MEMBERS", "");
$query = "SELECT * FROM Personne p "
         . " WHERE nom NOT IN ('admin', 'poleinfo3') ORDER BY nom ";
$res = $db->execRequete($query);
$first = ""; $list_letters = ""; $sep="";
while ($p = $db->objetSuivant($res))
{
  if ($first != substr (ucfirst($p->nom), 0, 1))
    {
      $first = substr (ucfirst($p->nom), 0, 1);
      $list_letters .= "$sep <a href='#$first'>$first</a>";
      $sep = "|";
    }
}
$tpl->set_var("LIST_LETTERS", $list_letters);

$res = $db->execRequete($query);
$first = "";
while ($p = $db->objetSuivant($res))
{
  if ($first != substr (ucfirst($p->nom), 0, 1))
    {
      $first = substr (ucfirst($p->nom), 0, 1);
      $tpl->set_var("LETTER", $first);
      $tpl->parse ("ANCHOR", "FC_ANCHOR");
    }
  else
    $tpl->set_var ("ANCHOR", "");
  
  InstanciatePersonVars ($p, $tpl, $db);
  
  $rpc =$db->execRequete ("SELECT m.nom AS nom_master, c2.nom as nom_contenu "
		    . " FROM Personne p, Cours c1, Contenu c2, Master m"
		    . " WHERE p.id='$p->id' AND id_enseignant=p.id "
		    . " AND c1.id_master=m.id AND id_contenu=c2.id ");
  $liste_cours = "";
  while ($pc = $db->objetSuivant($rpc)) {
    $liste_cours .= "$pc->nom_contenu ($pc->nom_master), ";
  }
  $tpl->set_var("LISTE_COURS", $liste_cours);

  if (!empty($liste_cours))
    $tpl->parse ("MEMBERS", "MEMBER", true);
  
}
$tpl->pparse("RESULT", "members");

?>
