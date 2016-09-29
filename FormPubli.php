<?php
// Load the libraries

require_once ("Util.php");
require_once ("Formulaire.class.php");

function FormPubli ($abstract, $mode, $target, $db, &$TEXTS)
{
  global $FILE_FORMATS;

  // get the list of person
  $persons = GetPersons ($db);

  // Create the form
  $form = new Formulaire ("POST", $target);
  $form->champCache ("mode", $mode);
   
  $form->debutTable(VERTICAL, array("BORDER"=>1), 1,
		    $TEXTS->get("FRM_SUBMISSION_TITLE"));

  for ($y = YEAR_MIN; $y <= YEAR_MAX; $y++) $years[$y] = $y;
  if ($mode != INSERTION)
    { 
      $id_publi = $abstract["id"];
      $form->champCache ("id", $id_publi);
      // Get the current list of authors
      $authors = GetAuthors($id_publi, $db, "id");
    }
  else
    {
      if (!isSet($abstract['title'])) $abstract['title']="";
      if (!isSet($abstract['year'])) $abstract['year']=date("Y");
      if (!isSet($abstract['abstract'])) $abstract['abstract']="";
      if (!isSet($abstract['format'])) $abstract['format']="pdf";
      if (!isSet($abstract['type'])) $abstract['type']="";
      if (!isSet($abstract['journal'])) $abstract['journal']="";
      if (!isSet($abstract['volume'])) $abstract['volume']="";
      if (!isSet($abstract['number'])) $abstract['number']="";
      if (!isSet($abstract['editor'])) $abstract['editor']="";
      if (!isSet($abstract['publisher'])) $abstract['publisher']="";
      if (!isSet($abstract['pages'])) $abstract['pages']="";
      if (!isSet($abstract['series'])) $abstract['series']="";
      if (!isSet($abstract['notes'])) $abstract['notes']="";
      if (!isSet($abstract['conference'])) $abstract['conference']="";
      if (!isSet($abstract['authors'])) 
	$authors=array();
      else
	{
	  // Get the default values for authors
	  $i=1;
	  foreach ($abstract['authors'] as  $author)
	    {
	      if ($author != 0)
		{
		  $authors[$i] = $author;
		  $i++;
		}
	    } 
	}
    }

  $form->champTexte ($TEXTS->get("FRM_PUBLI_TITLE"), 
		     "title", $abstract['title'], 55, 255);
  $form->champListe ("Year", "year", $abstract['year'], 1, $years);

  // Get the list of publi. types
  $types = GetCodeList ("PubliType", $db, $id="id", $name="name");
  $form->champListe ($TEXTS->get("FRM_PUBLI_TYPE"), "type", 
		     $abstract['type'], 1, $types);
  $form->champTexte ($TEXTS->get("FRM_PUBLI_JOURNAL"), 
		     "journal", $abstract['journal'], 55, 100);
  $form->champTexte ("Volume", "volume", $abstract['volume'], 10, 10);
  $form->champTexte ("Number", "number", $abstract['number'], 10, 10);
  $form->champTexte ($TEXTS->get("FRM_PUBLI_CONFERENCE"), 
		     "conference", $abstract['conference'], 55, 100);

  // Create an HTML table to display the list of authors
  $tableau = new Tableau (2, array("BORDER"=>1));
  // Create headers
  $tableau->setAfficheEntete (1, FALSE);
  $tableau->ajoutEntete(2, "authors", "Authors' list");
  // Create a local form 
  $local_form = new Formulaire ();

  for ($i=1; $i < MAX_AUTHORS; $i++)
    {

      // Get the default values
      if (isSet($authors[$i])) $id_def= $authors[$i];
      else	$id_def = 0;
      $tableau->ajoutValeur ($i, "authors", 
			     $local_form->getChamp
			     (
			      $local_form->champListe ("Author", "authors[$i]", 
						       $id_def, 1, $persons)
			      )
			     );
    }
  $form->champPLAIN ("Authors",$tableau->tableauHTML());

  $form->champFenetre ($TEXTS->get("FRM_PUBLI_ABSTRACT"), 
		       "abstract", $abstract['abstract'], 15, 40);

  $form->champFichier ($TEXTS->get("FRM_PUBLI_UPLOAD"), "file", 30);
  // Ask for the file type
  $form->champRadio ($TEXTS->get("FRM_PUBLI_FORMAT"), "format", 
		     $abstract['format'],  $FILE_FORMATS);

  $form->champTexte ("Editor", "editor", $abstract['editor'], 30, 50);
  $form->champTexte ("Publisher", "publisher", $abstract['publisher'], 30, 50);
  $form->champTexte ("Pages", "pages", $abstract['pages'], 10, 10);
  $form->champFenetre ("Notes", "notes", $abstract['notes'], 4, 40);
  $form->champTexte ("Series", "series", $abstract['series'], 10, 30);

  $form->finTable();


  $form->ajoutTexte("<P>");
  $form->champValider ($TEXTS->get("FRM_SUBMIT"), "submit");

  return $form->formulaireHTML();
}

?>