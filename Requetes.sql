#
# Les étudiants des masters 1, 3, 5 qui n'ont pas saisi leurs
# préférences
#

select prenom, nom FROM Personne LEFT Join Preference on
(id=id_personne) where niveau is NULL and roles like '%S%' AND
Personne.id_master IN (1, 3, 5);

#
# Les cours obligatoires pour un master
#

SELECT m.nom, u.nom
FROM Cours c, Contenu u, Master m
WHERE c.id_contenu= u.id
AND obligatoire='O'
AND m.id IN (1, 3, 5)
AND m.id=c.id_master
ORDER BY m.nom;

#
# Les affectations imperatives des etudiants
#

INSERT INTO Affectation
SELECT m.id, u.id, p.id, 2006, NULL
FROM Cours c, Contenu u, Master m, Personne p
WHERE c.id_contenu= u.id
AND obligatoire='O'
AND m.id IN (1, 3, 5)
AND m.id=c.id_master
AND p.id_master=m.id;


#
# Les étudiants qui ont mis un niveau de 4, pour des cours
# non obligatoires
#

INSERT INTO Affectation
SELECT DISTINCT m.id, u.id, p.id_personne, 2006, NULL
FROM Cours c, Contenu u, Master m, Preference p
WHERE c.id_contenu=u.id
AND m.id IN (1, 3, 5)
AND p.id_master=m.id
AND p.id_contenu=u.id
AND niveau=4
AND (u.id, p.id_personne) NOT IN (SELECT id_contenu, id_personne
             FROM Affectation)
ORDER BY m.nom;

#
# Les étudiants inscrits à un cours et ne faisant pas partie d'un ou plusieurs
# masters
#

SELECT id_personne, id_contenu 
FROM Affectation a
GROUP BY id_personne, id_contenu
HAVING count(*) > 1;


SELECT a.id_master, id_contenu, a.id_personne, prenom, nom, p.id_master 
FROM Affectation a LEFT JOIN Personne p ON (p.id=a.id_personne)
WHERE id_contenu=15 AND nom IS NULL
;

#
# Recherche des affectations incompatibles (raison hoarie, ou autre)
#

SELECT a1.id_personne
FROM Affectation a1, Affectation a2
WHERE a1.id_personne=a2.id_personne
AND a1.id_contenu=40
AND a2.id_contenu=17
;

# Les cours qui vont ensemble (J2EE, Agile)

SELECT prenom, nom
FROM Personne p, Affectation a
WHERE p.id=a.id_personne
AND id_contenu=63
AND not exists (SELECT * FROM Affectation a2 WHERE a2.id_personne=p.id
              and id_contenu=2);

# Controle d e non-doublon

SELECT prenom, p.nom, p.id_master, SUM(ects) as "Total ECTS"
FROM Personne p, Affectation a, Contenu c
WHERE p.id=a.id_personne
AND p.id_master IN (1, 3, 5)
AND a.id_contenu=c.id
GROUP BY prenom, nom
HAVING SUM(ects) < 40
        or  SUM(ects) > 42
;

SELECT * FROM Affectation 
where (id_contenu, id_personne) IN (
SELECT c.id, p.id
FROM Personne p, Affectation a, Contenu c
WHERE p.id=a.id_personne
AND a.id_contenu=c.id
GROUP BY c.id, p.id
HAVING count(*) > 1
)
and id_master=3
;

delete from Affectation where (id_contenu, id_personne)
in (select id_contenu, id_personne from to_del)
and id_master=3;

