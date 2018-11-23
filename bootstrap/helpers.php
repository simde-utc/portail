<?php
/**
 * Fonctions globales.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

if (!function_exists('validation_between')) {

    /**
     * Donne la chaine between pour les validations Requests
     *
     * @return string
     */
    function validation_between(string $id)
    {
        $values = config("validation.$id");
        return "between:".$values['min'].",".$values['max'];
    }

}

if (!function_exists('validation_max')) {

    /**
     * Donne la valeur entière max pour les Migrations
     *
     * @return integer
     */
    function validation_max(string $id)
    {
        return config("validation.$id.max");
    }

}

if (!function_exists('convertPipeToArray') && !function_exists('stringToArray')) {

    function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (! in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }

    function stringToArray($toArray)
    {
        if (is_string($toArray) && false !== strpos($toArray, '|')) {
            $toArray = convertPipeToArray($toArray);
        }

        if (is_string($toArray) || is_numeric($toArray)) {
            $toArray = [$toArray];
        }

        if (!is_array($toArray)) {
            return $toArray;
        }

        foreach ($toArray as $key => $value) {
            if (is_numeric($value)) {
                $toArray[$key] = intval($value);
            }
        }

        return $toArray;
    }

}

if (!function_exists('trimText')) {

    /**
     * trims text to a space then adds ellipses if desired
     * @param string  $input    text to trim
     * @param integer $length   in characters to trim to
     * @param boolean $ellipses if ellipses (...) are to be added
     * @return string
     */
    function trimText($input, $length, $ellipses='...')
    {
        if (strlen($input) <= $length) {
            return $input;
        }

        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        if ($ellipses) {
            $trimmed_text .= $ellipses;
        }

        return $trimmed_text;
    }

}
