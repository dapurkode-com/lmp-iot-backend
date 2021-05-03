<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Sleep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SleepResource;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\SleepCollection;

/**
 * Sleep Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Sleep",
 *     description="Heathlink - Controller of Sleep"
 * )
 */
class SleepController extends Controller
{
    /**
     * Sleep Index
     *
     * @OA\Get(
     *      path="/api/sleep",
     *      tags={"Sleep"},
     *      summary="Collection of Sleep Raw Data",
     *      operationId="sleepIndex",
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
     *          description="Collections of Sleep",
     *          @OA\JsonContent(ref="#/components/schemas/SleepCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return SleepCollection
     */
    public function index(ResourceRequest $request): SleepCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Sleep::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(start_microtime / 1000))'),
                [$request->start_date, $request->end_date]
            )->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(end_microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new SleepCollection($query->paginate($number_item));
    }


    /**
     * @OA\Get(
     *      path="/api/sleep/{id}",
     *      tags={"Sleep"},
     *      summary="Specific Raw Data of Sleep",
     *      operationId="phShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Sleep Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Sleep Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/SleepResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Sleep $sleep
     * @return SleepResource
     */
    public function show(Sleep $sleep): SleepResource
    {
        return new SleepResource($sleep);
    }

    /**
     * @OA\Get(
     *      path="/api/sleep/today",
     *      tags={"Sleep"},
     *      summary="Latest Data of Sleep today",
     *      operationId="sleepToday",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Todays Latest Sleep Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/SleepResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return SleepResource
     */
    public function todayLatest()
    {
        $sleep = Sleep::whereBetween(
            DB::raw('FROM_UNIXTIME(end_microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('end_microtime', 'DESC')->firstOrFail();

        return new SleepResource($sleep);
    }
}
