<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockResourceRequest extends FormRequest
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
            'range_month'   => 'nullable|numeric',
            'range_day'     => 'nullable|numeric',
            'sort'          => 'string|nullable|in:ASC,DESC',
            'number_item'   => 'numeric|nullable|min:1'
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
            'range_month'   => 'range expired date in month',
            'range_day'     => 'range expired date in day',
            'sort'          => 'sort',
            'number_item'   => 'number item'
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
            ], 400));
    }
}
