<H1>Liste des �tudiants du  <i>{TITRE_LISTE}</i></H1>

<a href="mailto:{LISTE_EMAILS}">Envoyer un 
email � tous ces �tudiants.

<P>
<form action="intranet.php?action=8" method="GET">

<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Photo</th>
     <th>Nom</th>
     <th>Email</th>
     <th>Master</th>
     <th>Adresse</th>
     <th align="right">Note</th>
     <th>Inscription aux cours</th>
  </tr> 

<!-- BEGIN ETUDIANT -->
<tr class='{CSS_CLASS}'>
  <td><img src="photos/photo_{MEMBER_ID}.jpg" 
          width="100"  alt="Photo manquante"/>
          </td>
  <td>{MEMBER_NAME}</td>
  <td><a href="mailto:{MEMBER_EMAIL}">{MEMBER_EMAIL}</a></td>
  <td>{MASTER_NOM}</td>
  <td>{MEMBER_ADDRESS}<br/>
       <b>T�l.</b>: {MEMBER_PHONE} <b>Mobile</b>: {MEMBER_MOBILE}</td>
   <td align="right"><input type="text" size="5"/></td>
  <td><a href="intranet.php?option=9&id_etudiant={MEMBER_ID}">inscriptions</a></td>
</tr> 
<!-- END ETUDIANT -->
</table>
</form>

<P>
