<?php

namespace App\Exports;

use App\Item;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;

class BackupInventoryExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if (auth()->user()->hasAnyrole('Head')) {
            return Item::query()
            ->select(
                'category',
                'item',
                'serial'
            )
            ->join('stocks', 'items.id', 'stocks.items_id')
            ->join('categories', 'items.category_id', 'categories.id')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
        }
        return Item::query()
            ->select(
                'category',
                'item', 
                DB::raw(
                    'SUM(CASE WHEN warehouses.status = \'in\' THEN 1 ELSE 0 END) as stock'
                ),
            )
            ->join('warehouses', 'items.id', 'warehouses.items_id')
            ->join('categories', 'items.category_id', 'categories.id')
            ->groupBy('item')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
    }

    public function headings(): array
    {
        if (auth()->user()->hasAnyrole('Head')) {
            return [
                ['Category',
                'Item Description',
                'Serial'],
            ];
        }
        return [
            ['Category',
            'Item Description',
            'Stock'],
        ];
    }

    public function columnWidths(): array
    {
        if (auth()->user()->hasAnyrole('Head')) {
            return [
                'A' => 35,
                'B' => 90,  
                'C' => 60,            
            ];
        }
        return [
            'A' => 35,
            'B' => 90,  
            'C' => 10,            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $style = array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'size'      =>  12,
                'bold'      =>  true
            )
        );
        
        $sheet->getStyle('1')->applyFromArray($style);
    }
}
