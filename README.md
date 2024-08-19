# Internship_AxiansIT
Projet de Plateforme de Gestion
Description
Ce projet est une plateforme de gestion d'appareils et de modèles, développée pour permettre aux administrateurs de gérer les paramètres, les modèles, et les dispositifs associés. Le système comprend des fonctionnalités pour ajouter, modifier et supprimer des modèles et des dispositifs, ainsi que pour gérer les paramètres associés.

Fonctionnalités
Gestion des Modèles

Création, modification et suppression de modèles.
Ajout de nouveaux paramètres à chaque modèle.
Affichage et gestion des paramètres associés à chaque modèle.
Gestion des Dispositifs

Ajout, modification et suppression de dispositifs.
Association des dispositifs avec des modèles spécifiques.
Téléchargement d'images pour les dispositifs.
Interface Utilisateur

Interface d'administration avec un panneau de gestion des dispositifs et des modèles.
Barre de navigation latérale pour une navigation facile.
Modals pour l'ajout et l'édition des dispositifs et des modèles.
Installation
Cloner le Répertoire

bash
Copier le code
git clone https://github.com/Chayma03SADIKI/Internship_AxiansIT.git
cd Internship_AxiansIT
Configurer le Serveur Web

Assurez-vous que votre serveur web (par exemple, Apache ou Nginx) est configuré pour pointer vers le répertoire de votre projet.

Configurer la Base de Données

Créez une base de données MySQL.
Importez les fichiers SQL fournis dans le dossier database/ pour créer les tables nécessaires.
Configurer les Fichiers de Configuration

Renommez le fichier config.sample.php en config.php.
Mettez à jour les informations de connexion à la base de données dans config.php.
Installer les Dépendances

Si vous utilisez Composer, exécutez :

bash
Copier le code
composer install
Utilisation
Accéder à la Plateforme

Ouvrez votre navigateur et accédez à http://localhost/ ou à l'URL de votre serveur.

Connexion

Connectez-vous avec les informations d'identification d'administrateur fournies.

Gestion des Modèles et Dispositifs

Utilisez les sections "Modèles" et "Dispositifs" pour ajouter, modifier, ou supprimer des éléments.
