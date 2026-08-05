<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ProductsExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        return Product::select(
            'name',
            'category',
            'price',
            'quantity',
            'minimum_stock',
            'status'
        )
        ->get()
        ->map(function ($product) {

            if ($product->quantity == 0) {
                $stockStatus = 'Out of Stock';
            }
            elseif ($product->quantity <= $product->minimum_stock) {
                $stockStatus = 'Low Stock';
            }
            else {
                $stockStatus = 'Available';
            }


            return [
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'minimum_stock' => $product->minimum_stock,
                'stock_status' => $stockStatus,
                'status' => $product->status,
            ];

        });
    }



    public function headings(): array
    {
        return [

            'Product Name',

            'Category',

            'Price',

            'Quantity',

            'Minimum Stock',

            'Stock Status',

            'Status'

        ];
    }

}