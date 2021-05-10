<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Stock Resource
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Stock Resource",
 *      description="Stock resource",
 * )
 */
class StockResource extends JsonResource
{
    /**
     * @OA\Property(property="id", type="integer", description="Id of collection", readOnly="true", example=1)
     * @OA\Property(property="barcode", type="string", description="Barcode", readOnly="true", example=7314123152)
     * @OA\Property(property="name", type="string", description="Item name", readOnly="true", example="Indomie")
     * @OA\Property(property="position", type="string", description="Stock Position (IN or OUT)", readOnly="true", example="IN")
     * @OA\Property(property="stock", type="integer", description="Stock amount", readOnly="true", example="5")
     * @OA\Property(property="expired_date", type="string", format="date", description="Expired date", readOnly="true", example="2021-04-27")
     * @OA\Property(property="image_file", type="string", description="Name of Image")
     * @OA\Property(property="image_url", type="string", description="Name of Image")
     */
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $path = config('app.stock_image_path');
        return [
            'id'            => $this->id,
            'barcode'       => $this->barcode,
            'name'          => $this->name,
            'expired_date'  => $this->expired_date,
            'stock'         => $this->stock,
            'position'      => $this->position,
            'image_file'    => $this->image_file,
            'image_url'     => url("/$path/$this->image_file")
        ];
    }
}
