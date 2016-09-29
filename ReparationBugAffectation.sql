CORRECTION BUG id_master dans Affectation (à verifier l'année prochaine):

POUR SITN (sauf Anglais) :
UPDATE Affectation SET id_master='5' WHERE id_contenu IN (SELECT id FROM Contenu, Cours WHERE id=id_contenu AND id_master=5 AND id!=15) AND annee=2010

POUR ID (sauf Anglais, Entrepot, XML) :
 UPDATE Affectation SET id_master = '3' WHERE id_contenu IN (
SELECT id
FROM Contenu, Cours
WHERE id = id_contenu
AND id_master =3
AND id !=15
AND id !=3
AND id !=1
)
AND annee =2010 


Pour mettre à jour l'anglais :
UPDATE Affectation SET id_master = '3' WHERE id_contenu =15 AND id_personne IN (
SELECT id
FROM Personne
WHERE id_master =3
) 

UPDATE Affectation SET id_master = '5' WHERE id_contenu =15 AND id_personne IN (
SELECT id
FROM Personne
WHERE id_master =5
) 

Pour XML :
 UPDATE Affectation SET id_master = '3' WHERE id_contenu =3 AND id_personne IN (
SELECT id
FROM Personne
WHERE id_master =3
) 

Pour Entrepot :
 UPDATE Affectation SET id_master = '3' WHERE id_contenu =1 AND id_personne IN (
SELECT id
FROM Personne
WHERE id_master =3
) 