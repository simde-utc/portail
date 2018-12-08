<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);

function arrayToTable($data) {
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

function adminValue($value) {
    if (is_array($value)) {
        return arrayToTable($value);
    } else if (is_bool($value)) {
        return $value ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
    } else if (is_null($value)) {
        return '<i class="fa fa-question text-warning"></i>';
    }

    return $value;
}
