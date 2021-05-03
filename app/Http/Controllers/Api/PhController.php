<?php

namespace App\Http\Controllers\Api;

use App\Models\Ph;
use Illuminate\Http\Request;
use App\Http\Resources\PhResource;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhCollection;
use App\Http\Requests\ResourceRequest;

/**
 * Ph Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Ph",
 *     description="Hydrogauges - Controller of Water Acidity"
 * )
 */
class PhController extends Controller
{
    /**
     * Ph Index
     *
     * @OA\Get(
     *      path="/api/ph",
     *      tags={"Ph"},
     *      summary="Collection of Ph Raw Data",
     *      operationId="phIndex",
     *
     *      @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="Sorting type",
     *          allowEmptyValue=true,
     *          explode=true,
     *          @OA\Schema(
     *              type="array",
     *              default="DESC",
     *              @OA\Items(
     *                  type="string",
     *                  enum = {"ASC", "DESC"},
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="number_item",
     *          in="query",
     *          description="Number of collection item per one page",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="integer",
     *              minimum=1,
     *              default=5
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="start_date",
     *          in="query",
     *          description="Filter microtime start date. Must be together with end_date",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="string",
     *              format="date"
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="end_date",
     *          in="query",
     *          description="Filter microtime end date. Must be together with start_date",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="string",
     *              format="date"
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Collections of Ph",
     *          @OA\JsonContent(ref="#/components/schemas/PhCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return PhCollection
     */
    public function index(ResourceRequest $request): PhCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Ph::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new PhCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/ph/{id}",
     *      tags={"Ph"},
     *      summary="Specific Raw Data of Ph",
     *      operationId="phShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Ph Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Ph Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/PhResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Ph $ph
     * @return PhResource
     */
    public function show(Ph $ph): PhResource
    {
        return new PhResource($ph);
    }

    /**
     * @OA\Get(
     *      path="/api/ph/latest",
     *      tags={"Ph"},
     *      summary="Latest Data of Ph today",
     *      operationId="phToday",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Ph Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/PhResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return PhResource
     */
    public function latest()
    {
        return new PhResource(Ph::latest()->firstOrFail());
    }
}
