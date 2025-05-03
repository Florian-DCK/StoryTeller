npx @tailwindcss/cli -i ./api/public/input.css -o ./api/public/global.css --watch

# Installation du projet
Nous avons utilisé `composer` pour installer des dépendances PHP utiles comme :
- `mustache/mustache` (v2.14) : Moteur de templates pour générer les vues HTML
- `vlucas/phpdotenv` (v5.6) : Gestion des variables d'environnement depuis le fichier `.env`

Pour installer les dépendances, exécutez la commande suivante à la racine du projet :
```bash
composer install
```

# Variables d'environnement
Les variables d'environnement sont stockées dans un fichier `.env` à la racine du projet. Il faut le créer et y ajouter les variables suivantes :

```bash
DB_HOST=ip
DB_NAME=name de la base de données (story_db par défaut)
DB_USER=username
DB_PASSWORD=password
DB_PORT=port
```

Nous utilisons le package `dotenv` pour charger les variables d'environnement.


# Script SQL
Le script sql contenant la création de la base de données et les données d'exemple se trouve à la racine du projet. Il faut l'exécuter et il créera une base de données nommée "story_db"

La base de données contient les tables suivantes :
- `Users` : Gestion des utilisateurs du site (identifiants, informations de profil)
- `Stories` : Informations générales sur les histoires (titre, auteur, likes, date)
- `Themes` : Liste des thèmes disponibles
- `StoriesThemes` : Table de jonction entre histoires et thèmes
- `Participations` : Contenu des contributions aux histoires
- `ParticipationImage` : Images associées aux participations
- `UserLikes` : Table de jonction pour gérer les likes des utilisateurs
- `UserStoriesFollow` : Table de jonction pour gérer les histoires suivies
- `ContentType` : Types de contenu possibles (text, image)

# Lazy loading
Nous avond rencontré un problème de chargement lent sur l'acceuil alors nous avons mis en place un lazy loading sur les histoires et les commentaires.