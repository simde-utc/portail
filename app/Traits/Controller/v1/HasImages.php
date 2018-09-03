<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasImages
{
	protected function setImage($request, $model, $path, $name = null, $input = 'image') {
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

	protected function deleteImage($path) {
		return unlink(public_path($path));
	}
}
