# Comment

It is really important to comment the written code. At least for each function.

The SiMDE uses the convention [DocBlock](http://docs.phpdoc.org/guides/docblocks.html#anatomy-of-a-docblock).

Example on a function :

```php
    /**
     * Describe here what this function does.
     *
     * @param string $astring
     * @param array $anArray
     * @param string|array $aStringOrArray
     * @return Illuminate\Database\Eloquent\Collection
     */
    public foo($aString, $anArray, $aStringOrArray) {
        
        ...

        return $bar; // $bar is a collection
    } 
```
**Note :** The line between the description and the parameter list must be emtpy.