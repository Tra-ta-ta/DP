<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromView, ShouldAutoSize
{
    public $dateFrom;
    public $dateTo;
    public function view(): View
    {
        $orders = Order::withTrashed()->whereBetween('created_at', [$this->dateFrom, $this->dateTo])->where('status', 'Выполнен')->get();
        $full_price = 0;
        foreach ($orders as $order) {
            $full_price += $order->checkType()->price;
        }
        return view('export.orders', [
            'orders' => $orders,
            'full_price' => $full_price
        ]);
    }
}
