# Introduction #

En complément d'outils indispensable, par exemple mod\_cband pour Apache, APC pour php, une extension de gestion de cache pour DotClear, il peut être intéressant d'affiner la gestion des ressources au niveau de DotClear.

En effet, certains grandes entreprises (ou des utilisateurs malveillants) peuvent aspirer votre blog à très grande vitesse, saturant le serveur. Il est souvent difficile de limiter cela car ces outils sont peu respectueux des craw-delay et aspirant préférentiellement les fichiers php, lourds à générer, au lieu des images et autres fichiers statiques.

Le but est donc de stocker en base de données les accès au blog DotClear, et si l'utilisateur a un comportement anormal : changement d'user-agent, fréquence de changement de page trop important, etc ... de le bloquer.


# Installation #

Télécharger la dernière version du plugin, et installez là comme extension dans DotClear2. C'est fini !
En cas de bug ou de problème, merci d'utiliser préférentiellement les "issues" Google Code.

# Paramétrages avancés #

Dans le menu about:config de DotClear, il est possible d'affiner les réglages du plugin. Tout est expliqué et vous pouvez affiner les réglages.
Une option pratique pour affiner les réglages est l'envoi d'email lors d'un blacklistage. Cela permet rapidement, sans aller chercher les logs du serveur web, de modifier les réglages si besoin.

# Comment participer au développement ? #

Tout le monde peut participer à son niveau : rapporter un bug, proposer une amélioration, tester, ajouter un bout de code. Connectez vous à votre compte Google et faites en la demande sur ce projet.