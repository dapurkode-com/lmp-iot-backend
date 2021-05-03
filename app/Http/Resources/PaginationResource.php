<?php

namespace App\Http\Resources;

/**
 * Base Pagination
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="PaginationResource",
 *      description="Pagination resource"
 * )
 *
 */
class PaginationResource
{
    /**
     * @OA\Property(property="total", type="integer", description="Total of collection", readOnly="true")
     *
     * @var number
     */
    private $total;
    /**
     * @OA\Property(property="count", type="integer", description="Count of collection", readOnly="true")
     *
     * @var number
     */
    private $count;
    /**
     * @OA\Property(property="per_page", type="integer", description="Total per page of collection", readOnly="true")
     *
     * @var number
     */
    private $per_page;
    /**
     * @OA\Property(property="current_page", type="integer", description="Current number page", readOnly="true")
     *
     * @var number
     */
    private $current_page;
    /**
     * @OA\Property(property="total_pages", type="integer", description="Total number of page", readOnly="true")
     *
     * @var number
     */
    private $total_pages;
}
