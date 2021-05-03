<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Ppm;
use Illuminate\Http\Request;
use App\Http\Requests\PpmRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PpmResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\PpmCollection;
use App\Http\Requests\ResourceRequest;

/**
 * PPM Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="PPM",
 *     description="Hydrogauges - Controller of Water Total Dissolved Solid"
 * )
 */
class PpmController extends Controller
{
    /**
     * PPM Index
     *
     * @OA\Get(
     *      path="/api/ppm",
     *      tags={"PPM"},
     *      summary="Collection of PPM Raw Data",
     *      operationId="heartRateIndex",
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
     *          description="Collections of PPM",
     *          @OA\JsonContent(ref="#/components/schemas/PpmCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return PpmCollection
     */
    public function index(ResourceRequest $request)
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Ppm::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new PpmCollection($query->paginate($number_item));
    }

    /**
     * @OA\Post(
     *      path="/api/ppm",
     *      operationId="PpmStore",
     *      tags={"PPM"},
     *      summary="Store new ppm",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/PpmRequest")
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
    public function store(PpmRequest $request): string
    {
        try {
            DB::beginTransaction();
            Ppm::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'ppm'   => $request->ppm
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
     *      path="/api/ppm/{id}",
     *      tags={"PPM"},
     *      summary="Specific Raw Data of PPM",
     *      operationId="ppmShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="PPM Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="PPM Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/PpmResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Ppm $ppm
     * @return PpmResource
     */
    public function show(Ppm $ppm): PpmResource
    {
        return new PpmResource($ppm);
    }

    /**
     * @OA\Get(
     *      path="/api/ppm/latest",
     *      tags={"PPM"},
     *      summary="Latest Data of PPM",
     *      operationId="ppmLatest",
     *
     *      @OA\Response(
     *          response=200,
     *          description="PPM Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/PpmResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return PpmResource
     */
    public function latest()
    {
        return new PpmResource(Ppm::latest()->firstOrFail());
    }
}
