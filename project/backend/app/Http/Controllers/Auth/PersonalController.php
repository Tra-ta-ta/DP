<?php

namespace App\Http\Controllers\Auth;

use App\Events\RoleChange;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $role = $request->query('role', null);
        $FIO = $request->query('FIO', null);
        $id = $request->query('id', null);

        $query = User::with('roles');

        if (!is_null($id)) {
            $query->where('id', $id);
        }
        if (!is_null($FIO)) {
            $query->whereRaw("CONCAT(name,' ', surname,' ', thirdname) LIKE ?", ["%{$FIO}%"]);
        }
        if (!is_null($role) and $role != 'Пользователь' and $role != 'Администратор') {
            $role = Role::where('value', '=', $role)->first();
            $query->where('roles_idRole', '=', $role->id);
        } else {
            $roles = Role::whereIn('value', ['Пользователь', 'Администратор'])->get();

            $query->whereNotIn('roles_idRole', $roles->pluck('id'));
        }

        $users = $query->get();
        if ($users->isEmpty()) {
            return response()->json(['message' => 'Таких пользователей нет!'], 200);
        }

        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.personalCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'login' => 'unique:users,login'
        ], [
            'login.unique' => 'Такой логин уже используется'
        ]);
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'thirdname' => $request->thirdname,
            'phone' => $request->phone,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'roles_idRole' => $request->role
        ]);
        $user->save();
        return response()->json(['user' => $user], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update([
            'roles_idRole' => $request->idRole
        ]);
        $role = Role::find($request->idRole);
        broadcast(new RoleChange($user->id, $role->value, $role->id));
        return response()->json(['status' => 'Пользователь с ID №' . $user->id . ' успешно изменён'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['status' => 'Пользователь был удалён'], 200);
    }
}
