<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use OpenApi\Annotations as OA;
use App\Models\CalorieExpended;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\CalorieResource;
use App\Http\Resources\CalorieCollection;

/**
 * Calorie Expended Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Calorie Expended",
 *     description="Healthlink - Controller of Calorie Expended"
 * )
 */
class CalorieExpendedController extends Controller
{
    /**
     * Calorie Expended Index
     *
     * @OA\Get(
     *      path="/api/calorie-expended",
     *      tags={"Calorie Expended"},
     *      summary="Collection of Calorie Expended Raw Data",
     *      operationId="calorieExpendedIndex",
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
     *              minimum=1
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
     *          description="Collections of Calorie",
     *          @OA\JsonContent(ref="#/components/schemas/CalorieCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return CalorieCollection
     */
    public function index(ResourceRequest $request): CalorieCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = CalorieExpended::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new CalorieCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/calorie-expended/{id}",
     *      tags={"Calorie Expended"},
     *      summary="Specific Raw Data of Calorie Expended",
     *      operationId="calorieExpendedShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Calorie Expended Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Calorie Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/CalorieResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param CalorieExpended $calorieExpended
     * @return CalorieResource
     */
    public function show(CalorieExpended $calorieExpended): CalorieResource
    {
        return new CalorieResource($calorieExpended);
    }

    /**
     * @OA\Get(
     *      path="/api/calorie-expended/today",
     *      tags={"Calorie Expended"},
     *      summary="Lastest Calorie Expended today",
     *      operationId="calorieExpendedTodayLatest",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Calorie Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/CalorieResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return CalorieResource
     */
    public function todayLatest(): CalorieResource
    {
        $calorie = CalorieExpended::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new CalorieResource($calorie);
    }
}
