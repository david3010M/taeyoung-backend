<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'buyRate' => round($this->buyRate, 2),
            'saleRate' => round($this->saleRate, 2),
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ];
    }
}
