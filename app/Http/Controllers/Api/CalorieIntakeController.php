<?php

namespace App\Http\Controllers\Api;

use App\Models\CalorieIntake;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalorieRequest;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\CalorieResource;
use App\Http\Resources\CalorieCollection;
use Carbon\Carbon;
use Exception;

/**
 * Calorie Intake Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Calorie Intake",
 *     description="Dieat - Controller of Calorie Intake"
 * )
 */
class CalorieIntakeController extends Controller
{
    /**
     * Calorie Intake Index
     *
     * @OA\Get(
     *      path="/api/calorie-intake",
     *      tags={"Calorie Intake"},
     *      summary="Collection of Calorie Intake Raw Data",
     *      operationId="calorieIntakeIndex",
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

        $query = CalorieIntake::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new CalorieCollection($query->paginate($number_item));
    }

    /**
     * @OA\Post(
     *      path="/api/calorie-intake",
     *      operationId="CalorieIntakeStore",
     *      tags={"Calorie Intake"},
     *      summary="Store new Calorie Intake",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CalorieRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Error"
     *      ),
     * )
     */
    public function store(CalorieRequest $request): string
    {
        try {
            DB::beginTransaction();
            CalorieIntake::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'calorie'   => $request->calorie
            ]);
            DB::commit();
            return response()->json([
                'status'    => 'ok',
                'message'   => 'Success inserting data.'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => 'failed',
                'message'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/calorie-intake/{id}",
     *      tags={"Calorie Intake"},
     *      summary="Specific Raw Data of Calorie Intake",
     *      operationId="calorieIntakeShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Calorie Intake Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
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
     * @param CalorieIntake $calorieIntake
     * @return CalorieResource
     */
    public function show(CalorieIntake $calorieIntake)
    {
        return new CalorieResource($calorieIntake);
    }

    /**
     * @OA\Get(
     *      path="/api/calorie-intake/today",
     *      tags={"Calorie Intake"},
     *      summary="Lastest Calorie Intake today",
     *      operationId="calorieIntakeTodayLatest",
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
    public function todayLatest()
    {
        $calorie = CalorieIntake::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new CalorieResource($calorie);
    }
}
