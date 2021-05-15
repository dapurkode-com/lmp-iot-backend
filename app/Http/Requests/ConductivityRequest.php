<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      title="Store Conductivity Request",
 *      description="Store conductivity request body data",
 *      type="object",
 *      required={"conductivity"},
 *      @OA\Xml(
 *          name="StoreConductivity"
 *      ),
 * )
 */
class ConductivityRequest extends FormRequest
{
    /**
     * @OA\Property(
     *      property="conductivity",
     *      description="Conductivity",
     *      type="number",
     *      example=3.5
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
            'conductivity' => 'required|numeric|min:0|max:10'
        ];
    }
}
