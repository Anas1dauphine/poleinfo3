<?php
// Constants
require_once ("DBInfo.php");
require_once ("Constant.php");

// HTML output
require_once ("HTML.php");  

// Modules and classes
// require_once ("Table.php");
require_once ("BD.class.php");
require_once ("BDMySQL.class.php");
require_once ("IhmBD.class.php");
require_once ("IhmPerson.class.php");
require_once ("CText.php");

// Misc functions
require_once ("GestionErreurs.php"); 
require_once ("GestionExceptions.php"); 
require_once ("functions.php"); 
require_once ("Session.php");
require_once ("Listes.php");

// Never escape quotes from external files: incompatible with templates
ini_set ("magic_quotes_runtime", "0");
ini_set ("magic_quotes_sybase", "0");

// If automatic escape is on: suppress the slashes. This
// makes the system independent from the magic_quotes_gpc value
// which changes so often...

if (get_magic_quotes_gpc())
{
  NormaliseHTTP($_POST); reset($_POST);
  NormaliseHTTP($_GET); reset($_GET);
  NormaliseHTTP($_REQUEST); reset($_REQUEST);
}

// Réglage du niveau d'erreur
error_reporting(E_ALL);

// Gestionnaire d'erreurs personnalisé. Voir GestionErreurs.php.
set_error_handler("GestionErreurs");

// Gestionnaire d'exceptions personnalisé. Voir GestionExceptions.php.
//set_exception_handler("GestionExceptions");

?>
