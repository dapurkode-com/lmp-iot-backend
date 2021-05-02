<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockSummaryResource extends JsonResource
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
            'barcode'           => $this->barcode,
            'name'              => $this->name,
            'expired_date'      => $this->expired_date,
            'summarize_stock'   => $this->summarize_stock
        ];
    }
}
