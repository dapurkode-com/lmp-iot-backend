<?php

namespace App\Http\Controllers\Api;

use App\Models\Ph;
use Illuminate\Http\Request;
use App\Http\Resources\PhResource;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhCollection;
use App\Http\Requests\ResourceRequest;

class PhController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ResourceRequest $request)
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Ph::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new PhCollection($query->paginate($number_item));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ph  $ph
     * @return \Illuminate\Http\Response
     */
    public function show(Ph $ph): PhResource
    {
        return new PhResource($ph);
    }
}
