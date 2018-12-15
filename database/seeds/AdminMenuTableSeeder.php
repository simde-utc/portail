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
                'title' => 'Rechercher',
                'uri' => 'search',
            ],
            [
                'title' => 'Ressources',
                'permission' => 'admin',
                'uri' => '',
                'icon' => 'fa-database',
                'elements' => [
                    [
                        'title' => 'Accès',
                        'uri' => 'resources/access',
                        'permission' => 'access',
                        'icon' => 'fa-door-closed',
                    ],
                    [
                        'title' => 'Articles',
                        'uri' => 'resources/articles',
                        'permission' => 'article',
                        'icon' => 'fa-newspaper',
                    ],
                    [
                        'title' => 'Actions d\'article',
                        'uri' => 'resources/article-actions',
                        'permission' => 'article-action',
                        'icon' => 'fa-actions',
                    ],
                    [
                        'title' => 'Accès par association',
                        'uri' => 'resources/asso-access',
                        'permission' => 'asso-access',
                        'icon' => 'fa-door',
                    ],
                    [
                        'title' => 'Utilisateurs',
                        'uri' => 'resources/users',
                        'permission' => 'user',
                        'icon' => 'fa-users',
                    ],
                    [
                        'title' => 'Clients OAuth',
                        'uri' => 'resources/clients',
                        'permission' => 'client',
                        'icon' => 'fa-fire',
                    ],
                ]
            ],
            [
                'title' => 'Administration',
                'permission' => 'admin',
                'uri' => '',
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
                'uri' => $menu['uri'],
                'icon' => ($menu['icon'] ?? 'fa-'.$menu['uri']),
                'permission' => ($menu['permission'] ?? $menu['uri']),
            ])->id;

            if (isset($menu['elements'])) {
                $this->createMenus($menu['elements'], $menu_id);
            }
        }
    }
}
