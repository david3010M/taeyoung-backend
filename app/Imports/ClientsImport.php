<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientsImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new FirstSheetClientImport(),
            1 => new SecondSheetClientImport(),
        ];
    }

}
