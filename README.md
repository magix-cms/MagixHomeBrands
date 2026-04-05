# MagixHomeBrands

[![Release](https://img.shields.io/github/release/magix-cms/MagixHomeBrands.svg)](https://github.com/magix-cms/MagixHomeBrands/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixHomeBrands.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixHomeBrands** est un plugin de gestion de partenaires, clients ou marques pour Magix CMS 4. Il permet d'afficher un carrousel de logos élégant, entièrement responsive et optimisé pour les performances web (Core Web Vitals) sur votre page d'accueil.

## 🌟 Fonctionnalités principales

* **Carrousel Fluide (SplideJS)** : Affichage en ruban défilant avec boucle infinie, navigation tactile (swipe) sur mobile, et adaptation automatique du nombre de logos visibles selon la taille de l'écran.
* **Traitement d'Image Intelligent** : Intégration native avec l'`ImageTool` de Magix CMS. Utilise un redimensionnement de type `basic` (proportionnel) combiné à `object-fit: contain` en CSS pour garantir qu'aucun logo rectangulaire ne soit tronqué.
* **Interface Drag & Drop** : Réorganisation intuitive de l'ordre d'affichage des partenaires par simple glisser-déposer dans l'administration (AJAX).
* **SEO & Accessibilité** : Support natif du Lazy Loading pour les images hors de la ligne de flottaison, gestion des balises `alt`, et intégration d'infobulles (Tooltips Bootstrap) pour afficher la description de la marque au survol.
* **Gestion Multilingue** : Les titres, descriptions et liens de redirection peuvent être traduits et adaptés pour chaque langue configurée sur le CMS.

## ⚙️ Installation

1. Téléchargez la dernière version du plugin.
2. Placez le dossier `MagixHomeBrands` dans le répertoire `plugins/` de votre projet.
3. Dans l'administration de Magix CMS, naviguez vers **Extensions > Plugins** et cliquez sur **Installer**.
4. Le script d'installation créera automatiquement les tables SQL nécessaires (`mc_plug_homebrands` et ses traductions) et injectera les formats de redimensionnement d'images optimisés (`small`, `medium`, `large`).
5. **Note** : Le composant frontend s'accroche automatiquement à la zone `displayHomeBottom` de votre layout.

## 🚀 Utilisation

### Côté Administration (Backend)
1. Accédez à l'interface du plugin depuis votre tableau de bord.
2. Cliquez sur **"Ajouter un Partenaire"**.
3. Chargez le logo (qui sera automatiquement redimensionné et converti en WebP si le serveur le supporte).
4. Renseignez le nom, l'URL de redirection (avec option d'ouverture dans un nouvel onglet) et une description optionnelle.
5. Utilisez la liste principale pour trier vos partenaires par glisser-déposer.

### Côté Public (Frontend)
Le carrousel s'affiche automatiquement. L'intégration garantit un HTML sémantique sans styles en ligne (zero inline-styles), laissant le contrôle visuel total au fichier SCSS du module.

## 🛠️ Architecture Technique

* **Composants Natifs** : Exploitation stricte de la fonction `{include file="components/img.tpl"}` du CMS pour une gestion parfaite du responsive (`srcset`, `sizes`) et du `fetchpriority`.
* **Zero-Reload** : Formulaire d'édition propulsé par la validation AJAX de Magix CMS, incluant une prévisualisation instantanée de l'image sélectionnée en JavaScript (`URL.createObjectURL`).
* **Design Pattern** : Respect strict du modèle MVC avec une séparation claire entre les requêtes SQL (QueryBuilder) du Backend et celles allégées du Frontend.

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)