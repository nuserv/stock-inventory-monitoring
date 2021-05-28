<?php

namespace App\Exports;

use App\Bstock;
use App\Item;
use App\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ExcelExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'Category',
            'Item Description',
            'Quantity',
        ];
    }

    public function collection()
    {
        $stock = Bstock::query()->select('category', 'itemname', 'count')->where('branch_id', 39)
            ->join('categories', 'categories.id', 'category_id')
            ->join('items', 'items.id', 'items_id')->get();
        return $stock;
    }
}
