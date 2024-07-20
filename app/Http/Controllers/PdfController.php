<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function getRepuestos(Request $request)
    {
        $validator = validator($request->query(), [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);

        $code = $request->query('code');
        $name = $request->query('name');
        $page = $request->query('page') ?? 1;
        $per_page = $request->query('per_page') ?? 5;

        if ($validator->fails()) {
            $page = 1;
            $per_page = 5;
        }

        $object = SparePart::where('code', 'like', '%' . $code . '%')
            ->where('name', 'like', '%' . $name . '%')
            ->paginate($per_page, ['*'], 'page', $page);


//        HORIZONTAL
        $pdf = Pdf::loadView('repuesto', [
            'repuestos' => $object,
            'page' => $page,
            'per_page' => $per_page,
            'code' => $code,
            'name' => $name,
        ]);
//        $pdf->setPaper('a3', 'landscape');

//        return $object;
        return $pdf->stream('orden-servicio.pdf');
    }
}
