<?php
/**
 * Model corresponding to notifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;
use Illuminate\Support\Arr;

class Notification extends Model
{
    protected $fillable = [
        'type', 'notifiable_id', 'notifiable_type', 'data', 'created_by_id', 'created_by_type',
        'created_at', 'updated_at', 'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $with = [
        'notifiable', 'created_by'
    ];

    protected $withModelName = [
        'notifiable',
    ];

    protected $hidden = [
        'type', 'notifiable_id', 'notifiable_type',
    ];

    protected $must = [
        'notifiable', 'data', 'created_at', 'read_at', 'created_by'
    ];

    protected $selection = [
        'paginate' => 10,
        'filter' => [],
    ];

    /**
     * Launched at the model creation.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (isset($model->data['created_by'])) {
                $data = $model->data;
                $created_by = Arr::pull($data, 'created_by');
                $model->data = $data;

                $model->created_by_id = $created_by['id'];
                $model->created_by_type = $created_by['type'];
            }

            return $model;
        });
    }

    /**
     * Relation with the notified entity.
     *
     * @return mixed
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Relation with creator entity.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo();
    }
}
