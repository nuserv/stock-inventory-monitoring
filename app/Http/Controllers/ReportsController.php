<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\MyChart;
use App\Charts\HighChart;
use App\ServiceOut;
use App\Branch;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $southarea = Branch::select('id')->where('area_id', 3)->get()->pluck('id');
        //dd($southarea);
        $south = ServiceOut::query()->wherein('branch_id', $southarea)->get()->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $southcount = [];
        $southArr = [];
        foreach ($south as $key => $value) {
            $southcount[(int)$key] = count($value);
        }
        
        for($i = 1; $i <= 12; $i++){
            if(!empty($southcount[$i])){
                array_push($southArr, $southcount[$i]);
            }else{
                array_push($southArr, 0);
            }
        }
        //dd($south);
        $mmarea = Branch::select('id')->where('area_id', 1)->get()->pluck('id');

        $mm = ServiceOut::query()->wherein('branch_id', $mmarea)->get()->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $mmcount = [];
        $mmArr = [];
        foreach ($mm as $key => $value) {
            $mmcount[(int)$key] = count($value);
        }
        for($i = 1; $i <= 12; $i++){
            if(!empty($mmcount[$i])){
                array_push($mmArr, $mmcount[$i]);
            }else{
                array_push($mmArr, 0);
            }
        }

        //north luzon
        $northarea = Branch::select('id')->where('area_id', 2)->get()->pluck('id');

        $north = ServiceOut::query()->wherein('branch_id', $northarea)->get()->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $northcount = [];
        $northArr = [];
        foreach ($north as $key => $value) {
            $northcount[(int)$key] = count($value);
        }
        for($i = 1; $i <= 12; $i++){
            if(!empty($northcount[$i])){
                array_push($northArr, $northcount[$i]);
            }else{
                array_push($northArr, 0);
            }
        }

        //visayas

        $visayasarea = Branch::select('id')->where('area_id', 4)->get()->pluck('id');

        $visayas = ServiceOut::query()->wherein('branch_id', $visayasarea)->get()->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $visayascount = [];
        $visayasArr = [];
        foreach ($visayas as $key => $value) {
            $visayascount[(int)$key] = count($value);
        }
        for($i = 1; $i <= 12; $i++){
            if(!empty($visayascount[$i])){
                array_push($visayasArr, $visayascount[$i]);
            }else{
                array_push($visayasArr, 0);
            }
        }

        //mindanao
        $mindanaoarea = Branch::select('id')->where('area_id', 5)->get()->pluck('id');

        $mindanao = ServiceOut::query()->wherein('branch_id', $mindanaoarea)->get()->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $mindanaocount = [];
        $mindanaoArr = [];
        foreach ($mindanao as $key => $value) {
            $mindanaocount[(int)$key] = count($value);
        }
        for($i = 1; $i <= 12; $i++){
            if(!empty($mindanaocount[$i])){
                array_push($mindanaoArr, $mindanaocount[$i]);
            }else{
                array_push($mindanaoArr, 0);
            }
        }
        //dd($southArr);
        $chartoption = [
            'responsive' => true,
            'legend' => ['display' => false],
            'tooltips' => ['enabled'=>true],
            //'aspectRatio' => 5,
            'scales' => [
                'yAxes'=> [[
                            'display'=>true,
                            'ticks'=> ['beginAtZero'=> true],
                            'gridLines'=> ['display'=> true],
                          ]],
                'xAxes'=> [[
                            'categoryPercentage'=> 0.8,
                            'barThickness' => 10,
                            'barPercentage' => 1,
                            'ticks' => ['beginAtZero' => true],
                            'gridLines' => ['display' => true],
                            'paddingLeft'=> 20,
                            //'stacked'=> true,
                          ]],
                //'width' => 100,
                //'height' => 200,
            ],
            'plugins' => '{datalabels: {color: \'red\'}}',];
        $chart = new MyChart;
        $chart->title('SERVICE OUT MONTHLY REPORT METRO MANILA');
        //$metro->size(['width' => 400, 'height' => 200]);
        $chart->labels(['JAN', 'FEB', 'MAR', 'APRIL','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']);
        $chart->dataset('Metro Manila', 'bar', $mmArr)->color('#0000ff')->backgroundcolor('#0000ff');
        $chart->dataset('North Luzon', 'bar', $northArr)->color('#010f00')->backgroundcolor('#010f00');
        $chart->dataset('South Luzon', 'bar', $southArr)->color('#00ff00')->backgroundcolor('#00ff00');
        $chart->dataset('Visayas', 'bar', $visayasArr)->color('#00f0ff')->backgroundcolor('#00f0ff');
        $chart->dataset('Mindanao', 'bar', $mindanaoArr)->color('#11ff88')->backgroundcolor('#11ff88');
        $chart->options($chartoption);
        /*$sl = new MyChart;
        $sl->title('SERVICE OUT MONTHLY REPORT SOUTH LUZON');
        $sl->labels(['JAN', 'FEB', 'MAR', 'APRIL','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']);
        $sl->dataset('South Luzon', 'bar', $southArr)->color('#00ff00')->backgroundcolor('#00ff00');
        $sl->options($chartoption);
        $nl = new MyChart;
        $nl->title('SERVICE OUT MONTHLY REPORT NORTH LUZON');
        $nl->labels(['JAN', 'FEB', 'MAR', 'APRIL','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']);
        $nl->dataset('North Luzon', 'bar', $northArr)->color('#010f00')->backgroundcolor('#010f00');
        $nl->options($chartoption);
        $vis = new MyChart;
        $vis->title('SERVICE OUT MONTHLY REPORT VISAYAS');
        $vis->labels(['JAN', 'FEB', 'MAR', 'APRIL','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']);
        $vis->dataset('Visayas', 'bar', $visayasArr)->color('#00f0ff')->backgroundcolor('#00f0ff');
        $vis->options($chartoption);
        $min = new MyChart;
        $min->title('SERVICE OUT MONTHLY REPORT MINDANAO');
        $min->labels(['JAN', 'FEB', 'MAR', 'APRIL','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']);
        $min->dataset('Mindanao', 'bar', $mindanaoArr)->color('#11ff88')->backgroundcolor('#11ff88');
        $min->options($chartoption);
        /*$chart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled'=>true],
            //'aspectRatio' => 5,
            'scales' => [
                'yAxes'=> [[
                            'display'=>true,
                            'ticks'=> ['beginAtZero'=> true],
                            'gridLines'=> ['display'=> true],
                          ]],
                'xAxes'=> [[
                            'categoryPercentage'=> 0.8,
                            'barThickness' => 10,
                            'barPercentage' => 1,
                            'ticks' => ['beginAtZero' => true],
                            'gridLines' => ['display' => true],
                            'paddingLeft'=> 20,
                            //'stacked'=> true,
                          ]],
            ],
            'plugins' => '{datalabels: {color: \'red\'}}',
        ]);*/

        $title = "Reports";
        return view('pages.reports.reports', ['chart' => $chart, 'title'=> $title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
