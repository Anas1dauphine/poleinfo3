Requête Philippe :
"SELECT o.*, a.*, 0 as id_enseignant, 'xx' as notes FROM  Contenu o,  "
      . " Affectation a WHERE a.id_personne='$person->id' AND "
      . " a.id_contenu=o.id "
      . " AND annee='$annee_master' order by o.nom ";

Remplacée par :

"(SELECT obligatoire, o.*, a.*, 0 as id_enseignant, 'xx' as notes FROM  Contenu o,"
              . " Affectation a, Personne p, Cours c WHERE a.id_personne='$person->id' AND "
              . " p.id=a.id_personne AND o.id= c.id_contenu AND p.id_master=c.id_master AND "
              . " a.id_master=p.id_master AND a.id_contenu=o.id AND annee='$annee_master' AND "
              . " obligatoire='O' order by o.nom) "
              . " UNION DISTINCT"
              . "(SELECT 'N' as obligatoire, o.*, a.*, 0 as id_enseignant, 'xx' as notes FROM  Contenu o, "
              . " Affectation a WHERE a.id_personne='$person->id' AND a.id_contenu=o.id AND "
              . " o.id NOT IN (SELECT o.id FROM  Contenu o, Affectation a, Personne p, Cours c "
              . " WHERE a.id_personne='$person->id' AND p.id=a.id_personne AND o.id= c.id_contenu AND "
              . " p.id_master=c.id_master AND a.id_master=p.id_master AND a.id_contenu=o.id AND "
              . " annee='$annee_master' AND obligatoire='O') AND "
              . " annee='$annee_master' order by o.nom)"
              . "ORDER by obligatoire DESC, nom" ;




 (
SELECT obligatoire, o . * , a . * , 0 AS id_enseignant, 'xx' AS notes
FROM Contenu o, Affectation a, Personne p, Cours c
WHERE a.id_personne = '1806'
AND p.id = a.id_personne
AND o.id = c.id_contenu
AND p.id_master = c.id_master
AND a.id_master = p.id_master
AND a.id_contenu = o.id
AND annee = '2010'
AND obligatoire = 'O'
ORDER BY o.nom
)
UNION DISTINCT (

SELECT 'N' AS obligatoire, o . * , a . * , 0 AS id_enseignant, 'xx' AS notes
FROM Contenu o, Affectation a
WHERE a.id_personne = '1806'
AND a.id_contenu = o.id
AND o.id NOT
IN (

SELECT o.id
FROM Contenu o, Affectation a, Personne p, Cours c
WHERE a.id_personne = '1806'
AND p.id = a.id_personne
AND o.id = c.id_contenu
AND p.id_master = c.id_master
AND a.id_master = p.id_master
AND a.id_contenu = o.id
AND annee = '2010'
AND obligatoire = 'O'
)
AND annee = '2010'
ORDER BY o.nom
)
ORDER BY obligatoire DESC , nom 


