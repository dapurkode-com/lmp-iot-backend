<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Step;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\StepResource;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\StepCollection;

/**
 * Step Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Step",
 *     description="Heathlink - Controller of Step"
 * )
 */
class StepController extends Controller
{
    /**
     * Step Index
     *
     * @OA\Get(
     *      path="/api/step",
     *      tags={"Step"},
     *      summary="Collection of Step Raw Data",
     *      operationId="stepIndex",
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
     *          description="Collections of Step",
     *          @OA\JsonContent(ref="#/components/schemas/StepCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return StepCollection
     */
    public function index(ResourceRequest $request): StepCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Step::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new StepCollection($query->paginate($number_item));
    }

    /**
     * @OA\Get(
     *      path="/api/step/{id}",
     *      tags={"Step"},
     *      summary="Specific Raw Data of Step",
     *      operationId="phShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Step Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Step Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/StepResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Step $step
     * @return StepResource
     */
    public function show(Step $step): StepResource
    {
        return new StepResource($step);
    }

    /**
     * @OA\Get(
     *      path="/api/step/today",
     *      tags={"Step"},
     *      summary="Latest Data of Step today",
     *      operationId="stepToday",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Todays Latest Step Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/StepResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return StepResource
     */
    public function todayLatest()
    {
        $step = Step::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new StepResource($step);
    }
}
