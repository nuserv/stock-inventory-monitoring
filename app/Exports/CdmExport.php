<?php
  
namespace App\Exports;
  
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Cdm;
use Carbon\Carbon;
use DB;

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
        // dd(Carbon::createFromTime(0, 0, 0)->subDays(6));
        if (Carbon::now()->format('d') == 1) {
            if (Carbon::now()->format('h') >= 4 && Carbon::now()->format('i') > 5) {
                $prev = DB::table('asterisk.'.Carbon::now()->subMonth()->Format('FY'))
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
                ->whereDate('calldate', Carbon::now())
                // ->whereBetween('calldate', [Carbon::now()->subDays('2'), Carbon::now()])
                ->get();
                $current = Cdm::query()
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
                ->whereDate('calldate', Carbon::now())
                ->get();
                $cdm = $prev->merge($current);
            }
            
        }
        else{
            if (Carbon::now()->format('h') == 0  && Carbon::now()->format('i') <= 12) {
                $cdm = Cdm::query()
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
                ->whereDate('calldate', Carbon::yesterday())
                ->get();
            }
            else{
                $cdm = Cdm::query()
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
                ->whereDate('calldate', Carbon::now())
                // ->whereDate('calldate', '>=', Carbon::now()->subDays('1'))
                // ->whereBetween('calldate', [Carbon::now()->subDays('2'), Carbon::now()])
                // ->whereBetween('calldate', [Carbon::now()->subDays('1'), Carbon::now()])
                ->get();
            }
        }
        // dd($cdm);
        return collect($cdm);
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