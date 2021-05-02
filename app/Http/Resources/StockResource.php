<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'barcode'       => $this->barcode,
            'name'          => $this->name,
            'expired_date'  => $this->expired_date,
            'stock'         => $this->stock,
            'position'      => $this->position
        ];
    }
}
