<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\TypeRoom;
use Illuminate\Http\Request;


class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $number = $request->query('number', null);
        $type = $request->query('type', null);
        $busy = $request->query('busy', false);
        $query = Room::with('types');
        if (!$busy) {
            $query->where('isBusy', 0);
        }
        if (!is_null($number)) {
            $query->where('number', $number);
        }
        if (!is_null($type)) {
            $query->where('typeRoom_idTypeRoom', $type);
        }
        $rooms = $query->get();

        return response()->json($rooms, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roomCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Room::query()->where('number', $request->number)->get()->isEmpty()) {
            return response()->json(['message' => 'Ошибка добавления, этот номер уже есть в списке'], 403);
        }
        $room = Room::create([
            'typeRoom_idTypeRoom' => $request->type,
            'number' => $request->number,
            'statusRoom' => 'Свободен'
        ]);
        $room->save();
        $newRoom = Room::with('types')->where('id', $room->id)->first();
        return response()->json($newRoom, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::find($id);
        return response()->json(['room' => $room], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $room = Room::find($id);
        $types = TypeRoom::all();
        return response()->json(['room' => $room, 'typeRooms' => $types], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::find($id);
        $request->validate([
            'statusRoom' => 'required',
            'typeRoom_idTypeRoom' => 'required'
        ]);
        $room->update([
            'statusRoom' => $request->status,
            'typeRoom_idTypeRoom' => $request->idTypeRoom
        ]);
        return response()->json(['status' => 'Номер №' . $room->number . ' был успешно изменён', 'data' => $room], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::find($id);
        $room->delete();
        return response()->json(['status' => 'Номер был успешно удалён'], 200);
    }
}
