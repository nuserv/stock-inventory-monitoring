<?php

namespace App\Exports;

use App\Defective;
use App\Item;
use App\Retno;
use App\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use DB;

class ExcelExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles,WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $retno;

    function __construct($retno) {
            $this->id = $retno;
    }
    
    public function headings(): array
    {
        $ret = Retno::select('branch', 'returns_no.created_at')
            ->where('return_no', $this->id)
            ->join('branches', 'branches.id', 'branch_id')
            ->first();
        return [
            ['','SERVICE CENTER STOCK MONITORING SYSTEM'],
            ['Reference Number', $this->id],
            ['Branch', $ret->branch],
            ['Date Created', $ret->created_at],
            ['Prepared by', auth()->user()->name.' '.auth()->user()->lastname],
            [],
            ['Category',
            'Item Description',
            'Serial'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        /*return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];*/
        $sheet->getStyle('1')->getFont()
        ->setSize(26)
        ->setBold(true)
        ->getColor()->setRGB('1F497D');
        //$sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getStyle(7)->getFont()->setBold(true);
        $sheet->getProtection()->setPassword('password');
        $sheet->getProtection()->setSheet(true);

    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/logo.JPG'));
        $drawing->setHeight(45);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 80,  
            'C' => 50,            
        ];
    }

    public function collection()
    {
        $def = Defective::select('category', 'item', 'serial')
            ->where('return_no', $this->id)
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')->get();
        return $def;
    }
}
