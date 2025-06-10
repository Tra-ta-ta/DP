<?php

use App\Events\StatusOrderChange;
use App\Models\Order;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $today = Carbon::today()->toDateString();

    $orders = Order::query()->where('endDate', $today)->get();
    foreach ($orders as $order) {
        if (!is_null($order->rooms_idRoom)) {
            $room = Room::query()->where('id', $order->rooms_idRoom)->first();
            $room->update([
                'statusRoom' => 'Свободно',
                'isBusy' => 0
            ]);
        }
        $order->update([
            'status' => 'Выполнен'
        ]);
        Broadcast(new StatusOrderChange('Выполнен', $order->id));
        $order->delete();
    }
})->everySecond();
