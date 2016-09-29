<H1>Liste des étudiants du  <i>{TITRE_LISTE}</i></H1>

<a href="mailto:{LISTE_EMAILS}">Envoyer un 
email à tous ces étudiants.

<P>

<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Photo</th>
     <th>Nom</th>
     <th>Email</th>
     <th>Master</th>
     <th>Adresse</th>
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
       <b>Tél.</b>: {MEMBER_PHONE} <b>Mobile</b>: {MEMBER_MOBILE}</td>
  <td><a href="intranet.php?option=9&id_etudiant={MEMBER_ID}">inscriptions</a></td>
</tr> 
<!-- END ETUDIANT -->
</table>

<P>
