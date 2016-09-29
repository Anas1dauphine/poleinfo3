<?php

  define ('NOM',"admTypo3");
  define ('PASSE', "mdpTypo3");
  define ('SERVEUR', "l1.lamsade.dauphine.fr");
  define ('BASE', "typo3_test");

$connexion = mysql_pconnect (SERVEUR, NOM, PASSE);

if (!$connexion) {
  echo "Désolé, connexion à " . SERVEUR . " impossible\n";
  exit;
}

if (!mysql_select_db (BASE, $connexion)) {
  echo "Désolé, accès à la base " . BASE . " impossible\n";
  exit;
}

$resultat = mysql_query ("SELECT * FROM FilmSimple", $connexion);

if ($resultat) {
  while ($film = mysql_fetch_object ($resultat)) {
    echo "$film->titre, paru en $film->annee, réalisé "
      . "par  $film->prenom_realisateur $film->nom_realisateur.<br/>\n";
  }
}
else {  
  echo "<b>Erreur dans l'exécution de la requête.</b><br/>";
  echo "<b>Message de MySQL :</b> " .  mysql_error($connexion);
}  
?>