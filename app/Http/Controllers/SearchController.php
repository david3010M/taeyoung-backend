<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/searchByDni/{dni}",
     *     tags={"Search"},
     *     summary="Search information by DNI",
     *     description="Search information about a person by their DNI number.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI number of the person to search",
     *         @OA\Schema(type="string")
     *     ),
     *       @OA\Response(
     *         response=200,
     *         description="Information found successfully.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="integer", example=0),
     *                 @OA\Property(property="dni", type="string", example="string"),
     *                 @OA\Property(property="apepat", type="string", example="string"),
     *                 @OA\Property(property="apemat", type="string", example="string"),
     *                 @OA\Property(property="apcas", type="string", example=""),
     *                 @OA\Property(property="nombres", type="string", example="string"),
     *                 @OA\Property(property="fecnac", type="string", format="date"),
     *                 @OA\Property(property="ubigeo", type="integer")
     *             )
     *         )
     *     ),
     *           @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */

    public function searchByDni($dni)
    {
        $validator = Validator::make(['dni' => $dni], [
            'dni' => 'required|numeric|digits:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $respuesta = array();
        $client = new Client();
        try {
            $res = $client->get('http://facturae-garzasoft.com/facturacion/buscaCliente/BuscaCliente2.php?' . 'dni=' . $dni . '&fe=N&token=qusEj_w7aHEpX');

            if ($res->getStatusCode() == 200) { // 200 OK
                $response_data = $res->getBody()->getContents();
                $respuesta = json_decode($response_data);
                return response()->json([
                    $respuesta,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "msg" => "Server Error",
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "msg" => "Server Error: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/searchByRuc/{ruc}",
     *     tags={"Search"},
     *     summary="Search information by RUC",
     *     description="Search information about a person by their RUC number.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ruc",
     *         in="path",
     *         required=true,
     *         description="RUC number of the person to search",
     *         @OA\Schema(type="string")
     *     ),
     *          @OA\Response(
     *         response=200,
     *         description="Information found successfully.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="integer", example=0),
     *                 @OA\Property(property="RUC", type="string", example="string"),
     *                 @OA\Property(property="RazonSocial", type="string", example="string"),
     *                 @OA\Property(property="Direccion", type="string", example="string"),
     *                 @OA\Property(property="Tipo", type="string"),
     *                 @OA\Property(property="Inscripcion", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */

    public function searchByRuc($ruc)
    {

        $validator = Validator::make(['ruc' => $ruc], [
            'ruc' => 'required|numeric|digits:11',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $respuesta = array();

        $client = new Client([
            'verify' => false,
        ]);
        $res = $client->get('https://comprobante-e.com/facturacion/buscaCliente/BuscaClienteRuc.php?fe=N&token=qusEj_w7aHEpX&' . 'ruc=' . $ruc);
        if ($res->getStatusCode() == 200) { // 200 OK
            $response_data = $res->getBody()->getContents();
            $respuesta = json_decode($response_data);
        } else {
            return response()->json([
                "status" => 0,
                "msg" => "Server error",
            ], 500);
        }
        return response()->json([
            $respuesta,
        ]);
    }
}

