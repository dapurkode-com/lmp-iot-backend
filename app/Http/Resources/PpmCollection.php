<?php

namespace App\Http\Resources;

use App\Http\Resources\PpmResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Ppm Collection
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Ppm Collection",
 *      description="Ppm collection",
 * )
 */
class PpmCollection extends ResourceCollection
{
    /**
     * @OA\Property(property="ppms", description="Collections of Ppm", readOnly="true")
     *
     * @var \App\Http\Resources\PpmResource[]
     */

    /**
     * @OA\Property(property="pagination", description="pagination", readOnly="true")
     *
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
            'ppms' => PpmResource::collection($this->collection),
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
