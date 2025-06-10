<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Orders_on_service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrdersOnServiceExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderOnServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->isPersonal()) {
            $orders = Orders_on_service::with('rooms')->get();
            return response()->json($orders, 200);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->isUser()) {
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->isUser()) {
            if (!Orders_on_service::query()->where('rooms_idRoom', $request->idRoom)
                ->where('users_idUser', $request->idUser)
                ->where('services_idService', $request->idService)
                ->where('status', 'Новый')->get()->isEmpty()) {
                return response()->json(['status' => 'Эта услуга уже заказана и будет доступна после её выполнения'], 403);
            }
            $order = Orders_on_service::create([
                'rooms_idRoom' => $request->idRoom,
                'users_idUser' => $request->idUser,
                'status' => 'Новый',
                'services_idService' => $request->idService
            ]);
            $order->save();
            return response()->json(['status' => 'Услуга успешно заказана, ожидайте выполнения'], 200);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->isPersonal()) {
            $order = Orders_on_service::find($id);

            return view('personal.order', ['order' => $order]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::user()->isPersonal()) {
            $order = Orders_on_service::find($id);
            return view('personal.orderEdit', ['order' => $order]);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->isPersonal()) {
            $order = Orders_on_service::find($id);
            $order->update([
                'status' => $request->status
            ]);
            $order->delete();
            return response()->json(['status' => 'Заказ выполнен'], 200);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->isPersonal()) {
            $order = Orders_on_service::find($id);
            $order->update([
                'status' => 'Выполнено'
            ]);
            $order->delete();
            return response()->json(['error' => 'Вы не авторизированы'], 401);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }
    public function createReport(Request $request)
    {
        $Export = new OrdersOnServiceExport();
        $Export->dateTo = $request->query('dateTo');
        $Export->dateFrom = $request->query('dateFrom');
        return Excel::download($Export, 'ordersOnServiceIn' . date('M') . '.xlsx');
    }
}
