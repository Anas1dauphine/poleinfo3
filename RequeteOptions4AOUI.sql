INSERT INTO Affectation
SELECT DISTINCT m.id, u.id, p.id_personne, 2010, NULL
FROM Cours c, Contenu u,
MASTER m, Preference p, Personne p1
WHERE c.id_contenu = u.id
AND m.id
IN ( 1, 3, 5 )
AND p.id_master = m.id
AND p.id_contenu = u.id
AND p1.id = p.id_personne
AND niveau =4
AND p1.id = '1805'
AND (
u.id, p.id_personne
) NOT
IN (

SELECT id_contenu, id_personne
FROM Affectation
)
AND p1.annee_master =2010
ORDER BY m.nom;
