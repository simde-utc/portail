<?php
/**
 * Modèle correspondant aux détails des événements.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use App\Models\Event;

class EventDetail extends Model
{
    use HasKeyValue;

    protected $table = 'events_details';

    protected $fillable = [
        'event_id', 'key', 'value', 'type',
    ];

    protected $must = [
        'key', 'value'
    ];

    /**
     * Relation avec l'événement.
     * @return mixed
     */
    public function event()
    {
        $this->belongsTo(Event::class);
    }
}
