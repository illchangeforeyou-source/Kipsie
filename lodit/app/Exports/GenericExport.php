<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($order) {
            return [
                'Date' => $order->created_at,
                'Customer' => $order->customer_name,
                'Items' => $order->items,
                'Total' => $order->total,
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Customer', 'Items', 'Total'];
    }
}
