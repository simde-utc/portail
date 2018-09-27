<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\PortailException;
use App\Traits\Model\CanHavePermissions;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->resource_type)
            $this->resource = \ModelResolver::getModelFromCategory($this->resource_type)->find($this->resource_id);
        else // On est sur /user/permissions ou /users/{user_id}/permissions
            $this->resource = \Auth::user();

        if (!$this->user_id)
            $this->user_id = \Auth::id();

        return (bool) $this->resource;
    }

    public function rules()
    {
        return [];
    }
}
