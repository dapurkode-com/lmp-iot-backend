<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="Store Ph Request",
 *      description="Store ph request body data",
 *      type="object",
 *      required={"ph"},
 *      @OA\Xml(
 *          name="StorePh"
 *      ),
 * )
 */
class TemperatureRequest extends FormRequest
{
    /**
     * @OA\Property(
     *      property="temperature",
     *      description="temperature",
     *      type="number",
     *      format="float",
     *      example=38.03
     * )
     *
     * @var float
     */
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
            'temperature' => 'required|numeric|max:100'
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
