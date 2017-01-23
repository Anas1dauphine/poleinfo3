<?php
  define ("NAME","root");
  define ("PASS", "MODIFIED-Olivier");
  define ("SERVER", "localhost");
  define ("BASE", "Master");

      $connexion = mysql_pconnect (SERVER, NAME, PASS);

      if (!$connexion) 
       echo "Sorry, unable to connect to " . SERVER . "<br>";
    else
       echo "Connexion OK<br>";

      // Connnect to the DB
      if (!@mysql_select_db (BASE, $connexion)) 
      {
        echo "Sorry, unable to access to the DB\n";
        echo "<B>MySQL says: </B>" .
                             mysql_error($connexion);
      }
    else
      echo "Connexion a la base OK<br>";
?>
