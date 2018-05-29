# Commenter

Il est important de commenter le code écrit. Au minimum toutes les fonctions.

Le SiMDE utilise la notation [DocBlock](http://docs.phpdoc.org/guides/docblocks.html#anatomy-of-a-docblock).

Exemple sur une fonction :

```php
    /**
     * Ici un résumé de ce que fait la fonction.
     *
     * @param string $astring
     * @param array $anArray
     * @param string|array $aStringOrArray
     * @return Illuminate\Database\Eloquent\Collection
     */
    public foo($aString, $anArray, $aStringOrArray) {
        
        ...

        return $bar; // $bar est une collection
    } 
```

**Remarque :** la ligne vide entre le résumé et la liste des paramètres.
