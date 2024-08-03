<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'currencyFrom' => $this->currencyFrom,
            'currencyTo' => $this->currencyTo,
//            rate rounded to 2 decimal places
            'rate' => round($this->rate, 2),
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ];
    }
}
