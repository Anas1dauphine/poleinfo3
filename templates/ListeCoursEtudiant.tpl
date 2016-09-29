Liste des cours de l'&eacute;tudiant {MEMBER_NAME}.

<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Nom</th>
      <th>Enseignant</th>
     <th align='right'>Volume horaire</th>
     <th align='right'>ECTS</th>
     <th align='right'>Obligatoire?</th> 
  </tr> 

<!-- BEGIN COURS -->
<tr class='{CSS_CLASS}'>
  <a name="{COURS_ID"}>

  <td>{COURS_NOM} 
        <a href='#{COURS_ID}'
onClick=
   "ShowWindow('ShowCours.php?id_master={MASTER_ID}&id_contenu={CONTENU_ID}');">
               (D&eacute;tails)</a></td>
  <td>
       <a href="mailto:{COURS_ENSEIGNANT_EMAIL}?subject={COURS_NOM}">{COURS_ENSEIGNANT}</a></td>
  <td align='right'>{COURS_VOLUME_HORAIRE} heures</td>
  <td align='right'>{COURS_ECTS}</td>
  <td>{COURS_OBLIGATOIRE}</td>
</tr> 
<!-- END COURS -->

<!-- BEGIN TOTAL -->
<tr class='{CSS_CLASS}'>
  <td colspan='2'>Totaux</td>
  <td align='right'>{TOTAL_HEURES}</td>
  <td align='right'>{TOTAL_ECTS}</td>
  <td>&nbsp;</td>
</tr> 
<!-- END TOTAL -->
</table>
