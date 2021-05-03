<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Stock Collection
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Stock Collection",
 *      description="Stock collection",
 * )
 */
class StockCollection extends ResourceCollection
{
    /**
     * @OA\Property(property="stocks", description="Collections of Stock", readOnly="true")
     * @var \App\Http\Resources\StockResource[]
     */
    /**
     * @OA\Property(property="pagination", description="Pagination", readOnly="true")
     * @var \App\Http\Resources\PaginationResource
     */

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'stocks' => StockResource::collection($this->collection),
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage()
            ],
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}
