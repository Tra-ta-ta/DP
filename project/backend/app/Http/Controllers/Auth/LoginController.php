<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only(['login', 'password']))) {
            return $request->user();
        } else {
            return response()->json(['message' => 'Таких данных нет'], 401);
        }
    }

    public function registration(Request $request)
    {
        $request->validate([
            'login' => 'unique:users,login',
        ], [
            'login.unique' => 'Такой логин уже существует'
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'thirdname' => $request->thirdname,
            'phone' => $request->phone,
            'login' => $request->login,
            'password' => Hash::make($request->password)
        ]);
        $user->save();
        Auth::attempt($request->only(['login', 'password']));
        return response()->json(User::find($user->id), 200);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['data' => 'Выход'], 200);
    }
}
