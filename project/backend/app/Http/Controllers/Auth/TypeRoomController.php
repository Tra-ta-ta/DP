<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TypeRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $typerooms = TypeRoom::all();
        return response()->json(['types' => $typerooms], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->isAdmin()) {
            return view('admin.typeroomsCreate');
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $request->validate([
                'typeRoom' => 'required|min:5',
                'description' => 'required',
                'price' => 'required|min_digits:1'
            ]);
            $typeroom = TypeRoom::create([
                'typeRoom' => $request->typeRoom,
                'description' => $request->description,
                'price' => $request->price
            ]);
            $typeroom->save();
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $typeroom = TypeRoom::find($id);
            return view('admin.typeroom', ['typeroom' => $typeroom]);
        }
        if (Auth::user()->isUser()) {
            $typeroom = TypeRoom::find($id);
            return view('auth.typeroom', ['typeroom' => $typeroom]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $typeroom = TypeRoom::find($id);
            return view('admin.typeroomEdit', ['typeroom' => $typeroom]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->isAdmin()) {
            $typeroom = TypeRoom::find($id);
            $request->validate([
                'typeRoom' => 'required|min:5',
                'description' => 'required',
                'price' => 'required|min_digits:1'
            ]);
            $typeroom->update([
                'typeRoom' => $request->typeRoom,
                'description' => $request->description,
                'price' => $request->price
            ]);
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $typeroom = TypeRoom::find($id);
            $typeroom->delete();
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }
}
