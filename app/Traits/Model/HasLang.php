<?php
/**
 * Ajoute un sélecteur concernant la langue.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasLang
{
    /**
     * Sélecteur de langue.
     *
     * @param  Builder $query
     * @param  string  $lang
     * @return mixed
     */
    public function scopeLang(Builder $query, string $lang)
    {
        if ($lang === '*') {
            $lang = \Auth::user()->getLang();
        }

        return $query->where('lang', $lang);
    }
}
