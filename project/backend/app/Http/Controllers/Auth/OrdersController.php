<?php

namespace App\Http\Controllers\Auth;

use App\Events\StatusOrderChange;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Models\TypeRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrdersExport;
use App\Rules\MinDateRange;
use Illuminate\Support\Facades\Broadcast;
use Maatwebsite\Excel\Facades\Excel;


class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::user()->isMenager()) {
            $orders = Order::all();
            return response()->json($orders, 200);
        }
        if (Auth::user()->isUser()) {
            return response()->json(Auth::user()->orders(), 200);
        }

        return response()->json(['status' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->isUser()) {
            $types = TypeRoom::all();
            return view('auth.orderCreate', ['types' => $types]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->isUser()) {
            $isUserHasOrder = Order::where('users_idUser', '=', $request->idUser)->first();
            $room = Room::where('users_idUser', '=', null)->where('typeRoom_idTypeRoom', '=', $request->idTypeRoom)->first();
            if (!isset($isUserHasOrder)) {
                if (isset($room)) {
                    $request->validate([
                        'onDate' => 'required|after_or_equal:tomorrow|date',
                        'endDate' => ['required', 'after:onDate', 'date',  new MinDateRange($request->onDate)]
                    ], [
                        'onDate.after_or_equal' => 'Дата начала должна быть на завтра или позже'
                    ]);
                    $order = Order::create([
                        'message' => $request->message,
                        'onDate' => $request->onDate,
                        'endDate' => $request->endDate,
                        'typeRoom_idTypeRoom' => $request->idTypeRoom,
                        'users_idUser' => $request->idUser,
                        'status' => 'Новый'
                    ]);
                    $order->save();
                    return response()->json(['status' => 'Вы успешно забронировались, ожидайте изменения статуса', 'order' => $order->id], 200);
                }
                return response()->json(['error' => 'Для данного типа нету номеров, приносим свои сожеления'], 402);
            }
            return response()->json(['error' => 'У вас уже имеется бронь, отмените её'], 402);
        }
        return response()->json(['error' => 'Вы не авторизированы, для бронирования необходимо авторизоватся'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $order = Order::find($id);
            return view('admin.order', ['order' => $order]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::user()->isUser()) {
            $order = Order::find($id);
            $types = TypeRoom::all();
            return view('auth.orderEdit', ['order' => $order, 'types' => $types]);
        }
        if (Auth::user()->isAdmin()) {
            $order = Order::find($id);
            $rooms = Room::all()->where('typeRoom_idTypeRoom', '=', $order->typeRoom_idTypeRoom)->where('users_idUser', '=', null);
            return view('admin.orderEdit', ['order' => $order, 'rooms' => $rooms]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::user()->isUser()) {
            $order = Order::find($request->id);
            $request->validate([
                'status' => 'required'
            ]);
            $order->update([
                'statusRoom' => $request->status
            ]);
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        if (Auth::user()->isMenager()) {
            $order = Order::find($request->id);

            if ($request->status == 'Отменен') {
                $room = Room::find($order->rooms_idRoom);
                $order->update([
                    'status' => $request->status,
                    'rooms_idRoom' => null
                ]);
                if ($room) {
                    $room->update([
                        'users_idUser' => null,
                        'statusRoom' => 'Свободно'
                    ]);
                }
                Broadcast(new StatusOrderChange($request->status, $request->id));
                return response()->json(Order::with('user')->where('id', $order->id)->first(), 200);
            }
            $room = Room::find($request->room);
            $order->update([
                'status' => $request->status,
                'rooms_idRoom' => $request->room
            ]);
            $room->update([
                'users_idUser' => $order->users_idUser,
                'statusRoom' => 'Занят'
            ]);
            Broadcast(new StatusOrderChange($request->status, $request->id));
            return response()->json(Order::with('user')->where('id', $order->id)->first(), 200);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $order = Order::find($id);
            $room = Room::where('users_idUser', '!=', null)->where('id', '=', $order->rooms_idRoom)->first();
            $order->update([
                'status' => 'Выполнен или отменён'
            ]);
            if (isset($room)) {
                $room->update([
                    'users_idUser' => null,
                    'statusRoom' => 'Свободно'
                ]);
            }

            $order->delete();
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        if (Auth::user()->isUser()) {
            $order = Order::find($id);
            $order->update([
                'status' => 'Удалён пользователем'
            ]);
            $order->delete();
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
    }
    public function createReport(Request $request)
    {
        $orderExport = new OrdersExport();
        $orderExport->dateTo = $request->query('dateTo');
        $orderExport->dateFrom = $request->query('dateFrom');
        return Excel::download($orderExport, 'ordersIn' . date('M') . '.xlsx');
    }
}
