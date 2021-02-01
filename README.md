# Projet PRWB 2021 - Trello

## Notes de livraison itération 1

Base de données:
De nouvelles données se trouvent dans le fichier database/prwb_2021_g05_add_data.sql
 - Ajout des utilisateurs suivants: `severine@test.be` et `sinouhe@test.be` et ont le mot de passe `Password1,`:
	- 100 tableaux ont été ajouté pour Sinouhé
	- 1 tableau (test100CC) a été ajouté pour Boris contenant 100 colonnes et 500 cartes)

Bugs:
 - Dans le cas très spécifique où il y a une erreur (par exemple, titre non unique) dans le edit board, nous obtenons une erreur avec l'extension validator W3C de chrome. Pourtant, sur le site officel (https://validator.w3.org/), il n'y a pas d'erreur sur cette page.


## Notes de livraison itération 2

## Notes de livraison itération 3


## Installation

- Déplacez le dossier à la racine de votre serveur web (dossier `projects` ou `htdocs` en fonction de votre installation)
- Accédez à l'url [http://localhost/prwb_2021_g05/setup/install](http://localhost/prwb_2021_g05/setup/install)

## Utilisateurs

Tous les utilisateurs (`boverhaegen@epfc.eu`, `bepenelle@epfc.eu`, `brlacroix@epfc.eu` et `xapigeolet@epfc.eu`) ont le mot de passe `Password1,` (remarquez qu'il se termine par une virgule).

## Sauvegarde de la base de données

- Vérifiez le chemin de `mysql dump` dans le fichier de configuration
- Accédez à l'url [http://localhost/prwb_2021_g05/setup/export](http://localhost/prwb_2021_g05/setup/export) 
    - `database/prwb_2021_g05.sql` contient le schéma de la base de données
    - `database/prwb_2021_g05_dump.sql` contient le dump de la base de données
- Pour la restaurer, accédez à l'url [http://localhost/prwb_2021_g05/setup/install](http://localhost/prwb_2021_g05/setup/install)


