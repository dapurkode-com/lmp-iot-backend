<?php

namespace App\Http\Controllers\Api;

use App\Models\Temperature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\TemperatureCollection;
use App\Http\Resources\TemperatureResource;

/**
 * Temperature Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Temperature",
 *     description="Hydrogauges - Controller of Water Temperature"
 * )
 */
class TemperatureController extends Controller
{
    /**
     * Temperature Index
     *
     * @OA\Get(
     *      path="/api/temperature",
     *      tags={"Temperature"},
     *      summary="Collection of Temperature Raw Data",
     *      operationId="heartRateIndex",
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
     *          description="Collections of Temperature",
     *          @OA\JsonContent(ref="#/components/schemas/TemperatureCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return TemperatureCollection
     */
    public function index(ResourceRequest $request): TemperatureCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Temperature::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new TemperatureCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/temperature/{id}",
     *      tags={"Temperature"},
     *      summary="Specific Raw Data of Temperature",
     *      operationId="temperatureShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Temperature Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Temperature Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/TemperatureResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Ppm $temperature
     * @return TemperatureResource
     */
    public function show(Temperature $temperature)
    {
        return new TemperatureResource($temperature);
    }

    /**
     * @OA\Get(
     *      path="/api/temperature/latest",
     *      tags={"Temperature"},
     *      summary="Latest Data of Temperature",
     *      operationId="temperatureLatest",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Temperature Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/TemperatureResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return TemperatureResource
     */
    public function latest()
    {
        return new TemperatureResource(Temperature::latest()->first());
    }
}
