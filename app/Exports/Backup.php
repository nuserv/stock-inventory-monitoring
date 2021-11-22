<?php

namespace App\Exports;

use App\Item;
use App\PmBranches;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;

class Backup implements FromCollection,WithHeadings,WithColumnWidths,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  PmBranches::query()
            ->select('pm_branches.customer_branches_code','customer_branch as client', 'branch')
            ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
            ->join('branches', 'branches.id', 'branch_id')
            ->where('customer_id', '1')
            ->get();
    }

    public function headings(): array
    {
        
            return [
                ['Client Branch code',
                'Client', 'Office']
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
