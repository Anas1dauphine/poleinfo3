<?
//require_once("IhmBD.class.php");

// Classe �tendant IhmBD, sp�cialis�e pour la table Person

class IhmPerson extends IhmBD
{
  // Le constructeur de la classe. Attention � bien penser
  // � appeler le constructeur de la super-classe.
  
  function IhmPerson($nomTable, $bd, $script="moi")
  {
    global $TEXTS;

    // Appel du constructeur de IhmBD
    parent::IhmBD($nomTable, $bd, $script);
  
    // On peut placer les ent�tes d�s maintenant
    $this->setEntete("nom", "Nom");
    $this->setEntete("prenom", "Pr&eacute;nom");
    $this->setEntete("email", "Email");
    $this->setEntete("id_master", "Master");
    $this->setEntete("telephone", "T&eacute;l&eacute;phone");
    $this->setEntete("mobile", "Mobile");
    $this->setEntete("password", "Mot de passe");
    $this->setEntete("adresse", "Adresse");
    $this->setEntete("conf_password", "Confirmation du mot de passe");
  }

  /*****************   Partie publique ********************/
  // M�thode effectuant des contr�les avant mise � jour 
  function controle(&$ligne, &$messages)
  {
	 IhmBD::controle($ligne, $messages);

    // On v�rifie que les champs importants ont �t� saisis
    if (empty($ligne['email']))
      $messages[]  = "Il faut entrer un email";
    else if (!ControleEmail($ligne['email']))
      $messages[] = "L'email doit �tre de la forme xxx@yyy[.zzz] !<BR>";

    if (empty($ligne['password']) and $ligne['action'] == MAJ_BD)
      {
	// On va chercher le mot de passe dans la table
	if (is_object($o = $this->chercheLigne($ligne, "object")))
	  {
	    $ligne['password'] = $o->password;
	    $ligne['conf_password'] = $o->password;
	  }
      }
    else
      {
	if (empty($ligne['password'])
	    or empty($ligne['conf_password'])
	    or $ligne['password'] != $ligne['conf_password'])
	  $messages[]= "Il faut entrer un mot de passe et le confirmer!<BR>";
	else
	  $ligne['password'] = $ligne['password'];
      }

    $ligne['prenom'] =$ligne['prenom'];
    $ligne['nom'] = $ligne['nom'];

    if (count($messages) == 0) 
      return true;
    else
      return false;
  }

  function save_photo($id) 
  {
      if (is_uploaded_file ($_FILES['photo']['tmp_name'])) {
        $file = $_FILES['photo'];
	  // Check the format (always in lowercase)
	  $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
//         echo "ext=$ext<br>";
	  if (strToLower($ext) != 'jpg' and
                 strToLower($ext) != 'gif') {
	    return "Le format de la photo doit �tre JPG ou GIF<br>";
          }

      if (!copy($file['tmp_name'], "photos/photo_$id.jpg"))
          return "Copie impossible<br>";
      else
        return "";       
    }
   return "";
  }

  // R�d�finition du formulaire
  function formulaire ($action, $ligne)
  {
	/* print_r($ligne);
	 echo"<br>";
	 */
    // Cr�ation de l'objet formulaire
    $form = new Formulaire ("POST", $this->nomScript, false);
    $form->setTitle ($this->nomTable);

    $masters = array();
    $result = $this->bd->execRequete 
      ("SELECT id, nom FROM Master WHERE id <= 6 OR id=8");
    while ($cursor = $this->bd->ligneSuivante ($result))
      $masters[$cursor["id"]] = $cursor["nom"];

    $yesno = array ("Y" => "Oui", "N" => "Non");

    $form->champCache ("action", $action);

    // Pas de mise � jour? On calcule la valeur de l'id
    if ($action != MAJ_BD) {
	$ligne['id'] = $this->GetNextPersonID($this->bd);
	$ligne['roles'] = STUDENT_ROLE;
	$ligne['annee_master'] = ANNEE_MASTER;
      }
    $form->champCache ("id",  $ligne['id']);
    $form->champCache ("roles",  $ligne['roles']);
    $form->champCache ("vacataire",  "N");
    $form->champCache ("annee_master",  $ligne['annee_master']);

    $form->champCache ("fax",  "");
    $form->champCache ("home_page", "");
    $form->champCache ("cv",  "");
    $form->champCache ("notes",  "");

    $form->debutTable (VERTICAL,array(),$nbLignes=1, 
		       "Formulaire d'inscription");

    // V�rifier que la valeur par d�faut existe
    foreach ($this->schemaTable as $nom => $options)
      if (!isSet($ligne[$nom])) $ligne[$nom] = "";
    $ligne['conf_password'] = $ligne['password'];

    $form->champTexte ($this->entetes['nom'], "nom",
		       $ligne['nom'], 30, 60);
    $form->champTexte ($this->entetes['prenom'], "prenom",
		       $ligne['prenom'], 30, 60);
    $form->champMotDePasse ($this->entetes['password'], "password",
		       "", 30, 30);
    $form->champMotDePasse ($this->entetes['conf_password'], "conf_password",
		       "", 30, 30);
    $form->champListe ($this->entetes['id_master'], "id_master", 
		       $ligne['id_master'], 1, $masters);
    
    $form->champTexte ($this->entetes['email'], "email",
		       $ligne['email'], 30, 60);
  /*  $form->champTexte ($this->entetes['email'], "email",
		       "hoho", 30, 60);
			   */
    $form->champTexte ($this->entetes['telephone'], "telephone",
		       $ligne['telephone'], 20, 20);
    $form->champTexte ($this->entetes['mobile'], "mobile",
		       $ligne['mobile'], 20, 20);
    $form->champFenetre ($this->entetes['adresse'], "adresse",
		       $ligne['adresse'], 4, 60);
   $form->champFichier ("Photo", "photo", 30);
    $form->finTable();
	
    if ($action == MAJ_BD)
      $form->champValider ("Modifier", "submit");
    else
      $form->champValider ("Ins�rer", "submit");
    
    return $form->formulaireHTML();
  }

  function GetNextPersonID ($db)
  {
    $result = $db->execRequete ("SELECT Max(id)+1 AS id FROM Personne");
    $o = $db->objetSuivant ($result);
    return $o->id;
  }


  // Fonction cr�ant une interface avec saisie, mise � jour
  // et consultation
  function GUIStudent ($paramsHTTP)
  {
    echo "<script language='JavaScript1.2' src='ShowWindow.js'></script>";
 
    // A-t-on demand� une action?
    if (isSet($paramsHTTP['action']))
      $action = $paramsHTTP['action'];
    else 
      $action = "";

    $affichage = "";
    switch ($action)
      {
      case INS_BD:
	// On a demand� une insertion
	if ($this->insertion($paramsHTTP))
	  {
            $affichage = $this->save_photo($paramsHTTP['id']);
	    $affichage .= "<I>Insertion effectu�e</I>";
	    $affichage .= "<h2>V�rifiez, et corrigez si n�cessaire</h2>";
	    
	    $person = $this->chercheLigne($paramsHTTP);
	    $affichage .= $this->formulaire(MAJ_BD, $person);
	    $affichage .= "<p><a href='intranet.php?logout=1'>Se d�connecter</a>";
	  }
	else{
	  $affichage .= $this->formulaire(INS_BD, $paramsHTTP);
	}
	break;

      case MAJ_BD:
	// On a demand� une modification
	if ($this->maj($paramsHTTP))
	  $affichage .= "<I>Mise � jour effectu�e.</I>";
        $affichage = $this->save_photo($paramsHTTP['id']);
	$ligne  = $this->chercheLigne ($paramsHTTP);
	$affichage .= $this->formulaire(MAJ_BD,$ligne);
	$affichage .= "<p><a href='intranet.php?logout=1'>Se d�connecter</a>";
	break;


      default:
	$affichage .= $this->formulaire(INS_BD,array());

      }
    return $affichage;
  }

}
?>
