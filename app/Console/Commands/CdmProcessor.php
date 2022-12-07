<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\CdmExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Cdm;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;
use Storage;

class CdmProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdm:processor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the excel file to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $this->info(Cdm::all());
        $cdm = Cdm::select(
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
        )->get();
        Excel::store(new CdmExport($cdm), 'Master.csv');
        exec('cat /var/www/html/stock/storage/app/Master.csv > /var/www/html/cdm/html/Master.csv');
        exec('rm /var/www/html/stock/storage/app/Master.csv');
        exec("echo 2 > /var/www/html/cdm/html/update.txt");
        exec("cd /var/www/html/cdm/html/ && bash 600a.sh");
        // Storage::disk('public')->put('Master.csv', Cdm::all());
        //    $public->put('users/'.$date.".sql", file_get_contents("{$date}.sql"));
        // Storage::put(Cdm::all(), '/csv/Master.csv');
    }
}
