<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\PortailException;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        dd($this->resource_type);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // return [
        //     'name' => Validation::make($this)
        //                 ->type('string')
        //                 ->length(validation_between('name'))
        //                 ->post('required')
        //                 ->get(),
        //     'value' => Validation::make($this)
        //                 ->type('string')
        //                 ->nullable()
        //                 ->post('required')
        //                 ->get(),
        //     'contact_type_id' => Validation::make($this)
        //                 ->type('integer')
        //                 ->exists('contacts_types', 'id')
        //                 ->post('required')
        //                 ->get(),
        //     'visibility_id' => Validation::make($this)
        //                 ->type('integer')
        //                 ->exists('visibilities', 'id')
        //                 ->post('required')
        //                 ->get(),
        // ];
    }
}
