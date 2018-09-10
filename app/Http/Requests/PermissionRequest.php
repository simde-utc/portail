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
            $this->resource = \ModelResolver::getModelFromCategory($this->resource_type, CanHavePermissions::class)->find($this->resource_id);
        else // On est sur /user/permissions
            $this->resource = \Auth::user();

        return (bool) $this->resource;
    }
}
