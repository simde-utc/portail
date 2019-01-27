<?php
/**
 * Fichier générant la commande portail:old-to-new.
 * Télécharge toutes les données dans l'ancien Portail vers celui-ci.
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
    Asso, AssoType, Article, Contact, ContactType, Client, Tag, Visibility
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

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $bar = $this->output->createProgressBar(2);
        $errors = [];

        try {
            $errors['Associations'] = $this->addAssos();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);

            $errors['Articles'] = $this->addArticles();
            $this->info(PHP_EOL);
            $bar->advance();
            $this->info(PHP_EOL);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->info(PHP_EOL.PHP_EOL);
            $this->info('Rapport:');
            $this->info(PHP_EOL.PHP_EOL);
            foreach ($errors as $name => $subErrors) {
                $this->info($name.':');

                foreach ($subErrors as $error) {
                    $this->error($error);
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

    protected function addAssos()
    {
        $this->info('Préparation des associations');

        // Nettoyage avant création massive.
        Client::getQuery()->delete();
        Asso::getQuery()->delete();
        $this->removeDir(public_path('/images/assos'));

        $assoTypes = $this->getDB()->select('SELECT * FROM type_asso');
        $poles = $this->getDB()->select('SELECT * FROM pole');
        $assos = $this->getDB()->select('SELECT * FROM asso');

        $this->info('Création des '.count($assos).' associations');

        $bar = $this->output->createProgressBar(count($assos));
        $errors = [];

        foreach ($assos as $asso) {
            try {
                if ($asso->login === 'cimassos') {
                    continue;
                }

                $pole_id = $asso->pole_id;

                if ($pole_id === self::CIMETIERE || starts_with($asso->login, 'pole')) {
                    $parent_id = Asso::where('login', 'bde')->first()->id ?? null;
                } else if ($pole_id) {
                    $pole = $this->getModelFrom($poles, $pole_id);
                    $parent_id = Asso::where('login', $this->getModelFrom($assos, $pole->asso_id)->login)->first()->id ?? null;
                }

                $model = new Asso;
                $model = $model->create([
                    'login' => $asso->login,
                    'shortname' => $asso->name,
                    'name' => $asso->summary ?: $asso->name,
                    'description' => $asso->description ?: $asso->name,
                    'type_id' => AssoType::where('name', $this->getModelFrom($assoTypes, $asso->type_id ?: 1)->name)->first()->id,
                    'parent_id' => $parent_id ?? null,
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

        $assos = $this->getDB()->select('SELECT * FROM asso');
        $articles = $this->getDB()->select('SELECT * FROM article');
        $visibility_id = Visibility::where('type', 'logged')->first()->id;

        $this->info('Création des '.count($articles).' articles');

        $bar = $this->output->createProgressBar(count($articles));
        $errors = [];

        foreach ($articles as $article) {
            try {
                $asso_id = Asso::where('login', $this->getModelFrom($assos, $article->asso_id)->login)->first()->id ?? null;

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
}
