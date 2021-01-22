<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use App\Branch;
use App\Item;
use App\Loan;
use App\Initial;
use App\Warehouse;
use App\StockRequest;
use App\PreparedItem;
use App\Stock;
use App\Defective;
use App\UserLog;

use Auth;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        
        $title = 'Dashboard';

        if (auth()->user()->status == '0') {
            return redirect('logout');
        }

        if (auth()->user()->branch->branch != "Warehouse" && !auth()->user()->hasrole('Repair')) {
            $units = Stock::where('status', 'in')->where('branch_id', auth()->user()->branch->id)->count();
            $returns = Defective::wherein('status', ['For return', 'For receiving'])->where('branch_id', auth()->user()->branch->id)->count();
            $stockreq = StockRequest::where('branch_id', auth()->user()->branch->id)
                ->wherein('status', ['0', '1'])
                ->count();
            $sunits = Stock::where('status', 'service unit')->where('branch_id', auth()->user()->branch->id)->count();
            $loans = Loan::where('status', 'pending')->where('to_branch_id', auth()->user()->branch->id)->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'sunits', 'title', 'loans'));
        }else if (auth()->user()->hasrole('Repair')){
            return view('pages.warehouse.return', compact('title'));
        }else{
            $stockreq = StockRequest::wherein('status', ['0', '1', '4', '5'])->count();
            $units = Warehouse::where('status', 'in')->count();
            $returns = Defective::where('status', 'For receiving')->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'title'));
        }

    }

    public function log()
    {
        $title = 'Activities';
        return view('pages.home', compact('title'));
    }

    public function unrepair()
    {
        $title = 'Unrepairable items';
        return view('pages.unrepair', compact('title'));
    }

    public function print($id)
    {
        sleep(2);
        $request = StockRequest::where('request_no', $id)->first();
        $title = 'Print Preview';
        return view('pages.warehouse.print', compact('request', 'title'));
    }

    public function printDefective()
    {
        sleep(2);
        $request = Defective::where('branch_id', auth()->user()->branch->id)->where('status', 'For receiving')->get();
        $title = 'Print Preview';
        return view('pages.branch.print', compact('title'));
    }

    public function initial($id)
    {
        if ($id == 'shadow046') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    $initial = new Initial;
                    $initial->items_id = $item->id;
                    $initial->branch_id = $branchs->id;
                    $initial->qty = 0;
                    $initial->save();
                }
            }
        }

        if ($id == 'ini') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    if ($branchs->id != 1) {
                        $stock = new Stock;
                        $stock->category_id = $item->category_id;
                        $stock->branch_id = $branchs->id;
                        $stock->items_id = $item->id;
                        $stock->serial = 'N/A';
                        $stock->status = 'in';
                        $stock->save();
                    }
                }
            }
        }

        if ($id == 'initial') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    if ($branchs->id != 1) {
                        $ini = Initial::where('items_id', $item->id)
                            ->where('branch_id', $branchs->id)
                            ->first();
                        $ini->qty = '5';
                        $ini->save();
                    }
                }
            }
        }

        if ($id == 'ware') {
            $items = Item::all();
                foreach ($items as $item) {
                    $ware = new Warehouse;
                    $ware->user_id = auth()->user()->id;
                    $ware->category_id = $item->category_id;
                    $ware->items_id = $item->id;
                    $ware->serial = '-';
                    $ware->status = 'in';
                    $ware->save();
                }
        }
        
        dd(Stock::all());
        
    }

    public function activity()
    {
        
        if (auth()->user()->hasAnyRole('Administrator', 'Viewer')) {
            $act = UserLog::select('user_logs.*', 'users.email')
                ->join('users', 'users.id', '=', 'user_logs.user_id')
                ->get();
        }

        if (auth()->user()->roles->first()->name == 'Head') {
            $myuser = [];
            $user = User::where('branch_id', auth()->user()->branch->id)->get();
            foreach ($user as $user) {
                $myuser[] = $user->id;
            }
            $act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'DESC')->get();
        }

        if (!auth()->user()->hasAnyRole('Head', 'Administrator', 'Viewer')) {
            $act = UserLog::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get();
        }
        
        
        return DataTables::of($act)
        
        ->addColumn('date', function (UserLog $request){

            return $request->updated_at->toFormattedDateString(). ' '.$request->updated_at->toTimeString();

        })

        ->addColumn('time', function (UserLog $request){
            return $request->updated_at->toTimeString();

        })

        ->addColumn('username', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->email;
        })

        ->addColumn('fullname', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->name. ' '. $username->lastname;
        })

        ->addColumn('userlevel', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->roles->first()->name;
        })
                
        ->make(true);

    }
    
    public function service_units()
    {
        $users = User::all();
        return view('pages.service-units', compact('users'));
    }

    public function getprint($id)
    {
        $request = StockRequest::where('request_no', $id)->first();
        $prepared = PreparedItem::where('request_no', $id)
            ->where('branch_id', $request->branch_id)
            ->join('items', 'items_id', '=', 'items.id')
            ->get();

        return DataTables::of($prepared)->make(true);
    }

    public function save(Request $request){

        Storage::disk('open')->put('company_info/description.txt', $request->description);

    }

    public function convert(){
        return view('pages.test');
    }
}
