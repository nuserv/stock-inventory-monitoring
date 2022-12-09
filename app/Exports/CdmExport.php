<?php
  
namespace App\Exports;
  
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Cdm;
use Carbon\Carbon;

class CdmExport implements FromCollection, WithHeadings
{
    protected $data;
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function collection()
    {
        return collect($cdm = Cdm::query()
        ->select(
            'accountcode',
            'src',
            'dst',
            'dcontext',
            'clid',
            'channel',
            'dstchannel',
            'lastapp',
            'lastdata',
            'calldate',
            'answerdate',
            'hangupdate',
            'duration',
            'billsec',
            'disposition',
            'amaflags',
            'uniqueid',
            'userfield'
        )
        ->whereBetween('calldate', [Carbon::now()->subDays(2), Carbon::now()])
        ->get());
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'accountcode',
            'src',
            'dst',
            'dcontext',
            'clid',
            'channel',
            'dstchannel',
            'lastapp',
            'lastdata',
            'calldate',
            'answerdate',
            'hangupdate',
            'duration',
            'billsec',
            'disposition',
            'amaflags',
            'uniqueid',
            'userfield'
        ];
    }
}