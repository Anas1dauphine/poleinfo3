<?php
session_start(); 

function DefVal ($entity, $name, &$tpl)
{
  if (isSet($_REQUEST[$name]))
    $tpl->set_var($entity, $_REQUEST[$name]); 
  else      $tpl->set_var($entity, "Any");
}

// Load the libraries
require_once ("Util.php");
require_once ("template.inc");
require_once ("Formulaire.class.php");

// Instanciate a template object
$tpl = new Template (".");

// Connect to the database
$db = new BDMySQL (NAME, PASS, BASE, SERVER);

// Load the required files and assign them to variables
$tpl->set_file ( array ("publis" => TPLDIR . "Publis.tpl"));

$tpl->set_var("TITLE", "Publications");
$tpl->set_block("publis", "RESULTAT");

$form = new Formulaire ("POST", "");

/************ Valeurs par défaut  **************/
Defval ("TITLE_DEF", "title", $tpl);
Defval ("ABSTRACT_DEF", "abstract", $tpl);
Defval ("AUTHOR_DEF", "author", $tpl);
Defval ("HOW_DEF", "how", $tpl);


$yearDef = -1;
$years[$yearDef] = "Any";
for ($y = YEAR_MIN; $y <= YEAR_MAX; $y++) $years[$y] = $y;
if (isSet($_REQUEST['year'])) $yearDef = $_REQUEST['year']; 
$tpl->set_var("YEARS_LIST", 
	      $form->getChamp($form->champListe 
		      ("Year", "year", $yearDef, 1, $years)));       


if (isSet($_REQUEST['type'])) $typeDef = $_REQUEST['type']; 
else $typeDef=NEUTRAL;
$types[NEUTRAL_CODE] = NEUTRAL;
$types = GetCodeList ("PubliType", $db, $id="id", $name="name", $types);
$tpl->set_var("TYPES_LIST", 
  $form->getChamp ($form->champListe ("Type", "type", 
		     $typeDef, 1, $types)));

if (isSet($_REQUEST['action']))
{
  // Execute the query
  PubliList ($_REQUEST, $db, $tpl);
}
else
$tpl->set_var("RESULTAT", "");

$tpl->pparse("RESULT", "publis");



?>
