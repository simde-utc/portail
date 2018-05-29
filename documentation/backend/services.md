# Services

Les services ont pour but d'implémenter des API externes ou des fonctionnalités particulières tels que le CAS ou Ginger.

Namespace : `\App\Services\...`
Dossier :   `app/Services`

Pour créer un nouveau service/système d'authentification, il suffit de créer une classe hérité du service AuthService.php et d'overrider les fonctions de base. Il est aussi nécessaire d'ajouter dans config/auth.php le service.

## CAS

Le CAS est un système d'authentification automatiquement géré par le service Cas créé pour l'occasion. Il gère automatiquement la connexion, et la déconnexion

## Ginger

https://github.com/simde-utc/ginger