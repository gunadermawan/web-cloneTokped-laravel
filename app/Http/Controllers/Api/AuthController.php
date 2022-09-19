<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:8',
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first());
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (password_verify($request->password, $user->password)) {
                return $this->successResponse($user);
            } else {
                return $this->errorResponse('your password is wrong!');
            }
        }
        return $this->errorResponse('something went wrong :( ');
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first());
        }
        // add to db
        $user = User::create(
            array_merge(
                $request->all(),
                ['password' => bcrypt($request->password)]
            )
        );
        if ($user) {
            return $this->successResponse($user);
        } else {
            return $this->errorResponse('something went wrong :(');
        }
    }

    public function successResponse($data, $msg = 'success')
    {
        return response()->json([
            'code' => 200,
            'msg' => $msg,
            'data' => $data
        ], 200);
    }

    public function errorResponse($msg)
    {
        return response()->json([
            'code' => 400,
            'msg' => $msg
        ], 400);
    }
}
