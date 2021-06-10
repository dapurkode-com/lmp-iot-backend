<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Heart Rate Resource
 * @author Satya Wibawa <i.g.b.n.satyawibawa@gmail.com>
 * @package Resource
 *
 * @OA\Schema(
 *      title="Heart Rate Resource",
 *      description="Heart Rate resource",
 * )
 */
class HeartRateResource extends JsonResource
{
    /**
     * @OA\Property(property="id", type="integer", description="Id of collection", readOnly="true", example=1)
     *
     * @var number
     */
    /**
     * @OA\Property(property="rate", type="integer", description="Calorie", readOnly="true", example=72)
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
        $mood = [];

        if (120 > $this->rate && $this->rate > 91) {
            array_push($mood, 'Neutral');
        }
        if (119 > $this->rate && $this->rate > 90) {
            array_push($mood, 'Happy');
        }
        if (108 > $this->rate && $this->rate > 75) {
            array_push($mood, 'Sad');
        }

        return [
            'id'        => $this->id,
            'rate'      => $this->rate,
            'microtime' => $this->microtime,
            'mood_text' => implode(', ', $mood),
            'neutral'   => in_array('Neutral', $mood) ? round(100 / count($mood)) : 0,
            'happy'   => in_array('Happy', $mood) ? round(100 / count($mood)) : 0,
            'sad'   => in_array('Sad', $mood) ? round(100 / count($mood)) : 0,
            'datetime'  => Carbon::parse((int) ($this->microtime / 1000))->timezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}
