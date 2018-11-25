<?php
/**
 * Modèle correspondant aux salles.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Room extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

    protected $fillable = [
        'location_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'calendar_id',
        'capacity',
    ];

    protected $hidden = [
        'location_id', 'calendar_id', 'created_by_id', 'created_by_type', 'owned_by_type', 'owned_by_type', 'visibility_id',
    ];

    protected $with = [
        'location', 'calendar', 'created_by', 'owned_by', 'visibility',
    ];

    protected $must = [
        'location', 'owned_by', 'calendar', 'capacity',
    ];

    /**
     * Appelé à la création du modèle.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->calendar_id) {
                $model->calendar_id = Calendar::create([
                    'name' => 'Réservation - '.(Location::find($model->location_id)->name),
                    'description' => 'Planning de réservation',
                    'visibility_id' => $model->visibility_id,
                    'created_by_id' => $model->created_by_id,
                    'created_by_type' => $model->created_by_type,
                    'owned_by_id' => $model->owned_by_id,
                    'owned_by_type' => $model->owned_by_type,
                ])->id;
            }
        });

        self::updated(function ($model) {
            $model->calendar->update([
                'visibility_id' => $model->visibility_id,
                'owned_by_id' => $model->owned_by_id,
                'owned_by_type' => $model->owned_by_type,
            ]);
        });
    }

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation avec le lieu.
     *
     * @return mixed
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation avec le calendrier.
     *
     * @return mixed
     */
    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * Relation avec le créateur.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo('created_by');
    }

    /**
     * Relation avec la possédeur.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Relation avec les réservations.
     *
     * @return mixed
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
