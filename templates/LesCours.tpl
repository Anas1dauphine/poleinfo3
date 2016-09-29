<HTML>
<HEAD>
<TITLE>Master Dauphine</TITLE>
<link href="styles.css" rel="stylesheet" type="text/css" />
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>

<!-- JavaScript function to display paper infos  -->
<script language="JavaScript1.2" src="ShowWindow.js"></script>


<H1>Liste des cours du Master  <i>{MASTER_NOM}</i></H1>

<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Nom</th>
      <th>Enseignant</th>
     <th align='right'>Volume horaire</th>
     <th align='right'>ECTS</th>
     <th align='right'>Obligatoire</th>
  </tr> 

<!-- BEGIN COURS -->
<tr class='{CSS_CLASS}'>
  <a name="{COURS_ID}">

  <td>{COURS_NOM} 
        <a href='#{COURS_ID}'
onClick=
   "ShowWindow('ShowCours.php?id_master={MASTER_ID}&id_contenu={CONTENU_ID}');">
               (Détails)</a></td>
  <td>{COURS_ENSEIGNANT}</td>
  <td align='right'>{COURS_VOLUME_HORAIRE} heures</td>
  <td align='right'>{COURS_ECTS}</td>
  <td align='right'>{COURS_OBLIGATOIRE}</td>
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
