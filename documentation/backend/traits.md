# Traits

PHP functionality to reduce some limitations of single inheritance.
Official Trait documentation : http://php.net/manual/en/language.oop5.traits.php

The traits we defined **must only be used** within models.
Conceptually speaking, they must apply to a model instance and not to a collection or a query builder. 

## HasMembers

## HasPermissions

## HasRoles

## HasVisibility

Specifies if some of the model attributes are to be hidden depending on wich user wants to access it.

### hide()

The main methode of this trait. Example of usage and answer:

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
We can also apply that method to a collection with the `map()` function:

```php
$groups = Group::all()->map(function ($group) {
    return $group->hide();
});
```

### getVisibilityType()

Return a user current visibility type.

### isVisible()

Returns true if current user is allowed to see the current model
