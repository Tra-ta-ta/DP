<?php

namespace App\Exports;

use App\Models\Orders_on_service;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersOnServiceExport implements FromView, ShouldAutoSize
{
    public $dateFrom;
    public $dateTo;
    public function view(): View
    {
        $orders = Orders_on_service::withTrashed()->whereBetween('created_at', [$this->dateFrom, $this->dateTo])->where('status', 'Выполнено')->get();
        return view('export.ordersService', [
            'orders' => $orders
        ]);
    }
}
