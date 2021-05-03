<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Sleep Resource
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Sleep Resource",
 *      description="Sleep resource",
 * )
 */
class SleepResource extends JsonResource
{
    /**
     * @OA\Property(property="id", type="integer", description="Id of collection", readOnly="true", example=1)
     *
     * @var number
     */
    /**
     * @OA\Property(property="start_microtime", type="integer", description="Start of sleep microtime format", readOnly="true", example=1619453606584)
     *
     * @var float
     */
    /**
     * @OA\Property(property="start_datetime", type="string", format="date-time", description="Start of sleep date time format", readOnly="true", example="2021-04-27 00:13:26")
     *
     * @var string
     */
    /**
     * @OA\Property(property="end_microtime", type="integer", description="End of sleep microtime format", readOnly="true", example=1619453606584)
     *
     * @var float
     */
    /**
     * @OA\Property(property="end_datetime", type="string", format="date-time", description="End of sleep date time format", readOnly="true", example="2021-04-27 00:13:26")
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
            'id'                => $this->id,
            'start_microtime'   => $this->start_microtime,
            'start_datetime'    => Carbon::parse((int) ($this->start_microtime / 1000))->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            'end_microtime'     => $this->end_microtime,
            'end_datetime'      => Carbon::parse((int) ($this->end_microtime / 1000))->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
        ];
    }
}
