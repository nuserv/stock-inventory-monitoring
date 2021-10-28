<?php

namespace App\Exports;

use App\PmSched;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;

class PmSchedExport implements FromCollection, WithStyles, ShouldAutoSize, WithColumnWidths, WithHeadings, WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $year;
    protected $from;
    protected $to;
    protected $branch;
    protected $branch_id;

    public function __construct($year, $from, $to, $branch, $branch_id) {
        $this->year = $year;
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
        $this->branch_id = $branch_id;
    }
    public function headings(): array
    {
        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        if (auth()->user()->hasanyrole('Manager', 'Editor') || auth()->user()->id == 142 || auth()->user()->id == 134) {
            return [
                [' ',' ',' '],
                ['SUMMARY OF PREVENTIVE MAINTENANCE REPORT'],
                ['CLIENT NAME: MERCURY DRUG CORPORATION'],
                ['COVERED MONTHS: '.strtoupper($months[$this->from-1]).' - '.strtoupper($months[$this->to-1].' '.$this->year)],
                ['AREA COVERED: '.strtoupper($this->branch->branch).' BRANCHES'],
                [''],
                ['DATE', 'BRANCH CODE', 'BRANCH'],
                [' ',' ',' ']
            ];
        }else{
            return [
                [' ',' ',' '],
                ['SUMMARY OF PREVENTIVE MAINTENANCE REPORT'],
                ['CLIENT NAME: MERCURY DRUG CORPORATION'],
                ['COVERED MONTHS: '.strtoupper($months[$this->from-1]).' - '.strtoupper($months[$this->to-1])],
                ['AREA COVERED: '.strtoupper(auth()->user()->branch->branch).' BRANCHES'],
                [''],
                ['DATE', 'BRANCH CODE', 'BRANCH'],
                [' ',' ',' ']
            ];
        }

    }

    public function styles(Worksheet $sheet)
    {
        if (auth()->user()->hasanyrole('Manager', 'Editor') || auth()->user()->id == 142 || auth()->user()->id == 134) {
            $data = PmSched::query()
                ->select(DB::raw("DATE_FORMAT(schedule, '%M %d, %Y') as Date"), 'code', 'customer_branch')
                ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
                ->whereYear('schedule', $this->year)
                ->whereMonth('schedule', '>=', $this->from)
                ->where('branch_id', $this->branch_id)
                ->whereMonth('schedule', '<=', $this->to)
                ->count();
        }else{
            $data = PmSched::query()
                ->select(DB::raw("DATE_FORMAT(schedule, '%M %d, %Y') as Date"), 'code', 'customer_branch')
                ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
                ->whereYear('schedule', $this->year)
                ->whereMonth('schedule', '>=', $this->from)
                ->where('branch_id', auth()->user()->branch->id)
                ->whereMonth('schedule', '<=', $this->to)
                ->count();
        }
        $styleHeader = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $styleHeaders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A2:C5')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => 'thin',
                ]
            ],
        ]);
        $sheet->getStyle('A1:C1')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A7:C8')->applyFromArray($styleHeader);
        $sheet->getStyle('A7:C7')->getFont()->setBold(true);
        $sheet->getStyle('A2:C5')->getFont()->setBold(true);
        for ($i=0; $i < $data; $i++) { 
            $sheet->getStyle('A'.($i+9).':B'.($i+9))->applyFromArray($styleHeader);
            $sheet->getStyle('C'.($i+9))->applyFromArray($styleHeaders);

        }
        $sheet->getRowDimension('1')->setRowHeight(60);
        $sheet->getPageSetup()->setPrintArea('A1:C44');;

    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('PLSI');
        $drawing->setDescription('PLSI LOGO');
        $drawing->setPath(public_path('/plsi1.jpg'));
        $drawing->setHeight(65);
        $drawing->setOffsetX(550);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,            
            'B' => 20        
        ];
    }

    public function collection()
    {
        if (auth()->user()->hasanyrole('Manager', 'Editor') || auth()->user()->id == 142 || auth()->user()->id == 134) {
            $data = PmSched::query()
                ->select(DB::raw("DATE_FORMAT(schedule, '%M %d, %Y') as Date"), 'code', 'customer_branch')
                ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
                ->whereYear('schedule', $this->year)
                ->whereMonth('schedule', '>=', $this->from)
                ->whereMonth('schedule', '<=', $this->to)
                ->where('branch_id', $this->branch_id)
                ->orderBy('schedule')
                ->get()
                ->map(function ($branch) {
                    $branch->customer_branch = str_replace('Mercury Drug', 'MDC', $branch->customer_branch);
                    return $branch;
                });
        }else{
            $data = PmSched::query()
                ->select(DB::raw("DATE_FORMAT(schedule, '%M %d, %Y') as Date"), 'code', 'customer_branch')
                ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
                ->whereYear('schedule', $this->year)
                ->whereMonth('schedule', '>=', $this->from)
                ->whereMonth('schedule', '<=', $this->to)
                ->where('branch_id', auth()->user()->branch->id)
                ->orderBy('schedule')
                ->get()
                ->map(function ($branch) {
                    $branch->customer_branch = str_replace('Mercury Drug', 'MDC', $branch->customer_branch);
                    return $branch;
                });
        }
        return $data;
    }
    
}