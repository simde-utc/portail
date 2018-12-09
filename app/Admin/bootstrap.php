<?php
/**
 * Fonctions chargées uniquement pour l'interface admin
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

/**
 * Permet de convertir un tableau pour l'interface admin.
 *
 * @param  array $data
 * @return mixed
 */
function arrayToTable(array $data)
{
    $rows = [];

    foreach ($data as $key => $value) {
        $value = adminValue($value);

        if (Illuminate\Support\Arr::isAssoc($data)) {
            $rows[] = ['<b>'.$key.'</b>', $value];
        } else {
            $rows[] = [$value];
        }
    }

    return new Encore\Admin\Widgets\Table([], $rows);
}

/**
 * Converti les valeurs pour l'admin.
 *
 * @param  mixed $value
 * @return mixed
 */
function adminValue($value)
{
    if (is_array($value)) {
        return arrayToTable($value);
    } else if (is_bool($value)) {
        return $value ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
    } else if (is_null($value)) {
        return '<i class="fa fa-question text-warning"></i>';
    }

    return $value;
}
