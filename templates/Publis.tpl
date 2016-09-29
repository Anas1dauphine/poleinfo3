<HTML>
<HEAD>
<TITLE>Lab. LAMSADE - Univ. Paris Dauphine</TITLE>
<link href="styles.css" rel="stylesheet" type="text/css" />
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>

<!-- JavaScript function to display paper infos  -->
<script language="JavaScript1.2" src="ShowWindow.js"></script>

Use the form below to express your search criteria. Note
that <i>Any</i> always represent a neutral choice. Once
the form is submitted, you will get the list
of publications that match your criteria. You can
refine them later if necessary.
<p>

The search on character strings always relies on pattern-matching.
Hence <i>omp</i>, <i>thi</i>, <i>Do co</i> will all match with the title
<i>Do computer think</i>.

<FORM  METHOD='POST' ACTION='publis.php' NAME='Form'>
<input type='hidden' name='action' value='1'>

<center>
<TABLE BORDER=2>
 <tr class='header'>
    <th  colspan='6'>Criterias</th>
  </tr>

 <TR class='even'>
  <TD><B>Title</B>
<br><font color='green' size=-1>Enter a substring for the title</font>
  </TD>
    <TD><INPUT TYPE='TEXT' NAME="title" VALUE="{TITLE_DEF}" 
                        SIZE='20' MAXLENGTH='80'></TD>
  <TD><B>Year</B>
<br><font color='green' size=-1>Choose a year</font>
     </TD><td>{YEARS_LIST}</td> 
  <TD><B>Author</B>
   <br><font color='green' size=-1>Enter a substring <br>for the 
        author's name</font></TD>
    <TD><INPUT TYPE='TEXT' NAME="author" VALUE="{AUTHOR_DEF}" 
                        SIZE='20' MAXLENGTH='80'></TD>
</TR>

 <TR class='odd'>
  <TD><B>Search in abstract for </B>
    <br><font color='green' size=-1>Enter a substring<br>
            for searching the papers' asbtract</font></TD>
    <TD><INPUT TYPE='TEXT' NAME="abstract" VALUE="{ABSTRACT_DEF}" 
                        SIZE='20' MAXLENGTH='80'></TD>
  <TD><B>How published</B>
    <br><font color='green' size=-1>Choose the publication type</font></TD>
      <td>{TYPES_LIST}</TD>
  <TD><B>Full text search</B>
<br><font color='green' size=-1>Enter a substring for<br>full-text search</font></td>
    <TD><INPUT TYPE='TEXT' NAME="editor" VALUE="{HOW_DEF}" 
                        SIZE='20' MAXLENGTH='80'></TD>
</TR>
</TABLE>

<tr valign=top class=even>
  <th><INPUT TYPE='SUBMIT' NAME="search" 
	VALUE="Search" SIZE='0' MAXLENGTH='0'></th>
</tr>
</table>
</center>
</FORM>

<!-- BEGIN RESULTAT -->
<h2>Nb of publications found: {NB_PUBLIS}</h2>
  <ol>{PUBLIS}</ol>
<!-- END RESULTAT -->
</BODY>
</HTML>