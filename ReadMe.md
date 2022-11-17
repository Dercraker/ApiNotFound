# Api NotFound

* Description
* Documentation
* Installation

### Description

L'Api NotFound a pour but de fournir un catalogue d'erreurs, chacune possèdant plusieurs images et de messages personnalisés.
Certains end point permettront d'obtenir tous les messages et images, tandis que d'autres permettront d'avoir une image et un message aléatoire en fonction du code erreur.

### Documentation

### Nécessité
Vous devez avoir d'installé un serveur Apache et MySQL. 
Nous vous recommendons :
 - XAMPP pour Windows/Mac  https://www.apachefriends.org/fr/index.html
 - LAMP pour Linux. https://doc.ubuntu-fr.org/lamp

### Installation
1. Cloner le repository.
2. Importer le dossier dans votre IDE.
3. Copier/Coller le fichier ".env" et renommer le ".env.local".
4. Modifier DATABASE_URL="mysql://root@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"
3. Ouvrir un terminal et effectuer un "composer install"
