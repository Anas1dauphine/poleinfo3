<?
//require_once("IhmBD.class.php");

// Classe étendant IhmBD, spécialisée pour la table Person

class IhmCandidature extends IhmBD
{
  // Le constructeur de la classe. Attention à bien penser
  // à appeler le constructeur de la super-classe.
  
  function IhmCandidature($nomTable, $bd, $script="moi")
  {
    global $TEXTS;

    // Appel du constructeur de IhmBD
    parent::IhmBD($nomTable, $bd, $script);
  
     $this->masters_choisis = array();
    // On peut placer les entêtes dès maintenant
    $this->setEntete("nom", "Nom");
    $this->setEntete("prenom", "Prénom");
    $this->setEntete("email", "Email");
    $this->setEntete("masters", "Indiquez les masters auxquels<br/>vous souhaitez candidater (à titre indicatif)");
    $this->setEntete("telephone", "Téléphone");
	 $this->setEntete("code_postal", "Code postal");
	 	 $this->setEntete("ville", "Ville");
	 	 $this->setEntete("conf_email", "Confirmation de l'email");
	 $this->setEntete("pays", "Pays");

	 $this->setEntete("diplome", "Votre diplôme");
	 $this->setEntete("etablissement", "Etablissement du diplôme");

	 $this->setEntete("ville_diplome", "Ville du diplôme");
	 $this->setEntete("pays_diplome", "Pays du diplôme");

    $this->setEntete("mois_obtention", "Mois d'obtention du diplôme");
    $this->setEntete("annee_obtention", "Année d'obtention du diplôme");
    $this->setEntete("adresse", "Adresse");
    $this->setEntete("conf_password", "Confirmation du mot de passe");
  }

  /*****************   Partie publique ********************/
  // Méthode effectuant des contrôles avant mise à jour 
  function controle(&$ligne, &$messages)
  {
	  if (empty($ligne['prenom']) or empty($ligne['nom'])) {
      $messages[]  = "Il faut entrer votre prénom et votre nom.";
	  }

    if (!isSet($ligne['masters']))
      $messages[]  = "Il faut choisir au moins un master.";
    if (empty($ligne['adresse']))  $messages[]  = "Adresse incomplète. Indiquez numéro et rue";
    if (empty($ligne['ville']))  $messages[]  = "Adresse incomplète. Indiquez une ville";
    if (empty($ligne['code_postal']))  $messages[]  = "Adresse incomplète. Indiquez un code postal";
    if (empty($ligne['pays']))  $messages[]  = "Adresse incomplète. Indiquez un pays";
    if (empty($ligne['diplome']))  $messages[]  = "Il faut indiquer un diplôme";
    if (empty($ligne['etablissement']))  $messages[]  = "Il faut indiquer un etablissement";
    if (empty($ligne['pays_diplome']))  $messages[]  = "Il faut indiquer le pays du diplôme";
    if (empty($ligne['ville_diplome']))  $messages[]  = "Il faut indiquer la ville du diplôme";

    // On vérifie que les champs importants ont été saisis
    if (empty($ligne['email']))
      $messages[]  = "Il faut entrer un email";
    else if (!ControleEmail($ligne['email']))
      $messages[] = "L'email doit être de la forme xxx@yyy[.zzz] !<BR>";
    else if ($this->chercheCandidat ($ligne['email']))
      $messages[] = "Cet email est déjà référencé: vous ne pouvez pas l'utiliser à nouveau<BR>";


	if (empty($ligne['conf_email'])
	    or $ligne['email'] != $ligne['conf_email'])
	  $messages[]= "Il faut entrer un email et le confirmer!<BR>";

    // On traitertoutes les chaînes des attributs
    foreach ($this->schemaTable as $nom => $options) 
      {
	if (!isSet($this->auto_increment_key[$nom])) {
	  // Transformation des ' en \'
	  $ligne[$nom] = $this->bd->prepareString($ligne[$nom]);
	}
      }

	if (count($messages) == 0) 
      return true;
    else
      return false;
  }

  // Rédéfinition du formulaire
  function formulaire ($action, $ligne)
  {
    // Création de l'objet formulaire
    $form = new Formulaire ("POST", $this->nomScript, false);
    $form->setTitle ($this->nomTable);

    $masters = array();
    $result = $this->bd->execRequete 
      ("SELECT id, nom FROM Master WHERE id <= 6");
    while ($cursor = $this->bd->ligneSuivante ($result)) {
      $masters[$cursor["id"]] = $cursor["nom"];
	  if ($cursor['id'] == 1 or $cursor['id'] == 3 or $cursor['id'] == 5)
		  $masters[$cursor["id"]] .= " (et formation continue) ";
	  }

    $liste_genres = array("F" => "Madame", "M" => "Monsieur");
    $yesno = array ("Y" => "Oui", "N" => "Non");
    $mois = array ("1" => "Janvier", "2" => "Février",
		                  "3" => "Mars", "4" => "Avril",
			              "5" => "Mai", "6" => "Juin",
		                  "7" => "Juillet", "8" => "Août", "9" => "Septembre",
			              "10" => "Octobre", "11" => "Novembre", "12"=> "Décembre");
   for ($a = 1975; $a <= ANNEE_MASTER + 1; $a++)
	   $liste_annees[$a] = $a;

    $form->champCache ("action", $action);

    // Pas de mise à jour? On calcule la valeur de l'id
    if ($action != MAJ_BD) {
    	$ligne['id'] = $this->GetNextCandidatID($this->bd);
	    $ligne['annee_obtention'] = ANNEE_MASTER;
			    $ligne['mois_obtention'] = 6;
      }

	 if (!isSet($ligne['masters']))
		$masters_choisis = array(1 => "1",  3 => "3", 5 => "5");
	  else {
		  // echo "LIgn : " . $ligne['masters'] . "<br>";
		  $masters_explode = explode (";", $ligne['masters']);
		foreach ($masters_explode as $m)	$masters_choisis[$m] = 1;
	  }
	  // print_r($masters_choisis);

    $form->champCache ("id",  $ligne['id']);
    $form->champCache ("annee_master",  ANNEE_MASTER);
    $form->champCache ("affectation",  "0");

    $form->debutTable (VERTICAL,array(),$nbLignes=1, 
		       "Formulaire de candidature - Master professionnel Informatique Dauphine");

    // Vérifier que la valeur par défaut existe
    foreach ($this->schemaTable as $nom => $options)
      if (!isSet($ligne[$nom])) $ligne[$nom] = "";

    $form->champListe ("Genre", "genre", 
		       $ligne['genre'], 1, $liste_genres);

    $form->champTexte ($this->entetes['nom'], "nom",
		       $ligne['nom'], 30, 60);
    $form->champTexte ($this->entetes['prenom'], "prenom",
		       $ligne['prenom'], 30, 60);

    $form->champCheckbox ($this->entetes['masters'], "masters[]", 
		       $masters_choisis,  $masters, 3);    
    $form->champTexte ($this->entetes['email'], "email",
		       $ligne['email'], 30, 60);
    $form->champTexte ($this->entetes['conf_email'], "conf_email",
		       "", 30, 30);
    $form->champTexte ($this->entetes['telephone'], "telephone",
		       $ligne['telephone'], 20, 20);
    $form->champFenetre ($this->entetes['adresse'], "adresse",
		       $ligne['adresse'], 3, 60);
 $form->champTexte ($this->entetes['ville'], "ville", $ligne['ville'], 60, 60);
 $form->champTexte ($this->entetes['code_postal'], "code_postal", $ligne['code_postal'], 10);
 $form->champTexte ($this->entetes['pays'], "pays", $ligne['pays'], 50);
 $form->champTexte ($this->entetes['diplome'], "diplome", $ligne['diplome'], 50);
 $form->champTexte ($this->entetes['etablissement'], "etablissement", $ligne['etablissement'], 50);
 $form->champTexte ($this->entetes['ville_diplome'], "ville_diplome", $ligne['ville_diplome'], 60, 60);
 $form->champTexte ($this->entetes['pays_diplome'], "pays_diplome", $ligne['pays_diplome'], 50);
    $form->champListe ($this->entetes['mois_obtention'], "mois_obtention", 
		       $ligne['mois_obtention'], 1, $mois);
	    $form->champListe ($this->entetes['annee_obtention'], "annee_obtention", 
		       $ligne['annee_obtention'], 1, $liste_annees);

    $form->finTable();
	
    if ($action == MAJ_BD)
      $form->champValider ("Modifier", "submit");
    else
      $form->champValider ("Valider", "submit");
    
    return $form->formulaireHTML();
  }

  // Formulaire simplifié
  function formulaire_simple ($action, $ligne)
  {
    // Création de l'objet formulaire
    $form = new Formulaire ("POST", $this->nomScript, false);
    $form->setTitle ($this->nomTable);

    $liste_genres = array("F" => "Madame", "M" => "Monsieur");
    $yesno = array ("Y" => "Oui", "N" => "Non");

    $form->champCache ("action", $action);

    // Pas de mise à jour? On calcule la valeur de l'id
    if ($action != MAJ_BD) {

    	$ligne['id'] = $this->GetNextCandidatID($this->bd);
    	$ligne['genre'] = "M";
		$def_mail = "inconnu" . $ligne['id'] . "@dauphine.fr";
        $form->champCache ("affectation",  "0");
      }
		else {
		$def_mail = $ligne['email'];
         $form->champCache ("affectation", $ligne['affectation']);
		}

	$id_cand= $ligne['id'];

    $form->champCache ("nouveau_candidat",  1);
    $form->champCache ("id",  $ligne['id']);
    $form->champCache ("annee_master",  ANNEE_MASTER);
    $form->champCache ("annee_obtention",  ANNEE_MASTER);
    $form->champCache ("mois_obtention",  6);
    $form->champCache ("diplome",  "null");
    $form->champCache ("etablissement",  "null");
    $form->champCache ("ville_diplome",  "null");
    $form->champCache ("pays_diplome",  "null");
    $form->champCache ("telephone",  "null");
    $form->champCache ("masters",  "1");

    $form->debutTable (VERTICAL,array(),$nbLignes=1, 
		       "Formulaire de candidature - Master professionnel Informatique Dauphine");

    // Vérifier que la valeur par défaut existe
    foreach ($this->schemaTable as $nom => $options)
      if (!isSet($ligne[$nom])) $ligne[$nom] = "";

    $form->champListe ("Genre", "genre", $ligne['genre'], 1, $liste_genres);
    $form->champTexte ($this->entetes['nom'], "nom",  $ligne['nom'], 30, 60);
    $form->champTexte ($this->entetes['prenom'], "prenom", $ligne['prenom'], 30, 60);
    $form->champTexte ($this->entetes['email'], "email", $def_mail, 30, 60);
    $form->champTexte ($this->entetes['conf_email'], "conf_email", $def_mail, 30, 30);
    $form->champFenetre ($this->entetes['adresse'], "adresse", $ligne['adresse'], 3, 60);
 $form->champTexte ($this->entetes['ville'], "ville", $ligne['ville'], 60, 60);
 $form->champTexte ($this->entetes['code_postal'], "code_postal", $ligne['code_postal'], 10);
 $form->champTexte ($this->entetes['pays'], "pays", $ligne['pays'], 50);

    $form->finTable();
	
    if ($action == MAJ_BD)
      $form->champValider ("Modifier", "submit");
    else
      $form->champValider ("Valider", "submit");
    
    return $form->formulaireHTML();
  }


  function GetNextCandidatID ($db)
  {
    $result = $db->execRequete ("SELECT Max(id)+1 AS id FROM Candidat");
    $o = $db->objetSuivant ($result);
    return $o->id;
  }

  function chercheCandidat ($email)
  {
	  $email = addSlashes($email);
    $result = $this->bd->execRequete ("SELECT * FROM Candidat WHERE email='$email'");
    return $this->bd->objetSuivant ($result);
  }

  // Fonction créant une interface avec saisie, mise à jour
  // et consultation
  function GUICandidat ($paramsHTTP, &$tpl)
  {
    echo "<script language='JavaScript1.2' src='ShowWindow.js'></script>";
 
    // A-t-on demandé une action?
    if (isSet($paramsHTTP['action']))
      $action = $paramsHTTP['action'];
    else 
      $action = "";

    $affichage = "";
    switch ($action)
      {
      case INS_BD:
	// On a demandé une insertion
	if (isSet($paramsHTTP['masters'])) {
		// Conversion des mastes en chaîne
		$paramsHTTP['masters'] = implode (";", $paramsHTTP['masters']);
	}

	if ($this->insertion($paramsHTTP))
	  {
		$tpl->parse("BODY", "page_dossier");
		return;
	  }
	else{
	  $affichage .= $this->formulaire(INS_BD, $paramsHTTP);
	}
	break;


      default:
	$affichage .= $this->formulaire(INS_BD,array());

      }
   $tpl->set_var("FORM_CANDIDATURE", $affichage);
   $tpl->parse ("BODY", "candidature");
  }

  // Fonction de mise à jour  d'une ligne
  function maj ($ligne)
  {
    // Initisalisations
    $listeAffectations = $virgule = "";


    // Parcours des attributs pour créer la requête
    foreach ($this->schemaTable as $nom => $options)
      {
	// Création de la clause WHERE
	$clauseWhere = $this->accesCle($ligne, "SQL");
	// Création des affectations nom='valeur'
	if (!$options['cle_primaire'])
	  {
	    $listeAffectations .= $virgule . "$nom='" . $ligne[$nom] . "'";   
	    // A partir du second, on sépare par des virgules 
	    $virgule= ",";
	  }
	else
	  $id_master = $ligne[$nom];
      }

    $requete = "UPDATE $this->nomTable SET $listeAffectations "
      . "WHERE $clauseWhere";

	$this->bd->execRequete ($requete);
    return true;
  }


}
?>
