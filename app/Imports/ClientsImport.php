<?php

namespace App\Imports;

use App\Models\Person;
use App\Models\Province;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ClientsImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = json_decode(json_encode($row), true);
            logger($row);
            if ($row[0] == 'N°') {
                continue;
            }
//            like
            $province = Province::where('name', 'like', '%' . $row[5] . '%')->first();
            Person::create([
                'type' => 'client',
                'typeDocument' => 'RUC',
                'businessName' => $row[2],
                'filterName' => $row[2],
                'representativeNames' => $row[4],
                'sector' => $row[3],
                'phone' => $row[6],
                'email' => $row[7],
                'website' => $row[8],
                'active' => $row[9] === 'ACTIVO',
                'country_id' => 179,
                'province_id' => $province?->id ?? 135,
            ]);
        }
    }

    public function startRow(): int
    {
        return 4;
    }
}
