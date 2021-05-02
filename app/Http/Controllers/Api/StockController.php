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

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StockResourceRequest $request)
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        return new StockResource($stock);
    }

    public function summary(StockResourceRequest $request)
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
