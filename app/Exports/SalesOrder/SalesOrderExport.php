<?php

namespace App\Exports\SalesOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesOrderExport implements FromView, ShouldAutoSize
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('sales_order.export', [
            'sales_order' => $this->data
        ]);
    }
}
