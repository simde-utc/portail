<?php
/**
 * Classe formulaire admin avec les corrections de bugs...
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin;

use Encore\Admin\Form as BaseForm;
use Encore\Admin\Form\Field;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\{
	DB, Input
};

class Form extends BaseForm
{
    /**
     * Set all fields value in form.
     *
     * @param mixed $model_id
     *
     * @return void
     */
    protected function setFieldValue($model_id)
    {
        $relations = $this->getRelations();

        $builder = $this->model();

        if ($this->isSoftDeletes) {
            // Le pointeur du builder n'est pas actualisé ce qui ne sert à rien...
            $builder = $builder->withTrashed();
        }

        $this->model = $builder->with($relations)->findOrFail($model_id);
        $this->callEditing();

        // Ici on récupère les attributes, plus les données "visibles", on est en admin quand même...
        $data = $this->model->getAttributes();

        $this->builder->fields()->each(function (Field $field) use ($data) {
            if (!in_array($field->column(), $this->ignored)) {
                $field->fill($data);
            }
        });
    }

    /**
     * Handle update.
     *
     * @param mixed $model_id
     * @param mixed $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($model_id, $data=null)
    {
        $data = ($data) ?: Input::all();

        $isEditable = $this->isEditable($data);

        $data = $this->handleEditable($data);

        $data = $this->handleFileDelete($data);

        if ($this->handleOrderable($model_id, $data)) {
            return response([
                'status'  => true,
                'message' => trans('admin.update_succeeded'),
            ]);
        }

        $builder = $this->model();

        // Ici, on ne testait même pas le soft delete...
        if ($this->isSoftDeletes) {
            $builder = $builder->withTrashed();
        }

        $this->model = $builder->with($this->getRelations())->findOrFail($model_id);

        $this->setFieldOriginalValue();

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            if (!$isEditable) {
                return back()->withInput()->withErrors($validationMessages);
            } else {
                return response()->json(['errors' => array_dot($validationMessages->getMessages())], 422);
            }
        }

        if (($response = $this->prepare($data)) instanceof Response) {
            return $response;
        }

        DB::transaction(function () {
            $updates = $this->prepareUpdate($this->updates);

            foreach ($updates as $column => $value) {
                // @var Model $this->model
                $this->model->setAttribute($column, $value);
            }

            $this->model->save();

            $this->updateRelation($this->relations);
        });

        if (($result = $this->callSaved()) instanceof Response) {
            return $result;
        }

        if ($response = $this->ajaxResponse(trans('admin.update_succeeded'))) {
            return $response;
        }

        return $this->redirectAfterUpdate($model_id);
    }
}
