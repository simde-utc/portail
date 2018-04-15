<?php

namespace App\Traits;

use App\Models\Permission;

// Trait qui permet d'ajouter des contraintes de vérification avant l'ajout d'un rôle à une personne
trait Permissions {
	public static function bootPermissions() {
        self::creating(function ($model) {
			return $model::preventBadPermissions($model);
		});

        self::updating(function ($model) {
			return $model::preventBadPermissions($model);
		});
    }

	private static function preventBadPermissions($model) {
        $permission = Permission::find($model->permission_id);

		if ($permission->only_system)
			return false;
		elseif ($permission->limited_at !== null) {
			$exists = (new $model)::where('permission_id', $model->$permission_id);

			if ($model->$permissionContraints) {
				foreach ($model->$permissionContraints as $contraint)
					$exists->where($contraint, $model->$contraint);
			}

			if ($exists->count() >= $permission->limited_at)
				return false;
		}
	}
}
