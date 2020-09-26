<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{
    protected $authenticatedUser = 'Protected';

    public function __construct()
    {
        $this->authenticatedUser = auth('api')->user();

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foundBills = Bill::where('user_id', $this->authenticatedUser->id)->paginate(5);

        return response()->json($foundBills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->only(['status', 'due_date', 'url']),
            [
                'status' => 'bail|required|in:Atrasada,Paga,Aberta',
                'due_date' => 'required|date',
                'url' => 'required|url|unique:bills,url',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $bill = Bill::create([
                'user_id' => $this->authenticatedUser->id,
                'status' => $request->input('status'),
                'due_date' => $request->input('due_date'),
                'url' => $request->input('url'),
            ]);

            return response()->json($bill);

        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        if (!$bill) {
            return response()->json(["message" => "Bill not found", 401]);
        }

        if ($bill->user_id !== $this->authenticatedUser->id) {
            return response()->json(["message" => "Access restrict", 403]);
        }

        return response()->json($bill);
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

        $validatorRules = [
            'status' => 'bail|required|in:Atrasada,Paga,Aberta',
            'due_date' => 'required|date',
            'url' => 'required|url',
        ];

        if ($request->input('url') !== $bill->url) {
            $validatorRules['url'] = 'required|url|unique:bills,url';
        }

        $validator = Validator::make($request->only(['status', 'due_date', 'url']),
            $validatorRules
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {

            $bill->status = $request->input('status');
            $bill->due_date = $request->input('due_date');
            $bill->url = $request->input('url');

            $bill->save();

            return response()->json($bill);

        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {
        if (!$bill) {
            return response()->json(["message" => "Bill not found", 401]);
        }

        if ($bill->user_id !== $this->authenticatedUser->id) {
            return response()->json(["message" => "Access restrict", 403]);
        }

        try {
            $bill->delete();
            return response()->json('', 204);
        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
