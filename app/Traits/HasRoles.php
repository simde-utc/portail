<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Carbon\Carbon;

trait HasRoles
{
    use HasPermissions;

    public static function bootHasRoles() {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
        });
    }

	public function assignRole($roles, int $semester_id = null, int $givenBy = null) {
		if (!is_array($roles))
			$roles = [$roles];

		if ($semester_id === null)
			$semester_id = Semester::getThisSemester()->id;

		$addRoles = [];

		foreach ($roles as $key => $role) {
			$one = Role::getRole($role, $this->getTable());

			if ($one === null)
				throw new \Exception('Il n\'est pas autorisé d\'associer ce role');

			if ($one->limited_at !== null && $one->countUsers() >= $one->limited_at)
				throw new \Exception('Le nombre de personnes ayant ce role a été dépassé');

			$addRoles[$one->id] = [
				'semester_id' => $semester_id,
				'given_by' => $givenBy,
				'created_at' => Carbon::now(),
			];
		}

		$this->roles()->attach($addRoles);

		return $this;
	}

    public function removeRole($roles, int $semester_id = null) {
		if (!is_array($roles))
			$roles = [$roles];

		if ($semester_id === null)
			$semester_id = Semester::getThisSemester()->id;

		$delRoles = [];

		foreach ($roles as $key => $role) {
			$one = Role::getRole($role, $this->getTable());

			array_push($delRoles, $one->id);
		}

		$this->roles()->wherePivot('semester_id', $semester_id)->detach($delRoles);

		return $this;
    }

    public function syncRoles($roles, int $semester_id = null) {
        $this->roles()->detach();

        return $this->assignRole($roles, $semester_id = null);
    }

    public function hasRole($roles, int $semester_id = null): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given role(s).
     *
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAnyRole($roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param string|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect($this->roles->pluck('name')) == $roles;
    }

    /**
     * Return all permissions directly coupled to the model.
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRoleNames(): Collection
    {
        return $this->roles->pluck('name');
    }

    protected function getStoredRole($role): Role
    {
        if (is_numeric($role)) {
            return app(Role::class)->findById($role, $this->getDefaultGuardName());
        }

        if (is_string($role)) {
            return app(Role::class)->findByName($role, $this->getDefaultGuardName());
        }

        return $role;
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (! in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }
}
