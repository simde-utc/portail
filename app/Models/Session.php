<?php
/**
 * Modèle correspondant aux sessions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class Session extends Model
{
    protected $fillable = [
        'id', 'user_id', 'auth_provider', 'ip_address', 'user_agent', 'payload', 'last_activity',
    ];
}
