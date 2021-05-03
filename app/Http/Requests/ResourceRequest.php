<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Resource Request
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 */
class ResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'sort'          => 'string|nullable|in:ASC,DESC',
            'number_item'   => 'numeric|nullable|min:1',
            'start_date'    => 'date|nullable|required_with:end_date|before_or_equal:end_date',
            'end_date'      => 'date|nullable|required_with:start_date|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'sort'          => 'sort',
            'number_item'   => 'number of items',
            'start_date'    => 'filter start date',
            'end_date'      => 'filter end date'
        ];
    }

    /**
     * Get custom fail response
     *
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()
            ->json([
                'status' => 'invalid',
                'validators' => $validator->errors(),
            ]));
    }
}
