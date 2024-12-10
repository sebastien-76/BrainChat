# Projet BrainChat s4 simplon

Ce repo contient une application brainstorming permettant de faire appel à l'IA pour assister les participants.

## Prérequis

- Linux, MacOS, Windows
- Bash
- PHP 8
- Composer
- Symfony-cli
- Mariadb 10

## Installation

```
git clone https://github.com/sebastien-76/brainchat
cd brainchat
composer install

```
Créer une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créer un fichier `.env.local` à la racine du projet

```
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=123
DATABASE_URL="mysql://user:password@127.0.0.1:3306/brainchat?serverVersion=mariadb-10.6.18&charset=utf8mb4"
```

Penser  à changer la variable `APP_SECRET` et les codes d'accès dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaîne de caractère de 32 caractères en hexadécimal.**

## Migration et fixtures

Pour que l'application soit utilisable, il faut créer le schéma de la base de données et charger les données.

On utilise le script suivant :

```
./bin/dofilo.sh
```

## Mail
Pour tester les fonctionalités utilisant le mail, on utilise mailpit

On utilise le script suivant :

```
./bin/mailpit
```

Pour avoir accès aux mails envoyés par l'application, ouvrir la page suivante: [https://localhost:8025](https://localhost:8025)


## Utilisation
Lancer le serveur web de développement :

```
symfony serve -d
```

Puis ouvrir la page suivante : [https://localhost:8000](https://localhost:8000)