<?php

namespace App\Exports;

use App\Defective;
use App\Item;
use App\PreparedItem;
use App\StockRequest;
use App\Retno;
use Carbon\Carbon;
use App\Pullno;
use App\RepairedNo;
use App\Pullout;
use App\Category;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use DB;

class ExcelExport implements FromCollection,WithHeadings,WithColumnWidths,WithStyles,WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $retno;

    function __construct($retno, $type) {

            $this->id = $retno;
            $this->type = $type;

    }
    
    public function headings(): array
    {
        if ($this->type == 'DDR') {
            $header = 'DEFECTIVE DELIVERY RECEIPT';
            $ret = Retno::select('branch', 'returns_no.created_at')
                ->where('return_no', $this->id)
                ->join('branches', 'branches.id', 'branch_id')
                ->first();
            $to = 'Warehouse';
            $from = $ret->branch;
        }
        if ($this->type == 'CDR') {
            $header = 'CONVERSION DELIVERY RECEIPT';
            $ret = Retno::select('branch', 'returns_no.created_at')
                ->where('return_no', substr($this->id, 0, strpos($this->id, '/')))
                ->join('branches', 'branches.id', 'branch_id')
                ->first();
            $pullout = Defective::query()->select('pullout_date', 'branch', 'customer_branch')
                ->join('branches', 'branches.id', 'bid')
                ->join('customer_branches', 'customer_branches.id', 'customer_branches_id')
                ->where('return_no', $ret->return_no)->first();
            $to = 'Warehouse';
            $from = $pullout->branch;

            return [
                ['','SERVICE CENTER STOCK MONITORING SYSTEM'],
                ['',$header],
                ['Date Created', Carbon::parse($ret->created_at)->isoFormat('lll')],
                ['Date Pullout', $pullout->pullout_date],
                ['Return Reference Number', substr($this->id, 0, strpos($this->id, '/'))],
                ['DR Reference Number', substr($this->id, strpos($this->id, "/") + 1)],
                ['Pullout from', $pullout->customer_branch],
                ['To', $to],
                ['From', $from],
                ['Prepared by', auth()->user()->name.' '.auth()->user()->lastname],
                [],
                ['Category',
                'Item Description',
                'Serial'],
            ];

        }
        if ($this->type == 'DSR') {
            $header = 'DELIVERY RECEIPT';
            $ret = StockRequest::select('branch', 'requests.updated_at as created_at')
                ->where('request_no', $this->id)
                ->join('branches', 'branches.id', 'branch_id')
                ->first();
            $from = 'Warehouse';
            $to = $ret->branch;
        }

        if ($this->type == 'PR') {
            $header = 'PULLOUT RECEIPT';
            $ret = Pullno::select('branch', 'pullouts_no.created_at')
                ->where('pullout_no', $this->id)
                ->join('branches', 'branches.id', 'branch_id')
                ->first();
            $to = 'Warehouse';
            $from = $ret->branch;
        }
        if ($this->type == 'RR') {
            $header = 'REPAIRED RECEIPT';
            $ret = RepairedNo::select('repaired_no.created_at')
                ->where('repaired_no', $this->id)
                ->first();
            $to = 'Warehouse';
            $from = 'Repair';
        }
        

        return [
            ['','SERVICE CENTER STOCK MONITORING SYSTEM'],
            ['',$header],
            ['Reference Number', $this->id],
            ['To', $to],
            ['Date Created', Carbon::parse($ret->created_at)->isoFormat('lll')],
            ['From', $from],
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
        $sheet->getStyle('2')->getFont()
        ->setSize(26)
        ->setBold(true);
        /*$sheet->cell('B2', function($cell){
            $cell->setAlignment('center');
        });*/
        $style = array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            )
        );
        $sheet->getStyle('2')->applyFromArray($style);
        //$sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle(12)->getFont()->setBold(true);
        $sheet->getProtection()->setPassword('nuserv-demo');
        $sheet->getProtection()->setSheet(true);

    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('IDSI');
        $drawing->setDescription('IDSI LOGO');
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
        
        if ($this->type == 'CDR') {
            $conversion = Defective::select('category', 'item', 'serial')
            ->where('return_no', substr($this->id, 0, strpos($this->id, '/')))
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
            return $conversion;
        }

        if ($this->type == 'DDR') {
            $def = Defective::select('category', 'item', 'serial')
            ->where('return_no', $this->id)
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
            return $def;
        }

        if ($this->type == 'DSR') {
            $stock = PreparedItem::select('category', 'item', 'serial')
            ->where('request_no', $this->id)
            ->join('items', 'items.id', 'items_id')
            ->join('categories', 'categories.id', 'items.category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
            return $stock;
        }
        if ($this->type == 'PR') {
            $pr = Pullout::select('category', 'item', 'serial')
            ->where('pullout_no', $this->id)
            ->join('items', 'items.id', 'items_id')
            ->join('categories', 'categories.id', 'pullouts.category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
            return $pr;
        }
        if ($this->type == 'RR') {
            $rr = Defective::select('category', 'item', 'serial')
            ->where('repaired_no', $this->id)
            ->join('items', 'items.id', 'items_id')
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get();
            return $rr;
        }
        
    }
}
