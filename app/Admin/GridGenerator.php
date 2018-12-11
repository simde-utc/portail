<?php

namespace App\Admin;

use Encore\Admin\Grid;

class GridGenerator extends Generator
{
    public function __construct(string $model) {
        parent::__construct(Grid::class, $model);
    }

    protected function callCustomMethods($field) {
        return $field->sortable();
    }
}
