<?php
/**
 * Global functions.
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
     * Return the `between` string for validations Requests.
     *
     * @param string $validationId
     * @return string
     */
    function validation_between(string $validationId)
    {
        $values = config("validation.$validationId");
        return "between:".$values['min'].",".$values['max'];
    }
}

if (!function_exists('validation_max')) {
    /**
     * Return the max integer value for Migrations
     *
     * @param string $validationId
     * @return integer
     */
    function validation_max(string $validationId)
    {
        return config("validation.$validationId.max");
    }
}

if (!function_exists('convertPipeToArray') && !function_exists('stringToArray')) {
    /**
     * Convert string lists into an array.
     *
     * @param  string|array $pipeString
     * @return mixed
     */
    function convertPipeToArray($pipeString)
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

    /**
     * Convert string lists into an array.
     *
     * @param  string|array $toArray
     * @return array
     */
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
     * Cut the given text at a given length.
     *
     * @param string  $input
     * @param integer $length
     * @param string  $ellipses
     * @return string
     */
    function trimText(string $input, int $length, string $ellipses='...')
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
