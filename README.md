# MOODLE CLIENT
*ENSPY - 4e année GENIE INFORMATIQUE*

*Unité d'Enseignement: Interface Homme Machine (IHM)*  
*Encadreur: Pr. BATCHAKUI*

## Table des matières
1. [A propos du projet](#a-propos-du-projet)
2. [Contexte technologique](#contexte-technologique)
3. [Installation et Lancement](#installation-et-lancement)
   - 3.1. [Configuration du serveur Moodle](#configuration-du-serveur-moodle)
   - 3.2. [Lancement du serveur](#lancement-du-serveur)
4. [Documentation](#documentation)
5. [Equipe du projet](#equipe-du-projet)
6. [Contribution](#contribution)
7. [Contact](#contact)
8. [License](#license)


## A propos du projet
Moodle Client est une application web qui permet aux utilisateurs d'interagir avec une plateforme Moodle depuis un appareil externe, comme un ordinateur ou un appareil mobile leur permettant ainsi de gérer leurs cours, évaluations, forums, et ressources pédagogiques.

## Contexte technologique
Moodle Client est réalisé à l'aide des technologies :

- **PHP**, Langage de programmation web
- **Laravel**, Framework PHP
- **Blade**, Moteur de templates Laravel

## Installation et Lancement

### Configuration du serveur Moodle

#### Notes
- On désignera par `moodle_wwwroot` l'adresse de base du site de moodle. (Vous pouvez retrouver cette valeur dans le fichier `/var/www/html/moodle/config.php` via la variable `$CFG->wwwroot`).
- Toutes les instructions détaillées ci-dessous peuvent être suivies sur à partir de la page `<moodle_wwwroot>/admin/settings.php?section=webservicesoverview`.
- Pour la suite, connectez-vous d'abord en tant qu'administrateur sur votre serveur Moodle.

#### 1. Activer les services web
1. Cliquez sur l'étape 1.
2. Activez l'option **Activer les services web**.
3. Enregistrez vos modifications.

#### 2. Activer les protocoles
1. Cliquez sur l'étape 2.
2. Activez le protocole **REST** en cliquant sur l'icône d'activation.
3. Désactivez les protocoles inutilisés pour sécuriser le système.
4. Enregistrez vos modifications.

#### 3. Sélectionner un service
1. Rendez-vous directement à l'étape 5.
2. Cliquez sur **Ajouter un service**.
3. Nommez le service *(exple: `moodle`)*.
4. Cochez **Activer** et décochez **Utilisateurs autorisés uniquement**.
5. Enregistrez vos modifications.

#### 4. Ajouter des fonctions
L'étape précédente vous a ramené sur la page des services
1. Cliquez sur le lien **Fonctions** qui se trouve sur la ligne du service que vous avez créé.
2. Cliquez sur **Ajouter des fonctions**.
3. Ajoutez par exemple `core_user_create_users` (qui sera nécessaire pour le test).

#### 5. Vérifier les capacités des utilisateurs
1. Créez un rôle spécifique pour le service web via **Administration du site > Utilisateurs > Permissions > Définir des rôles** par exemple `admin`.
2. Cliquez sur **Ajouter un rôle**.
3. Attribuez des configurations selon votre bon-vouloir.
4. A la toute fin, permettez les capacités suivantes *(vous pouvez les rechercher via la barre de recherche tels que cités)*: 
   - **webservice:createtoken**
   - **webservice/rest:use**
5. Créer le rôle (sauvegarde)
6. Rendez-vous sur **Administration du site > Utilisateurs > Permissions > Attribuer des rôles système**.
7. Choisissez le rôle que vous venez de créer et ajoutez ce rôle à votre utilisateur. *La sauvegarde est automatique.*

#### 6. Génerer le jeton d'authentification
1. Rendez-vous sur **Administration du site > Serveur > Services Web > Gérer les jetons**.
2. Créer un jeton, configurez-le et enregistrez les modifications.

#### 7. Tester le service
A l'aide d'un outil comme Postman ou Curl, vous pouvez tester le service en utilisant le jeton géneré.

1. Executez la requête suivante en renseignant la valeur de `<JETON>`:
```bash
   curl -X POST "http://localhost/webservice/rest/server.php?wstoken=<JETON>&wsfunction=core_user_create_users&moodlewsrestformat=json" \
      --data "users[0][username]=newuser" \
      --data "users[0][password]=#Password123" \
      --data "users[0][firstname]=John" \
      --data "users[0][lastname]=Doe" \
      --data "users[0][email]=newuser@example.com"
```

*Vous devriez voir un résultat du type : `[{"id":3,"username":"newuser"}]`. Cela prouve que le test est réussi !**

2. Copiez le jeton pour le sauvegarder précieusement. Il doit vous être secret.

### Lancement du projet Moodle Client

#### Prérequis
Avant de lancer le projet, assurez-vous que les éléments suivants sont installés sur votre système :

- **PHP** >= 8.2
- **Composer**
- **Node.js** et **npm** (ou **Yarn**) pour gérer les assets front-end
- **MySQL** ou un autre serveur de base de données compatible
- **Serveur web** (Apache ou Nginx)
- **Moodle** configuré avec des services web activés

#### Étapes pour lancer le projet

##### 1. Cloner le dépôt
```bash
git clone https://github.com/atabong45/moodle_laravel.git
cd moodle_laravel
```

##### 2. Installer les dépendances
Exécutez la commande suivante pour installer les dépendances backend avec Composer :

```bash
composer install
```

Ensuite, installez les dépendances front-end avec npm :

```bash
npm install
```

Enfin, compilez le projet :

```bash
npm run build
```

##### 3. Configurer l'environnement
Copiez le fichier `.env.example` en `.env` avec la commande `cp .env.example .env`. Ensuite, configurez les paramètres suivants :

- **Connexion à la base de données** : Renseignez les champs `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, et `DB_PASSWORD`.

- **URL et token Moodle** : Ajoutez ceci dans votre fichier `.env` en modifiant ces variables selon votre convenance :

```bash   
MOODLE_API_URL=http://localhost/webservice/rest/server.php
MOODLE_API_TOKEN=<TOKEN>
```

##### 4. Générer une clé d'application Laravel
```bash
php artisan key:generate
```

##### 5. Migrer la base de données
Lancez les migrations pour créer les tables nécessaires :

```bash
php artisan migrate
```

##### 6. Compiler les assets
Pour compiler les fichiers front-end avec Vite, exécutez :

```bash
npm run build
```

En mode développement, utilisez :

```bash
npm run dev
```

##### 7. Lancer le serveur local
Démarrez le serveur Laravel avec la commande suivante :

```bash
php artisan serve
```

L'application sera disponible sur [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Equipe du projet
Notre équipe est constituée d'étudiants de l'Ecole Nationale Supérieure Polytechnique de Yaoundé qui suivent :

| NAME                         | Matriculation Number |
|------------------------------|----------------------|
| DANGA PATCHOUM Blonde        | 21P169               |
| VUIDE OUENDEU Jordan         | 21P018               |


## Contribution
We welcome contributions from the academic community and industry professionals. To contribute:

1. Fork the project
2. Create your branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Contact
For more information about the project, contact the development team at Polytechnique Yaoundé.

## License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

---
*Projet développé dans le cadre de l'unité d'enseignement Interface Homme Machine (IHM)*
*Département de Genie Informatique*  
*Polytechnique Yaoundé, 2024*