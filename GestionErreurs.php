<?php
// Définition d'un gestionnaire d'erreurs. 
// Elle affiche en français le message.
function GestionErreurs ($niveau_erreur, $message, 
			 $script, $no_ligne, $contexte=array())
{
  // Regardons quel est le niveau de l'erreur
  switch ($niveau_erreur)
    {
      // Les erreurs suivantes ne doivent pas être transmises ici!
    case E_ERROR: 
    case E_PARSE: 
    case E_CORE_ERROR:
    case E_CORE_WARNING:
    case E_COMPILE_ERROR:
    case E_COMPILE_WARNING:
      echo "Ca ne doit jamais arriver !!";
      exit;
      
    case E_WARNING: 
      $typeErreur = "Avertissement PHP";
      break;

    case E_NOTICE: 
      $typeErreur = "Remarque PHP";
      break;

    case E_STRICT: 
      $typeErreur = "Syntaxe obsolète PHP 5";
      break;

    case E_USER_ERROR: 
      $typeErreur = "Avertissement de l'application";
      break;

    case E_USER_WARNING: 
      $typeErreur = "Avertissement de l'application";
      break;

    case E_USER_NOTICE: 
      $typeErreur = "Remarque PHP";
      break;

    default:
      $typeErreur = "Erreur inconnue";
    }

  // Maintenant on affiche en rouge
  echo "<FONT COLOR='red'><B>$typeErreur</B> : " . $message
    . "<BR>Ligne $no_ligne du script $script</FONT><P>";

  // Erreur utilisateur? On stoppe le script.
  if ($niveau_erreur == E_USER_ERROR) exit;
}
?>