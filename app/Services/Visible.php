<?php

namespace App\Services;

use Auth;
use App\Models\Visibility;

class Visible {
    /**
     *  @return Collection
     */
    public static function checkCollection($collection, $user_id = null) {
        if ($user_id === null && Auth::user() !== null)
			$user_id = Auth::user()->id;

		$visibilities = Visibility::get();

		foreach ($collection as $key => $model) {
			$visibility = $visibilities->get($model->visibility_id);

			if (!self::isVisible($visibility, $user_id)) {
				$collection[$key] = [
					'id' => $model->id,
					'visibility' => $visibility,
				];
			}
		}

		return $collection;
    }

	/**
	 *  @return Collection
	 */
	public static function are($collection, $user_id = null) {
	    if ($user_id === null && Auth::user() !== null)
			$user_id = Auth::user()->id;

		$visibilities = Visibility::get();

		foreach ($collection as $key => $model) {
			$visibility = $visibilities->get($model->visibility_id);

			if (!self::isVisible($visibility, $user_id)) {
				$collection->forget($key);
			}
		}

		return $collection;
	}

	/**
	 *  @return Collection
	 */
	protected static function isVisible($visibility, $user_id = null) {
	    return false;
	}
}
