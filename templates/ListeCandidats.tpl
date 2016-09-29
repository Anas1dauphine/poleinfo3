<H1>Liste des candidats pour l'année <i>{ANNEE_COURANTE}</i></H1>

<a href="intranet.php?option=11&amp;saisie=1">Saisir un nouveau candidat</a>
<p>

<form action="intranet.php?option=11" method="post">
<table border='2'>
 <tr class='header'>
  <td rowspan='4'>Sélection des candidats par critères</td>
 </tr>
 <tr class='A0'>
   <td>Dont le nom commence par : </td>
   <td>{LISTE_LETTERS}</td>
   <td>Ayant le statut </td>
    <td>{LISTE_STATUTS}</td>
 </tr>
 <tr class='A1'>
 <td colspan='2'><input type='submit' value='Valider la sélection'/></td>
 <td colspan='2'><input type='submit' name='exporter' value='Exporter en Excel'/></td>
 </tr>
</table>

</form>

<p></p>
<form action="intranet.php?option=11" method="post">

<a href="mailto:{LISTE_EMAILS}">Envoyer un mail à ces candidats</a>
<p>

<input type='submit' value='Valider les modifications'/>
<input type='hidden' name='cand_master' value='{DEF_MASTER}'/>
<input type='hidden' name='statut' value='{DEF_STATUT}'/>
<input type='hidden' name='letter' value='{DEF_LETTER}'/>
<input type='hidden' name='option' value='11'/>

<table border='3' cellpadding='2'>
  <tr class='header'>
      <th>No</th>
     <th>Affectation</th>
     <th>Action</th>
        <th>Genre</th>
        <th>Nom</th>
     <th>Adresse</th>
     <th>Masters souhaités</th>
      <th>Diplôme</th>
     <th>Etablissement</th>
   </tr> 

<!-- BEGIN CANDIDAT -->
<tr class='{CSS_CLASS}'>
   <td>{CAND_NO} </td>
  <td>{LISTE_CHOIX_MASTERS}</td>
 <!-- <td><a href="intranet.php?option=11&amp;choix=modifier&amp;id={CANDIDAT_ID}">modifier</a></td> -->
 <td><a href="intranet.php?option=11&amp;modifier=1&amp;id={CANDIDAT_ID}&amp;letter={DEF_LETTER}">Modifier</a> /
      <a onClick="ConfirmAction('Suppression de {CANDIDAT_NAME} !?', 'intranet.php?option=11&amp;choix=supprimer&amp;candidat_id={CANDIDAT_ID}&amp;letter={DEF_LETTER}')" 
           href='#'>supprimer</a></td>
  <td>{LISTE_GENRES}</td>
 <td><a href="mailto:{CANDIDAT_EMAIL}">{CANDIDAT_NAME}</a></td>
  <td>{CANDIDAT_ADDRESS}</td>
  <td>{CANDIDAT_MASTERS}</td>
   <td>{CANDIDAT_DIPLOME}, {CANDIDAT_ANNEE_DIPLOME}</td>
  <td>{CANDIDAT_ETABLISSEMENT}</td>
  </tr> 
<!-- END CANDIDAT -->
</table>
</form>

<a href="mailto:{LISTE_EMAILS}">Envoyer un 
email a tous ces candidats.</a>
