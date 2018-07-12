# Traits

Fonctionnalité php permettant de palier les problèmes dus à l'héritage simple.
Documentation php mieux expliquée : http://php.net/manual/fr/language.oop5.traits.php

Les traits que nous avons définis sont à **utiliser uniquement** dans les modèles. Conceptuellement parlant, ceux-ci doivent s'appliquer à une instance de modèle (et non une collection ou un query builder).

## HasMembers

## HasPermissions

## HasRoles

## HasVisibility

Permet de spécifier si les attributs d'un modèle doivent être cachés en fonction de l'utilisateur qui y accède.

### hide()

La méthode principale de ce trait. Exemple d'utilisation et de réponse :

```php
$group = Group::find(1)->hide();

return response()->json($group, 200);
```

```json
{
    "error": "Vous ne pouvez pas voir cela.",
    "visibility": {
        "id": 10,
        "type": "private",
        "name": "Privée"
    }
}
```

On peut également appliquer la méthode à une collection grâce à la fonction map() :

```php
$groups = Group::all()->map(function ($group) {
    return $group->hide();
});
```

### getVisibilityType()

Permet de renvoyer le type de la visibilité actuelle de l'utilisateur.

### isVisible()

Renvoie vrai si l'utilisateur actuel peut voir le modèle.
