# API NotFound

## English

* Description
* Requirement
* Installation

### Description
The NotFound API aims to provide a catalogue of errors, each with several custom images and messages.
Some endpoints will get all messages and images, while others will get a random image and message based on the error code.
### Requirement
You must have an Apache and MySQL server installed. 
We recommend :
 - [XAMPP](https://www.apachefriends.org/index.html) for Windows/Mac
 - [LAMP](https://ubuntu.com/server/docs/lamp-applications) for Linux

### Installation
1. Clone the repository.
2. Import the folder into your IDE.
3. Copy/Paste the `.env` file and rename it `.env.local`.
4. Open it and edit
`# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"` in `DATABASE_URL="mysql://root@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"`
5. Open a terminal and perform a `composer install`.
6. Perform a `symfony serve`.


## Français

* Description
* Prérequis
* Installation

### Description

L'API NotFound a pour but de fournir un catalogue d'erreurs, chacune possédant plusieurs images et messages personnalisés.
Certains end-point permettront d'obtenir tous les messages et images, tandis que d'autres permettront d'avoir une image et un message aléatoire en fonction du code erreur.

### Prérequis
Vous devez avoir d'installé un serveur Apache et MySQL. 
Nous vous recommandons :
 - [XAMPP](https://www.apachefriends.org/fr/index.html) pour Windows/Mac 
 - [LAMP](https://doc.ubuntu-fr.org/lamp) pour Linux.

### Installation
1. Cloner le repository.
2. Importer le dossier dans votre IDE.
3. Copier/Coller le fichier `.env` et renommer le `.env.local`.
4. L'ouvrir et modifier 
`# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"` en `DATABASE_URL="mysql://root@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"`
5. Ouvrir un terminal et effectuer un `composer install`
6. Effectuer un `symfony serve`.
