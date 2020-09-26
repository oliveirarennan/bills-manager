<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillStatusController extends Controller
{
    protected $authenticatedUser = 'Protected';

    public function __construct()
    {
        $this->authenticatedUser = auth('api')->user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bill $bill)
    {
        if (!$bill) {
            return response()->json(["message" => "Bill not found", 401]);
        }

        if ($bill->user_id !== $this->authenticatedUser->id) {
            return response()->json(["message" => "Access restrict", 403]);
        }

        $validator = Validator::make($request->only(['status']),
            [
                'status' => 'required|in:Atrasada,Paga,Aberta',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {

            $bill->status = $request->input('status');

            $bill->save();

            return response()->json($bill);

        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

}
