
Here is the list of your publications, sorted
by year in reverse order. 


<table border='3' cellpadding='2'>
  <tr class='header'>
     <th>Year</th>
      <th>Title</th>
     <th>Authors</th>
     <th colspan=2>Action</th>
  </tr> 

<!-- BEGIN PUBLIS -->
<tr class='{CSS_CLASS}'>
  <a name="{PUBLI_ID"}>

  <td>{PUBLI_YEAR}</td>
  <td>{PUBLI_TITLE}</td>
  <td>{PUBLI_AUTHORS}</td>
  <td><a href="intranet.php?option=9&id={PUBLI_ID}&modify=1">Modify</a></td>
    <td><a  onClick="ConfirmAction('This will remove this publication?', 
	   'intranet.php?option=9&id={PUBLI_ID}&delete=1')" 
	  href='#{PUBLI_ID}'>Delete</a></td>
</tr> 
<!-- END PUBLIS -->
</table>
