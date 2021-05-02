<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SleepResource extends JsonResource
{
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
