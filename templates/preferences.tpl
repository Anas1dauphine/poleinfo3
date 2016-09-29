<!-- JavaScript function to display paper infos  -->

<script language="JavaScript1.2" src="ShowWindow.js"></script>


<form action="preferences.php" method="POST">


<!-- BEGIN RATING_MESSAGE -->
Vous pouvez consulter la liste des cours du P&ocirc;le Info 3
 ci-dessous et exprimer vos pr&eacute;f&eacute;rences.
<BR><BR>
Notez que les cours obligatoires du Master o&ugrave; vous &ecirc;tes inscrit(e) 
vous seront affect&eacute;s de toute fa&ccedil;on. Le niveau de pr&eacute;f&eacute;rence
par d&eacute;faut pour ces cours obligatoires est "Je veux ce cours".

<BR><p>
Donnez un niveau de
 pr&eacute;f&eacute;rence pour les autres cours, sachant que nous ferons de notre 
mieux pour vous satisfaire, sans pouvoir vous garantir
que ce sera le cas &agrave; chaque fois. 
<BR><BR><font color="red">Attention aux contraintes indiqu&eacute;es lors de la r&eacute;union de rentr&eacute;e et rappel&eacute;es dans les <A href="pedagogie/presentation.pdf">transparents</A> de la rentr&eacute;e.</font>


<BR><BR>
Vous avez jusqu'au <b>20 septembre</b> inclus pour exprimer vos pr&eacute;f&eacute;rences. Les affectations seront disponibles le <b>23 septembre au plus tard</b>,
 sur l'intranet.
<!-- END RATING_MESSAGE -->

<!-- BEGIN ACK_RATING_MESSAGE -->
Vos pr&eacute;f&eacute;rences ont &eacute;t&eacute; stock&eacute;es. Vous pouvez le modifier &agrave; tout moment,
jusqu'&agrave; l'affectation.
<!-- END ACK_RATING_MESSAGE -->

<p>

<INPUT TYPE="SUBMIT" VALUE="Validez vos pr&eacute;f&eacute;rences">
<p>

<table border=1 cellspacing=2 cellpadding=2>
<tr class='header'>
  <th>Intitule du cours</th><th>Master</th><th>Votre niveau de pr&eacute;f&eacute;rence</th>
</tr>

<!-- BEGIN PREF_DETAIL -->
<tr class='{CSS_CLASS}'>

 <td><a name='{COURS_ID}'>
     {COURS_NOM}
     ({COURS_MASTER}, {COURS_ECTS} ects, {COURS_VOLUME_HORAIRE} heures)
        <a href='#{COURS_ID}'
onClick=
   "ShowWindow('ShowCours.php?id_master={MASTER_ID}&id_contenu={CONTENU_ID}');">
                 (infos)</A>
  </td>
 <td>{COURS_MASTER}</td>
 <td>{COURS_NIVEAU}</td>
</tr>
<!-- END PREF_DETAIL -->

</table>

</form>
<p>

<a href="intranet.php?logout=1">Se d&eacute;connecter</a> 
