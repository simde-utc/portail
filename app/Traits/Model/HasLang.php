<?php
/**
 * Adds a selector concerning language.
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
     * Lang selector.
     *
     * @param  Builder $query
     * @param  string  $lang
     * @return mixed
     */
    public function scopeLang(Builder $query, string $lang)
    {
        if ($lang === '*') {
            return $lang;
        } else if ($lang === '~') {
            if ($user = \Auth::user()) {
                $lang = $user->getLang();
            } else {
                $lang = 'fr';
            }
        }

        return $query->where('lang', $lang);
    }
}
