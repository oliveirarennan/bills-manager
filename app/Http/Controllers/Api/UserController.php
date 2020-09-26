<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display the logged user
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->show();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->only(['name', 'email', 'password']),
            [
                'name' => 'bail|required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            return response()->json($user);

        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the logged user
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $authenticatedUser = auth('api')->user();
        unset($authenticatedUser->id);
        unset($authenticatedUser->created_at);
        unset($authenticatedUser->updated_at);

        return response()->json($authenticatedUser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (!$user) {
            return response()->json(["message" => "User not found", 401]);
        }

        $authenticatedUser = auth('api')->user();

        if ($user->id !== $authenticatedUser->id) {
            return response()->json(["message" => "Access restrict", 403]);
        }

        $validatorRules = [
            'name' => 'bail|required',
            'email' => 'required|email',
        ];

        if ($request->input('email') !== $user->email) {
            $validatorRules['email'] = 'required|email|unique:users,email';
        }

        $validator = Validator::make($request->only(['name', 'email', 'password']),
            $validatorRules
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {

            $user->name = $request->input('name');
            $user->email = $request->input('email');

            if ($request->filled('password')) {

                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            return response()->json($user);

        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!$user) {
            return response()->json(["message" => "User not found", 401]);
        }

        $authenticatedUser = auth('api')->user();

        if ($user->id !== $authenticatedUser->id) {
            return response()->json(["message" => "Access restrict", 403]);
        }

        try {
            $user->delete();

            auth('api')->logout();

            return response()->json('', 204);
        } catch (Exception $error) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
