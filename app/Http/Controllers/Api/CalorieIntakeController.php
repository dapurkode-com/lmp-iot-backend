<?php

namespace App\Http\Controllers\Api;

use App\Models\CalorieIntake;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalorieRequest;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\CalorieResource;
use App\Http\Resources\CalorieCollection;
use Exception;

class CalorieIntakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CalorieRequest $request): string
    {
        try {
            DB::beginTransaction();
            CalorieIntake::create([
                'microtime' => round(microtime(true) * 1000),
                'calorie'   => $request->calorie
            ]);
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
     * @param  \App\Models\CalorieIntake  $calorieIntake
     * @return \Illuminate\Http\Response
     */
    public function show(CalorieIntake $calorieIntake)
    {
        return new CalorieResource($calorieIntake);
    }
}
