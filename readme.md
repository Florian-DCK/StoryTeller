npx @tailwindcss/cli -i ./api/public/input.css -o ./api/public/global.css --watch

# Version En ligne du Site
Pour simplifier le test et la démonstration du site, nous avons mis en place une version en ligne sur un VPS privé. Vous pouvez y accéder à l'adresse suivante : [http://51.38.186.179/](http://51.38.186.179/)

# Installation du projet
Nous avons utilisé `composer` pour installer des dépendances PHP utiles comme :
- `mustache/mustache` (v2.14) : Moteur de templates pour générer les vues HTML
- `vlucas/phpdotenv` (v5.6) : Gestion des variables d'environnement depuis le fichier `.env`

Pour installer les dépendances, exécutez la commande suivante à la racine du projet :
```bash
composer install
```

Tailwind CSS est utilisé pour le style du site. Nous avons installé `tailwindcss` et `postcss` via npm. Pour installer les dépendances, exécutez la commande suivante à la racine du projet :
```bash
npm install
```

Cependant Tailwind CSS n'est pas nécessaire pour le bon fonctionnement du site et n'est utile que lors de la création de nouvelles pages. Pour l'utilisation, il est possible de se passer de Tailwind CSS et donc de ne pas l'installer.

## Variables d'environnement
Les variables d'environnement sont stockées dans un fichier `.env` à la racine du projet. Il faut le créer et y ajouter les variables suivantes :

```bash
DB_HOST=ip
DB_NAME=name de la base de données (story_db par défaut)
DB_USER=username
DB_PASSWORD=password
DB_PORT=port
```

Nous utilisons le package `dotenv` pour charger les variables d'environnement.

## Script SQL
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

### schéma de la base de données
![Schéma de la base de données](/api/public/images/diagram.png)

# But du site internet
Le site internet a pour but de permettre aux utilisateurs de créer des histoires collaboratives. Chaque utilisateur peut créer une histoire et ensuite d'autres utilisateurs peuvent y participer en ajoutant du contenu. Les histoires sont organisées par thèmes et les utilisateurs peuvent liker les histoires qui les intéressent.

## Fonctionnalités
- Authentification : Les utilisateurs peuvent se connecter et s'inscrire sur le site.
- Création d'histoires : Les utilisateurs peuvent créer des histoires en choisissant un titre et un thème.
- Participation : Les utilisateurs peuvent participer à des histoires en ajoutant du texte.
- consulter les histoires : Les utilisateurs peuvent consulter les histoires et les participations des autres utilisateurs.
- consulter le profil : Les utilisateurs peuvent consulter leur profil et celui des autres utilisateurs.

### La gestion des images
Les images sont stockées dans un dossier local `ressources/avatar/` et sont référencées dans la base de données par leur path. Elles sont vérifiées pour s'assurer qu'elles sont bien au format image et qu'elles ne dépassent pas une taille de 5Mo. Le nom de l'image est celui de l'id de l'utilisateur pour les retrouver facilement.


# Utilisation du projet
Une fois le projet installé, la base de données créée, le .env configuré, les dépendances installées et le serveur démarré, vous arriverez sur la page d'accueil du site. Ici sans être connecté vous pourrez consulter les histoires déjà créées normalement par le script SQL (Ou certaines par de vrais utilisateurs pour la version en ligne).
Vous pourrez également consulter les profiles des utilisateurs et les histoires qu'ils ont créées en cliquant sur leur profil.

Pour vous connecter, il faut cliquer sur le bouton "Connexion" en haut à droite de la page. Vous serez redirigé vers la page de connexion. Si vous n'avez pas de compte, vous pouvez en créer un en cliquant sur le bouton "Inscription".

Sur la version en ligne vous pouvez vous connecter avec les identifiants suivants :
- **Identifiant** : `admin`
- **Mot de passe** : `admin`
Pour la version locale installée, vous pouvez créer un compte avec les identifiants de votre choix, et ensuite manuellement switcher la colonne `isAdmin` de la table `Users` à 1 pour avoir accès à toutes les fonctionnalités.

Une fois connecté, vous pourrez créer une histoire en cliquant sur le bouton "Créer une histoire" en haut à gauche de la page. Vous serez redirigé vers la page de création d'histoire. Vous pourrez choisir un titre et un thème pour votre histoire. Une fois l'histoire créée, vous serez redirigé vers la page de l'histoire où vous pourrez ajouter du contenu supplémentaire si vous le souhaitez.

> **Attention** : Il est possible de créer une histoire sans thème ou sans contenu, mais il est fortement recommandé d'en choisir un pour que les autres utilisateurs puissent la trouver facilement. (dans un futur, nous mettrons en place une vérification pour éviter cela)

> **Attention** : Vos participations sont limitées à un maximum de 1000 caractères. Si vous souhaitez ajouter plus de contenu, il faudra créer une nouvelle participation.

Une fois l'histoire créée, vous pourrez la consulter en cliquant sur le titre de l'histoire. Vous pourrez également consulter les participations des autres utilisateurs et liker les histoires qui vous plaisent.

Une autre fonctionnalité débloquée lors de la connexion est la possibilité de participer à une histoire. Pour cela, il suffit de cliquer sur le bouton "Participer" en bas de l'histoire (n'est pas présent si l'histoire a déjà plus de deux participations, dans ce cas le bouton se transforme en "voir plus" et il faut cliquer dessus pour atteindre l'histoire complète et pouvoir y participer). Vous serez redirigé vers la page de participation où vous pourrez ajouter du contenu à l'histoire.


# Les fonctionnalités que nous aurions aimé ajouter
## L'ajout de la possibilité d'illustrer les participations
Dans l'idée originale du projet, nous avions prévu d'ajouter la possibilité d'illustrer les participations avec des images. Cependant, nous n'avons pas eu le temps de mettre en place cette fonctionnalité.

## La possibilité de supprimer une histoire ou une participation
Pour l'instant le compte admin ne peut pas faire grand chose de plus qu'un utilisateur lambda, à part atteindre un dashboard admin. Nous aurions aimé mettre en place un système de suppression d'histoires et de participations, mais nous n'avons pas eu le temps de le faire.

## La possibilité de modifier son profil
Nous aurions aimé mettre en place un système de modification de profil, mais nous n'avons pas eu le temps de le faire.
Cependant, nous avions commencé une implémentation par ces lignes de code : 
```php
function changeAvatar($db, $userId, $avatarPath)
{
    $query = "UPDATE Users SET avatar = ? WHERE id = ?";
    $result = $db->QueryParams($query, 'ss', $avatarPath, $userId);

    return $result;
}
```
`api/src/controller/user.php`

## La possibilité de commenter les participations
Nous avions prévu d'ajouter un système de commentaires sur les participations, mais nous n'avons pas eu le temps de le faire.
Chaque utilisateur aurait pu commenter les participations des autres utilisateurs et les liker. Cela aurait permis d'ajouter une dimension sociale au site et de favoriser les échanges entre les utilisateurs.


# Les problèmes rencontrés et les solutions apportées
## Lazy loading pour contrer le problème de chargement lent
Nous avons rencontré un problème de chargement lent sur l'accueil alors nous avons mis en place un chargement asynchrone sur les histoires et les participations en suivant la logique AJAX. Cette implémentation peut être observée dans le fichier `api/models/lazyLoadService.js`, et est utilisé à la fois dans le fichier `api/public/index.php`, `api/views/search.php`, `api/views/contributions.php` et toutes les autres pages affichant les histoires.
Cette fonctionnalité permet de charger les histoires et les participations pendant que l'utilisateur navigue sur le site, ce qui améliore considérablement la vitesse de chargement des pages.

## Les images sont stockées localement
Ce qui amener comme problèmes des différences entre les images sur le serveur et lorsque l'on travaillais localement. Nous avons donc décidé de réaliser deux bases de données différentes, une pour le serveur et une pour le local. Cela nous a permis de ne pas avoir des comptes enregistré sur la production qui n'ont aucune image car stockée localement. En plus de cela, nous avons mis en place une photo de profil par défaut pour les utilisateurs lorsque la photo de profil rencontre un problème de chargement. Cela peut arriver parceque l'utilisateur n'as pas encore de photo de profil ou que la photo de profile n'est pas trouvée.


# Screenshots
![Page d'accueil](/api/public/images/acceuil.png)
![Page de connexion](/api/public/images/connexion.png)
![Page de création d'histoire](/api/public/images/newStory.png)
![Page de profil](/api/public/images/user.png)
![Composant de recherche](/api/public/images/search.png)