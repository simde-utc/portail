<?php
/**
 * Adds the controller an Image management for a given resource.
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
     * Defines the image for a resource. 
     *
     * @param Request $request
     * @param mixed   $model
     * @param string  $path
     * @param string  $model_id
     * @param string  $input
     * @return mixed
     */
    protected function setImage(Request $request, $model, string $path, string $model_id=null, string $input='image')
    {
        if ($request->hasFile($input)) {
            $image = $request->file($input);
            $path = '/images/'.$path.'/'.($model_id ? $model_id.'/' : '');
            $name = time().'.'.$image->getClientOriginalExtension();

            $image->move(public_path($path), $name);

            return $model->update([
                $input => url($path.$name),
            ]);
        }

        return $model;
    }

    /**
     * Deletes an image.
     *
     * @param  string $path
     * @return boolean
     */
    protected function deleteImage(string $path)
    {
        return unlink(public_path($path));
    }
}
