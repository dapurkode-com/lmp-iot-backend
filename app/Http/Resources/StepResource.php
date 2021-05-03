<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Step Resource
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Step Resource",
 *      description="Step resource",
 * )
 */
class StepResource extends JsonResource
{
    /**
     * @OA\Property(property="id", type="integer", description="Id of collection", readOnly="true", example=1)
     *
     * @var number
     */
    /**
     * @OA\Property(property="step", type="integer", description="Step", readOnly="true", example=72)
     *
     * @var number
     */
    /**
     * @OA\Property(property="microtime", type="integer", description="Microtime format", readOnly="true", example=1619453606584)
     *
     * @var float
     */
    /**
     * @OA\Property(property="datetime", type="string", format="date-time", description="Date time format", readOnly="true", example="2021-04-27 00:13:26")
     *
     * @var string
     */
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'step'          => $this->step,
            'microtime'     => $this->microtime,
            'datetime'      => Carbon::parse((int) ($this->microtime / 1000))->timezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
    }
}
