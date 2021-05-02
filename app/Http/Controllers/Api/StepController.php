<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Step;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\StepResource;
use App\Http\Requests\ResourceRequest;
use App\Http\Resources\StepCollection;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  \App\Models\Step  $step
     * @return \Illuminate\Http\Response
     */
    public function show(Step $step): StepResource
    {
        return new StepResource($step);
    }

    public function todayLatest()
    {
        $step = Step::whereBetween(
            DB::raw('FROM_UNIXTIME(microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('microtime', 'DESC')->firstOrFail();

        return new StepResource($step);
    }
}
