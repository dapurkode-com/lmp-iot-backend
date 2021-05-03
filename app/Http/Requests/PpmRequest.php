<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="Store Ppm Request",
 *      description="Store ppm request body data",
 *      type="object",
 *      required={"ppm"},
 *      @OA\Xml(
 *          name="StorePpm"
 *      ),
 * )
 */
class PpmRequest extends FormRequest
{

    /**
     * @OA\Property(
     *      property="ppm",
     *      description="Ppm",
     *      type="number",
     *      example=100
     * )
     *
     * @var integer
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
            'ppm' => 'required|numeric|min:0|max:1200'
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
