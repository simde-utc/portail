<?php

namespace App\Traits;

use Spatie\Permission\Models\Role;

// Trait qui permet d'ajouter des contraintes de vérification avant l'ajout d'un rôle à une personne
trait Roles {
	public static function bootRoles() {
        self::creating(function ($model) {
			return $model::preventBadRoles($model);
		});

        self::updating(function ($model) {
			return $model::preventBadRoles($model);
		});
    }

	private static function preventBadRoles($model) {
        $role = Role::find($model->role_id);

		if ($role->only_system)
			return false;
		elseif ($role->limited_at !== null) {
			$exists = (new $model)::where('role_id', $model->role_id);

			if ($model->roleContraints) {
				foreach ($model->roleContraints as $contraint)
					$exists->where($contraint, $model->$contraint);
			}

			if ($exists->count() >= $role->limited_at)
				return false;
		}
	}
}
