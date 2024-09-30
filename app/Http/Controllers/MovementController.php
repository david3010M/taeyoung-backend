<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreMovementRequest $request)
    {
        $total = $request->cash + $request->yape + $request->plin +
            $request->card + $request->deposit;

        if ($total <= 0) {
            return response()->json([
                'message' => 'El monto total no puede ser menor o igual a 0'
            ], 400);
        }

        if ($request->isBankPayment) {
            $image = $request->file('voucher');
            $imageName = time() . '.' . $image->extension();
            
        }


        $data = [
            'number' => $request->number,
            'paymentDate' => $request->paymentDate,
            'typeDocument' => $request->typeDocument,
            'total' => $total,
            'cash' => $request->cash,
            'yape' => $request->yape,
            'plin' => $request->plin,
            'card' => $request->card,
            'deposit' => $request->deposit,
            'isBankPayment' => $request->isBankPayment,
            'numberVoucher' => $request->numberVoucher,
            'routeVoucher' => $request->routeVoucher,
            'comment' => $request->comment,
            'status' => 'GENERADA',
            'user_id' => $request->user_id,
            'bank_id' => $request->bank_id,
        ];
        $movement = Movement::create($request->validated());
        return response()->json($movement, 201);
    }

    public function show(int $id)
    {
        //
    }

    public function update(Request $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}

