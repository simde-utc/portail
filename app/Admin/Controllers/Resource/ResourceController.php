<?php
/**
 * Génère une gestion d'une ressource admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\{
    GridGenerator, ShowGenerator, FormGenerator
};

abstract class ResourceController extends Controller
{
    use HasResourceActions;

    /**
     * Modèle de la ressource.
     *
     * @var string
     */
    protected $model;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    abstract protected function getFields(): array;

    /**
     * Définition des valeurs par défaut champs à afficher.
     *
     * @return array
     */
    protected function getDefaults(): array
    {
        return [];
    }

    /**
     * Retourne les dépendances.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [];
    }

    /**
     * Retourne le nom du modèle.
     *
     * @return string
     */
    protected function getName(): string
    {
        return ucfirst(\ModelResolver::getName($this->model));
    }

    /**
     * Interface d'affichage global.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);

        $grid->addFields($this->getFields());

        return $content
            ->header($this->getName())
            ->description('Affichage global')
            ->body($grid->get());
    }

    /**
     * Montre une instance.
     *
     * @param mixed   $model_id
     * @param Content $content
     * @return Content
     */
    public function show($model_id, Content $content)
    {
        $model = $this->model::with($this->getWith());

        if (method_exists(new $this->model, 'trashed')) {
            $model = $model->withTrashed();
        }

        $show = new ShowGenerator($model->findOrFail($model_id));

        $show->addFields(array_keys($this->getFields()));

        return $content
            ->header($this->getName())
            ->description('Affichage détaillé')
            ->body($show->get());
    }

    /**
     * Modifie une instance.
     *
     * @param mixed   $model_id
     * @param Content $content
     * @return Content
     */
    public function edit($model_id, Content $content)
    {
        return $content
            ->header($this->getName())
            ->description('Modification')
            ->body($this->form()->get()->edit($model_id));
    }

    /**
     * Crée une nouvelle instance.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header($this->getName())
            ->description('Création')
            ->body($this->form()->get());
    }

    /**
     * Créer le formulaire de base.
     *
     * @return FormGenerator
     */
    protected function form()
    {
        $form = new FormGenerator($this->model);
        $defaults = $this->getDefaults();

        $form->addFields($this->getFields(), $defaults);
        $form->saving(function (Form $form) use ($defaults) {
            foreach ($defaults as $key => $default) {
                $form->$key = $form->$key ?: $default;
            }
        });

        return $form;
    }
}
