<?php

namespace App\Services\Visible;

use Auth;
use App\Models\Visibility;

class Visible {
    /**
     *  Fonction cachant les infos dont nous n'avons pas la visibilité
     */
    public static function hide($collection, $user_id = null) {
        if ($user_id === null && Auth::user() !== null)
			$user_id = Auth::user()->id;

		$visibilities = Visibility::get();

		if (get_class($collection) === 'Illuminate\Database\Eloquent\Collection') {
			foreach ($collection as $key => $model) {
				if (!self::isVisible($visibilities, $model, $user_id)) {
					$collection[$key] = self::hideData($model, $visibilities);
				}
			}

			return $collection;
		}
		else {
			if (!self::isVisible($visibilities, $collection, $user_id))
				return self::hideData($collection, $visibilities);
			else
				return $collection;
		}
    }

	/**
	 *  Fonction renvoyant uniquement les visibles
	 */
	public static function with($collection, $user_id = null) {
	    return self::remove($collection, $user_id, false);
	}

	/**
	 *  Fonction renvoyant uniquement les non-visibles
	 */
	public static function without($collection, $user_id = null) {
	    return self::remove($collection, $user_id, true);
	}

	/**
	 *  Fonction renvoyant uniquement les non-visibles avec les infos cachés
	 */
	public static function hideAndWithout($collection, $user_id = null) {
		if ($user_id === null && Auth::user() !== null)
			$user_id = Auth::user()->id;

		$visibilities = Visibility::get();

		if (get_class($collection) === 'Illuminate\Database\Eloquent\Collection') {
			foreach ($collection as $key => $model) {
				if (!self::isVisible($visibilities, $model, $user_id)) {
					$collection[$key] = self::hideData($model, $visibilities);
				}
				else
					$collection->forget($key);
			}

			return $collection;
		}
		else {
			if (!self::isVisible($visibilities, $collection, $user_id)) {
				return self::hideData($collection, $visibilities);
			}
			else
				return null;
		}
	}

    /**
     *  Fonction retirant les modèles à ne pas afficher
     */
    protected static function remove($collection, $user_id, $visible) {
        if ($user_id === null && Auth::user() !== null)
			$user_id = Auth::user()->id;

		$visibilities = Visibility::get();

		if (get_class($collection) === 'Illuminate\Database\Eloquent\Collection') {
			foreach ($collection as $key => $model) {
				if (self::isVisible($visibilities, $model, $user_id) === $visible)
					$collection->forget($key);
			}

			return $collection;
		}
		else {
			if (self::isVisible($visibilities, $collection, $user_id) === $visible)
				return null;
			else
				return $collection;
		}
    }

    /**
     *  Fonction retirant les modèles à ne pas afficher
     */
    protected static function hideData($model, $visibilities) {
        return [
			'id' => $model->id,
			'hidden' => true,
			'visibility' => $visibilities->find($model->visibility_id),
		];
    }

	/**
	 *  Fonction permettant d'indiquer si la ressource peut-être visible ou non pour la personne
	 *  @return boolean
	 */
	protected static function isVisible($visibilities, $model, $user_id = null) {
		if ($visibilities === null || $visibilities->count() === 0 || $visibilities === null)
			return true;

		$visibility_id = $model->visibility_id;

		if ($visibility_id === null)
			$visibility_id = $visibilities->first()->id;

		if ($user_id === null)
			return $visibilities->find($visibility_id)->type === 'public'; // Si on est pas co, on check si la visibilité est publique ou non

		while ($visibility_id !== null) {
			$visibility = $visibilities->find($visibility_id);

			if ($visibility === null)
				return false;

			$type = 'is'.ucfirst($visibility->type);

			if (method_exists(get_class(), $type) && self::$type($model, $user_id))
				return true;

			$visibility_id = $visibility->parent_id;
		}

	    return false;
	}

	protected static function isPublic($model, $user_id) {
		return true;
	}

	protected static function isLogged($model, $user_id) {
		return $user_id !== null;
	}

	protected static function isCas($model, $user_id) {
		return self::isLogged($model, $user_id) && AuthCas::find($user_id)->exists();
	}

	protected static function isContributor($model, $user_id) {
		return self::isCas($model, $user_id) && false; // TODO avec Ginger
	}

	protected static function isPrivate($model, $user_id) {
		try {
			$memberModel = resolve(get_class($model).'Member'); // En faisant ça, on optimise notre requête SQL en évitant de trier la liste des membres
		}
		catch (Exception $e) {
			$memberModel = null;
		}

		return $memberModel !== null && $memberModel::where('user_id', $user_id)->exists() > 0;
	}

	protected static function isOwner($model, $user_id) {
		return $model !== null && $model->user_id === $user_id;
	}
}
