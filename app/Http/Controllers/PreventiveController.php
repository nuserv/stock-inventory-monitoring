<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PmSchedExport;
use App\CustomerBranch;
use Carbon\Carbon;
use App\PmSched;
use App\Fsr;
use App\Area;
use App\Branch;
use App\PmBranches;
use App\User;
use App\Pm;
use DB;

class PreventiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function ExportData(Request $request) 
    {
        $branch = Branch::where('id', $request->branch)->first();
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
        if (auth()->user()->hasanyrole('Manager', 'Editor')) {
            return Excel::download(new PmSchedExport($request->year, $request->from, $request->to, $branch, $request->branch), strtoupper($branch->branch).' PM REPORT '.$months[$request->from-1].'-'.$months[$request->to-1].' '.$request->year.'.xlsx');
        }else{
            return Excel::download(new PmSchedExport($request->year, $request->from, $request->to, '', ''), strtoupper(auth()->user()->branch->branch).' PM REPORT '.$months[$request->from-1].'-'.$months[$request->to-1].' '.$request->year.'.xlsx');
        }
    }

    public function ReportData(Request $request) 
    {
        $branch = Branch::where('id', $request->branch)->first();
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
        if (auth()->user()->hasanyrole('Manager', 'Editor')) {
            return Excel::download(new PmSchedExport($request->year, $request->from, $request->to, $branch, $request->branch), strtoupper($branch->branch).' PM REPORT '.$months[$request->from-1].'-'.$months[$request->to-1].' '.$request->year.'.xlsx');
        }else{
            return Excel::download(new PmSchedExport($request->year, $request->from, $request->to, '', ''), strtoupper(auth()->user()->branch->branch).' PM REPORT '.$months[$request->from-1].'-'.$months[$request->to-1].' '.$request->year.'.xlsx');
        }
    }

    public function index()
    {
        $areas = Area::all();
        return view('pages.schedule', compact('areas'));
    }

    public function checkfsr(Request $request)
    {
        $fsr = PmSched::where('fsrno', $request->fsrno)->first();
        if ($fsr) {
            return response()->json('meron');
        }else{
            return response()->json('wala');
        }
        
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
        $date = explode('/', $request->schedule);
        $customer = CustomerBranch::where('customer_branch', $request->customer)->first();
        $save = PmSched::create([
            'branch_id' => auth()->user()->branch->id,
            'user_id' => auth()->user()->id,
            'customer_id' => $customer->id,
            'schedule' => $date[2].'/'.$date[0].'/'.$date[1],
            'fsrno' => $request->fsrno,
            'Status' => "Completed"
        ]);
        $code = $customer->code*1;
        if ($save) {
            $pmbranch = PmBranches::where('customer_branches_code', $code)->update(['quarter' => Carbon::parse($save->schedule)->quarter]);
        }
        return response()->json($pmbranch);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getfsr(Request $request)
    {
        $reqdate = explode("/",$request->date);
        $fsr = Fsr::query()
            ->select('fsr_num')
            ->join('pm_branches', DB::raw('(customer_branches_code*1)'), DB::raw('(custbrch*1)'))
            ->where(DB::raw('(custbrch*1)'), $request->branchCode)
            ->whereDate('fsr.txndate', '>=', $reqdate[2].'-'.$reqdate[0].'-'.$reqdate[1])
            ->get();
        return response()->json($fsr);

    }
    public function show()
    {
        if (auth()->user()->hasanyrole('Manager', 'Editor')) {
            if (Carbon::now() <= Carbon::now()->firstOfQuarter()->add(7, 'day')) {
                $pmbranches = PmBranches::query()
                ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'branch', 'area', 'customer_branches.id as customer_id')
                ->join('branches', 'branches.id', 'branch_id')
                ->join('areas', 'areas.id', 'area_id')
                ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                ->where('customer_id', '1')
                ->where('quarter', '!=', Carbon::now()->subquarter(2)->quarter)
                ->get();
            }else{
                $pmbranches = PmBranches::query()
                    ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'branch', 'area', 'customer_branches.id as customer_id')
                    ->join('branches', 'branches.id', 'branch_id')
                    ->join('areas', 'areas.id', 'area_id')
                    ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                    ->where('customer_id', '1')
                    ->where('quarter', '!=', Carbon::now()->quarter)
                    ->get();
            }
        }else{
            if (auth()->user()->id == 142) {
                if (Carbon::now() <= Carbon::now()->firstOfQuarter()->add(7, 'day')) {
                    $pmbranches = PmBranches::query()
                        ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'customer_branches.id as customer_id')
                        ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                        ->join('branches', 'branches.id', 'branch_id')
                        ->where('customer_id', '1')
                        ->where('quarter', '!=', Carbon::now()->subquarter(2)->quarter)
                        ->where('area_id', 5)
                        ->get();
                }else{
                    $pmbranches = PmBranches::query()
                        ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'customer_branches.id as customer_id')
                        ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                        ->join('branches', 'branches.id', 'branch_id')
                        ->where('customer_id', '1')
                        ->where('quarter', '!=', Carbon::now()->quarter)
                        ->where('area_id', 5)
                        ->get();
                }
            }else{
                if (Carbon::now() <= Carbon::now()->firstOfQuarter()->add(7, 'day')) {
                    $pmbranches = PmBranches::query()
                        ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'customer_branches.id as customer_id')
                        ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                        ->where('customer_id', '1')
                        ->where('quarter', '!=', Carbon::now()->subquarter(2)->quarter)
                        ->where('branch_id', auth()->user()->branch->id)
                        ->get();
                }else{
                    $pmbranches = PmBranches::query()
                        ->select('customer_branch as client', 'pm_branches.customer_branches_code', 'customer_branches.id as customer_id')
                        ->join('customer_branches', DB::raw('(code*1)'),DB::raw('(customer_branches_code*1)'))
                        ->where('customer_id', '1')
                        ->where('quarter', '!=', Carbon::now()->quarter)
                        ->where('branch_id', auth()->user()->branch->id)
                        ->get();
                }
            }
        }
        
        return DataTables::of($pmbranches)
        ->addColumn('lastpm', function (PmBranches $sched){
            $code = PmSched::where('customer_id', $sched->customer_id)->orderBy('id', 'desc')->first();
            if ($code) {
                return Carbon::parse($code->schedule)->formatLocalized('%B %d, %Y');
            }
            return '';
        })
        
        ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $areas = Area::all();
        return view('pages.pmdone', compact('areas'));
    }

    public function getbranch(Request $request)
    {
        $branches = Branch::where('status', 1)->where('area_id', $request->areaid)->get();
        return response()->json($branches);
    }
    
    public function getpm(Request $request)
    {
        $month = $request->month;
        if ($request->month < 10) {
            $month = '0'.$request->month;
        }
        $customer = PmSched::query()
            ->select('customers.id','customer')
            ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
            ->join('customers', 'customers.id', 'customer_branches.customer_id')
            ->whereYear('pm_sched.updated_at', $request->year)
            ->whereMonth('pm_sched.updated_at', $month)
            ->where('branch_id', auth()->user()->branch->id)
            ->groupBy('customer')
            ->get();
        return response()->json($customer);
    }
    public function genpm(Request $request)
    {
        $month = $request->month;
        if ($request->month < 10) {
            $month = '0'.$request->month;
        }
        $sched = PmSched::select(
                'pm_sched.*',
                'code',
                'customer_branches.customer_branch')
            ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
            ->where('customer_branches.customer_id', $request->customer_id)
            ->whereYear('pm_sched.updated_at', $request->year)
            ->whereMonth('pm_sched.updated_at', $month)
            ->where('branch_id', auth()->user()->branch->id)
            ->where('pm_sched.Status', 'Completed')
            ->get();
        return DataTables::of($sched)
        ->addColumn('date', function (PmSched $sched){
            return Carbon::parse($sched->schedule)->formatLocalized('%B %d, %Y');
            // return Carbon::parse($sched->updated_at->toFormattedDateString().' '.$sched->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('schedule', function (PmSched $sched){
            return Carbon::parse($sched->schedule)->formatLocalized('%B %d, %Y');
        })
        ->addColumn('user', function (PmSched $sched){
            $user = User::where('id', $sched->user_id)->first()->name.' '.User::where('id', $sched->user_id)->first()->lastname;
            return $user;
        })
        ->addColumn('client', function (PmSched $sched){
            $client = CustomerBranch::where('customer_branches.id', $sched->customer_id)
                ->join('customers', 'customers.id', 'customer_id')
                ->first()->customer;
            return $client;
        })
        ->addColumn('branch', function (PmSched $sched){
            $branch = CustomerBranch::where('id', $sched->customer_id)->first()->customer_branch;
            return $branch;
        })

        ->make(true);
    }
    public function data()
    {
        $sched = PmSched::select(
                'pm_sched.*',
                'code',
                'customer_branches.customer_branch')
            ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('pm_sched.Status', 'Completed')
            ->get();
        if (auth()->user()->hasanyrole('Manager', 'Editor')) {
            $sched = PmSched::select(
                'pm_sched.*',
                'code',
                'customer_branches.customer_branch')
            ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
            ->where('pm_sched.Status', 'Completed')
            ->get();
        }else{
            $sched = PmSched::select(
                'pm_sched.*',
                'code',
                'customer_branches.customer_branch')
            ->join('customer_branches', 'customer_branches.id', 'pm_sched.customer_id')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('pm_sched.Status', 'Completed')
            ->get();
        }
        return DataTables::of($sched)
        ->addColumn('date', function (PmSched $sched){
            return Carbon::parse($sched->schedule)->formatLocalized('%B %d, %Y');
        })
        ->addColumn('schedule', function (PmSched $sched){
            return Carbon::parse($sched->schedule)->formatLocalized('%B %d, %Y');
        })
        ->addColumn('user', function (PmSched $sched){
            $user = User::where('id', $sched->user_id)->first()->name.' '.User::where('id', $sched->user_id)->first()->lastname;
            return $user;
        })
        ->addColumn('client', function (PmSched $sched){
            $client = CustomerBranch::where('customer_branches.id', $sched->customer_id)
                ->join('customers', 'customers.id', 'customer_id')
                ->first()->customer;
            return $client;
        })
        ->addColumn('branch', function (PmSched $sched){
            $branch = CustomerBranch::where('id', $sched->customer_id)->first()->customer_branch;
            return $branch;
        })

        ->make(true);
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
