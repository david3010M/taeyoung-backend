<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineryPurchaseResource extends JsonResource
{

    protected static $isCollection = false;

    public static function collection($resource)
    {
        self::$isCollection = true;
        return parent::collection($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date,
            'supplier' => $this->supplier->businessName,
            'country' => $this->supplier->country->name,
            'features' => $this->detail,
            'quantity' => $this->quantity,
            'total' => $this->totalExpense,
        ];

        if (!self::$isCollection) {
            $data['supplier_id'] = $this->supplier_id;
            $data['date'] = Carbon::parse($this->date)->format('Y-m-d');
        }

        return $data;
    }
}
