<?php
// Load the libraries

require_once ("Util.php");
require_once ("Formulaire.class.php");


function Publi ($publi, $target, $db, &$tpl, &$TEXTS)
{
  $id = 0;
  if (!isSet($publi['mode']))
    {
      // Show the form
      $tpl->set_var("BODY",
		    FormPubli ($publi, INSERTION, $target, $db, $TEXTS));
    }
  else{

    // Get the variables, and escape quotes
    $title = $db->prepareChaine($publi['title']);  
    $conference = $db->prepareChaine($publi['conference']);  
    $journal = $db->prepareChaine($publi['journal']);  
    $abstract = $db->prepareChaine($publi['abstract']);
    $format = $publi['format'];
    $type = $publi['type'];
    $year = $publi['year'];
    $volume = $publi['volume'];
    $number = $publi['number'];
    $editor = $db->prepareChaine($publi['editor']);
    $publisher = $db->prepareChaine($publi['publisher']);
    $pages = $db->prepareChaine($publi['pages']);
    $notes = $db->prepareChaine($publi['notes']);
    $series = $db->prepareChaine($publi['series']);
    $authors = $publi['authors'];

    $now = date('U'); // Unix time
    $mode = $publi['mode'];

    $message = array();
    // Some tests...
    if (empty ($title)) $message[] = "You must enter a title";
    //    if (empty ($abstract)) $message[] = "You must enter an abstract";
    $one_author = false;
    foreach ($authors as $i => $id)
      if ($id !=0) $one_author = true;
    reset($authors);

    if (!$one_author) $message[] = "Please give AT LEAST one author!";
      
    if (isSet($_FILES['file'])
	and is_uploaded_file ($_FILES['file']['tmp_name']))
      {
	$file = $_FILES['file'];
	// Check the format (always in lowercase)
	$ext = substr($file['name'], strrpos($file['name'], '.') + 1);
	if (strToLower($ext) != $publi['format']) {
	  $message[]= "Invalid file format ". 
	    " (extension:$ext, format:" . $publi['format'] . ")";
	}
      }

    if (count($message) > 0)
      {
	$mess =  "";
	foreach ($message as $m) $mess.="<li>$m</li>";
	$tpl->set_var("BODY",
		      "<b>Error</b>: <font color=red><ol>$mess</ol></font>" . 
		      FormPubli ($publi, INSERTION, $target, $db, $TEXTS));
	return;
      };

    if ($mode == INSERTION)  
    {
      // Insert
      $query = "INSERT INTO Publi (title, abstract, year, format, type, "
	. "conference, journal, volume, number, editor, publisher, pages, "
	. "notes, series) "
	. "VALUES ('$title', '$abstract', '$year', "
	. "'$format', '$type', '$conference', '$journal', '$volume', "
	. " '$number', '$editor', '$publisher', '$pages', '$notes', '$series') ";

      $db->execRequete ($query);
      $id = $db->idDerniereLigne();
      $feedback = "Publication inserted<p>";
    }
    else  {
      $id = $publi['id'];

      // Update
      $query = "UPDATE Publi SET title='$title', abstract='$abstract', "
        . "year='$year', format='$format', type='$type', volume='$volume', "
	. "number='$number', conference='$conference', journal='$journal', "
	. "editor='$editor', publisher='$publisher', pages='$pages', "
	. "notes='$notes', series='$series' "
	. " WHERE id='$id'";

      $feedback = "Publication updated<p>";
      $db->execRequete ($query);
    }

  // Insert/update the authors if necessary
  if (isSet($publi['authors']))
    {
      // Remove all current authors, and replace
      $qDel = "DELETE FROM Author WHERE id_publi='$id'";
      $db->execRequete($qDel);
      // Insert all authors
      $authors = $publi['authors']; 
      for ($i=1; $i <= count($authors); $i++) 
	{
	  if ($authors[$i] != 0)	
	    {   
	      $id_author = $authors[$i];
	      $position = $i+1;
	      $qIns = "INSERT INTO Author (id_publi, id_author, position) "
		. "VALUES ($id, $id_author, $i) ";
	      $db->execRequete ($qIns);
	    }
	}
    }

  // Upload the file if any
  if (isSet($_FILES['file'])
	      and is_uploaded_file ($_FILES['file']['tmp_name']))
    {
      $file = $_FILES['file'];
      if (StorePubli  ($id, $file, $format, $db))
	{
	  // Publi submission phase
	  $fileSize = $_FILES['file']['size'];
	  $qUpdPubli = 
	      "UPDATE Publi SET is_uploaded='Y', file_size='$fileSize' "
	    . " WHERE id='$id'";
	  $db->execRequete ($qUpdPubli);
	}
      else
	echo  "<font color='red'>Unable to store the file</font>";
    }

  $publi = GetPubli ($id, $db);
  
  $tpl->set_var("BODY", $feedback .
		    FormPubli ($publi, MAJ, $target, $db, $TEXTS));
  }
}

// Store the file
function StorePubli ($id, $fic, $format, $db)
{
  // Encode the file name
  $publiName = "FILES/publi$id.$format";

  if (!copy($fic['tmp_name'], $publiName))
    return FALSE;
  else
    return TRUE;
}

// Get a publi with its id
function GetPubli ($id, $bd, $mode="array") 
{
  $query = "SELECT * FROM Publi WHERE id = '$id'" ;
  $result = $bd->execRequete ($query);
  if ($mode == "array")
    $publi = $bd->ligneSuivante ($result);
  else
    $publi = $bd->objetSuivant ($result);
  return $publi;
}

?>