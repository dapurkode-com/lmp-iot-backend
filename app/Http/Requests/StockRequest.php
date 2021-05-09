<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *      title="StockRequest",
 *      description="Store stock request body data",
 *      type="object",
 *      required={"barcode", "image_file", "name", "position", "stock","expired_date"},
 *      @OA\Xml(
 *          name="StockRequest"
 *      ),
 * )
 */
class StockRequest extends FormRequest
{
    /**
     * @OA\Property(property="barcode", type="string", description="Barcode", example="7314123152")
     * @var string
     */
    /**
     * @OA\Property(property="image_file", type="string", format="binary", description="Image stock", example="7314123152")
     * @var string
     */
    /**
     * @OA\Property(property="name", type="string", description="Item name", example="Indomie")
     *  @var string
     */
    /**
     * @OA\Property(property="position", type="string", description="Stock Position (IN or OUT)", example="IN")
     * * @var string
     */
    /**
     * @OA\Property(property="stock", type="integer", description="Stock amount",  example="5")
     * @var integer
     */
    /**
     * @OA\Property(property="expired_date", type="string", format="date", description="Expired date", example="2021-04-27")
     * @var string
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
            'image_file'    => 'required|image',
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
