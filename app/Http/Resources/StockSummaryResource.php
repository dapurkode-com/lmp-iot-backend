<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Stock Summary Resource
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Stock Summary Resource",
 *      description="Stock Summary resource",
 * )
 */
class StockSummaryResource extends JsonResource
{
    /**
     * @OA\Property(property="barcode", type="string", description="Barcode", readOnly="true", example=7314123152)
     * @OA\Property(property="name", type="string", description="Item name", readOnly="true", example="Indomie")
     * @OA\Property(property="stock", type="integer", description="Stock amount", readOnly="true", example="5")
     * @OA\Property(property="expired_date", type="string", format="date", description="Expired date", readOnly="true", example="2021-04-27")
     */
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'barcode'           => $this->barcode,
            'name'              => $this->name,
            'expired_date'      => $this->expired_date,
            'summarize_stock'   => $this->summarize_stock
        ];
    }
}
