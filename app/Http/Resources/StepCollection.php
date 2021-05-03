<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Step Collection
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Step Collection",
 *      description="Step collection",
 * )
 */
class StepCollection extends ResourceCollection
{
    /**
     * @OA\Property(property="steps", description="Collections of Step", readOnly="true")
     *
     * @var \App\Http\Resources\StepResource[]
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
            'steps' => StepResource::collection($this->collection),
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
