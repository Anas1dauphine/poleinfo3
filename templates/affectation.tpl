<!-- JavaScript function to display paper infos  -->
<script language="JavaScript1.2" src="ShowWindow.js"></script>


<form action="affectation.php" method="POST">

<input type='hidden' name='selectedTopic' value='1'>
Afficher les &eacute;tudiants du Master:  {LIST_MASTER_ETUDIANT} 

<!-- Affficher les cours du Master: {LIST_MASTER_COURS} 
-->

Affficher pour l'&eacute;tudiant(e): {LIST_ETUDIANTS} 
<input type='submit' value='Go'>
</form>


<hr/>
<hr/>

<form action="affectation.php" method="POST">

<input type='hidden' name='changeAssignment' value='1'>
<input type='hidden' name='master_etudiant' value='{MASTER_ETUDIANT}'>
<input type='hidden' name='master_cours' value='{MASTER_COURS}'>
<input type='hidden' name='etudiant' value='{ETUDIANT}'>

<input type='submit' value='Valider'>

<small>

<table border="2">
<tr class='header'><th>&nbsp;</th>
  <!-- BEGIN COURS_DETAIL -->
   <th valign='top'><small>{COURS_NOM}<br>{COURS_NB_INSCRITS} inscrits
           <br/>{COURS_ECTS} ects</small></th>
  <!-- END COURS_DETAIL -->
</tr>


<!-- BEGIN ETUDIANT_DETAIL -->
<tr class='{CSS_CLASS}'>
  <td><small>{MEMBER_NAME} ({ETUDIANT_NB_ECTS} Ects)</small></td>

	<!-- BEGIN ASSIGNMENT_DETAIL -->
         <td bgcolor='{BG_COLOR}' NOWRAP>
          <small>
           O <input type='RADIO' 
			name='assignments[{MEMBER_ID}][{COURS_ID}]'
		   	 value=1 {CHECKED_YES}>
            N <input type='RADIO' 
		name='assignments[{MEMBER_ID}][{COURS_ID}]'
	value=0 {CHECKED_NO}><br>Pr&eacute;f.={PREF_COURS}
               ({ETUDIANT_NB_ECTS} Ects)</small></td>
	<!-- END ASSIGNMENT_DETAIL -->
</tr>
<!-- END ETUDIANT_DETAIL -->

</table>
</small>

</form>
