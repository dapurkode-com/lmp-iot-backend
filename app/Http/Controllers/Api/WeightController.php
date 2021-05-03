<?php

namespace App\Http\Controllers\Api;

use App\Models\Weight;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\WeightResource;
use App\Http\Resources\WeightCollection;
use Carbon\Carbon;

/**
 * Weight Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Weight",
 *     description="Heathlink - Controller of Weight"
 * )
 */
class WeightController extends Controller
{
    /**
     * Weight Index
     *
     * @OA\Get(
     *      path="/api/weight",
     *      tags={"Weight"},
     *      summary="Collection of Weight Raw Data",
     *      operationId="weightIndex",
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
     *          description="Collections of Weight",
     *          @OA\JsonContent(ref="#/components/schemas/WeightCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return WeightCollection
     */
    public function index(ResourceRequest $request): WeightCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Weight::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new WeightCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/weight/{id}",
     *      tags={"Weight"},
     *      summary="Specific Raw Data of Weight",
     *      operationId="phShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Weight Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Weight Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/WeightResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Weight $weight
     * @return WeightResource
     */
    public function show(Weight $weight): WeightResource
    {
        return new WeightResource($weight);
    }

    /**
     * @OA\Get(
     *      path="/api/weight/today",
     *      tags={"Weight"},
     *      summary="Latest Data of Weight today",
     *      operationId="weightToday",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Todays Latest Weight Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/WeightResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return WeightResource
     */
    public function todayLatest()
    {
        $weight = Weight::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new WeightResource($weight);
    }
}
