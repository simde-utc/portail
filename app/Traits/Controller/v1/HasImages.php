<?php
/**
 * Ajoute au controlleur une gestion des images pour la ressource concernée.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasImages
{
    /**
     * Défini l'image pour une ressource.
     *
     * @param Request $request
     * @param mixed   $model
     * @param string  $path
     * @param string  $name
     * @param string  $input
     * @return mixed
     */
    protected function setImage(Request $request, $model, string $path, string $name=null, string $input='image')
    {
        if ($request->hasFile($input)) {
            $image = $request->file($input);
            $path = '/images/'.$path.'/';
            $name = ($name ?: time()).'.'.$image->getClientOriginalExtension();

            $image->move(public_path($path), $name);

            return $model->update([
                $input => url($path.$name),
            ]);
        }

        return $model;
    }

    /**
     * Supprime une image.
     *
     * @param  string $path
     * @return boolean
     */
    protected function deleteImage(string $path)
    {
        return unlink(public_path($path));
    }
}
