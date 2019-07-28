<?php
/**
 * File generating the command: portail:old-to-new.
 * Download all data from the old portal to this one.
 * Based on the old portal version from 1st January 2019.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\{
    DB, Storage
};
use App\Models\{
    Asso, AssoType, Article, Contact, ContactType, Client, Tag, Role, Semester, User, Visibility, AuthCas, Event, Service,
    Access, Room, Location, Booking, BookingType
};

class OldToNew extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:old-to-new {--quick}';

    /**
     * @var string
     */
    protected $description = 'Download all data from the old Portail';

    protected const CIMETIERE = 6;

    protected const MAX_LOGICAL_MEMBERS = 8;

    protected $users = [];
    protected $assos = [];
    protected $semesters = [];
    protected $roles = [];
    protected $events = [];
    protected $access = [];
    protected $rooms = [];
    protected $resultedRoles = [];

    /**
     * Transform old roles labels into the new ones.
     *
     * @var array
     */
    protected const OLD_TO_NEW_ROLES = [
        'Président' => 'Président',
        'Bureau' => 'Bureau',
        'Co-Président' => 'Bureau',
        'Vice-président' => 'Vice-Président',
        'Trésorier' => 'Trésorier',
        'Vice-trésorier' => 'Vice-Trésorier',
        'Secrétaire Général' => 'Secrétaire Général',
        'Secrétaire' => 'Secrétaire Général',
        'Resp Communication' => 'Responsable Communication',
        'Resp Partenariat' => 'Responsable Partenariat',
        'Resp Anim\'' => 'Responsable Animation',
        'Resp Info' => 'Responsable Informatique',
        'Resp Logistique' => 'Responsable Logistique',
        'Développeur' => 'Développeur'
    ];

    protected const DEFAULT_ROLE = 'Membre de l\'association';

    /**
     * Command execution.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->confirm('Ceci va supprimer toutes les données actuelles en faveur de l\'ancien Portail.\
Cela prend en moyenne entre 30 min et 2h. Confirmer ?')) {
            return;
        }

        $bar = $this->output->createProgressBar(11);
        $errors = [];

        $this->info('Migration et nettoyage de la base de données');

        exec('APP_DEBUG=0 php artisan migrate:fresh --seed');

        $bar->advance();
        $this->info(PHP_EOL);

        $this->info('Préparation des données à récupérer');

        $this->users = $this->getDB()->select('SELECT * FROM sf_guard_user');
        $this->assos = $this->getDB()->select('SELECT * FROM asso');
        $this->users = $this->getDB()->select('SELECT * FROM sf_guard_user');
        $this->semesters = $this->getDB()->select('SELECT * FROM semestre');
        $this->roles = $this->getDB()->select('SELECT * FROM role');
        $this->events = $this->getDB()->select('SELECT * FROM event');
        $this->access = $this->getDB()->select('SELECT * FROM charte_locaux_type');
        $this->rooms = $this->getDB()->select('SELECT * FROM salle');

        $bar->advance();
        $this->info(PHP_EOL);

        $next = function() use ($bar) {
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);
        };

        try {
            $errors['Associations'] = $this->addAssos();
            $next();

            $errors['Articles'] = $this->addArticles();
            $next();

            $errors['Utilisateurs'] = $this->addUsers();
            $next();

            $errors['Membres'] = $this->addMembers();
            $next();

            $errors['Evénements'] = $this->addEvents();
            $next();

            $errors['Services'] = $this->addServices();
            $next();

            $errors['Accès'] = $this->addAssosAccess();
            $next();

            $errors['Salles'] = $this->addRooms();
            $next();

            $errors['Réservations'] = $this->addBookings();
            $next();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::delete('DELETE FROM jobs; DELETE FROM failed_jobs;');

            $this->info(PHP_EOL.PHP_EOL);
            $this->info('Rapport:');
            foreach ($errors as $name => $subErrors) {
                $this->info(PHP_EOL);
                $this->info($name.':');

                foreach ($subErrors as $error) {
                    $this->error($error);
                }
            }

            $this->info(PHP_EOL);
            $this->info('Roles:');
            foreach ($this->resultedRoles as $name => $value) {
                if ($value) {
                    $this->info('Le rôle '.$name.' est devenu: '.$value);
                } else {
                    $this->warn('Le rôle '.$name.' est devenu: '.self::DEFAULT_ROLE);
                }
            }
        }
    }

    /**
     * Retrieve old portal database connection.
     *
     * @return Connection
     */
    protected function getDB(): Connection
    {
        return DB::connection('old-portail');
    }

    /**
     * Create image from the old portal.
     *
     * @param  string $url
     * @param  mixed  $model
     * @param  string $path
     * @param  string $name
     * @param  string $input
     * @return mixed
     */
    protected function createImageFromUrl(string $url, $model, string $path, string $name=null, string $input='image')
    {
        $image = file_get_contents($url);
        $path = '/images/'.$path.'/';
        $temp = explode('.', $url);
        $name = ($name ?: time()).'.'.end($temp);
        $dir = public_path($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir.$name, $image);

        return $model->update([
            $input => url($path.$name),
        ]);
    }

    /**
     * Retrieve model from field's value.
     *
     * @param  array   $models
     * @param  integer $value
     * @param  string  $key
     * @return mixed|null
     */
    protected function getModelFrom(array $models, int $value, string $key='id')
    {
        foreach ($models as $model) {
            if ($model->$key === $value) {
                return $model;
            }
        }
    }

    /**
     *
     * Retrieve User.
     *
     * @param  integer $user_id
     * @return mixed|null
     */
    protected function getUser(int $user_id)
    {
        $oldUser = $this->getModelFrom($this->users, $user_id);

        return User::where('email', $oldUser->email_address)->first();
    }

    /**
     * Retrieve Association.
     *
     * @param  integer $asso_id
     * @return mixed|null
     */
    protected function getAsso(int $asso_id)
    {
        $oldAsso = $this->getModelFrom($this->assos, $asso_id);

        return Asso::withTrashed()->where('login', $oldAsso->login)->first();
    }

    /**
     * Retrieve role.
     *
     * @param  integer $role_id
     * @return mixed|null
     */
    protected function getRole(int $role_id)
    {
        $oldRole = $this->getModelFrom($this->roles, $role_id);

        if (isset(self::OLD_TO_NEW_ROLES[$name = $oldRole->name])) {
            return Role::where('name', $this->resultedRoles[$name] = self::OLD_TO_NEW_ROLES[$name])->first();
        }

        $this->resultedRoles[$name] = false;
        return Role::where('name', self::DEFAULT_ROLE)->first();
    }

    /**
     *
     * Retrieve semester.
     *
     * @param  integer $semester_id
     * @return mixed|null
     */
    protected function getSemester(int $semester_id)
    {
        $oldSemester = $this->getModelFrom($this->semesters, $semester_id);

        return Semester::where('name', $oldSemester->name)->first();
    }

    /**
     * Retrieve access.
     *
     * @param  integer $access_id
     * @return mixed|null
     */
    protected function getAccess(int $access_id)
    {
        $oldAccess = $this->getModelFrom($this->access, $access_id);

        return Access::where('utc_access', $oldAccess->correspondance)->first();
    }

    /**
     * Retrieve room.
     *
     * @param  integer $room_id
     * @return mixed|null
     */
    protected function getRoom(int $room_id)
    {
        $oldRoom = $this->getModelFrom($this->rooms, $room_id);
        $names = explode(' ', $oldRoom->name);

        return Room::where('location_id', Location::where('name', 'LIKE', '%'.end($names).'%')->first()->id)->first();
    }

    /**
     * Create associations from the old portal.
     *
     * @return mixed
     */
    protected function addAssos()
    {
        $this->info('Préparation des associations');

        $assoTypes = $this->getDB()->select('SELECT * FROM type_asso');
        $poles = $this->getDB()->select('SELECT * FROM pole');

        $this->info('Création des '.count($this->assos).' associations');

        $bar = $this->output->createProgressBar(count($this->assos));
        $errors = [];

        foreach ($this->assos as $asso) {
            try {
                if ($asso->login === 'cimassos') {
                    $bar->advance();
                    continue;
                }

                $pole_id = $asso->pole_id;

                if ($pole_id === self::CIMETIERE || starts_with($asso->login, 'pole')) {
                    $parent_id = (Asso::where('login', 'bde')->first()->id ?? null);
                } else if ($pole_id) {
                    $pole = $this->getModelFrom($poles, $pole_id);
                    $parent_id = ($this->getAsso($pole->asso_id)->id ?? null);
                }

                $typeName = $this->getModelFrom($assoTypes, $asso->type_id ?: 1)->name;
                $model = Asso::create([
                    'login' => $asso->login,
                    'shortname' => $asso->name,
                    'name' => $asso->summary ?: $asso->name,
                    'description' => $asso->description ?: $asso->name,
                    'type_id' => AssoType::where('name', $typeName)->first()->id,
                    'parent_id' => ($parent_id ?? null),
                ]);

                // Forced to define dates after creation.
                $model->timestamps = false;
                $model->created_at = $asso->created_at ?: $model->created_at;
                $model->updated_at = $asso->updated_at ?: $model->updated_at;
                $model->save();

                // Allow to know if an asso is in the asso's cemetery.
                $isDeleted = !$asso->active ||
                    (!$asso->pole_id && ($parent_id ?? false) && !starts_with($asso->login, 'pole')) ||
                    $pole_id === self::CIMETIERE;

                if ($isDeleted) {
                    $model->timestamps = false;
                    $model->deleted_at = ($asso->updated_at ?? now());
                    $model->save();
                }

                if ($asso->logo && !$this->option('quick')) {
                    try {
                        $image = $this->createImageFromUrl('https://assos.utc.fr/uploads/assos/source/'.$asso->logo,
                            $model, 'assos/'.$model->id);
                    } catch (\Exception $e) {
                        $errors[] = 'Image incorrecte pour l\'association '.$asso->name;
                    }
                }

                $this->addAssoContacts($asso, $model);

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer l\'association '.$asso->name);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer l\'association '.$asso->name);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Add association contact means.
     *
     * @param mixed $asso
     * @param mixed $model
     * @return void
     */
    protected function addAssoContacts($asso, $model)
    {
        if ($asso->salle) {
            try {
                $model->contacts()->create([
                    'name' => 'Bureau',
                    'value' => $asso->salle,
                    'type_id' => ContactType::where('type', 'door')->first()->id,
                    'visibility_id' => Visibility::findByType('active')->id,
                ]);
            } catch (\Exception $e) {
                $errors[] = 'Salle incorrecte pour l\'association '.$asso->name;
            }
        }

        if ($asso->phone) {
            try {
                $model->contacts()->create([
                    'name' => 'Téléphone',
                    'value' => $asso->phone,
                    'type_id' => ContactType::where('type', 'phone')->first()->id,
                    'visibility_id' => Visibility::findByType('public')->id,
                ]);
            } catch (\Exception $e) {
                $errors[] = 'Numéro incorrect pour l\'association '.$asso->name;
            }
        }

        if ($asso->facebook) {
            try {
                $model->contacts()->create([
                    'name' => 'Facebook',
                    'value' => $asso->facebook,
                    'type_id' => ContactType::where('type', 'facebook')->first()->id,
                    'visibility_id' => Visibility::findByType('public')->id,
                ]);
            } catch (\Exception $e) {
                $errors[] = 'Facebook incorrect pour l\'association '.$asso->name;
            }
        }
    }

    /**
     * Create articles from the old portal.
     *
     * @return mixed
     */
    protected function addArticles()
    {
        $this->info('Préparation des articles');

        if (!($tag = Tag::where('name', 'old-portail')->first())) {
            $tag = Tag::create([
                'name' => 'old-portail',
                'description' => 'Article de l\'ancien Portail',
            ]);
        }

        $articles = $this->getDB()->select('SELECT * FROM article');
        $visibility_id = Visibility::findByType('active')->id;

        $this->info('Création des '.count($articles).' articles');

        $bar = $this->output->createProgressBar(count($articles));
        $errors = [];

        foreach ($articles as $article) {
            try {
                $asso_id = ($this->getAsso($article->asso_id)->id ?? null);

                $model = Article::create([
                    'title' => $article->name,
                    'description' => $article->summary,
                    'content' => $article->text,
                    'visibility_id' => $visibility_id,
                    'created_by_id' => $asso_id,
                    'created_by_type' => Asso::class,
                    'owned_by_id' => $asso_id,
                    'owned_by_type' => Asso::class,
                ]);

                // Forced to define dates after creation.
                $model->timestamps = false;
                $model->created_at = $article->created_at ?: $model->created_at;
                $model->updated_at = $article->updated_at ?: $model->updated_at;
                $model->save();

                // Add old portal tag.
                $model->tags()->save($tag);

                if ($article->image && !$this->option('quick')) {
                    try {
                        $image = $this->createImageFromUrl('https://assos.utc.fr/uploads/articles/source/'.$article->image,
                            $model, 'articles/'.$model->id);
                    } catch (\Exception $e) {
                        $errors[] = 'Image incorrecte pour l\'article '.$article->name;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer l\'article '.$article->name);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer l\'article '.$article->name);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create users from old portal.
     *
     * @return mixed
     */
    protected function addUsers()
    {
        $this->info('Préparation des utilisateurs');
        $this->info('Création des '.count($this->users).' utilisateurs');

        $bar = $this->output->createProgressBar(count($this->users));
        $errors = [];

        foreach ($this->users as $user) {
            try {
                $model = User::create([
                    'firstname' => $user->first_name,
                    'lastname' => strtoupper($user->last_name),
                    'email' => $user->email_address,
                ]);

                // Forced to define dates after creation.
                $model->timestamps = false;
                $model->created_at = $user->created_at ?: $model->created_at;
                $model->updated_at = $user->updated_at ?: $model->updated_at;
                $model->save();

                $model->cas()->create([
                    'email' => $user->email_address,
                    'login' => $user->username,
                    'is_active' => false,
                    'is_confirmed' => false,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer l\'utilisateur '.$user->first_name);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer l\'utilisateur '.$user->first_name);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create associations members from the old portal.
     *
     * @return mixed
     */
    protected function addMembers()
    {
        $this->info('Préparation des membres');

        $members = $this->getDB()->select('SELECT * FROM asso_member');

        $this->info('Création des '.count($members).' membres');

        $bar = $this->output->createProgressBar(count($members) + 1);
        $errors = [];

        foreach ($members as $member) {
            try {
                try {
                    $asso = $this->getAsso($member->asso_id);
                    if (!$asso) {
                        $errors[] = 'Association non existante n°'.$member->asso_id;
                        $bar->advance();
                        continue;
                    }

                    $role = $this->getRole($member->role_id);
                    if (!$role) {
                        $errors[] = 'Rôle non existant n°'.$member->role_id;
                        $bar->advance();
                        continue;
                    }

                    $user = $this->getUser($member->user_id);
                    if (!$user) {
                        $errors[] = 'Utilisateur non existant n°'.$member->user_id;
                        $bar->advance();
                        continue;
                    }

                    $semester = $this->getSemester($member->semestre_id);
                    if (!$semester) {
                        $errors[] = 'Semestre non existant n°'.$member->semestre_id;
                        $bar->advance();
                        continue;
                    }

                    try {
                        $model = $asso->assignMembers($user->id, [
                            'role_id' => $role->id,
                            'semester_id' => $semester->id,
                            'validated_by_id' => $user->id,
                            'created_at' => $member->created_at ?: now(),
                            'updated_at' => $member->updated_at ?: now(),
                        ], true);
                    } catch (\Exception $e) {
                        $errors[] = 'n°'.$member->id.': '.$e->getMessage();
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Information manquante pour le membre n°'.$member->id;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer le membre n°'.$member->id);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer le membre n°'.$member->id);

                throw $e;
            }
        }

        $this->fixMembers($errors);
        $bar->advance();

        return $errors;
    }

    /**
     * Fix non-logical members. (I hate you)
     * Exemple: Christina
     *
     * @param array $errors
     * @return void
     */
    protected function fixMembers(array &$errors)
    {
        $excededMembers = DB::select('SELECT user_id, semester_id, users.lastname, firstname, semesters.name,
            count(asso_id) as assos FROM assos_members, semesters, users WHERE assos_members.semester_id = semesters.id and
            user_id = users.id and role_id is not NULL GROUP BY user_id, semester_id HAVING assos >
            '.self::MAX_LOGICAL_MEMBERS.' ORDER BY assos DESC');

        foreach ($excededMembers as $member) {
            $errors[] = 'Suppression du membre '.$member->lastname.' '.$member->firstname.' de '.$member->assos.'
                associations au semestre '.$member->name;

            DB::delete('DELETE FROM assos_members WHERE user_id = "'.$member->user_id.'" AND
                semester_id = "'.$member->semester_id.'"');
        }
    }

    /**
     * Create events from the old portal.
     *
     * @return mixed
     */
    protected function addEvents()
    {
        $this->info('Préparation des événements');

        $events = $this->getDB()->select('SELECT * FROM event');
        $eventTypes = $this->getDB()->select('SELECT * FROM event_type');
        $visibility_id = Visibility::findByType('active')->id;

        $this->info('Création des '.count($events).' événements');

        $bar = $this->output->createProgressBar(count($events));
        $errors = [];

        foreach ($events as $event) {
            try {
                $asso_id = ($this->getAsso($event->asso_id)->id ?? null);

                $model = Event::create([
                    'name' => $event->name,
                    'begin_at' => $event->start_date,
                    'end_at' => $event->end_date,
                    'visibility_id' => $visibility_id,
                    'created_by_id' => $asso_id,
                    'created_by_type' => Asso::class,
                    'owned_by_id' => $asso_id,
                    'owned_by_type' => Asso::class,
                ]);

                // Forced to define dates after creation.
                $model->timestamps = false;
                $model->created_at = $event->created_at ?: $model->created_at;
                $model->updated_at = $event->updated_at ?: $model->updated_at;
                $model->save();

                // Add event type.
                $model->details()->create([
                    'key' => 'TYPE',
                    'value' => $this->getModelFrom($eventTypes, $event->type_id)->name,
                ]);

                // Add old portal tag.
                $model->details()->create([
                    'key' => 'TAG',
                    'value' => 'old-portail',
                ]);

                if ($event->affiche && !$this->option('quick')) {
                    try {
                        $image = $this->createImageFromUrl('https://assos.utc.fr/uploads/events/source/'.$event->affiche,
                            $model, 'events/'.$model->id);
                    } catch (\Exception $e) {
                        $errors[] = 'Image incorrecte pour l\'événement '.$event->name;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer l\'événement '.$event->name);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer l\'événement '.$event->name);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create services from the old portal.
     *
     * @return mixed
     */
    protected function addServices()
    {
        $this->info('Préparation des services');

        $services = $this->getDB()->select('SELECT * FROM service');
        $visibility_id = Visibility::findByType('active')->id;

        $this->info('Création des '.count($services).' services');

        $bar = $this->output->createProgressBar(count($services));
        $errors = [];

        foreach ($services as $service) {
            try {
                $model = Service::create([
                    'name' => $service->nom,
                    'shortname' => $service->nom,
                    'login' => $service->nom,
                    'description' => $service->resume,
                    'visibility_id' => $visibility_id,
                    'url' => $service->url,
                ]);

                // Forced to define dates after creation.
                $model->timestamps = false;
                $model->created_at = $service->created_at ?: $model->created_at;
                $model->updated_at = $service->updated_at ?: $model->updated_at;
                $model->save();

                if ($service->logo && !$this->option('quick')) {
                    try {
                        $image = $this->createImageFromUrl('https://assos.utc.fr/uploads/services/source/'.$service->logo,
                            $model, 'services/'.$model->id);
                    } catch (\Exception $e) {
                        $errors[] = 'Image incorrecte pour le service '.$service->nom;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer le service '.$service->nom);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer le service '.$service->nom);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create associations accesses from the old portal.
     *
     * @return mixed
     */
    protected function addAssosAccess()
    {
        $this->info('Préparation des accès');

        $accessList = $this->getDB()->select('SELECT * FROM charte_locaux');

        $this->info('Création des '.count($accessList).' accès');

        $bar = $this->output->createProgressBar(count($accessList));
        $errors = [];

        foreach ($accessList as $access) {
            try {
                try {
                    $asso = $this->getAsso($access->asso_id);
                    if (!$asso) {
                        $errors[] = 'Association non existante n°'.$access->asso_id;
                        $bar->advance();
                        continue;
                    }

                    $type = $this->getAccess($access->type_id);
                    if (!$type) {
                        $errors[] = 'Accès non existant n°'.$access->type_id;
                        $bar->advance();
                        continue;
                    }

                    $user = $this->getUser($access->user_id);
                    if (!$user) {
                        $errors[] = 'Utilisateur non existant n°'.$access->user_id;
                        $bar->advance();
                        continue;
                    }

                    $semester = $this->getSemester($access->semestre_id);
                    if (!$semester) {
                        $errors[] = 'Semestre non existant n°'.$access->semestre_id;
                        $bar->advance();
                        continue;
                    }

                    try {
                        $model = $asso->access()->create([
                            'member_id' => $user->id,
                            'access_id' => $type->id,
                            'semester_id' => $semester->id,
                            'description' => $access->motif,
                        ]);

                        switch ($access->statut) {
                            case 1:
                                // Waiting for confirmation.
                                $model->validated = false;
                                break;

                            case 3:
                                // Accepted.
                                $model->validated = true;
                            case 0:
                                // Refused   => Same user by default.
                                $model->validated = ($model->validated ?? false);
                                $model->validated_by_id = $user->id;
                            case 2:
                                // Confirmed => Same user by default.
                                $model->confirmed_by_id = $user->id;
                        }

                        // Forced to define dates after creation.
                        $model->timestamps = false;
                        $model->created_at = $access->created_at ?: $model->created_at;
                        $model->updated_at = $access->updated_at ?: $model->updated_at;
                        $model->save();
                    } catch (\Exception $e) {
                        $errors[] = 'n°'.$access->id.': '.$e->getMessage();
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Information manquante pour l\'accès n°'.$access->id;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer l\'accès n°'.$access->id);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer l\'accès n°'.$access->id);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create rooms from the old portal.
     *
     * @return mixed
     */
    protected function addRooms()
    {
        $this->info('Préparation des salles');

        $poles = $this->getDB()->select('SELECT * FROM pole');

        $this->info('Création des '.count($this->rooms).' salles');

        $bar = $this->output->createProgressBar(count($this->rooms));
        $errors = [];

        $visibility_id = Visibility::findByType('contributorBde')->id;

        foreach ($this->rooms as $room) {
            try {
                $pole = $this->getModelFrom($poles, $room->id_pole);
                if (!$pole) {
                    $errors[] = 'Pôle non existant n°'.$room->id_pole;
                    $bar->advance();
                    continue;
                }

                $asso = $this->getAsso($pole->asso_id);
                if (!$asso) {
                    $errors[] = 'Association non existante n°'.$pole->asso_id;
                    $bar->advance();
                    continue;
                }

                switch ($room->id) {
                    case 1:
                        $location = Location::where('name', 'Salle de réunion 1 (1er étage)')->first();
                        break;
                    case 2:
                        $location = Location::where('name', 'Salle de réunion 2 (2ème étage)')->first();
                        break;
                    case 3:
                        $location = Location::where('name', 'Salle de réunion PAE')->first();
                        break;
                    case 4:
                        $location = Location::where('name', 'Salle de réunion PVDC (Réunion)')->first();
                        break;
                    case 6:
                        $location = Location::where('name', 'Salle de réunion PSEC')->first();
                        break;
                    case 7:
                        $location = Location::where('name', 'Salle de réunion PVDC (Bureau)')->first();
                        break;
                    default:
                        $location = Location::where('name', 'Bâtiment E - MDE')->first();
                }

                $model = Room::create([
                    'location_id' => $location->id,
                    'created_by_id' => $asso->id,
                    'created_by_type' => Asso::class,
                    'owned_by_id' => $asso->id,
                    'owned_by_type' => Asso::class,
                    'visibility_id' => $visibility_id,
                    'capacity' => $room->capacite,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer la salle '.$room->name);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer la salle '.$room->name);

                throw $e;
            }
        }

        return $errors;
    }

    /**
     * Create bookings from old portal.
     *
     * @return array
     */
    protected function addBookings()
    {
        $this->info('Préparation des réservations');

        $bookings = $this->getDB()->select('SELECT * FROM reservation');

        $this->info('Création des '.count($bookings).' réservations');

        $bar = $this->output->createProgressBar(count($bookings));
        $errors = [];

        foreach ($bookings as $booking) {
            try {
                try {
                    $asso = $this->getAsso($booking->id_asso);
                    if (!$asso) {
                        $errors[] = 'Association non existante n°'.$booking->asso_id;
                        $bar->advance();
                        continue;
                    }
                } catch (\Error $e) {
                    $errors[] = 'Association non donnée, réservation sautée n°'.$booking->id;
                    $bar->advance();
                    continue;
                } catch (\Exception $e) {
                    $errors[] = 'Association non existante';
                    $bar->advance();
                    continue;
                }

                try {
                    if ($booking->id_user_valid) {
                        $validated_by = $this->getUser($booking->id_user_valid);
                        if (!$validated_by) {
                            $errors[] = 'Utilisateur non existant n°'.$booking->id_user_valid;
                            $bar->advance();
                            continue;
                        }
                    }

                    $user = $this->getUser($booking->id_user_reserve);
                    if (!$user) {
                        $errors[] = 'Utilisateur non existant n°'.$booking->id_user_reserve;
                        $bar->advance();
                        continue;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Utilisateur non existant';
                    $bar->advance();
                    continue;
                }

                $room = $this->getRoom($booking->id_salle);
                if (!$room) {
                    $errors[] = 'Salle non existante n°'.$booking->id_salle;
                    $bar->advance();
                    continue;
                }

                // If no type is found, we consider it as blocking.
                $type = BookingType::where('name', $booking->activite)->first();
                $name = ($booking->commentaire ? $booking->commentaire : ($type ? $type->name : 'Blocage'));

                if ($booking->estvalide && !isset($validated_by)) {
                    $validated_by = $user;
                }

                try {
                    $event = Event::create([
                        'name' => $name,
                        'begin_at' => $booking->date.' '.$booking->heuredebut,
                        'end_at' => $booking->date.' '.$booking->heurefin,
                        'full_day' => ($booking->allDay ?? false),
                        'location_id' => $room->location->id,
                        'created_by_id' => $user->id,
                        'created_by_type' => User::class,
                        'owned_by_id' => $asso->id,
                        'owned_by_type' => Asso::class,
                        'visibility_id' => $room->calendar->visibility_id,
                    ]);
                } catch (\Exception $e) {
                    $errors[] = 'Evénement déjà créé pour la réservation n°'.$booking->id;
                    $bar->advance();
                    continue;
                }

                $room->calendar->events()->attach($event);
                $model = $room->bookings()->create([
                    'type_id' => ($type->id ?? null),
                    'event_id' => $event->id,
                    'description' => $booking->commentaire,
                    'created_by_id' => $user->id,
                    'created_by_type' => User::class,
                    'owned_by_id' => $asso->id,
                    'owned_by_type' => Asso::class,
                    'validated_by_id' => ($validated_by->id ?? null),
                    'validated_by_type' => User::class,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->output->error('Impossible de créer la réservation n°'.$booking->id);

                throw $e;
            } catch (\Error $e) {
                $this->output->error('Impossible de créer la réservation n°'.$booking->id);

                throw $e;
            }
        }

        return $errors;
    }
}
