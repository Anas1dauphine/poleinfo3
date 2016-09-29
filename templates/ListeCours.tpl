
<!-- JavaScript function to display paper infos  -->
<script language="JavaScript1.2" src="ShowWindow.js"></script>

<H1>Liste des cours du Master  <i>{MASTER_NOM}</i></H1>

<a href="mailto:{LISTE_EMAILS}?subject={MASTER_NOM}">Envoyer un 
email &aacute; tous les enseignants.</a>
<P>

<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Nom</th>
      <th>Enseignant</th>
     <th align='right'>Volume horaire</th>
     <th align='right'>Volume projet</th>
     <th align='right'>ECTS</th>
     <th align='right'>Effectif</th>
     <th align='right'>Obligatoire?</th>
     <th align='right'>Feuille de pr&eacute;sence</th>
  </tr> 

<!-- BEGIN COURS -->
<tr class='{CSS_CLASS}'>
  <a name="{COURS_ID"}>

  <td>{COURS_NOM} 
        <a href='#{CONTENU_ID}'
onClick=
   "ShowWindow('ShowCours.php?id_master={MASTER_ID}&id_contenu={CONTENU_ID}');">
               (D&eacute;tails)</a></td>
  <td>
       <a href="mailto:{COURS_ENSEIGNANT_EMAIL}?subject={COURS_NOM}">{COURS_ENSEIGNANT}</a></td>
  <td align='right'>{COURS_VOLUME_HORAIRE} heures</td>
  <td align='right'>{COURS_VOLUME_PROJET} heures</td>
  <td align='right'>{COURS_ECTS}</td>
  <td>{COURS_EFFECTIF}</td>
  <td>{COURS_OBLIGATOIRE}</td>
  <td>
        <a href='#{CONTENU_ID}'
         onClick=
   "ShowWindow('ShowCours.php?id_master={MASTER_ID}&id_contenu={CONTENU_ID}&feuille=1');">
                 (feuille)</A>
   </td>
</tr> 
<!-- END COURS -->

<!-- BEGIN TOTAL -->
<tr class='{CSS_CLASS}'>
  <td colspan='2'>Totaux</td>
  <td align='right'>{TOTAL_HEURES}</td>
  <td align='right'>{TOTAL_HEURES_PROJET}</td>
  <td align='right'>{TOTAL_ECTS}</td>
  <td colspan='2'>&nbsp;</td>
</tr> 
<!-- END TOTAL -->
</table>

<P>
