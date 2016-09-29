INSERT INTO Affectation
SELECT DISTINCT m.id, u.id, p.id_personne, 2010, NULL
FROM Cours c, Contenu u, Master m, Preference p, Personne p1
WHERE p1.id_master = c.id_master
AND p1.id = '1805'
AND p1.id=p.id_personne
AND  p.id_contenu=u.id
AND  c.id_contenu=u.id
AND obligatoire = 'O'
AND m.id IN (1, 3, 5)
AND p.id_master = m.id
AND c.id_master = m.id
AND (
u.id, p.id_personne
) NOT IN (SELECT id_contenu, id_personne
FROM Affectation
)
AND p1.annee_master =2010
ORDER BY m.nom 

SELECT * FROM Affectation a, Contenu c  WHERE id_personne='1805' AND id=a.id_contenu AND annee=2010 ORDER by nom

DELETE FROM Affectation WHERE id_personne='1805'