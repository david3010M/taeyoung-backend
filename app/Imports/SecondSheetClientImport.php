<?php

namespace App\Imports;

use App\Models\Country;
use App\Models\Person;
use App\Models\Province;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SecondSheetClientImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = json_decode(json_encode($row), true);
            $province = Province::where('name', 'like', '%' . $row[5] . '%')->first();
            $country = Country::where('name', 'like', '%' . $row[5] . '%')->first();
            $personExists = Person::where('businessName', $row[1])->first();
            if (!$personExists) {
                $person = Person::make([
                    'type' => 'client',
                    'typeDocument' => 'RUC',
                    'businessName' => $row[1],
                    'filterName' => $row[1],
                    'representativeNames' => $row[4],
                    'sector' => $row[3],
                    'phone' => $row[6],
                    'email' => $row[7],
                    'website' => $row[8],
                    'active' => $row[9] === 'ACTIVO',
                    'country_id' => $country->id ?? 179,
                    'province_id' => $province?->id ?? ($country ? ($country->id !== 179 ? null : 135) : 135),
                ]);

                if ($person->filterName) $person->save();

            }
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
