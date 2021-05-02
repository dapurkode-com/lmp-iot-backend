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

class SleepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  \App\Models\Sleep  $sleep
     * @return \Illuminate\Http\Response
     */
    public function show(Sleep $sleep): SleepResource
    {
        return new SleepResource($sleep);
    }

    public function todayLatest()
    {
        $sleep = Sleep::whereBetween(
            DB::raw('FROM_UNIXTIME(end_microtime / 1000)'),
            [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]
        )->orderBy('end_microtime', 'DESC')->firstOrFail();

        return new SleepResource($sleep);
    }
}
