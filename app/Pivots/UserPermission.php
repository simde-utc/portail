<?php
/**
 * Pivot corresponding to the permissions - users relation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Pivots;

use App\Models\{
	User, Semester, Permission
};

class UserPermission extends Pivot
{
    protected $table = 'users_permissions';

    protected $attributes = [
        'user_id', 'semester_id', 'permission_id', 'validated_by_id', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'user_id', 'semester_id', 'permission_id', 'validated_by_id'
    ];

    protected $with = [
        'user',
        'semester',
        'permission',
        'validated_by',
    ];

    /**
     * Relation with access.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with the semester.
     *
     * @return mixed
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relation with access.
     *
     * @return mixed
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Relation with the user user who validated him.
     *
     * @return mixed
     */
    public function validated_by()
    {
        return $this->belongsTo(User::class);
    }
}
