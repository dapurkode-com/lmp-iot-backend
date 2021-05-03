<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StockRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockCollection;
use App\Http\Requests\StockResourceRequest;
use App\Http\Resources\StockSummaryCollection;

/**
 * Stock Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Stock",
 *     description="RAMZ - Controller of Stock"
 * )
 */
class StockController extends Controller
{
    /**
     * Stock Index
     *
     * @OA\Get(
     *      path="/api/stock",
     *      tags={"Stock"},
     *      summary="Collection of Stock Raw Data",
     *      operationId="stockIndex",
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
     *          name="range_month",
     *          in="query",
     *          description="Filter expired date range month from today",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="integer",
     *              minimum=1,
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="range_day",
     *          in="query",
     *          description="Filter expired date range day from today",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="integer",
     *              minimum=1
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Collections of Stock",
     *          @OA\JsonContent(ref="#/components/schemas/StockCollection")
     *      ),
     * )
     *
     *
     *
     * @param StockResourceRequest $request
     * @return StockCollection
     */
    public function index(StockResourceRequest $request): StockCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Stock::orderBy('id', $sort);
        $additional = [];

        if ($request->has('range_month') || $request->has('range_day')) {
            $filter = Carbon::now();
            if ($request->has('range_month')) {
                $filter->addMonths($request->range_month);
            }
            if ($request->has('range_day')) {
                $filter->addDays($request->range_day);
            }

            $query = $query->whereBetween('expired_date', [Carbon::now()->subWeek(), $filter]);

            $additional = [
                'expired_date_before_equals' => $filter->format('Y-m-d'),
                'expired_date_after_equals' => Carbon::now()->subWeek()->format('Y-m-d'),
            ];
        }

        return (new StockCollection($query->paginate($number_item)))->additional($additional);
    }

    /**
     * @OA\Post(
     *      path="/api/stock",
     *      operationId="stockStore",
     *      tags={"Stock"},
     *      summary="Store new Stock",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StockRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
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
    public function store(StockRequest $request)
    {
        try {
            DB::beginTransaction();
            Stock::create($request->only([
                'barcode',
                'name',
                'expired_date',
                'stock',
                'position'
            ]));
            DB::commit();
            return response()->json([
                'status'    => 'ok',
                'message'   => 'Success inserting data.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => 'failed',
                'message'   => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/stock/{id}",
     *      tags={"Stock"},
     *      summary="Specific Raw Data of Stock",
     *      operationId="stockShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Stock Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Stock Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/StockResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param stock $stock
     * @return StockResource
     */
    public function show(Stock $stock): StockResource
    {
        return new StockResource($stock);
    }

    /**
     * Stock Summary
     *
     * @OA\Get(
     *      path="/api/stock/summary",
     *      tags={"Stock"},
     *      summary="Collection of Stock Summary",
     *      operationId="stockSummary",
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
     *          name="range_month",
     *          in="query",
     *          description="Filter expired date range month from today",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="integer",
     *              minimum=1,
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="range_day",
     *          in="query",
     *          description="Filter expired date range day from today",
     *          allowEmptyValue=true,
     *          @OA\Schema(
     *              type="integer",
     *              minimum=1
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Collections of Stock Summary",
     *          @OA\JsonContent(ref="#/components/schemas/StockSummaryCollection")
     *      ),
     * )
     *
     *
     *
     * @param StockResourceRequest $request
     * @return StockSummaryCollection
     */
    public function summary(StockResourceRequest $request): StockSummaryCollection
    {
        $sort = $request->input('sort', 'ASC');
        $number_item = $request->input('number_item', 5);

        $query = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->orderBy('expired_date', $sort)
            ->groupBy('barcode', 'name', 'expired_date');

        $additional = [];

        if ($request->has('range_month') || $request->has('range_day')) {
            $filter = Carbon::now();
            if ($request->has('range_month')) {
                $filter->addMonths($request->range_month);
            }
            if ($request->has('range_day')) {
                $filter->addDays($request->range_day);
            }

            $query = $query->whereBetween('expired_date', [Carbon::now()->subWeek(), $filter]);

            $additional = [
                'expired_date_before_equals' => $filter->format('Y-m-d'),
                'expired_date_after_equals' => Carbon::now()->subWeek()->format('Y-m-d'),
            ];
        }

        return (new StockSummaryCollection($query->paginate($number_item)))->additional($additional);
    }
}
