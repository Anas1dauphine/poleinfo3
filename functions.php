<?
// Get a code list
function GetCodeList ($tableName, $db, $id="id", $name="name",
		      $output=array()) 
{
  $res = $output;
  $result = $db->execRequete ("SELECT $id, $name FROM $tableName");
  while ($cursor = $db->ligneSuivante ($result))
    $res[$cursor[$id]] = $cursor[$name];
  
  return $res;
}

// Get a table row 
function GetRow ($query, $db, $mode="array") 
{
  $result = $db->execRequete ($query);
  if ($mode == "array")
    return  $db->ligneSuivante ($result);
  else
    return $db->objetSuivant ($result);
}

// R�cup�ration d'un cours
function GetCours ($id_master, $id_contenu, $bd, $mode="array") 
{
  $query = "SELECT o.*, c.id_enseignant, c.id_master, c.id_contenu, "
    . "c.obligatoire, c.notes FROM Cours c, Contenu o "
    . " WHERE id_master = '$id_master'"
    . " AND id_contenu='$id_contenu' AND c.id_contenu=o.id order by id_master, obligatoire DESC, nom";

  $result = $bd->execRequete ($query);
  if ($mode == "array")
    return $bd->ligneSuivante ($result);
  else
    return $bd->objetSuivant ($result);
}

function CheckEMail($email){
  // Check the fields of an email
  return eregi("^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$", $email);
}

/*************************************************************/

// Get a person
function GetPersonne($id, $bd, $format="object") 
{
  $result = $bd->execRequete ("SELECT * FROM Personne WHERE id='$id'");
  if ($format == "object")
    return $bd->objetSuivant ($result);
  else
    return $bd->ligneSuivante ($result);
}

// Get a master
function GetMaster($id, $bd) 
{
  $result = $bd->execRequete ("SELECT * FROM Master WHERE id='$id'");
  return $bd->objetSuivant ($result);
}

// Get a group name
function GetGroup($id, $bd) 
{
  $result = $bd->execRequete ("SELECT * FROM Groups WHERE id='$id'");
  $res = $bd->objetSuivant ($result);
  return $res->short_name;
}

// Get a person by its email
function GetPersonByEmail($email, $bd, $format="object") 
{
  $result = $bd->execRequete ("SELECT * FROM Person WHERE email='$email'");
  if ($format == "object")
    return $bd->objetSuivant ($result);
  else
    return $bd->ligneSuivante ($result);
}

// Get an array of the lab. members
function GetPersons ($bd) 
{
  $tab = array(0 => "Nobody");
  $query = "SELECT * FROM Person ORDER BY last_name ";
  $result = $bd->execRequete ($query);
  while ($p =  $bd->objetSuivant ($result))
    $tab[$p->id] = "$p->first_name $p->last_name ($p->affiliation)";

  return $tab;
}


// Determine whether there is a conflict for a publi
function PubliInConflict ($idPubli, $bd) 
{
  $query = "SELECT p.* FROM Publi p, Review r1, Review r2"
    . " WHERE p.id=$idPubli AND r1.idPubli=$idPubli AND r2.idPubli=$idPubli "
    . "AND r1.overall IS NOT NULL "
    . "AND r2.overall IS NOT NULL "
    . "AND r1.email != r2.email "
    . "AND ABS(r1.overall - r2.overall) >= " . CONFLICT_GAP ;
    
  $result = $bd->execRequete ($query);
  $rev =  $bd->objetSuivant ($result);
  if (is_object($rev)) 
    return true;
  else 
    return false;
}

// Determine whether there is a missing review for a publi
function MissingReview ($idPubli, $bd) 
{
  $query = "SELECT p.* FROM Publi p, Review r1"
    . " WHERE p.id=$idPubli AND r1.idPubli=$idPubli AND r1.overall IS NULL";
    
  $result = $bd->execRequete ($query);
  $rev =  $bd->objetSuivant ($result);
  if (is_object($rev)) 
    return true;
  else 
    return false;
}

// Compute the overall rate for a publi
function Overall ($idPubli, $bd) 
{
  // Note: does not take account of NULL values
  $query = "SELECT AVG(overall) AS overall FROM Review WHERE idPubli=$idPubli";
  
  $result = $bd->execRequete ($query);
  $rev =  $bd->objetSuivant ($result);
  if (is_object($rev)) 
    return $rev->overall;
  else 
    return 0;
}

// Average rating for a user
function AvgUserRating ($email, $bd)
{
  $requete = "SELECT AVG(rate) AS avgRate FROM Rating "
    . "WHERE email='$email' AND rate!=0 AND significance=1";
  $res = $bd->execRequete ($requete);
  $obj = $bd->objetSuivant ($res);
  return $obj->avgRate;
}

// Encapsulate the mail function
function SendMail ($to, $subject, $mail, 
		   $from="", $replyTo="", $cc="")
{
  // Construct the header
  $header = "";
  if (!empty($from)) $header .= "From: $from\r\n";
  if (!empty($cc)) $header .= "Cc: $cc\r\n";
  if (!empty($cc)) $header .= "Reply-to: $replyTo\r\n";

  // Add the signature file
  /*  if (file_exists("Signature"))
    {
      $mail .= readfile ("Signature");
    }
  */
  // Use the standard mail function
  mail ($to, $subject, $mail, $header, "-f $from");
}

// Recherche de l'intitul� d'une codif
function LibelleCodif ($nomCodif, $code, $db) 
{
  $query = "SELECT * FROM $nomCodif WHERE id = '$code'" ;
  $result = $db->execRequete ($query);
  $codif = $db->ligneSuivante ($result);
  return $codif['label'];
}

// Nombre d'inscrit dans un cours
function CountInscrits ($id_master, $id_contenu, $db) 
{
  if ($id_master == 1 or $id_master == 3 or $id_master == 5)
	  $liste_masters = " (1,3,5) ";
  else
	  $liste_masters = " (2, 4, 6) ";
  $query = "SELECT COUNT(*) AS nb_inscrits FROM Affectation a, Personne p "
    . " WHERE a.annee=" . ANNEE_MASTER . " AND p.id=a.id_personne AND id_contenu='$id_contenu' "
	. " AND p.id_master IN $liste_masters AND annee_master = " . ANNEE_MASTER;
  $result = $db->execRequete ($query);
  $data = $db->ligneSuivante ($result);
  return $data['nb_inscrits'];
}

// Nombre d'ECTS pour un etudiant
function CountECTS ($id_etudiant, $db) 
{
  $query = "SELECT SUM(ects) AS nb_ects "
    . " FROM Personne p, Affectation i, Contenu c "
    . " WHERE p.id='$id_etudiant' AND p.id=i.id_personne "
    . " AND i.id_contenu= c.id AND i.annee=" . ANNEE_MASTER;
  $result = $db->execRequete ($query);
  $data = $db->ligneSuivante ($result);
  return $data['nb_ects'];
}

/***************** INSTANCIATION OF TEMPLATES VARIABLES ************/
function InstanciateCoursVars ($cours, &$tpl, $db)
{
  global $YESNO;
  $p = GetPersonne($cours->id_enseignant, $db);
  $master = GetMaster ($cours->id_master, $db);
  // Instanciate template variables related to a publi
  $tpl->set_var("MASTER_ID", $cours->id_master);
  $tpl->set_var("CONTENU_ID", $cours->id_contenu);
  $tpl->set_var("COURS_NOM", $cours->nom);
  $tpl->set_var("COURS_MASTER", $master->nom);
  $tpl->set_var("COURS_ECTS", $cours->ects);
  $tpl->set_var("COURS_DESCRIPTION", $cours->description);
  $tpl->set_var("COURS_CONTENU", $cours->contents);
  $tpl->set_var("COURS_OBJECTIFS", $cours->objectives);
  $tpl->set_var("COURS_BIBLIO", $cours->biblio);
  if (!empty($cours->obligatoire))
    $tpl->set_var("COURS_OBLIGATOIRE", $YESNO[$cours->obligatoire]);
  else
    $tpl->set_var("COURS_OBLIGATOIRE", "");
  if ($cours->apprentissage == "" or $cours->apprentissage=="0")
     $cours->apprentissage = 'N';
  $tpl->set_var("COURS_APPRENTISSAGE", $YESNO[$cours->apprentissage]);
  $tpl->set_var("COURS_VOLUME_HORAIRE", $cours->volume_horaire);
  $tpl->set_var("COURS_VOLUME_PROJET", $cours->volume_projet);
  $tpl->set_var("COURS_ENSEIGNANT", $p->prenom . " " . $p->nom);
  $tpl->set_var("COURS_ENSEIGNANT_EMAIL", $p->email);
  $tpl->set_var("COURS_DESCRIPTION", String2HTML($cours->description, true));
  $tpl->set_var("COURS_NOTES", String2HTML($cours->notes, true));
}

function InstanciatePersonVars ($person, &$tpl, $db)
{
  $html = true;
  // Instanciate template variables related to a PC member
  $tpl->set_var("MEMBER_ID", String2HTML($person->id, $html));
  $tpl->set_var("MEMBER_FIRST_NAME", String2HTML($person->prenom, $html));
  $tpl->set_var("MEMBER_LAST_NAME", String2HTML($person->nom, $html));
  $tpl->set_var("MEMBER_NAME", 
		String2HTML($person->prenom . " " . $person->nom, 
			    $html));
  $tpl->set_var("MEMBER_PHONE", $person->telephone);
  $tpl->set_var("MEMBER_MOBILE", $person->mobile);
  $tpl->set_var("MEMBER_FAX", $person->fax);
  $tpl->set_var("MEMBER_HOME_PAGE",$person->home_page); 
  $tpl->set_var("MEMBER_NOTES",$person->notes); 
  $tpl->set_var("MEMBER_CV",$person->cv); 
  $tpl->set_var("MEMBER_EMAIL",$person->email); 
  $tpl->set_var("MEMBER_ADDRESS",$person->adresse); 
  $tpl->set_var("MEMBER_",$person->adresse); 

   if ($person->vacataire=='Y') $vac ="Oui";
   else $vac="Non";
  $tpl->set_var("MEMBER_VACATAIRE",$vac); 
}

function InstantiateCandidateVars ($cand, &$tpl, &$db)
{
  $html = true;
  // Instanciate template variables related to a PC CANDIDAT
  $tpl->set_var("CANDIDAT_ID", String2HTML($cand->id, $html));
  $tpl->set_var("CANDIDAT_FIRST_NAME", String2HTML($cand->prenom, $html));
  $tpl->set_var("CANDIDAT_LAST_NAME", String2HTML($cand->nom, $html));
  $tpl->set_var("CANDIDAT_NAME", 
		String2HTML($cand->prenom . " " . $cand->nom, 
			    $html));
  $tpl->set_var("CANDIDAT_PHONE", $cand->telephone);
 $tpl->set_var("CANDIDAT_EMAIL",$cand->email); 
 $tpl->set_var("CANDIDAT_GENRE",$cand->genre); 
  $tpl->set_var("CANDIDAT_ADDRESS",$cand->adresse . ",<br/> " . $cand->code_postal 
  	. " " . $cand->ville . ", <br/>" . $cand->pays); 
  	$master = GetMaster ($cand->affectation, $db);
  $tpl->set_var("CANDIDAT_DIPLOME", $cand->diplome);
   $tpl->set_var("CANDIDAT_ETABLISSEMENT", $cand->etablissement);
   $tpl->set_var("CANDIDAT_ANNEE_DIPLOME", $cand->annee_obtention);
    if($cand->genre == 'M')
		$tpl->set_var("CANDIDAT_CIVILITE", "Monsieur");
	else
		$tpl->set_var("CANDIDAT_CIVILITE", "Madame");

  	$str_masters = $comma = "";
  	$masters = explode (";", $cand->masters);
  	if (is_array($masters)) {
  	 foreach ($masters as $id_master) {
  	  $m = GetMaster ($id_master, $db);
  	  $str_masters .= $comma . $m->nom;
  	  $comma = ", ";
  	 }
  	}
 	$tpl->set_var("CANDIDAT_MASTERS", $str_masters);
 }

/****************************** DATES ********************************/
function DBtoDisplay($date){
  $tab=explode('-',$date);
  return $tab[2]."/".$tab[1]."/".$tab[0];
}

function DisplaytoDB($date){
  $tab=explode('/',$date);
  return $tab[2]."-".$tab[1]."-".$tab[0];
}

function is_num($str){
  return preg_match("/^\d+$/",$str);
}

function isCorrectOrder($date1,$date2){
  return (strtotime(DisplaytoDB($date1))<=strtotime(DisplaytoDB($date2)));
}

function isCorrectDate($date){
  /* controle de la longueur de la chaine jj/mm/aaaa = 10 */
  if(strlen($date)==10){
    if(substr($date,2,1)=="/" && substr($date,5,1)=="/"){
      /* les caract�res 1 et 6 sont des " / "  */
      if (is_num(substr($date,0,2)) && is_num(substr($date,3,2)) 
	  && is_num(substr($date,6,4))) {
	$jour=intval(substr($date,0,2)); /* PHP num�rote les chaines depuis 0 */
	$mois=intval(substr($date,3,2));
	$annee=intval(substr($date,6,4));
	if($mois>=1 && $mois<=12){  /* verifie que le mois verifie 1<mois<12 */
	  if($jour<=longueurMois($mois,$annee)){ /* controle le jour par */
	    return true;                        /* rapport a la longueur du mois */
	  }    
	  else {
	    return false;
	  }
	}
	else {
	  return false;
	}
      }
      else {
	return false;
      }
    }
    else {
      return false;
    }
  }
  else {
    return false;
  }
}


function longueurMois($mois,$annee){
  if ($mois==4 || $mois==6 || $mois==9 || $mois==11) return 30;
  else if (($mois==2) && estBissextile($annee)) return 29;
  else if ($mois==2) return 28;
  else return 31;
}

function estBissextile($ans){
  if ((($ans % 4 == 0) && $ans % 100 != 0) || $ans % 400 == 0)
    return true;/*c'est une ann�e bissextile */
  else
    return false;/*ce n'en est pas une */
}


function UploadError($file, &$MESSAGES)
{
  // Get the error code (only available in PHP 4.2 and later)
  if (isSet($file['error']))
    {
      // Ok the code exists
      switch ($file['error']) 
	{
	case UPLOAD_ERR_NO_FILE:
	  return $MESSAGES->get("MISSING_FILE");
	  break;
	  
	case UPLOAD_ERR_INI_SIZE: 	case UPLOAD_ERR_FORM_SIZE:
	  return $MESSAGES->get("FILE_TOO_LARGE");
	  break;
	  
	case UPLOAD_ERR_PARTIAL:
	  return $MESSAGES->get("PARTIAL_UPLOAD");
	  break;
      
	default:
	  return "Unknown upload error";
	}
    }
  else
    {
      // No way to know what is going on
      return "Unable to upload the file";
    }
}

// The following function strips slashes from
// an HTTP input. Note: parameter is passed by reference

function NormaliseHTTP(&$arr)
{
  // Scan the array
  foreach ($arr as $key => $value) 
    {
      if (!is_array($value)) // Let's go
	{
	  $arr[$key] = trim(stripSlashes($value));
	}
      else  // Recursive call.
	{
	  NormaliseHTTP($arr[$key]);
	}
    }
  reset($arr);
}

// The following function trims (removal of white spaces
// from the beginning and end of a string) all the elements of an array
// Useful for cleaning HTTP inputs
function TrimArray(&$arr)
{
  // Scan the array
  foreach ($arr as $key => $value) 
    {
      if (!is_array($value)) // Let's go
	$arr[$key] = trim($value);
      else  // Recursive call
	TrimArray($arr[$key]);
    }
}

// The following function prepares a string for HTML output.
// All special characters are replaced by their entity ref.,
// and the newlines are converted to <br>
function String2HTML ($str, $html=true)
{
  if ($html)
    return nl2br(htmlSpecialChars($str));
  else
    return $str;
}

// Contr�le d'un mail par une expression r�guli�re
function ControleEMail($email){
  // A d�crypter...
  return eregi("^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$", $email);
}

// Compute the password for a PC member
function PWDPerson ($password)
{
  // MD5 encryption
  return md5(strToLower($password));
}

function IsStudent ($id, $db) 
{
  $person= GetPersonne ($id, $db);
  return strstr($person['roles'], STUDENT_ROLE);
}

function GetPref ($id_master, $id_contenu, $id_personne, $bd)
{
  $query = "SELECT * FROM Preference p "
    . " WHERE p.id_master = '$id_master' and p.id_contenu='$id_contenu' "
    . " AND id_personne = '$id_personne' "; 
  $result = $bd->execRequete ($query);
  return $bd->objetSuivant($result);
}

function GetInscrit ($id_master, $id_contenu, $id_personne, $bd)
{
  $query = "SELECT * FROM Affectation "
    . " WHERE id_contenu='$id_contenu' "
    . " AND id_personne = '$id_personne' AND annee=" . ANNEE_MASTER; 
  $result = $bd->execRequete ($query);
  return $bd->objetSuivant($result);
}

// Select list for HTML Forms
function  SelectField ($nom, $liste, $defaut, $taille=1)
{
  $s = "<SELECT NAME=\"$nom\" SIZE='$taille'>\n";
  while (list ($val, $libelle) = each ($liste))
    {
	// Attention aux probl�mes d'affichage
      $val = htmlSpecialChars($val);
      $defaut = htmlSpecialChars($defaut);
      if (strlen($libelle) > 70)
         $libelle = substr($libelle,0, 70) . "...";

      if ($val != $defaut)
	$s .=  "<OPTION VALUE=\"$val\">$libelle</OPTION>\n";
      else
	$s .= "<OPTION VALUE=\"$val\" SELECTED>$libelle</OPTION>\n";
      }
  return $s . "</SELECT>\n";
}

// Export a file
function Export($filename, $content)
{
/*	echo $content;
	exit;
	*/
  header("Content-disposition: attachment; filename=$filename");
  header("Content-Type: application/force-download");
  header("Content-Transfer-Encoding: application/octet-stream\n");
  header("Content-Length: ".strlen($content));
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate,post-check=0,pre-check=0,public");
  header("Expires: 0");
  echo ($content);
} 

// Transform a name: capitalize every first letter
function UCName ($name) {
	$comp = explode (" ", $name);
	$res = "";
	foreach ($comp as $c) {
		$res .= " " . ucfirst (strtolower($c));
	}
	return $res;
}
?>