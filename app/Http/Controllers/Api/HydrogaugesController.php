<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Ph;
use Carbon\Carbon;
use App\Models\Hydrogauges;
use App\Models\Temperature;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\HydrogaugesRequest;

/**
 * Hydrogauges Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Hydrogauges",
 *     description="Hydrogauges - Controller"
 * )
 */
class HydrogaugesController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/hydrogauges",
     *      operationId="HydrogaugesStore",
     *      tags={"Hydrogauges"},
     *      summary="Store new hydrogauges",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/HydrogaugesRequest")
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
    public function store(HydrogaugesRequest $request)
    {
        try {
            DB::beginTransaction();
            Ph::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'ph'   => $request->ph
            ]);
            Hydrogauges::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'ppm'   => $request->ppm
            ]);
            Temperature::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'temperature'   => $request->temperature
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
}
