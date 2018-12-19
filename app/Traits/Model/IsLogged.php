<?php
/**
 * Indique que le modèle est surveillé.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Monolog\Handler\StreamHandler;

trait IsLogged
{
    /**
     * On log les actions.
     *
     * @param  string $operation
     * @param  string $title
     * @param  array  $old
     * @param  array  $new
     * @return void
     */
    protected static function logChanges(string $operation, string $title, array $old, array $new)
    {
        $user = (\Auth::guard('admin')->user() ?? \Auth::guard('web')->user());

        if ($user) {
            $user = $user->hideData();
            $defaultHandlers = \Log::getHandlers();
            $data = ['exception' => compact('user', 'operation', 'old', 'new')];
            $name = \ModelResolver::getName(static::class);
            $path = 'logs/models/'.$name.'/'.$name.date('-Y-m-d').'.log';
            \Log::setHandlers([new StreamHandler(storage_path($path))])
                ->info($title.' par '.$user->name.' (id: '.$user->id.') '.json_encode($data));

            \Log::setHandlers($defaultHandlers);
        }
    }

    /**
     * Au lancement du modèle, crée log dynamiquement.
     *
     * @return void
     */
    protected static function bootIsLogged()
    {
        static::created(function ($model) {
            static::logChanges('create', 'Création', [], $model->getAttributes());
        });

        static::updating(function ($model) {
            static::logChanges('update', 'Modification', $model->getOriginal(), $model->getAttributes());
        });

        static::deleting(function ($model) {
            static::logChanges('delete', 'Suppression', $model->getOriginal(), []);
        });
    }
}
