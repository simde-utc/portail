<?php
/**
 * Ajoute un sélecteur concernant la suppression.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasDeletedSelection
{
    /**
     * Sélecteur suppression.
     *
     * @param  Builder $query
     * @param  string  $choice
     * @return mixed
     */
    public function scopeDeleted(Builder $query, string $choice='without')
    {
        $fieldName = $this->getSelectionOption('deleted.columns.deleted', 'deleted_at');

        switch ($choice) {
            case 'without':
                $query->whereNull($this->getTable().'.'.$fieldName);
                break;
            case 'only':
                $query->whereNotNull($this->getTable().'.'.$fieldName);
                break;
        }

        return $query;
    }
}
