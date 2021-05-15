<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Conductivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Http\Requests\ConductivityRequest;
use App\Http\Resources\ConductivityResource;
use App\Http\Resources\ConductivityCollection;

/**
 * Conductivity Controller
 *
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 *
 * @OA\Tag(
 *     name="Conductivity",
 *     description="Hydrogauges - Controller of Electrical Conductivity"
 * )
 */
class ConductivityController extends Controller
{
    /**
     * Conductivity Index
     *
     * @OA\Get(
     *      path="/api/conductivity",
     *      tags={"Conductivity"},
     *      summary="Collection of Conductivity Raw Data",
     *      operationId="conductivityIndex",
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
     *          description="Collections of Conductivity",
     *          @OA\JsonContent(ref="#/components/schemas/ConductivityCollection")
     *      ),
     * )
     *
     *
     *
     * @param ResourceRequest $request
     * @return ConductivityCollection
     */
    public function index(ResourceRequest $request): ConductivityCollection
    {
        $sort = $request->input('sort', 'DESC');
        $number_item = $request->input('number_item', 5);

        $query = Conductivity::orderBy('id', $sort);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query = $query->whereBetween(
                DB::raw('DATE(FROM_UNIXTIME(microtime / 1000))'),
                [$request->start_date, $request->end_date]
            );
        }

        return new ConductivityCollection($query->paginate($number_item));
    }

    /**
     * @OA\Post(
     *      path="/api/conductivity",
     *      operationId="ConductivityStore",
     *      tags={"Conductivity"},
     *      summary="Store new conductivity",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ConductivityRequest")
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
    public function store(ConductivityRequest $request)
    {
        try {
            DB::beginTransaction();
            Conductivity::create([
                'microtime' => Carbon::now()->timestamp * 1000,
                'conductivity'   => $request->conductivity
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
     *      path="/api/conductivity/{id}",
     *      tags={"Conductivity"},
     *      summary="Specific Raw Data of Conductivity",
     *      operationId="conductivityShow",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Conductivity Id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *          example=1
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Conductivity Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/ConductivityResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @param Conductivity $ph
     * @return ConductivityResource
     */
    public function show(Conductivity $conductivity): ConductivityResource
    {
        return new ConductivityResource($conductivity);
    }

    /**
     * @OA\Get(
     *      path="/api/conductivity/latest",
     *      tags={"Conductivity"},
     *      summary="Latest Data of Conductivity",
     *      operationId="conductivityLatest",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Conductivity Raw Data",
     *          @OA\JsonContent(ref="#/components/schemas/ConductivityResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found"
     *      )
     * )
     *
     * @return ConductivityResource
     */
    public function latest()
    {
        return new ConductivityResource(Conductivity::latest()->firstOrFail());
    }
}
