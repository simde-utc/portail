<?php

namespace App\Admin\Controllers;

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

    protected $model;

    abstract protected function getFields(): array;
    abstract protected function getDefaults(): array;

    protected function getWith(): array
    {
        return [];
    }
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);

        $grid->addFields(array_keys($this->getFields()));

        return $content
            ->header('Index')
            ->description('description')
            ->body($grid->get());
    }

    /**
     * Show interface.
     *
     * @param mixed   $model_id
     * @param Content $content
     * @return Content
     */
    public function show($model_id, Content $content)
    {
        $show = new ShowGenerator($this->model::with($this->getWith())->findOrFail($model_id));

        $show->addFields(array_keys($this->getFields()));

        return $content
            ->header('Detail')
            ->description('description')
            ->body($show->get());
    }

    /**
     * Edit interface.
     *
     * @param mixed   $model_id
     * @param Content $content
     * @return Content
     */
    public function edit($model_id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($model_id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }
    /**
     * Make a form builder.
     *
     * @return Form
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

        return $form->get();
    }
}
