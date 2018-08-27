<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasImages
{
	protected function prepareImage($path, $input = 'image', $name = null) {
		if ($request->hasFile($input)) {
			$image = $request->file($input);
			$path = '/images/'.$path.'/';
			$name = ($name ?: time()).'.'.$image->getClientOriginalExtension();

	        $image->move(public_path($path), $name);
			$request->merge([
				$input => url($path.$name),
			]);
		}
	}
}
