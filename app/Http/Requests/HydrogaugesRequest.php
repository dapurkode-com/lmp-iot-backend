<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="Store Hydrogauge Request",
 *      description="Store Hydrogauge batch request body data",
 *      type="object",
 *      required={"ph", "ppm", "temperature"},
 *      @OA\Xml(
 *          name="StoreHydro"
 *      ),
 * )
 */
class HydrogaugesRequest extends FormRequest
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
     * @OA\Property(
     *      property="conductivity",
     *      description="Electrical conductivity",
     *      type="number",
     *      example=7.5
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
            'ppm' => 'required|numeric|min:0|max:1200',
            'ph' => 'required|integer|min:1|max:14',
            'temperature' => 'required|numeric|max:100',
            'conductivity'  => 'required|numeric|min:0|max:10'
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
