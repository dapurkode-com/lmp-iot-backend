<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="Store Calorie Request",
 *      description="Store calorie request body data",
 *      type="object",
 *      required={"calorie"},
 *      @OA\Xml(
 *          name="StoreCalorie"
 *      ),
 * )
 */
class CalorieRequest extends FormRequest
{

    /**
     * @OA\Property(
     *      title="calorie",
     *      description="Calorie",
     *      type="number",
     *      format="float",
     *      example=42.03
     * )
     *
     * @var float
     */
    public $calorie;

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
            'calorie' => 'required|numeric'
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
            'calorie' => 'calorie',
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
