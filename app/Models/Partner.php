<?php
/**
 * Model corresponding to partners.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = [
        'name', 'description', 'image', 'website', 'address', 'postal_code', 'city',
    ];

    protected $must = [
        'name', 'description', 'image', 'website', 'address', 'postal_code', 'city',
    ];

    protected $selection = [
        'paginate' 	=> 10,
        'order'		=> [
            'default' 	=> 'latest',
            'columns'	=> [
                'name' 	=> 'name',
            ],
        ],
    ];
}
