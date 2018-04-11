<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;
use App\Services\Visible\Visible;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->isMethod('put') || $this->isMethod('delete')) {
            $group = Group::find($this->route('group'));
            return $group && Visible::isOwner($group, $request->user()->id);
        }
        else
            return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
	        'name' => Validation::make($this)
                        ->type('string')
                        ->length(validation_between('name'))
                        //->unique('groups','name')
                        ->post('required')
                        ->get(),
	        'icon' => Validation::make($this)
                        ->type('image')
                        ->length(validation_between('url'))
                        ->nullable()
                        ->get(),
	        'visibility_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('visibilities', 'id')
                        ->post('required')
                        ->get(),
	        'is_active' => Validation::make($this)
                        ->type('boolean')
                        ->get(),
        ];
    }
}
