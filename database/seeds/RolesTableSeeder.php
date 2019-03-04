<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;
use App\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'type' => 'superadmin',
                'name' => 'Super administrateur',
                'description' => 'Personne ayant réellement tous les droits sur le service',
                'position' => 0,
                'limited_at' => 2,
                'owned_by' => new User,
                'permissions' => [
                    'superadmin',
                    'user',
                    'auth',
                    'user-preference',
                    'user-detail',
                    'session',
                ],
            ],
            [
                'type' => config('portail.roles.admin.users'),
                'name' => 'Administrateur',
                'description' => 'Personne ayant tous les droits sur le serveur',
                'position' => 1,
                'owned_by' => new User,
                'parents' => [
                    'superadmin',
                ],
                'permissions' => [
                    'admin',
                    'asso',
                    'group',
                    'client',
                    'service',
                    'room',
                    'role',
                    'permission',
                    'bobby',
                    'access',
                    'search',
                    'user-impersonate',
                    'user-contributeBde',
                    'article',
                    'article-action',
                    'handle-access',
                    'asso-access',
                    'asso-type',
                    'semester',
                    'place',
                    'event',
                    'partner',
                    'contact-type',
                    'tag',
                    'visibility',
                    'asso-type',
                    'contact',
                    'booking',
                    'comment',
                    'calendar',
                    'booking-type',
                    'location',
                    'event-detail',
                    'notification',
                ],
            ],
            [
                'type' => config('portail.roles.admin.assos'),
                'name' => 'Président',
                'description' => 'Responsable d\'une organisation',
                'position' => 0,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'permissions' => [
                    'treasury',
                    'ticketing',
                    'calendar',
                    'event',
                    'contact',
                    'article',
                    'comment',
                    'data',
                    'booking',
                    'role',
                    'permission',
                    'bobby',
                    'access',
                ],
            ],
            [
                'type' => 'vice-president',
                'name' => 'Vice-Président',
                'description' => 'Co-responsable d\'une organisation',
                'position' => 1,
                'limited_at' => 4,
                'owned_by' => new Asso,
                'parents' => [
                    config('portail.roles.admin.assos'),
                ],
                'permissions' => [
                    'treasury',
                    'ticketing',
                    'calendar',
                    'event',
                    'contact',
                    'comment',
                    'article',
                    'data',
                    'booking',
                    'role',
                    'permission',
                    'bobby',
                    'access',
                ],
            ],
            [
                'type' => 'secretaire general',
                'name' => 'Secrétaire Général',
                'description' => 'Administrateur de l\'organisation',
                'position' => 5,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'vice-president',
                ],
                'permissions' => [
                    'calendar',
                    'event',
                    'contact',
                    'article',
                    'comment',
                    'data',
                    'booking',
                    'role',
                    'permission',
                ],
            ],
            [
                'type' => 'vice-secretaire',
                'name' => 'Vice-Secrétaire',
                'description' => 'Adjoint du secrétaire',
                'position' => 25,
                'limited_at' => 4,
                'owned_by' => new Asso,
                'parents' => [
                    'secretaire general',
                ],
                'permissions' => [
                    'calendar',
                    'event',
                    'contact',
                    'article',
                    'data',
                    'booking',
                ],
            ],
            [
                'type' => 'treasury',
                'name' => 'Trésorier',
                'description' => 'Responsable de la trésorie',
                'position' => 10,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'vice-president',
                ],
                'permissions' => [
                    'event',
                    'treasury',
                ],
            ],
            [
                'type' => 'vice-treasury',
                'name' => 'Vice-Trésorier',
                'position' => 30,
                'description' => 'Co-responsable de la trésorie',
                'limited_at' => 4,
                'owned_by' => new Asso,
                'parents' => [
                    'treasury',
                ],
                'permissions' => [
                    'treasury',
                    'event',
                ],
            ],
            [
                'type' => 'bureau',
                'name' => 'Bureau',
                'description' => 'Membre du bureau',
                'position' => 15,
                'owned_by' => new Asso,
                'parents' => [
                    'vice-president',
                ],
                'permissions' => [
                    'booking',
                    'event',
                ],
            ],
            [
                'type' => 'resp informatique',
                'name' => 'Responsable Informatique',
                'description' => 'Responsable informatique de l\'association',
                'position' => 50,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'calendar',
                    'booking',
                    'event',
                    'article'
                ],
            ],
            [
                'type' => 'developer',
                'name' => 'Développeur',
                'description' => 'Membre de l\'équipe informatique de l\'association',
                'position' => 100,
                'owned_by' => new Asso,
                'parents' => [
                    'resp informatique',
                ],
            ],
            [
                'type' => 'resp communication',
                'name' => 'Responsable Communication',
                'description' => 'Responsable communication de l\'association',
                'position' => 55,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'event',
                    'article',
                    'comment',
                    'booking',
                    'data',
                ],
            ],
            [
                'type' => 'communication',
                'name' => 'Chargé de communication',
                'description' => 'Membre de l\'équipe communication de l\'association',
                'position' => 105,
                'owned_by' => new Asso,
                'parents' => [
                    'resp communication',
                ],
            ],
            [
                'type' => 'resp animation',
                'name' => 'Responsable Animation',
                'description' => 'Responsable animation de l\'association',
                'position' => 60,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'event',
                    'booking',
                ],
            ],
            [
                'type' => 'animation',
                'name' => 'Chargé de l\'animation',
                'description' => 'Membre de l\'équipe animation de l\'association',
                'position' => 110,
                'owned_by' => new Asso,
                'parents' => [
                    'resp animation',
                ],
            ],
            [
                'type' => 'resp partenariat',
                'name' => 'Responsable Partenariat',
                'description' => 'Responsable partenariat de l\'association',
                'position' => 65,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'event',
                    'booking',
                ],
            ],
            [
                'type' => 'partenariat',
                'name' => 'Chargé du partenariat',
                'description' => 'Membre de l\'équipe partenariat de l\'association',
                'position' => 115,
                'owned_by' => new Asso,
                'parents' => [
                    'resp partenariat',
                ],
            ],
            [
                'type' => 'resp logistique',
                'name' => 'Responsable Logistique',
                'description' => 'Responsable logistique de l\'association',
                'position' => 70,
                'limited_at' => 1,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'event',
                    'booking',
                    'bobby',
                    'access',
                ],
            ],
            [
                'type' => 'logistique',
                'name' => 'Chargé de la logistique',
                'description' => 'Membre de l\'équipe logistique de l\'association',
                'position' => 120,
                'owned_by' => new Asso,
                'parents' => [
                    'resp logistique',
                ],
                'permissions' => [
                    'bobby',
                ],
            ],
            [
                'type' => 'resp',
                'name' => 'Responsable',
                'description' => 'Responsable dans l\'association',
                'position' => 75,
                'owned_by' => new Asso,
                'parents' => [
                    'bureau',
                ],
                'permissions' => [
                    'event',
                    'booking',
                ],
            ],
            [
                'type' => 'membre',
                'name' => 'Membre de l\'association',
                'description' => 'Membre de l\'équipe associative',
                'owned_by' => new Asso,
                'parents' => [
                    'resp',
                ],
            ],
            [
                'type' => 'group admin',
                'name' => 'Administrateur',
                'description' => 'Administrateur du group',
                'position' => 1,
                'limited_at' => 1,
                'owned_by' => new Group,
                'permissions' => [
                    'member',
                    'calendar',
                    'event',
                    'contact',
                    'article',
                    'role',
                ],
            ],
            [
                'type' => 'group planner',
                'name' => 'Planificateur',
                'description' => 'Personne planifiant les évènements et les calendriers du groupe',
                'position' => 5,
                'owned_by' => new Group,
                'permissions' => [
                    'calendar',
                    'event',
                ],
            ],
            [
                'type' => 'group writer',
                'name' => 'Ecrivain',
                'description' => 'Personne écrivant les articles du groupe',
                'position' => 10,
                'owned_by' => new Group,
                'permissions' => [
                    'article',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::create([
                'type' => $role['type'],
                'name' => $role['name'],
                'position' => ($role['position'] ?? null),
                'description' => $role['description'],
                'limited_at' => ($role['limited_at'] ?? null),
                'owned_by_id' => $role['owned_by']->id,
                'owned_by_type' => get_class($role['owned_by']),
            ])->givePermissionTo(($role['permissions'] ?? []))
                ->assignParentRole(($role['parents'] ?? []));
        }
    }
}
