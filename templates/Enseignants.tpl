<HTML>
<HEAD>
<TITLE>Lab. LAMSADE - Univ. Paris Dauphine</TITLE>
<link href="styles.css" rel="stylesheet" type="text/css" />
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>

<!-- JavaScript function to display paper infos  -->
<script language="JavaScript1.2" src="ShowWindow.js"></script>

<h2>Liste des enseignants intervenant dans le Pôle Info 3</h2>

{LIST_LETTERS}

<ul>
<!-- BEGIN MEMBER -->
<li>
  <!-- BEGIN FC_ANCHOR -->
  <A NAME="{LETTER}"></A>
<!-- END FC_ANCHOR -->
   <b>{MEMBER_FIRST_NAME}
               {MEMBER_LAST_NAME}</a></b>, {MEMBER_CV}
         <a target=_top href="{MEMBER_HOME_PAGE}">Home page</a>
    <br>
    <b>Cours</b>: {LISTE_COURS}    
  </li>
<!-- END MEMBER -->
</ul>
</BODY>
</HTML>
