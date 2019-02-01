<?php
/**
 * Fichier générant la commande portail:old-to-new.
 * Télécharge toutes les données dans l'ancien Portail vers celui-ci.
 * Basé sur la version de l'ancien Portail en date du 1er Janvier 2019.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{
    DB, Storage
};
use App\Models\{
    Asso, AssoType, Article, Contact, ContactType, Client, Tag, Role, Semester, User, Visibility, AuthCas, Event, Service
};

class OldToNew extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:old-to-new';

    /**
     * @var string
     */
    protected $description = 'Download all data from the old Portail';

    protected const CIMETIERE = 6;

    protected $users = [];
    protected $assos = [];
    protected $semesters = [];
    protected $roles = [];
    protected $resultedRoles = [];

    /**
     * Défini les anciens rôles vers les nouveaux.
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
        'Secrétaire' => 'Vice-Secrétaire',
        'Resp Communication' => 'Responsable Communication',
        'Resp Partenariat' => 'Responsable Partenariat',
        'Resp Anim\'' => 'Responsable Animation',
        'Resp Info' => 'Responsable Informatique',
        'Resp Logistique' => 'Responsable Logistique',
        'Développeur' => 'Développeur'
    ];

    protected const DEFAULT_ROLE = 'Membre de l\'association';

    /**
     * Exécution de la commande.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->confirm('Ceci va supprimer toutes les données actuelles en faveur de l\'ancien Portail. Confirmer ?')) {
            return;
        }

        $bar = $this->output->createProgressBar(7);
        $errors = [];

        $this->info('Préparation des données à récupérer');

        $this->users = $this->getDB()->select('SELECT * FROM sf_guard_user');
        $this->semesters = $this->getDB()->select('SELECT * FROM semestre');
        $this->roles = $this->getDB()->select('SELECT * FROM role');
        $this->events = $this->getDB()->select('SELECT * FROM event');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $bar->advance();
        $this->info(PHP_EOL);

        try {
            $errors['Associations'] = $this->addAssos();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Articles'] = $this->addArticles();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Utilisateurs'] = $this->addUsers();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Membres'] = $this->addMembers();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Evénements'] = $this->addEvents();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Services'] = $this->addServices();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->info(PHP_EOL);
            $this->info(PHP_EOL);
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

    protected function getDB()
    {
        return DB::connection('old-portail');
    }

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

    protected function removeDir(string $path)
    {
        if (file_exists($path)) {
            foreach (array_diff(scandir($path), ['..', '.']) as $file) {
                $subPath = $path.DIRECTORY_SEPARATOR.$file;
                if (is_dir($subPath)) {
                    $this->removeDir($subPath);
                } else {
                    unlink($subPath);
                }
            }

            rmdir($path);
        }
    }

    protected function getModelFrom(array $models, int $value, string $key='id')
    {
        foreach ($models as $model) {
            if ($model->$key === $value) {
                return $model;
            }
        }
    }

    protected function getUser(int $user_id)
    {
        $oldUser = $this->getModelFrom($this->users, $user_id);

        return User::where('email', $oldUser->email_address)->first();
    }

    protected function getAsso(int $asso_id)
    {
        $oldAsso = $this->getModelFrom($this->assos, $asso_id);

        return Asso::withTrashed()->where('login', $oldAsso->login)->first();
    }

    protected function getRole(int $role_id)
    {
        $oldRole = $this->getModelFrom($this->roles, $role_id);

        if (isset(self::OLD_TO_NEW_ROLES[$name = $oldRole->name])) {
            return Role::where('name', $this->resultedRoles[$name] = self::OLD_TO_NEW_ROLES[$name])->first();
        }

        $this->resultedRoles[$name] = false;
        return Role::where('name', self::DEFAULT_ROLE)->first();
    }

    protected function getSemester(int $semester_id)
    {
        $oldSemester = $this->getModelFrom($this->semesters, $semester_id);

        return Semester::where('name', $oldSemester->name)->first();
    }

    protected function addAssos()
    {
        $this->info('Préparation des associations');

        // Nettoyage avant création massive.
        Client::getQuery()->delete();
        Asso::getQuery()->delete();
        $this->removeDir(public_path('/images/assos'));

        $assoTypes = $this->getDB()->select('SELECT * FROM type_asso');
        $poles = $this->getDB()->select('SELECT * FROM pole');
        $this->assos = $this->getDB()->select('SELECT * FROM asso');

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
                    $parent_id = Asso::where('login', 'bde')->first()->id ?? null;
                } else if ($pole_id) {
                    $pole = $this->getModelFrom($poles, $pole_id);
                    $parent_id = $this->getAsso($pole->asso_id)->id ?? null;
                }

                $model = Asso::create([
                    'login' => $asso->login,
                    'shortname' => $asso->name,
                    'name' => $asso->summary ?: $asso->name,
                    'description' => $asso->description ?: $asso->name,
                    'type_id' => AssoType::where('name', $this->getModelFrom($assoTypes, $asso->type_id ?: 1)->name)->first()->id,
                    'parent_id' => $parent_id ?? null,
                ]);

                // On crée un calendrier pour chaque association.
                $model->calendars()->create([
                    'name' => 'Evénements',
                    'description' => 'Calendrier regroupant les événements de l\'associations',
                    'visibility_id' => Visibility::where('type', 'public')->first()->id,
                    'created_by_id' => $model->id,
                    'created_by_type' => Asso::class,
                ]);

                // Obliger de définir les dates après création.
                $model->timestamps = false;
                $model->created_at = $asso->created_at ?: $model->created_at;
                $model->updated_at = $asso->updated_at ?: $model->updated_at;
                $model->save();

                // Permet de déterminer si une association est dans le cimetière ou non.
                $isDeleted = !$asso->active ||
                    (!$asso->pole_id && ($parent_id ?? false) && !starts_with($asso->login, 'pole')) ||
                    $pole_id === self::CIMETIERE;

                if ($isDeleted) {
                    $model->timestamps = false;
                    $model->deleted_at = ($asso->updated_at ?? now());
                    $model->save();
                }

                if ($asso->logo) {
                    try {
                        $image = $this->createImageFromUrl('https://assos.utc.fr/uploads/assos/source/'.$asso->logo,
                            $model, 'assos/'.$model->id);
                    } catch (\Exception $e) {
                        $errors[] = 'Image incorrecte pour l\'association '.$asso->name;
                    }
                }

                if ($asso->salle) {
                    try {
                        $model->contacts()->create([
                            'name' => 'Bureau',
                            'value' => $asso->salle,
                            'type_id' => ContactType::where('type', 'door')->first()->id,
                            'visibility_id' => Visibility::findByType('logged')->id,
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

    protected function addArticles()
    {
        $this->info('Préparation des articles');

        // Nettoyage avant création massive.
        Article::getQuery()->delete();
        $this->removeDir(public_path('/images/articles'));

        if (!($tag = Tag::where('name', 'old-portail')->first())) {
            $tag = Tag::create([
                'name' => 'old-portail',
                'description' => 'Article de l\'ancien Portail'
            ]);
        }

        $articles = $this->getDB()->select('SELECT * FROM article');
        $visibility_id = Visibility::where('type', 'logged')->first()->id;

        $this->info('Création des '.count($articles).' articles');

        $bar = $this->output->createProgressBar(count($articles));
        $errors = [];

        foreach ($articles as $article) {
            try {
                $asso_id = $this->getAsso($article->asso_id)->id ?? null;

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

                // Obliger de définir les dates après création.
                $model->timestamps = false;
                $model->created_at = $article->created_at ?: $model->created_at;
                $model->updated_at = $article->updated_at ?: $model->updated_at;
                $model->save();

                // On ajoute le tag de l'ancien Portail.
                $model->tags()->save($tag);

                if ($article->image) {
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

    protected function addUsers()
    {
        $this->info('Préparation des utilisateurs');

        // Nettoyage avant création massive.
        User::getQuery()->delete();
        AuthCas::getQuery()->delete();
        $this->removeDir(public_path('/images/users'));

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

                // Obliger de définir les dates après création.
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

    protected function addMembers()
    {
        $members = $this->getDB()->select('SELECT * FROM asso_member');

        $this->info('Création des '.count($members).' membres');

        $bar = $this->output->createProgressBar(count($members));
        $errors = [];

        foreach ($members as $member) {
            try {
                try {
                    $asso = $this->getAsso($member->asso_id);
                    if (!$asso) {
                        $this->output->error('Association non existante n°'.$member->asso_id);
                        continue;
                    }

                    $role = $this->getRole($member->role_id);
                    if (!$role) {
                        $this->output->error('Rôle non existant n°'.$member->role_id);
                        continue;
                    }

                    $user = $this->getUser($member->user_id);
                    if (!$user) {
                        $this->output->error('Utilisateur non existant n°'.$member->user_id);
                        continue;
                    }

                    $semester = $this->getSemester($member->semestre_id);
                    if (!$semester) {
                        $this->output->error('Semestre non existant n°'.$member->semestre_id);
                        continue;
                    }

                    try {
                        $model = $asso->assignMembers($user->id, [
                            'role_id' => $role->id,
                            'semester_id' => $semester->id,
                            'validated_by' => $user->id
                        ], true);

                        // Obliger de définir les dates après création.
                        $model->timestamps = false;
                        $model->created_at = $member->created_at ?: $model->created_at;
                        $model->updated_at = $member->updated_at ?: $model->updated_at;
                        $model->save();
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

        return $errors;
    }

    protected function addEvents()
    {
        $this->info('Préparation des événements');

        // Nettoyage avant création massive.
        Event::getQuery()->delete();
        $this->removeDir(public_path('/images/events'));

        $events = $this->getDB()->select('SELECT * FROM event');
        $eventTypes = $this->getDB()->select('SELECT * FROM event_type');
        $visibility_id = Visibility::where('type', 'logged')->first()->id;

        $this->info('Création des '.count($events).' événements');

        $bar = $this->output->createProgressBar(count($events));
        $errors = [];

        foreach ($events as $event) {
            try {
                $asso_id = $this->getAsso($event->asso_id)->id ?? null;

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

                // Obliger de définir les dates après création.
                $model->timestamps = false;
                $model->created_at = $event->created_at ?: $model->created_at;
                $model->updated_at = $event->updated_at ?: $model->updated_at;
                $model->save();

                // On ajoute le type de l'événement.
                $model->details()->create([
                    'key' => 'TYPE',
                    'value' => $this->getModelFrom($eventTypes, $event->type_id)->name
                ]);

                // On ajoute le tag de l'ancien Portail.
                $model->details()->create([
                    'key' => 'TAG',
                    'value' => 'old-portail'
                ]);

                if ($event->affiche) {
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

    protected function addServices()
    {
        $this->info('Préparation des services');

        // Nettoyage avant création massive.
        Service::getQuery()->delete();
        $this->removeDir(public_path('/images/services'));

        $services = $this->getDB()->select('SELECT * FROM service');
        $visibility_id = Visibility::where('type', 'logged')->first()->id;

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

                // Obliger de définir les dates après création.
                $model->timestamps = false;
                $model->created_at = $service->created_at ?: $model->created_at;
                $model->updated_at = $service->updated_at ?: $model->updated_at;
                $model->save();

                if ($service->logo) {
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
}
