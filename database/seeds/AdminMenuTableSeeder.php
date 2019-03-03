<?php

use Illuminate\Database\Seeder;
use App\Admin\Models\Menu;

class AdminMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'title' => 'Ressources',
                'permission' => '',
                'icon' => 'fa-search',
                'elements' => [
                    [
                        'title' => 'Un cotisant',
                        'permission' => 'user-contributeBde',
                        'icon' => 'fa-money',
                        'uri' => 'search/contributor',
                    ],
                    [
                        'title' => 'Un utilisateur',
                        'permission' => 'user',
                        'icon' => 'fa-user',
                        'uri' => 'search/user',
                    ]
                ]
            ],
            [
                'title' => 'Gestion des accès',
                'uri' => 'access',
                'permission' => 'handle-access',
                'icon' => 'fa-universal-access',
            ],
            [
                'title' => 'Ressources',
                'permission' => '',
                'icon' => 'fa-database',
                'elements' => [
                    [
                        'title' => 'Accès',
                        'uri' => 'resources/access',
                        'permission' => 'access',
                        'icon' => 'fa-universal-access',
                    ],
                    [
                        'title' => 'Actions d\'article',
                        'uri' => 'resources/article-actions',
                        'permission' => 'article-action',
                        'icon' => 'fa-thumbs-o-up',
                    ],
                    [
                        'title' => 'Articles',
                        'uri' => 'resources/articles',
                        'permission' => 'article',
                        'icon' => 'fa-newspaper-o',
                    ],
                    [
                        'title' => 'Associations',
                        'uri' => 'resources/assos',
                        'permission' => 'asso',
                        'icon' => 'fa-university',
                    ],
                    [
                        'title' => 'Accès par association',
                        'uri' => 'resources/asso-access',
                        'permission' => 'asso-access',
                        'icon' => 'fa-chain',
                    ],
                    [
                        'title' => 'Authentifications',
                        'permission' => 'auth',
                        'icon' => 'fa-unlock-alt',
                        'elements' => [
                            [
                                'title' => 'Application',
                                'uri' => 'resources/auth-apps',
                                'permission' => 'auth',
                                'icon' => 'fa-mobile',
                            ],
                            [
                                'title' => 'CAS-UTC',
                                'uri' => 'resources/auth-cas',
                                'permission' => 'auth',
                                'icon' => 'fa-hand-o-down',
                            ],
                            [
                                'title' => 'Mot de passe',
                                'uri' => 'resources/auth-passwords',
                                'permission' => 'auth',
                                'icon' => 'fa-lock',
                            ],
                        ]
                    ],
                    [
                        'title' => 'Calendriers',
                        'uri' => 'resources/calendars',
                        'permission' => 'calendar',
                        'icon' => 'fa-calendar',
                    ],
                    [
                        'title' => 'Clients OAuth',
                        'uri' => 'resources/clients',
                        'permission' => 'client',
                        'icon' => 'fa-fire',
                    ],
                    [
                        'title' => 'Commentaires',
                        'uri' => 'resources/comments',
                        'permission' => 'comment',
                        'icon' => 'fa-comments-o',
                    ],
                    [
                        'title' => 'Détails utilisateurs',
                        'uri' => 'resources/user-details',
                        'permission' => 'user-detail',
                        'icon' => 'fa-address-card-o',
                    ],
                    [
                        'title' => 'Détails événements',
                        'uri' => 'resources/event-details',
                        'permission' => 'event-detail',
                        'icon' => 'fa-tint',
                    ],
                    [
                        'title' => 'Emplacements',
                        'uri' => 'resources/places',
                        'permission' => 'place',
                        'icon' => 'fa-map-marker',
                    ],
                    [
                        'title' => 'Evènements',
                        'uri' => 'resources/events',
                        'permission' => 'event',
                        'icon' => 'fa-clock-o',
                    ],
                    [
                        'title' => 'Groupes',
                        'uri' => 'resources/groups',
                        'permission' => 'group',
                        'icon' => 'fa-users',
                    ],
                    [
                        'title' => 'Lieux',
                        'uri' => 'resources/locations',
                        'permission' => 'location',
                        'icon' => 'fa-map-pin',
                    ],
                    [
                        'title' => 'Moyen de contacts',
                        'uri' => 'resources/contacts',
                        'permission' => 'contact',
                        'icon' => 'fa-phone',
                    ],
                    [
                        'title' => 'Notifications',
                        'uri' => 'resources/notifications',
                        'permission' => 'notification',
                        'icon' => 'fa-bell-o',
                    ],
                    [
                        'title' => 'Partenaires',
                        'uri' => 'resources/partners',
                        'permission' => 'partner',
                        'icon' => 'fa-handshake-o',
                    ],
                    [
                        'title' => 'Permissions',
                        'uri' => 'resources/permissions',
                        'permission' => 'permission',
                        'icon' => 'fa-certificate',
                    ],
                    [
                        'title' => 'Préférences utilisateurs',
                        'uri' => 'resources/user-preferences',
                        'permission' => 'user-preference',
                        'icon' => 'fa-suitcase',
                    ],
                    [
                        'title' => 'Réservations',
                        'uri' => 'resources/bookings',
                        'permission' => 'booking',
                        'icon' => 'fa-plus-square-o',
                    ],
                    [
                        'title' => 'Roles',
                        'uri' => 'resources/roles',
                        'permission' => 'role',
                        'icon' => 'fa-user-secret',
                    ],
                    [
                        'title' => 'Salles',
                        'uri' => 'resources/rooms',
                        'permission' => 'room',
                        'icon' => 'fa-home',
                    ],
                    [
                        'title' => 'Semestres',
                        'uri' => 'resources/semesters',
                        'permission' => 'semester',
                        'icon' => 'fa-hourglass-half',
                    ],
                    [
                        'title' => 'Services',
                        'uri' => 'resources/services',
                        'permission' => 'service',
                        'icon' => 'fa-fa',
                    ],
                    [
                        'title' => 'Tags',
                        'uri' => 'resources/tags',
                        'permission' => 'tag',
                        'icon' => 'fa-tag',
                    ],
                    [
                        'title' => 'Types',
                        'permission' => 'asso-type|contact-type|booking-type',
                        'icon' => 'fa-hashtag',
                        'elements' => [
                            [
                                'title' => 'Association',
                                'uri' => 'resources/asso-types',
                                'permission' => 'asso-type',
                                'icon' => 'fa-university',
                            ],
                            [
                                'title' => 'Contact',
                                'uri' => 'resources/contact-types',
                                'permission' => 'contact-type',
                                'icon' => 'fa-address-card-o',
                            ],
                            [
                                'title' => 'Réservation',
                                'uri' => 'resources/booking-types',
                                'permission' => 'booking-type',
                                'icon' => 'fa-plus-square-o',
                            ],
                        ]
                    ],
                    [
                        'title' => 'Utilisateurs',
                        'uri' => 'resources/users',
                        'permission' => 'user',
                        'icon' => 'fa-user',
                    ],
                    [
                        'title' => 'Visibilités',
                        'uri' => 'resources/visibilities',
                        'permission' => 'visibility',
                        'icon' => 'fa-eye',
                    ],
                ]
            ],
            [
                'title' => 'Administration',
                'permission' => 'admin',
                'icon' => 'fa-unlock-alt',
                'elements' => [
                    [
                        'title' => 'Dashboard',
                        'uri' => 'dashboard',
                        'permission' => 'admin',
                        'icon' => 'fa-columns'
                    ],
                    [
                        'title' => 'Graphiques',
                        'uri' => 'charts',
                        'permission' => 'admin',
                        'icon' => 'fa-area-chart'
                    ],
                    [
                        'title' => 'Logs',
                        'uri' => 'logs',
                        'permission' => 'admin',
                        'icon' => 'fa-eye'
                    ],
                    [
                        'title' => 'Tester l\'Api',
                        'uri' => 'api-tester',
                        'permission' => 'admin',
                        'icon' => 'fa-sliders'
                    ],
                ]
            ],
        ];

        $this->createMenus($menus);
    }

    /**
     * Création des menus et des sous-menus.
     *
     * @param  array   $menus
     * @param  integer $parent_id
     * @return void
     */
    public function createMenus(array $menus, int $parent_id=0)
    {
        foreach ($menus as $menu) {
            $menu_id = Menu::create([
                'parent_id' => $parent_id,
                'title' => $menu['title'],
                'uri' => ($menu['uri'] ?? ''),
                'icon' => ($menu['icon'] ?? 'fa-'.$menu['uri']),
                'permission' => ($menu['permission'] ?? $menu['uri']),
            ])->id;

            if (isset($menu['elements'])) {
                $this->createMenus($menu['elements'], $menu_id);
            }
        }
    }
}
