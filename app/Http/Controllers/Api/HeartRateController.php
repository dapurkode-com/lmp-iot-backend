<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\HeartRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\HeartRateResource;
use App\Http\Resources\HeartRateCollection;

/**
 * Heart Rate Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Heart Rate",
 *     description="Healthlink - Controller of Heart Rate"
 * )
 */
class HeartRateController extends Controller
{
    /**
     * Heart Rate Index
     *
     * @OA\Get(
     *      path="/api/heart-rate",
     *      tags={"Heart Rate"},
     *      summary="Collection of Heart Rate Raw Data",
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
     *          description="Collections of Heart Rate",
     *          @OA\JsonContent(ref="#/components/schemas/HeartRateCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return HeartRateCollection
     */
    public function index(ResourceRequest $request): HeartRateCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = HeartRate::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new HeartRateCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/heart-rate/{id}",
     *      tags={"Heart Rate"},
     *      summary="Specific Raw Data of Heart Rate",
     *      operationId="heartRateShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Heart Rate Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Heart Rate Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/HeartRateResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param HeartRate $heartRate
     * @return HeartRateResource
     */
    public function show(HeartRate $heartRate): HeartRateResource
    {
        return new HeartRateResource($heartRate);
    }

    /**
     * @OA\Get(
     *      path="/api/heart-rate/today",
     *      tags={"Heart Rate"},
     *      summary="Latest Data of Heart Rate today",
     *      operationId="heartRateToday",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Heart Rate Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/HeartRateResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param HeartRate $heartRate
     * @return HeartRateResource
     */
    public function todayLatest()
    {
        $rate = HeartRate::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new HeartRateResource($rate);
    }
}
