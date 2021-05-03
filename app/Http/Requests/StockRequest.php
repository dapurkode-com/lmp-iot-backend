<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="Store Stock Request",
 *      description="Store stock request body data",
 *      type="object",
 *      required={"calorie"},
 * )
 */
class StockRequest extends FormRequest
{
    /**
     * @OA\Property(property="barcode", type="string", description="Barcode", readOnly="true", example=7314123152)
     * @OA\Property(property="name", type="string", description="Item name", readOnly="true", example="Indomie")
     * @OA\Property(property="position", type="string", description="Stock Position (IN or OUT)", readOnly="true", example="IN")
     * @OA\Property(property="stock", type="integer", description="Stock amount", readOnly="true", example="5")
     * @OA\Property(property="expired_date", type="string", format="date", description="Expired date", readOnly="true", example="2021-04-27")
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
            'barcode'       => 'nullable|string',
            'name'          => 'required|string',
            'expired_date'  => 'required|date',
            'stock'         => 'required|numeric',
            'position'      => 'required|in:IN,OUT'
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
            'barcode'       => 'barcode',
            'name'          => 'item\'s name',
            'expired_date'  => 'expired date',
            'stock'         => 'stock'
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
