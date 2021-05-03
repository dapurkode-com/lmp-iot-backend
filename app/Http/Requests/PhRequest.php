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
class PhRequest extends FormRequest
{
    /**
     * @OA\Property(
     *      property="ph",
     *      description="Ph",
     *      type="number",
     *      example=7
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
            'ph' => 'required|integer|min:1|max:14'
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
