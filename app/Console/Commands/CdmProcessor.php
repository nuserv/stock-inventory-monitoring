<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\CdmExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

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
        
        Excel::store(new CdmExport('test'), 'Master.csv');
        // exec('cd /var/www/html/stock && ./cdm.sh');
        //600a.sh
        // $tDATE = Carbon::now()->subDays(2)->isoFormat('Y-MM-DD');
        // $DATE = Carbon::now()->subDays(2)->isoFormat('YMMDD');
        // // $currenttime = Carbon::now()->isoFormat('HHmm');
        // $homedir = "/var/www/html/cdm/html/$tDATE";
        // exec("rm -Rf $homedir");
        // exec("mkdir $homedir");
        // exec("cp /var/www/html/cdm/html/*.php $homedir");
        // exec("cp /var/www/html/cdm/html/backup.index.php /var/www/html/cdm/html/$tDATE/index.php");
        // exec("grep \"$tDATE\" /var/www/html/stock/storage/app/Master.csv > /var/www/html/cdm/html/$tDATE/600ada1te12.csv");
        // exec("grep -v \"Transferred Call\" $homedir/600ada1te12.csv > $homedir/600ada1te1.csv");
        // exec("rm $homedir/600ada1te12.csv -f");
        // exec("ruby -rcsv -ne'puts CSV.generate_line(CSV.parse_line(\$_), :col_sep=>\"|\")' $homedir/600ada1te1.csv > $homedir/600ar1uby.csv");
        // exec("rm $homedir/600ada1te1.csv -f");
        // exec("cut -d\| -f1,3,5,7,10,13-15 $homedir/600ar1uby.csv > $homedir/600are1move.csv");
        // exec("rm -f $homedir/600ar1uby.csv");
        // exec("grep -e '|604|' -e '|5501|' -e '|5502|' -e '|5505|' -e '|5506|' -e '|5507|' -e '|5509|' -e '|614|' -e '|5521|' -e '|5522|' -e '|5520|' -e '|608|' -e '|1130|' -e '|1132|' -e '|1133|' -e '|607|' -e '|1123|' -e '|2201|' -e '|2222|' -e '|2213|' -e '|600|' -e '|2230|' -e '|2231|' -e '|2202|' -e '|2233|' -e '|2205|' -e '|2210|' -e '|2232|' -e '|2235|' -e '|2234|' $homedir/600are1move.csv > $homedir/600a600.csv");
        // exec("rm -f $homedir/600are1move.csv");
        // exec("cut -d\\| -f2- $homedir/600a600.csv > $homedir/600are2move.csv");
        // exec("rm -f $homedir/600a600.csv");
        // # convert pipe delimiter
        // #ruby -rcsv -ne 'puts CSV.generate_line(CSV.parse_line($_), :col_sep=>",")' $homedir/600are2move.csv > $homedir/600aruby.csv
        // exec("csvtool -t '|' -u ',' cat $homedir/600are2move.csv -o $homedir/600aru2by.csv");
        // exec("rm -f $homedir/600are2move.csv");
        // exec("grep '$tDATE' $homedir/600aru2by.csv > $homedir/600arubys.csv");
        // exec("rm -f $homedir/600aru2by.csv");
        // exec("grep -v 's,' $homedir/600arubys.csv > $homedir/600aruby.csv");
        // exec("rm -f $homedir/600arubys.csv");
        // #grep "$PREV_DAT" 600aru2by.csv > $homedir/600aruby.csv
        // #move column(call date) to the 1st column
        // exec("awk -F, -vOFS=\",\" '{k=$5; print $0,k}' $homedir/600aruby.csv > $homedir/600acalldate.csv");
        // exec("rm -f $homedir/600aruby.csv");
        // exec("cut -d, -f5 --complement $homedir/600acalldate.csv > $homedir/600calldate1.csv");
        // exec("rm -f $homedir/600acalldate.csv");
        // #move column(from) to 2nd column
        // exec("awk -F, -vOFS=\",\" '{k=$2; print $0,k}' $homedir/600calldate1.csv > $homedir/600afrom.csv");
        // exec("rm -f $homedir/600calldate1.csv");
        // exec("cut -d, -f2 --complement $homedir/600afrom.csv > $homedir/600afrom1.csv");
        // exec("rm -f $homedir/600afrom.csv");
        // // exec("#move column(to) to 3rd column");
        // exec("awk -F, -vOFS=\",\" '{k=$1; print $0,k}' $homedir/600afrom1.csv > $homedir/600ato.csv");
        // exec("rm -f $homedir/600afrom1.csv");
        // exec("cut -d, -f1 --complement $homedir/600ato.csv > $homedir/600ato1.csv");
        // exec("rm -f $homedir/600ato.csv");
        // // exec("#move column(destination channel) to 4th column");
        // exec("awk -F, -vOFS=\",\" '{k=$1; print $0,k}' $homedir/600ato1.csv > $homedir/600adstchannel.csv");
        // exec("rm -f $homedir/600ato1.csv");
        // exec("cut -d, -f1 --complement $homedir/600adstchannel.csv > $homedir/600adstchannel1.csv");
        // exec("rm -f $homedir/600adstchannel.csv");
        // // exec("#move column(dispositon) to 5th column");
        // exec("awk -F, -vOFS=\",\" '{k=$3; print $0,k}' $homedir/600adstchannel1.csv > $homedir/600adisposition.csv");
        // exec("rm -f $homedir/600adstchannel1.csv");
        // exec("cut -d, -f3 --complement $homedir/600adisposition.csv > $homedir/600adisposition1.csv");
        // exec("rm -f $homedir/600adisposition.csv");
        // // exec("#move column(duration) to 6th column");
        // exec("awk -F, -vOFS=\",\" '{k=$3; print $0,k}' $homedir/600adisposition1.csv > $homedir/600aduration.csv");
        // exec("rm -f $homedir/600adisposition1.csv");
        // exec("cut -d, -f3 --complement $homedir/600aduration.csv > $homedir/600aduration1.csv");
        // exec("rm -f $homedir/600aduration.csv");
        // // exec("#duration less billsec to last column");
        // exec("awk 'BEGIN{FS=\",\";OFS=\",\"} {print $0, $6-$2}' $homedir/600aduration1.csv > $homedir/600a2.csv");
        // exec("rm -f $homedir/600aduration1.csv");
        // exec("cut -d, -f2 --complement $homedir/600a2.csv > $homedir/600a11.csv");
        // exec("rm -f $homedir/600a2.csv");
        // exec("awk 'BEGIN{FS=\",\";OFS=\",\"} {print $0, $6+$7}' $homedir/600a11.csv > $homedir/600a12.csv");
        // exec("rm -f $homedir/600a11.csv");
        // exec("cut -d, -f7 --complement $homedir/600a12.csv > $homedir/600a1.csv");
        // exec("rm -f $homedir/600a12.csv");
        // // exec("#format duration and time answered before call");
        // exec("awk -F',' '{cmd=\"date -d@\"$6-28800\" +\\\"%H:%M:%S\\\"\";cmd |getline $6; close(cmd) }1' OFS=, $homedir/600a1.csv > $homedir/600anospace4.csv");
        // exec("rm -f $homedir/600a1.csv");
        // exec("awk -F, -v OFS=, '{sub(//, \"&\\047\", $6)}1' $homedir/600anospace4.csv > $homedir/600anospace3.csv");
        // exec("rm -f $homedir/600anospace4.csv");
        // exec("awk -F',' '{cmd=\"date -d@\"$7-28800\" +\\\"%H:%M:%S\\\"\";cmd |getline $7; close(cmd) }1' OFS=, $homedir/600anospace3.csv > $homedir/600anospace5.csv");
        // exec("rm -f $homedir/600anospace3.csv");
        // exec("awk -F, -v OFS=, '{sub(//, \"&\\047\", $7)}1' $homedir/600anospace5.csv > $homedir/600anospace2.csv");
        // exec("rm -f  $homedir/600anospace5.csv");
        // // exec("#add column to end of csv");
        // exec("sed 's/$/,date to convert/' $homedir/600anospace2.csv > $homedir/600anospace.csv");
        // exec("rm -f $homedir/600anospace2.csv");
        // // exec("#convert date");
        // exec("awk -F',' '{cmd=\"date -d\\\"\"$1\"\\\" +\\\"%Y%m%d%H%M\\\"\";cmd |getline $8; close(cmd) }1' OFS=, $homedir/600anospace.csv > $homedir/600aready1.csv");
        // exec("rm -f $homedir/600anospace.csv");
        // exec("awk -F',' '{cmd=\"date -d\\\"\"$8\"\\\" +\\\"%Y%m%d%H%M\\\"\";cmd |getline $8; close(cmd) }1' OFS=, $homedir/600aready1.csv > $homedir/600aready.csv");
        // exec("rm -f $homedir/600aready1.csv");
        // // exec("#remove qoute to last column");
        // exec("awk -F, -v OFS=\",\" '{gsub(/\"/,\"\",$8)}1' $homedir/600aready.csv > $homedir/600aremoveqoute.csv");
        // exec("rm -f $homedir/600aready.csv");
        // // exec("#filter 1st 12 character form last column");
        // exec("awk -v OFS=\",\" -F\",\" '{if(length($8)>12) $8=substr($8,1,12); print;}' $homedir/600aremoveqoute.csv > $homedir/600a1trim.csv");
        // exec("rm -f $homedir/600aremoveqoute.csv");
        // // exec("#filter 12-6");
        // // exec("#grep "ANSWERED" $homedir/600a1trim.csv > $homedir/600atrim.csv");
        // // exec("#grep "NO ANSWER" $homedir/600a1trim.csv >> $homedir/600atrim.csv");
        // exec("grep -e 'ANSWERED' -e 'NO ANSWER' $homedir/600a1trim.csv > $homedir/600atrim2.csv");
        // exec("rm -f $homedir/600a1trim.csv");
        // // exec("#grep "NO ANSWER" $homedir/600a1trim.csv >> $homedir/600atrim.csv");
        // exec("awk -F',' '$3~/600|2230|2231|2202|2233|2205|2232|2235|2234|2210/' $homedir/600atrim2.csv > $homedir/600atrim3.csv");
        // // exec("#rm -f $homedir/600atrim2.csv");
        // exec("grep -E \"SIP\"  $homedir/600atrim3.csv > $homedir/600atrim.csv");
        // // exec("#");
        // $first = $DATE.'0000';
        // $end = $DATE.'0559';
        // exec("awk -v t=\"$first\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tfirst1.csv");
        // // exec("#filter 6-3");
        // $second = $DATE.'0600';
        // $end = $DATE.'1459';
        // exec("awk -v t=\"$second\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tsecond1.csv");
        // // exec("#filter 3-12");
        // $third = $DATE.'1500';
        // $end = $DATE.'2359';
        // exec("awk -v t=\"$third\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tthird1.csv");
        // // exec("#");
        // exec("cut -d, -f8 --complement $homedir/600atrim.csv > $homedir/600tall2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tfirst1.csv > $homedir/600tfirst2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tsecond1.csv > $homedir/600tsecond2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tthird1.csv > $homedir/600tthird2.csv");
        // exec("rm -f $homedir/600atrim.csv");
        // // exec("rm -f $homedir/600tfirst1.csv");
        // exec("rm -f $homedir/600tsecond1.csv");
        // exec("rm -f $homedir/600tthird1.csv");
        // // exec("#");
        // exec("grep -Ev \"<[0-8][0-9]{3}>\"  $homedir/600tall2.csv > $homedir/600tall.csv");
        // exec("grep -Ev \"<[0-8][0-9]{3}>\" $homedir/600tfirst2.csv > $homedir/600tfirst.csv");
        // exec("grep -Ev \"<[0-8][0-9]{3}>\" $homedir/600tsecond2.csv > $homedir/600tsecond.csv");
        // exec("grep -Ev \"<[0-8][0-9]{3}>\" $homedir/600tthird2.csv > $homedir/600tthird.csv");
        // // exec("#");
        // exec("grep -E \"<[0-8][0-9]{3}>\" $homedir/600tall2.csv > $homedir/600tall4.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsitotal1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsianswered1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/plsinoanswe1.csv");
        // exec("grep \"ANSWERED\" $homedir/600tfirst.csv >> $homedir/plsianswered1.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tfirst.csv >> $homedir/plsinoanswe1.csv");
        // exec("cat $homedir/600tfirst.csv >> $homedir/plsitotal1.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsitotal2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsianswered2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/plsinoanswe2.csv");
        // exec("grep \"ANSWERED\" $homedir/600tsecond.csv >> $homedir/plsianswered2.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tsecond.csv >> $homedir/plsinoanswe2.csv");
        // exec("cat $homedir/600tsecond.csv >> $homedir/plsitotal2.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsitotal3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsianswered3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/plsinoanswe3.csv");
        // exec("grep \"ANSWERED\" $homedir/600tthird.csv >> $homedir/plsianswered3.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tthird.csv >> $homedir/plsinoanswe3.csv");
        // exec("cat $homedir/600tthird.csv >> $homedir/plsitotal3.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsitotal.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsianswered.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/plsinoanswe.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall.csv >> $homedir/plsianswered.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall.csv >> $homedir/plsinoanswe.csv");
        // exec("cat $homedir/600tall.csv >> $homedir/plsitotal.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsitotal4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/plsianswered4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/plsinoanswe4.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall4.csv >> $homedir/plsianswered4.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall4.csv >> $homedir/plsinoanswe4.csv");
        // exec("cat $homedir/600tall4.csv >> $homedir/plsitotal4.csv");
        // // exec("#");
        // exec("#cat $homedir/600nnospace.csv >> $homedir/plsinoanswe.csv");
        // exec("cut -d, -f7 --complement $homedir/plsinoanswe.csv > $homedir/plsinoanswer.csv");
        // exec("cut -d, -f7 --complement $homedir/plsinoanswe1.csv > $homedir/plsinoanswer1.csv");
        // exec("cut -d, -f7 --complement $homedir/plsinoanswe2.csv > $homedir/plsinoanswer2.csv");
        // exec("cut -d, -f7 --complement $homedir/plsinoanswe3.csv > $homedir/plsinoanswer3.csv");
        // exec("cut -d, -f7 --complement $homedir/plsinoanswe4.csv > $homedir/plsinoanswer4.csv");
        // // exec("rm -f $homedir/plsinoanswe.csv");
        // // exec("rm -f $homedir/plsinoanswe1.csv");
        // // exec("rm -f $homedir/plsinoanswe2.csv");
        // // exec("rm -f $homedir/plsinoanswe3.csv");
        // // exec("rm -f $homedir/plsinoanswe4.csv");
        // exec("#cut -d, -f1-2,9 --complement $homedir/600at.csv > $homedir/plsianswered.csv");
        // exec("wc -l <$homedir/plsitotal1.csv > $homedir/plsitotal1.txt");
        // exec("cat $homedir/plsitotal1.txt", $plsitotal1, $retval);
        // foreach ($plsitotal1 as $key) {
        //     $at = $key-1;
        // }
        // // exec("at1=$(($at-1))");
        // exec("echo \"$at\" > $homedir/plsitotal1.txt");
        // exec("wc -l <$homedir/plsianswered1.csv > $homedir/plsianswered1.txt");
        // exec("wc -l <$homedir/plsinoanswer1.csv > $homedir/plsinoanswer1.txt");
        // exec("cat $homedir/plsianswered1.txt", $plsianswered1, $retval);
        // foreach ($plsianswered1 as $key) {
        //     $ba = $key-1;
        // }
        // exec("cat $homedir/plsinoanswer1.txt", $plsinoanswer1, $retval);
        // foreach ($plsinoanswer1 as $key) {
        //     $da = $key-1;
        // }
        // // exec("ba=$(($aa-1))");
        // // exec("da=$(($ca-1))");
        // // exec("#ea=$(($ba+$da))");
        // // exec("#echo "$ea" > $homedir/plsitotal1.txt");
        // exec("echo \"$ba\" > $homedir/plsianswered1.txt");
        // exec("echo \"$da\" > $homedir/plsinoanswer1.txt");
        // exec("echo \"disposition,value\" > $homedir/plsi1.csv");
        // exec("echo \"ANSWERED,$ba\" >> $homedir/plsi1.csv");
        // exec("echo \"NO ANSWER,$da\" >> $homedir/plsi1.csv");
        // // exec("#");
        // exec("wc -l <$homedir/plsitotal2.csv > $homedir/plsitotal2.txt");
        // exec("cat $homedir/plsitotal2.txt", $plsitotal2, $retval);
        // foreach ($plsitotal2 as $key) {
        //     $at22 = $key-1;
        // }
        // // exec("at22=$(($at2-1))");
        // exec("echo \"$at22\" > $homedir/plsitotal2.txt");
        // exec("wc -l <$homedir/plsianswered2.csv > $homedir/plsianswered2.txt");
        // exec("wc -l <$homedir/plsinoanswer2.csv > $homedir/plsinoanswer2.txt");
        // exec("cat $homedir/plsianswered2.txt", $plsianswered2, $retval);
        // foreach ($plsianswered2 as $key) {
        //     $bb = $key-1;
        // }
        // exec("cat $homedir/plsinoanswer2.txt", $plsinoanswer2, $retval);
        // foreach ($plsinoanswer2 as $key) {
        //     $db = $key-1;
        // }
        // // exec("bb=$(($ab-1))");
        // // exec("db=$(($cb-1))");
        // // exec("#eb=$(($bb+$db))");
        // // exec("#echo "$eb" > $homedir/plsitotal2.txt");
        // exec("echo \"$bb\" > $homedir/plsianswered2.txt");
        // exec("echo \"$db\" > $homedir/plsinoanswer2.txt");
        // exec("echo \"disposition,value\" > $homedir/plsi2.csv");
        // exec("echo \"ANSWERED,$bb\" >> $homedir/plsi2.csv");
        // exec("echo \"NO ANSWER,$db\" >> $homedir/plsi2.csv");
        // // exec("#");
        // exec("wc -l <$homedir/plsitotal3.csv > $homedir/plsitotal3.txt");
        // exec("cat $homedir/plsitotal3.txt", $plsitotal3, $retval);
        // foreach ($plsitotal3 as $key) {
        //     $at33 = $key-1;
        // }
        // // exec("at33=$(($at3-1))");
        // exec("echo \"$at33\" > $homedir/plsitotal3.txt");
        // exec("wc -l <$homedir/plsianswered3.csv > $homedir/plsianswered3.txt");
        // exec("wc -l <$homedir/plsinoanswer3.csv > $homedir/plsinoanswer3.txt");
        // exec("cat $homedir/plsianswered3.txt", $plsianswered3, $retval);
        // foreach ($plsianswered3 as $key) {
        //     $bc = $key-1;
        // }
        // exec("cat $homedir/plsinoanswer3.txt", $plsinoanswer3, $retval);
        // foreach ($plsinoanswer3 as $key) {
        //     $dc = $key-1;
        // }
        // // exec("bc=$(($ac-1))");
        // // exec("dc=$(($cc-1))");
        // // exec("#ec=$(($bc+$dc))");
        // // exec("#echo "$ec" > $homedir/plsitotal3.txt");
        // exec("echo \"$bc\" > $homedir/plsianswered3.txt");
        // exec("echo \"$dc\" > $homedir/plsinoanswer3.txt");
        // exec("echo \"disposition,value\" > $homedir/plsi3.csv");
        // exec("echo \"ANSWERED,$bc\" >> $homedir/plsi3.csv");
        // exec("echo \"NO ANSWER,$dc\" >> $homedir/plsi3.csv");
        // // exec("#");
        // exec("wc -l <$homedir/plsitotal.csv > $homedir/plsitotal.txt");
        // exec("cat $homedir/plsitotal.txt", $plsitotal, $retval);
        // foreach ($plsitotal as $key) {
        //     $at44 = $key-1;
        // }
        // // exec("at44=$(($at4-1))");
        // exec("echo \"$at44\" > $homedir/plsitotal.txt");
        // exec("wc -l <$homedir/plsianswered.csv > $homedir/plsianswered.txt");
        // exec("wc -l <$homedir/plsinoanswer.csv > $homedir/plsinoanswer.txt");
        // exec("cat $homedir/plsianswered.txt", $plsianswered, $retval);
        // foreach ($plsianswered as $key) {
        //     $b = $key-1;
        // }
        // exec("cat $homedir/plsinoanswer.txt", $plsinoanswer, $retval);
        // foreach ($plsinoanswer as $key) {
        //     $d = $key-1;
        // }
        // // exec("b=$(($a-1))");
        // // exec("d=$(($c-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/plsitotal.txt");
        // exec("echo \"$b\" > $homedir/plsianswered.txt");
        // exec("echo \"$d\" > $homedir/plsinoanswer.txt");
        // exec("echo \"disposition,value\" > $homedir/plsi.csv");
        // exec("echo \"ANSWERED,$b\" >> $homedir/plsi.csv");
        // exec("echo \"NO ANSWER,$d\" >> $homedir/plsi.csv");
        // // exec("#");
        // exec("wc -l <$homedir/plsitotal4.csv > $homedir/plsitotal4.txt");
        // exec("cat $homedir/plsitotal4.txt", $plsitotal4, $retval);
        // foreach ($plsitotal4 as $key) {
        //     $at55 = $key-1;
        // }
        // // exec("at55=$(($at5-1))");
        // exec("echo \"$at55\" > $homedir/plsitotal4.txt");
        // exec("wc -l <$homedir/plsianswered4.csv > $homedir/plsianswered4.txt");
        // exec("wc -l <$homedir/plsinoanswer4.csv > $homedir/plsinoanswer4.txt");
        // exec("cat $homedir/plsianswered4.txt", $plsianswered4, $retval);
        // foreach ($plsianswered4 as $key) {
        //     $bg = $key-1;
        // }
        // exec("cat $homedir/plsinoanswer4.txt", $plsinoanswer4, $retval);
        // foreach ($plsinoanswer4 as $key) {
        //     $dg = $key-1;
        // }
        // // exec("bg=$(($ag-1))");
        // // exec("dg=$(($cg-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/plsitotal.txt");
        // exec("echo \"$bg\" > $homedir/plsianswered4.txt");
        // exec("echo \"$dg\" > $homedir/plsinoanswer4.txt");
        // exec("echo \"disposition,value\" > $homedir/plsi4.csv");
        // exec("echo \"ANSWERED,$bg\" >> $homedir/plsi4.csv");
        // exec("echo \"NO ANSWER,$dg\" >> $homedir/plsi4.csv");



        // //607b.sh
        // exec("awk -F',' '$3~/607|1123|2201|2222|2213/' $homedir/600atrim2.csv > $homedir/600atrim3.csv");
        // exec("grep -E \"SIP\"  $homedir/600atrim3.csv > $homedir/600atrim.csv");
        // // exec("#");
        // $first = $DATE.'0000';
        // $end = $DATE.'0559';
        // exec("awk -v t=\"$first\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600atrim1.csv");
        // // exec("#filter 6-3");
        // $second = $DATE.'0500';
        // $end = $DATE.'1459';
        // exec("awk -v t=\"$second\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tsecond1.csv");
        // // exec("#filter 3-12");
        // $third = $DATE.'1500';
        // $end = $DATE.'2200';
        // exec("awk -v t=\"$third\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tthird1.csv");
        // // exec("#");
        // exec("cut -d, -f8 --complement $homedir/600atrim1.csv > $homedir/600tall.csv");
        // exec("cut -d, -f8 --complement $homedir/600tfirst1.csv > $homedir/600tfirst.csv");
        // exec("cut -d, -f8 --complement $homedir/600tsecond1.csv > $homedir/600tsecond.csv");
        // exec("cut -d, -f8 --complement $homedir/600tthird1.csv > $homedir/600tthird.csv");
        // // exec("#");
        // // exec("#grep -Ev "<[0-9]{4}>" $homedir/600tall2.csv > $homedir/600tall.csv");
        // // exec("#grep -Ev "<[0-9]{4}>" $homedir/600tfirst2.csv > $homedir/600tfirst.csv");
        // // exec("#grep -Ev "<[0-9]{4}>" $homedir/600tsecond2.csv > $homedir/600tsecond.csv");
        // // exec("#grep -Ev "<[0-9]{4}>" $homedir/600tthird2.csv > $homedir/600tthird.csv");
        // // exec("#");
        // // exec("#grep -E "<[0-9]{4}>" $homedir/600tall2.csv > $homedir/600tall4.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/admintotal1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/adminanswered1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/adminnoanswe1.csv");
        // exec("grep \"ANSWERED\" $homedir/600tfirst.csv >> $homedir/adminanswered1.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tfirst.csv >> $homedir/adminnoanswe1.csv");
        // exec("cat $homedir/600tfirst.csv >> $homedir/admintotal1.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/admintotal2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/adminanswered2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/adminnoanswe2.csv");
        // exec("grep \"ANSWERED\" $homedir/600tsecond.csv >> $homedir/adminanswered2.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tsecond.csv >> $homedir/adminnoanswe2.csv");
        // exec("cat $homedir/600tsecond.csv >> $homedir/admintotal2.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/admintotal3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/adminanswered3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/adminnoanswe3.csv");
        // exec("grep \"ANSWERED\" $homedir/600tthird.csv >> $homedir/adminanswered3.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tthird.csv >> $homedir/adminnoanswe3.csv");
        // exec("cat $homedir/600tthird.csv >> $homedir/admintotal3.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/admintotal.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/adminanswered.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/adminnoanswe.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall.csv >> $homedir/adminanswered.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall.csv >> $homedir/adminnoanswe.csv");
        // exec("cat $homedir/600tall.csv >> $homedir/admintotal.csv");
        // // exec("#");
        // // exec("#sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/admintotal4.csv");
        // // exec("#sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/adminanswered4.csv");
        // // exec("#sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/adminnoanswe4.csv");
        // // exec("#grep "ANSWERED" $homedir/600tall4.csv >> $homedir/adminanswered4.csv");
        // // exec("#grep "NO ANSWER" $homedir/600tall4.csv >> $homedir/adminnoanswe4.csv");
        // // exec("#cat $homedir/600tall4.csv >> $homedir/admintotal4.csv");
        // // exec("#");
        // // exec("#cat $homedir/600nnospace.csv >> $homedir/adminnoanswe.csv");
        // exec("cut -d, -f7 --complement $homedir/adminnoanswe.csv > $homedir/adminnoanswer.csv");
        // exec("cut -d, -f7 --complement $homedir/adminnoanswe1.csv > $homedir/adminnoanswer1.csv");
        // exec("cut -d, -f7 --complement $homedir/adminnoanswe2.csv > $homedir/adminnoanswer2.csv");
        // exec("cut -d, -f7 --complement $homedir/adminnoanswe3.csv > $homedir/adminnoanswer3.csv");
        // // exec("#cut -d, -f7 --complement $homedir/adminnoanswe4.csv > $homedir/adminnoanswer4.csv");
        // // exec("#cut -d, -f1-2,9 --complement $homedir/600at.csv > $homedir/adminanswered.csv");
        // exec("wc -l <$homedir/admintotal1.csv > $homedir/admintotal1.txt");
        // exec("cat $homedir/admintotal1.txt", $admintotal1, $retval);
        // foreach ($admintotal1 as $key) {
        //     $at1 = $key-1;
        // }
        // // exec("at1=$(($at-1))");
        // exec("echo \"$at1\" > $homedir/admintotal1.txt");
        // exec("wc -l <$homedir/adminanswered1.csv > $homedir/adminanswered1.txt");
        // exec("wc -l <$homedir/adminnoanswer1.csv > $homedir/adminnoanswer1.txt");
        // exec("cat $homedir/adminanswered1.txt", $adminanswered1, $retval);
        // foreach ($adminanswered1 as $key) {
        //     $ba = $key-1;
        // }
        // // exec("aa=`cat $homedir/adminanswered1.txt`");
        // exec("cat $homedir/adminnoanswer1.txt", $adminnoanswer1, $retval);
        // foreach ($adminnoanswer1 as $key) {
        //     $da = $key-1;
        // }
        // // exec("ca=`cat $homedir/adminnoanswer1.txt`");
        // // exec("ba=$(($aa-1))");
        // // exec("da=$(($ca-1))");
        // // exec("#ea=$(($ba+$da))");
        // // exec("#echo "$ea" > $homedir/admintotal1.txt");
        // exec("echo \"$ba\" > $homedir/adminanswered1.txt");
        // exec("echo \"$da\" > $homedir/adminnoanswer1.txt");
        // exec("echo \"disposition,value\" > $homedir/admin1.csv");
        // exec("echo \"ANSWERED,$ba\" >> $homedir/admin1.csv");
        // exec("echo \"NO ANSWER,$da\" >> $homedir/admin1.csv");
        // // exec("#");
        // exec("wc -l <$homedir/admintotal2.csv > $homedir/admintotal2.txt");
        // exec("cat $homedir/admintotal2.txt", $admintotal2, $retval);
        // foreach ($admintotal2 as $key) {
        //     $at22 = $key-1;
        // }
        // // exec("at2=`cat $homedir/admintotal2.txt`");
        // // exec("at22=$(($at2-1))");
        // exec("echo \"$at22\" > $homedir/admintotal2.txt");
        // exec("wc -l <$homedir/adminanswered2.csv > $homedir/adminanswered2.txt");
        // exec("wc -l <$homedir/adminnoanswer2.csv > $homedir/adminnoanswer2.txt");
        // exec("cat $homedir/adminanswered2.txt", $adminanswered2, $retval);
        // foreach ($adminanswered2 as $key) {
        //     $bb = $key-1;
        // }
        // // exec("ab=`cat $homedir/adminanswered2.txt`");
        // exec("cat $homedir/adminnoanswer2.txt", $adminnoanswer2, $retval);
        // foreach ($adminnoanswer2 as $key) {
        //     $db = $key-1;
        // }
        // // exec("cb=`cat $homedir/adminnoanswer2.txt`");
        // // exec("bb=$(($ab-1))");
        // // exec("db=$(($cb-1))");
        // // exec("#eb=$(($bb+$db))");
        // // exec("#echo "$eb" > $homedir/admintotal2.txt");
        // exec("echo \"$bb\" > $homedir/adminanswered2.txt");
        // exec("echo \"$db\" > $homedir/adminnoanswer2.txt");
        // exec("echo \"disposition,value\" > $homedir/admin2.csv");
        // exec("echo \"ANSWERED,$bb\" >> $homedir/admin2.csv");
        // exec("echo \"NO ANSWER,$db\" >> $homedir/admin2.csv");
        // // exec("#");
        // exec("wc -l <$homedir/admintotal3.csv > $homedir/admintotal3.txt");
        // exec("cat $homedir/admintotal3.txt", $admintotal3, $retval);
        // foreach ($admintotal3 as $key) {
        //     $at33 = $key-1;
        // }
        // // exec("at3=`cat $homedir/admintotal3.txt`");
        // // exec("at33=$(($at3-1))");
        // exec("echo \"$at33\" > $homedir/admintotal3.txt");
        // exec("wc -l <$homedir/adminanswered3.csv > $homedir/adminanswered3.txt");
        // exec("wc -l <$homedir/adminnoanswer3.csv > $homedir/adminnoanswer3.txt");
        // exec("cat $homedir/adminanswered3.txt", $adminanswered3, $retval);
        // foreach ($adminanswered3 as $key) {
        //     $bc = $key-1;
        // }
        // // exec("ac=`cat $homedir/adminanswered3.txt`");
        // exec("cat $homedir/adminnoanswer3.txt", $adminnoanswer3, $retval);
        // foreach ($adminnoanswer3 as $key) {
        //     $dc = $key-1;
        // }
        // // exec("cc=`cat $homedir/adminnoanswer3.txt`");
        // // exec("bc=$(($ac-1))");
        // // exec("dc=$(($cc-1))");
        // // exec("#ec=$(($bc+$dc))");
        // // exec("#echo "$ec" > $homedir/admintotal3.txt");
        // exec("echo \"$bc\" > $homedir/adminanswered3.txt");
        // exec("echo \"$dc\" > $homedir/adminnoanswer3.txt");
        // exec("echo \"disposition,value\" > $homedir/admin3.csv");
        // exec("echo \"ANSWERED,$bc\" >> $homedir/admin3.csv");
        // exec("echo \"NO ANSWER,$dc\" >> $homedir/admin3.csv");
        // // exec("#");
        // exec("wc -l <$homedir/admintotal.csv > $homedir/admintotal.txt");
        // exec("cat $homedir/admintotal.txt", $admintotal, $retval);
        // foreach ($admintotal as $key) {
        //     $at44 = $key-1;
        // }
        // // exec("at4=`cat $homedir/admintotal.txt`");
        // // exec("at44=$(($at4-1))");
        // exec("echo \"$at44\" > $homedir/admintotal.txt");
        // exec("wc -l <$homedir/adminanswered.csv > $homedir/adminanswered.txt");
        // exec("wc -l <$homedir/adminnoanswer.csv > $homedir/adminnoanswer.txt");
        // exec("cat $homedir/adminanswered.txt", $adminanswered, $retval);
        // foreach ($adminanswered as $key) {
        //     $b = $key-1;
        // }
        // // exec("a=`cat $homedir/adminanswered.txt`");
        // exec("cat $homedir/adminnoanswer.txt", $adminnoanswer, $retval);
        // foreach ($adminnoanswer as $key) {
        //     $d = $key-1;
        // }
        // // exec("c=`cat $homedir/adminnoanswer.txt`");
        // // exec("b=$(($a-1))");
        // // exec("d=$(($c-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/admintotal.txt");
        // exec("echo \"$b\" > $homedir/adminanswered.txt");
        // exec("echo \"$d\" > $homedir/adminnoanswer.txt");
        // exec("echo \"disposition,value\" > $homedir/admin.csv");
        // exec("echo \"ANSWERED,$b\" >> $homedir/admin.csv");
        // exec("echo \"NO ANSWER,$d\" >> $homedir/admin.csv");



        // //608b.sh
        // exec("awk -F',' '$3~/608|1130|1132|1133/' $homedir/600atrim2.csv > $homedir/600atrim3.csv");
        // exec("grep -E \"SIP\"  $homedir/600atrim3.csv > $homedir/600atrim.csv");
        // // exec("#filter 12-6");
        // $first = $DATE.'0000';
        // $end = $DATE.'0559';
        // exec("awk -v t=\"$first\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600atrim1.csv");
        // // exec("#filter 6-3");
        // $second = $DATE.'0600';
        // $end = $DATE.'1459';
        // exec("awk -v t=\"$second\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tsecond1.csv");
        // // exec("#filter 3-12");
        // $third = $DATE.'1500';
        // $end = $DATE.'2200';
        // exec("awk -v t=\"$third\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tthird1.csv");
        // // exec("#");
        // exec("cut -d, -f8 --complement $homedir/600atrim1.csv > $homedir/600tall2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tfirst1.csv > $homedir/600tfirst2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tsecond1.csv > $homedir/600tsecond2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tthird1.csv > $homedir/600tthird2.csv");
        // // exec("#");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\"  $homedir/600tall2.csv > $homedir/600tall.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tfirst2.csv > $homedir/600tfirst.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tsecond2.csv > $homedir/600tsecond.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tthird2.csv > $homedir/600tthird.csv");
        // // exec("#");
        // exec("grep -E \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tall2.csv > $homedir/600tall4.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsitotal1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsianswered1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/idsinoanswe1.csv");
        // exec("grep \"ANSWERED\" $homedir/600tfirst.csv >> $homedir/idsianswered1.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tfirst.csv >> $homedir/idsinoanswe1.csv");
        // exec("cat $homedir/600tfirst.csv >> $homedir/idsitotal1.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsitotal2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsianswered2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/idsinoanswe2.csv");
        // exec("grep \"ANSWERED\" $homedir/600tsecond.csv >> $homedir/idsianswered2.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tsecond.csv >> $homedir/idsinoanswe2.csv");
        // exec("cat $homedir/600tsecond.csv >> $homedir/idsitotal2.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsitotal3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsianswered3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/idsinoanswe3.csv");
        // exec("grep \"ANSWERED\" $homedir/600tthird.csv >> $homedir/idsianswered3.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tthird.csv >> $homedir/idsinoanswe3.csv");
        // exec("cat $homedir/600tthird.csv >> $homedir/idsitotal3.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsitotal.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsianswered.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/idsinoanswe.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall.csv >> $homedir/idsianswered.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall.csv >> $homedir/idsinoanswe.csv");
        // exec("cat $homedir/600tall.csv >> $homedir/idsitotal.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsitotal4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/idsianswered4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/idsinoanswe4.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall4.csv >> $homedir/idsianswered4.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall4.csv >> $homedir/idsinoanswe4.csv");
        // exec("cat $homedir/600tall4.csv >> $homedir/idsitotal4.csv");

        // // exec("#cat $homedir/600nnospace.csv >> $homedir/idsinoanswe.csv");
        // exec("cut -d, -f7 --complement $homedir/idsinoanswe.csv > $homedir/idsinoanswer.csv");
        // exec("cut -d, -f7 --complement $homedir/idsinoanswe1.csv > $homedir/idsinoanswer1.csv");
        // exec("cut -d, -f7 --complement $homedir/idsinoanswe2.csv > $homedir/idsinoanswer2.csv");
        // exec("cut -d, -f7 --complement $homedir/idsinoanswe3.csv > $homedir/idsinoanswer3.csv");
        // exec("cut -d, -f7 --complement $homedir/idsinoanswe4.csv > $homedir/idsinoanswer4.csv");
        // exec("#cut -d, -f1-2,9 --complement $homedir/600at.csv > $homedir/idsianswered.csv");
        // exec("wc -l <$homedir/idsitotal1.csv > $homedir/idsitotal1.txt");
        // exec("cat $homedir/idsitotal1.txt", $idsitotal1, $retval);
        // foreach ($idsitotal1 as $key) {
        //     $at1 = $key-1;
        // }
        // // exec("at=`cat $homedir/idsitotal1.txt`");
        // // exec("at1=$(($at-1))");
        // exec("echo \"$at1\" > $homedir/idsitotal1.txt");
        // exec("wc -l <$homedir/idsianswered1.csv > $homedir/idsianswered1.txt");
        // exec("wc -l <$homedir/idsinoanswer1.csv > $homedir/idsinoanswer1.txt");
        // exec("cat $homedir/idsianswered1.txt", $idsianswered1, $retval);
        // foreach ($idsianswered1 as $key) {
        //     $ba = $key-1;
        // }
        // // exec("aa=`cat $homedir/idsianswered1.txt`");
        // exec("cat $homedir/idsinoanswer1.txt", $idsinoanswer1, $retval);
        // foreach ($idsinoanswer1 as $key) {
        //     $da = $key-1;
        // }
        // // exec("ca=`cat $homedir/idsinoanswer1.txt`");
        // // exec("ba=$(($aa-1))");
        // // exec("da=$(($ca-1))");
        // // exec("#ea=$(($ba+$da))");
        // // exec("#echo "$ea" > $homedir/idsitotal1.txt");
        // exec("echo \"$ba\" > $homedir/idsianswered1.txt");
        // exec("echo \"$da\" > $homedir/idsinoanswer1.txt");
        // exec("echo \"disposition,value\" > $homedir/idsi1.csv");
        // exec("echo \"ANSWERED,$ba\" >> $homedir/idsi1.csv");
        // exec("echo \"NO ANSWER,$da\" >> $homedir/idsi1.csv");
        // // exec("#");
        // exec("wc -l <$homedir/idsitotal2.csv > $homedir/idsitotal2.txt");
        // exec("cat $homedir/idsitotal2.txt", $idsitotal2, $retval);
        // foreach ($idsitotal2 as $key) {
        //     $at22 = $key-1;
        // }
        // // exec("at2=`cat $homedir/idsitotal2.txt`");
        // // exec("at22=$(($at2-1))");
        // exec("echo \"$at22\" > $homedir/idsitotal2.txt");
        // exec("wc -l <$homedir/idsianswered2.csv > $homedir/idsianswered2.txt");
        // exec("wc -l <$homedir/idsinoanswer2.csv > $homedir/idsinoanswer2.txt");
        // exec("cat $homedir/idsianswered2.txt", $idsianswered2, $retval);
        // foreach ($idsianswered2 as $key) {
        //     $bb = $key-1;
        // }
        // // exec("ab=`cat $homedir/idsianswered2.txt`");
        // exec("cat $homedir/idsinoanswer2.txt", $idsinoanswer2, $retval);
        // foreach ($idsinoanswer2 as $key) {
        //     $db = $key-1;
        // }
        // // exec("cb=`cat $homedir/idsinoanswer2.txt`");
        // // exec("bb=$(($ab-1))");
        // // exec("db=$(($cb-1))");
        // // exec("#eb=$(($bb+$db))");
        // // exec("#echo "$eb" > $homedir/idsitotal2.txt");
        // exec("echo \"$bb\" > $homedir/idsianswered2.txt");
        // exec("echo \"$db\" > $homedir/idsinoanswer2.txt");
        // exec("echo \"disposition,value\" > $homedir/idsi2.csv");
        // exec("echo \"ANSWERED,$bb\" >> $homedir/idsi2.csv");
        // exec("echo \"NO ANSWER,$db\" >> $homedir/idsi2.csv");
        // // exec("#");
        // exec("wc -l <$homedir/idsitotal3.csv > $homedir/idsitotal3.txt");
        // exec("cat $homedir/idsitotal3.txt", $idsitotal3, $retval);
        // foreach ($idsitotal3 as $key) {
        //     $at33 = $key-1;
        // }
        // // exec("at3=`cat $homedir/idsitotal3.txt`");
        // // exec("at33=$(($at3-1))");
        // exec("echo \"$at33\" > $homedir/idsitotal3.txt");
        // exec("wc -l <$homedir/idsianswered3.csv > $homedir/idsianswered3.txt");
        // exec("wc -l <$homedir/idsinoanswer3.csv > $homedir/idsinoanswer3.txt");
        // exec("cat $homedir/idsianswered3.txt", $idsianswered3, $retval);
        // foreach ($idsianswered3 as $key) {
        //     $bc = $key-1;
        // }
        // // exec("ac=`cat $homedir/idsianswered3.txt`");
        // exec("cat $homedir/idsinoanswer3.txt", $idsinoanswer3, $retval);
        // foreach ($idsinoanswer3 as $key) {
        //     $dc = $key-1;
        // }
        // // exec("cc=`cat $homedir/idsinoanswer3.txt`");
        // // exec("bc=$(($ac-1))");
        // // exec("dc=$(($cc-1))");
        // // exec("#ec=$(($bc+$dc))");
        // // exec("#echo "$ec" > $homedir/idsitotal3.txt");
        // exec("echo \"$bc\" > $homedir/idsianswered3.txt");
        // exec("echo \"$dc\" > $homedir/idsinoanswer3.txt");
        // exec("echo \"disposition,value\" > $homedir/idsi3.csv");
        // exec("echo \"ANSWERED,$bc\" >> $homedir/idsi3.csv");
        // exec("echo \"NO ANSWER,$dc\" >> $homedir/idsi3.csv");
        // // exec("#");
        // exec("wc -l <$homedir/idsitotal.csv > $homedir/idsitotal.txt");
        // exec("cat $homedir/idsitotal.txt", $idsitotal, $retval);
        // foreach ($idsitotal as $key) {
        //     $at44 = $key-1;
        // }
        // // exec("at4=`cat $homedir/idsitotal.txt`");
        // // exec("at44=$(($at4-1))");
        // exec("echo \"$at44\" > $homedir/idsitotal.txt");
        // exec("wc -l <$homedir/idsianswered.csv > $homedir/idsianswered.txt");
        // exec("wc -l <$homedir/idsinoanswer.csv > $homedir/idsinoanswer.txt");
        // exec("cat $homedir/idsianswered.txt", $idsianswered, $retval);
        // foreach ($idsianswered as $key) {
        //     $b = $key-1;
        // }
        // // exec("a=`cat $homedir/idsianswered.txt`");
        // exec("cat $homedir/idsinoanswer.txt", $idsinoanswer, $retval);
        // foreach ($idsinoanswer as $key) {
        //     $d = $key-1;
        // }
        // // exec("c=`cat $homedir/idsinoanswer.txt`");
        // // exec("b=$(($a-1))");
        // // exec("d=$(($c-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/idsitotal.txt");
        // exec("echo \"$b\" > $homedir/idsianswered.txt");
        // exec("echo \"$d\" > $homedir/idsinoanswer.txt");
        // exec("echo \"disposition,value\" > $homedir/idsi.csv");
        // exec("echo \"ANSWERED,$b\" >> $homedir/idsi.csv");
        // exec("echo \"NO ANSWER,$d\" >> $homedir/idsi.csv");
        // // exec("#");
        // exec("wc -l <$homedir/idsitotal4.csv > $homedir/idsitotal4.txt");
        // exec("cat $homedir/idsitotal4.txt", $idsitotal4, $retval);
        // foreach ($idsitotal4 as $key) {
        //     $at55 = $key-1;
        // }
        // // exec("at5=`cat $homedir/idsitotal4.txt`");
        // // exec("at55=$(($at5-1))");
        // exec("echo \"$at55\" > $homedir/idsitotal4.txt");
        // exec("wc -l <$homedir/idsianswered4.csv > $homedir/idsianswered4.txt");
        // exec("wc -l <$homedir/idsinoanswer4.csv > $homedir/idsinoanswer4.txt");
        // exec("cat $homedir/idsianswered4.txt", $idsianswered4, $retval);
        // foreach ($idsianswered4 as $key) {
        //     $bg = $key-1;
        // }
        // exec("ag=`cat $homedir/idsianswered4.txt`");
        // exec("cat $homedir/idsinoanswer4.txt", $idsinoanswer4, $retval);
        // foreach ($idsinoanswer4 as $key) {
        //     $bg = $key-1;
        // }
        // exec("cg=`cat $homedir/idsinoanswer4.txt`");
        // // exec("bg=$(($ag-1))");
        // // exec("dg=$(($cg-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/idsitotal.txt");
        // exec("echo \"$bg\" > $homedir/idsianswered4.txt");
        // exec("echo \"$dg\" > $homedir/idsinoanswer4.txt");
        // exec("echo \"disposition,value\" > $homedir/idsi4.csv");
        // exec("echo \"ANSWERED,$bg\" >> $homedir/idsi4.csv");
        // exec("echo \"NO ANSWER,$dg\" >> $homedir/idsi4.csv");



        // //614b.sh

        // exec("awk -F',' '$3~/614|5521|5522|5520/' $homedir/600atrim2.csv > $homedir/600atrim3.csv");
        // exec("grep -E \"SIP\"  $homedir/600atrim3.csv > $homedir/600atrim.csv");
        // // exec("#filter 12-6");
        // $first = $DATE.'0000';
        // $end = $DATE.'0559';
        // exec("awk -v t=\"$first\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600atrim1.csv");
        // // exec("#filter 6-3");
        // $second = $DATE.'0500';
        // $end = $DATE.'1459';
        // exec("awk -v t=\"$second\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tsecond1.csv");
        // // exec("#filter 3-12");
        // $third = $DATE.'1500';
        // $end = $DATE.'2200';
        // exec("awk -v t=\"$third\" -v ts=\"$end\" -F \",\" '{ if ( $8 >= t && $8 <=ts ) print $0 }' $homedir/600atrim.csv > $homedir/600tthird1.csv");
        // // exec("#");
        // exec("cut -d, -f8 --complement $homedir/600atrim1.csv > $homedir/600tall2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tfirst1.csv > $homedir/600tfirst2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tsecond1.csv > $homedir/600tsecond2.csv");
        // exec("cut -d, -f8 --complement $homedir/600tthird1.csv > $homedir/600tthird2.csv");
        // exec("#");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\"  $homedir/600tall2.csv > $homedir/600tall.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tfirst2.csv > $homedir/600tfirst.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tsecond2.csv > $homedir/600tsecond.csv");
        // exec("grep -Ev \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tthird2.csv > $homedir/600tthird.csv");
        // // exec("#");
        // exec("grep -E \"<[0-9]{4}>|<[0-9]{6}>\" $homedir/600tall2.csv > $homedir/600tall4.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsofttotal1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsoftanswered1.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/apsoftnoanswe1.csv");
        // exec("grep \"ANSWERED\" $homedir/600tfirst.csv >> $homedir/apsoftanswered1.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tfirst.csv >> $homedir/apsoftnoanswe1.csv");
        // exec("cat $homedir/600tfirst.csv >> $homedir/apsofttotal1.csv");
        // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsofttotal2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsoftanswered2.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/apsoftnoanswe2.csv");
        // exec("grep \"ANSWERED\" $homedir/600tsecond.csv >> $homedir/apsoftanswered2.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tsecond.csv >> $homedir/apsoftnoanswe2.csv");
        // exec("cat $homedir/600tsecond.csv >> $homedir/apsofttotal2.csv");
        // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsofttotal3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsoftanswered3.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/apsoftnoanswe3.csv");
        // exec("grep \"ANSWERED\" $homedir/600tthird.csv >> $homedir/apsoftanswered3.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tthird.csv >> $homedir/apsoftnoanswe3.csv");
        // exec("cat $homedir/600tthird.csv >> $homedir/apsofttotal3.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsofttotal.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsoftanswered.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/apsoftnoanswe.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall.csv >> $homedir/apsoftanswered.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall.csv >> $homedir/apsoftnoanswe.csv");
        // exec("cat $homedir/600tall.csv >> $homedir/apsofttotal.csv");
        // // exec("#");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsofttotal4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/aheader.csv > $homedir/apsoftanswered4.csv");
        // exec("sed 's/$/,Time Before Answered/' /var/www/html/cdm/html/nheader.csv > $homedir/apsoftnoanswe4.csv");
        // exec("grep \"ANSWERED\" $homedir/600tall4.csv >> $homedir/apsoftanswered4.csv");
        // exec("grep \"NO ANSWER\" $homedir/600tall4.csv >> $homedir/apsoftnoanswe4.csv");
        // exec("cat $homedir/600tall4.csv >> $homedir/apsofttotal4.csv");
        // exec("#cat $homedir/600nnospace.csv >> $homedir/apsoftnoanswe.csv");
        // exec("cut -d, -f7 --complement $homedir/apsoftnoanswe.csv > $homedir/apsoftnoanswer.csv");
        // exec("cut -d, -f7 --complement $homedir/apsoftnoanswe1.csv > $homedir/apsoftnoanswer1.csv");
        // exec("cut -d, -f7 --complement $homedir/apsoftnoanswe2.csv > $homedir/apsoftnoanswer2.csv");
        // exec("cut -d, -f7 --complement $homedir/apsoftnoanswe3.csv > $homedir/apsoftnoanswer3.csv");
        // exec("cut -d, -f7 --complement $homedir/apsoftnoanswe4.csv > $homedir/apsoftnoanswer4.csv");
        // exec("#cut -d, -f1-2,9 --complement $homedir/600at.csv > $homedir/apsoftanswered.csv");
        // exec("wc -l <$homedir/apsofttotal1.csv > $homedir/apsofttotal1.txt");
        // // exec("at=`cat $homedir/apsofttotal1.txt`");
        // exec("cat $homedir/apsofttotal1.txt", $apsofttotal1, $retval);
        // foreach ($apsofttotal1 as $key) {
        //     $at1 = $key-1;
        // }
        // // exec("at1=$(($at-1))");
        // exec("echo \"$at1\" > $homedir/apsofttotal1.txt");
        // exec("wc -l <$homedir/apsoftanswered1.csv > $homedir/apsoftanswered1.txt");
        // exec("wc -l <$homedir/apsoftnoanswer1.csv > $homedir/apsoftnoanswer1.txt");
        // // exec("aa=`cat $homedir/apsoftanswered1.txt`");
        // exec("cat $homedir/apsoftanswered1.txt", $apsoftanswered1, $retval);
        // foreach ($apsoftanswered1 as $key) {
        //     $ba = $key-1;
        // }
        // // exec("ca=`cat $homedir/apsoftnoanswer1.txt`");
        // exec("cat $homedir/apsoftnoanswer1.txt", $apsoftnoanswer1, $retval);
        // foreach ($apsoftnoanswer1 as $key) {
        //     $da = $key-1;
        // }
        // // exec("ba=$(($aa-1))");
        // // exec("da=$(($ca-1))");
        // // exec("#ea=$(($ba+$da))");
        // // exec("#echo "$ea" > $homedir/apsofttotal1.txt");
        // exec("echo \"$ba\" > $homedir/apsoftanswered1.txt");
        // exec("echo \"$da\" > $homedir/apsoftnoanswer1.txt");
        // exec("echo \"disposition,value\" > $homedir/apsoft1.csv");
        // exec("echo \"ANSWERED,$ba\" >> $homedir/apsoft1.csv");
        // exec("echo \"NO ANSWER,$da\" >> $homedir/apsoft1.csv");
        // // exec("#");
        // exec("wc -l <$homedir/apsofttotal2.csv > $homedir/apsofttotal2.txt");
        // // exec("at2=`cat $homedir/apsofttotal2.txt`");
        // exec("cat $homedir/apsofttotal2.txt", $apsofttotal2, $retval);
        // foreach ($apsofttotal2 as $key) {
        //     $at22 = $key-1;
        // }
        // // exec("at22=$(($at2-1))");
        // exec("echo \"$at22\" > $homedir/apsofttotal2.txt");
        // exec("wc -l <$homedir/apsoftanswered2.csv > $homedir/apsoftanswered2.txt");
        // exec("wc -l <$homedir/apsoftnoanswer2.csv > $homedir/apsoftnoanswer2.txt");
        // // exec("ab=`cat $homedir/apsoftanswered2.txt`");
        // exec("cat $homedir/apsoftanswered2.txt", $apsoftanswered2, $retval);
        // foreach ($apsoftanswered2 as $key) {
        //     $bb = $key-1;
        // }
        // // exec("cb=`cat $homedir/apsoftnoanswer2.txt`");
        // exec("cat $homedir/apsoftnoanswer2.txt", $apsoftnoanswer2, $retval);
        // foreach ($apsoftnoanswer2 as $key) {
        //     $db = $key-1;
        // }
        // // exec("bb=$(($ab-1))");
        // // exec("db=$(($cb-1))");
        // // exec("#eb=$(($bb+$db))");
        // // exec("#echo "$eb" > $homedir/apsofttotal2.txt");
        // exec("echo \"$bb\" > $homedir/apsoftanswered2.txt");
        // exec("echo \"$db\" > $homedir/apsoftnoanswer2.txt");
        // exec("echo \"disposition,value\" > $homedir/apsoft2.csv");
        // exec("echo \"ANSWERED,$bb\" >> $homedir/apsoft2.csv");
        // exec("echo \"NO ANSWER,$db\" >> $homedir/apsoft2.csv");
        // // exec("#");
        // exec("wc -l <$homedir/apsofttotal3.csv > $homedir/apsofttotal3.txt");
        // // exec("at3=`cat $homedir/apsofttotal3.txt`");
        // exec("cat $homedir/apsofttotal3.txt", $apsofttotal3, $retval);
        // foreach ($apsofttotal3 as $key) {
        //     $at33 = $key-1;
        // }
        // // exec("at33=$(($at3-1))");
        // exec("echo \"$at33\" > $homedir/apsofttotal3.txt");
        // exec("wc -l <$homedir/apsoftanswered3.csv > $homedir/apsoftanswered3.txt");
        // exec("wc -l <$homedir/apsoftnoanswer3.csv > $homedir/apsoftnoanswer3.txt");
        // // exec("ac=`cat $homedir/apsoftanswered3.txt`");
        // exec("cat $homedir/apsoftanswered3.txt", $apsoftanswered3, $retval);
        // foreach ($apsoftanswered3 as $key) {
        //     $bc = $key-1;
        // }
        // // exec("cc=`cat $homedir/apsoftnoanswer3.txt`");
        // exec("cat $homedir/apsoftnoanswer3.txt", $apsoftnoanswer3, $retval);
        // foreach ($apsoftnoanswer3 as $key) {
        //     $dc = $key-1;
        // }
        // // exec("bc=$(($ac-1))");
        // // exec("dc=$(($cc-1))");
        // // exec("#ec=$(($bc+$dc))");
        // // exec("#echo "$ec" > $homedir/apsofttotal3.txt");
        // exec("echo \"$bc\" > $homedir/apsoftanswered3.txt");
        // exec("echo \"$dc\" > $homedir/apsoftnoanswer3.txt");
        // exec("echo \"disposition,value\" > $homedir/apsoft3.csv");
        // exec("echo \"ANSWERED,$bc\" >> $homedir/apsoft3.csv");
        // exec("echo \"NO ANSWER,$dc\" >> $homedir/apsoft3.csv");
        // // exec("#");
        // exec("wc -l <$homedir/apsofttotal.csv > $homedir/apsofttotal.txt");
        // // exec("at4=`cat $homedir/apsofttotal.txt`");
        // exec("cat $homedir/apsofttotal.txt", $apsofttotal, $retval);
        // foreach ($apsofttotal as $key) {
        //     $at44 = $key-1;
        // }
        // // exec("at44=$(($at4-1))");
        // exec("echo \"$at44\" > $homedir/apsofttotal.txt");
        // exec("wc -l <$homedir/apsoftanswered.csv > $homedir/apsoftanswered.txt");
        // exec("wc -l <$homedir/apsoftnoanswer.csv > $homedir/apsoftnoanswer.txt");
        // // exec("a=`cat $homedir/apsoftanswered.txt`");
        // exec("cat $homedir/apsoftanswered.txt", $apsoftanswered, $retval);
        // foreach ($apsoftanswered as $key) {
        //     $b = $key-1;
        // }
        // // exec("c=`cat $homedir/apsoftnoanswer.txt`");
        // exec("cat $homedir/apsoftnoanswer.txt", $apsoftnoanswer, $retval);
        // foreach ($apsoftnoanswer as $key) {
        //     $d = $key-1;
        // }
        // // exec("b=$(($a-1))");
        // // exec("d=$(($c-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/apsofttotal.txt");
        // exec("echo \"$b\" > $homedir/apsoftanswered.txt");
        // exec("echo \"$d\" > $homedir/apsoftnoanswer.txt");
        // exec("echo \"disposition,value\" > $homedir/apsoft.csv");
        // exec("echo \"ANSWERED,$b\" >> $homedir/apsoft.csv");
        // exec("echo \"NO ANSWER,$d\" >> $homedir/apsoft.csv");
        // // exec("#");
        // exec("wc -l <$homedir/apsofttotal4.csv > $homedir/apsofttotal4.txt");
        // // exec("at5=`cat $homedir/apsofttotal4.txt`");
        // exec("cat $homedir/apsofttotal4.txt", $apsofttotal4, $retval);
        // foreach ($apsofttotal4 as $key) {
        //     $at55 = $key-1;
        // }
        // // exec("at55=$(($at5-1))");
        // exec("echo \"$at55\" > $homedir/apsofttotal4.txt");
        // exec("wc -l <$homedir/apsoftanswered4.csv > $homedir/apsoftanswered4.txt");
        // exec("wc -l <$homedir/apsoftnoanswer4.csv > $homedir/apsoftnoanswer4.txt");
        // // exec("ag=`cat $homedir/apsoftanswered4.txt`");
        // exec("cat $homedir/apsoftanswered4.txt", $apsoftanswered4, $retval);
        // foreach ($apsoftanswered4 as $key) {
        //     $bg = $key-1;
        // }
        // // exec("cg=`cat $homedir/apsoftnoanswer4.txt`");
        // exec("cat $homedir/apsoftnoanswer4.txt", $apsoftnoanswer4, $retval);
        // foreach ($apsoftnoanswer4 as $key) {
        //     $dg = $key-1;
        // }
        // // exec("bg=$(($ag-1))");
        // // exec("dg=$(($cg-1))");
        // // exec("#e=$(($b+$d))");
        // // exec("#echo "$e" > $homedir/apsofttotal.txt");
        // exec("echo \"$bg\" > $homedir/apsoftanswered4.txt");
        // exec("echo \"$dg\" > $homedir/apsoftnoanswer4.txt");
        // exec("echo \"disposition,value\" > $homedir/apsoft4.csv");
        // exec("echo \"ANSWERED,$bg\" >> $homedir/apsoft4.csv");
        // exec("echo \"NO ANSWER,$dg\" >> $homedir/apsoft4.csv");
        // // exec("done");
        // exec("rm -f $homedir/600aremoveqoute.csv");
        // exec("rm -f $homedir/600tsecond1.csv");
        // exec("rm -f $homedir/600tthird1.csv");
        // exec("rm -f $homedir/600a1trim.csv");
        // exec("rm -f $homedir/600atrim.csv");
        // exec("rm -f $homedir/600atrim1.csv");
        // exec("rm -f $homedir/600anospace4.csv");
        // exec("rm -f $homedir/600anospace3.csv");
        // exec("rm -f $homedir/600anospace5.csv");
        // exec("rm -f $homedir/600anospace2.csv");
        // exec("rm -f $homedir/600anospace.csv");
        // exec("rm -f $homedir/600aready1.csv");
        // exec("rm -f $homedir/600aready.csv");
        // exec("rm -f $homedir/600adstchannel.csv");
        // exec("rm -f $homedir/600adisposition.csv");
        // exec("rm -f $homedir/600adstchannel1.csv");
        // exec("rm -f $homedir/600adisposition1.csv");
        // exec("rm -f $homedir/600aduration.csv");
        // exec("rm -f $homedir/600aduration1.csv");
        // exec("rm -f $homedir/600a2.csv");
        // exec("rm -f $homedir/600a11.csv");
        // exec("rm -f $homedir/600a12.csv");
        // exec("rm -f $homedir/600a1.csv");
        // exec("rm -f $homedir/600acalldate.csv");
        // exec("rm -f $homedir/600acalldate1.csv");
        // exec("rm -f $homedir/600afrom.csv");
        // exec("rm -f $homedir/600afrom1.csv");
        // exec("rm -f $homedir/600ato.csv");
        // exec("rm -f $homedir/600ato1.csv");
        // exec("rm -f $homedir/600ada1te1.csv");
        // exec("rm -f $homedir/600aruby.csv");
        // exec("rm -f $homedir/600ar1uby.csv");
        // exec("rm -f $homedir/600are1move.csv");
        // exec("rm -f $homedir/600a600.csv");
        // exec("rm -f $homedir/600ar2uby.csv");
        // exec("rm -f $homedir/600are2move.csv");
        // exec("rm -f $homedir/600tall.csv");
        // exec("rm -f $homedir/600tfirst.csv");
        // exec("rm -f $homedir/600tthird.csv");
        // exec("rm -f $homedir/600tsecond.csv");
        // exec("rm -f $homedir/600arubys.csv");
    }
}
