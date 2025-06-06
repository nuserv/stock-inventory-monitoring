<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExcelExport;
use App\Exports\BackupInventoryExport;
use App\Exports\Backup;
use Maatwebsite\Excel\Excel as BaseExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Warehouse;
use App\Item;
use App\Buffersend;
use App\AddItem;
use App\AddCategory;
use App\Category;
use App\RepairCategory;
use App\RepairItem;
use App\RepairStock;
use App\District;
use App\Pullno;
use App\PmSched;
use App\Stock;
use App\PreparedItem;
use App\RequestedItem;
use App\CustomerBranch;
use App\WarehouseInitial;
use App\Customer;
use App\Pullout;
use Carbon\Carbon;
use App\Loan;
use App\ServiceOut;
use App\Billable;
use App\Branch;
use App\User;
use App\Pm;
use App\Initial;
use Mail;
use App\Defective;
use App\UserLog;
use DB;
use Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    public function pullview()
    {
        $title = 'Pullout';
        return view('pages.pullout', compact('title'));
    }
    public function pullviewlist()
    {
        $title = 'Pullout list';
        return view('pages.branch.pullout', compact('title'));
    }

    public function get_item()
    {
        $data = Item::selectRaw(
                'items.id as item_code,
                item as item_name,
                category_id,
                category as category_name'
            )
            ->join('categories', 'categories.id', 'category_id')
            ->get();
        return DataTables::of($data)->make(true);
    }

    public function pullitem(Request $request)
    {
        $pullout = Pullout::query()
            ->select('pullouts.id', 'category', 'item', 'serial')
            ->join('categories', 'categories.id', 'pullouts.category_id')
            ->join('items', 'items.id', 'items_id')
            ->wherein('status', ['For receiving'])
            ->where('pullout_no', $request->pullno)
            ->get();
        return DataTables::of($pullout)->make(true);
    }
    public function pullnr(Request $request)
    {
        $pullno = Pullno::where('status', 'For receiving')
            ->where('pullout_no', $request->pull_no)
            ->update(['status' => 'Incomplete']);
        return response()->json($pullno);

    }
    public function pullrec(Request $request)
    {
        foreach ($request->id as $id) {
            $pullout= Pullout::where('status', 'For receiving')
                ->where('id', $id)
                ->where('pullout_no', $request->pull_no)->first();
            $pullout->status = 'Received';
            $pullout->save();
                //->update(['status' => 'Received']);
            $warehouse = new Warehouse;
            $warehouse->items_id = $pullout->items_id;
            $warehouse->serial = $pullout->serial;
            $warehouse->category_id = $pullout->category_id;
            $warehouse->status = 'in';
            $warehouse->save();

            $check = Pullout::where('status', 'For receiving')
                ->where('pullout_no', $request->pull_no)->count();
            if ($check > 0) {
                Pullno::where('status', 'For receiving')->where('pullout_no', $request->pull_no)->update(['status' => 'Incomplete']);
            }else{
                Pullno::where('status', 'For receiving')->where('pullout_no', $request->pull_no)->update(['status' => 'Completed']);
            }
        }
        return response()->json($pullout);

    }
    public function pullget(Request $request)
    {
        if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder')) {
            $pullout = Pullno::query()
                ->select('pullouts_no.id', 'pullouts_no.updated_at', 'pullouts_no.status', 'pullout_no')
                ->wherein('pullouts_no.status', ['For receiving', 'Incomplete'])
                ->get();
            foreach ($pullout as $key) {
                if ($key->status == 'Incomplete') {
                    if (!Pullout::where('pullout_no', $key->pullout_no)->where('status', 'For receiving')->first()) {
                       $key->status = 'Completed';
                       $key->Save();
                    }
                }
            }
            $pullout = Pullno::query()
                ->select('pullouts_no.id', 'pullouts_no.updated_at', 'pullouts_no.status', 'pullout_no', 'branch')
                ->wherein('pullouts_no.status', ['For receiving', 'Incomplete'])
                ->join('branches', 'branches.id', 'branch_id')
                ->get();
            return DataTables::of($pullout)
                ->addColumn('updated_at', function (Pullno $pullout){
                    return Carbon::parse($pullout->updated_at->toFormattedDateString().' '.$pullout->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
        if (auth()->user()->hasanyrole('Head')) {
            if ($request->list == 'list') {
                $pullout = Pullno::query()
                ->select('pullouts_no.updated_at', 'pullouts_no.status', 'pullout_no')
                ->wherein('pullouts_no.status', ['For receiving', 'Incomplete'])
                ->where('branch_id', auth()->user()->branch->id)
                ->get();
                return DataTables::of($pullout)
                ->addColumn('updated_at', function (Pullno $pullout){
                    return Carbon::parse($pullout->updated_at->toFormattedDateString().' '.$pullout->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
            }
            $pullout = Pullout::query()
                ->select('pullouts.updated_at', 'item', 'serial')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('status', 'For pullout')
                ->join('items', 'items.id', 'items_id')
                ->get();
            return DataTables::of($pullout)
                ->addColumn('updated_at', function (Pullout $pullout){
                    return Carbon::parse($pullout->updated_at->toFormattedDateString().' '.$pullout->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
        
    }
    public function pullupdate(Request $request)
    {
        if ($request->send == 1) {
            $pullout = Pullout::query()
            ->where('branch_id', auth()->user()->branch->id)
            ->where('status', 'For pullout')
            ->update(['status' => 'For receiving', 'pullout_no' => $request->retno]);
            $pullno = new Pullno;
            $pullno->user_id = auth()->user()->id;
            $pullno->branch_id = auth()->user()->branch->id;
            $pullno->status = 'For receiving';
            $pullno->pullout_no = $request->retno;
            $pullno->save();
            $bcc = \config('email.bcc');
            $no = $pullno->pullout_no;
            $excel = Excel::raw(new ExcelExport($pullno->pullout_no, 'PR'), BaseExcel::XLSX);
            $data = array('office'=> auth()->user()->branch->branch, 'return_no'=>$pullno->pullout_no, 'dated'=>Carbon::now()->toDateTimeString());
            if (env('MAIL') == 'yes') {
                Mail::send('pr', $data, function($message) use($excel, $no, $bcc) {
                    $message->to(auth()->user()->email, auth()->user()->name)->subject
                        ('PR no. '.$no);
                    $message->attachData($excel, 'PR No. '.$no.'.xlsx');
                    $message->from('noreply@ideaserv.com.ph', 'BSMS');
                    $message->bcc($bcc);
                });
            }
            return response()->json($pullno);
        }
    }

    public function Backupinv() 
    {
        return Excel::download(new BackupInventoryExport, 'Back up Inventory -'.Carbon::now()->isoFormat('lll').'.xlsx');
    }   
    public function Backupbranch() 
    {
        return Excel::download(new Backup, 'branch.xlsx');
    }   
    public function index()
    {
        if (auth()->user()->hasanyrole( 'Repair', 'Warehouse Administrator', 'Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $title = 'Stocks';
        $categories = Category::orderBy('category')->get();
        $service_units = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->get();
        $customers = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->get();
        $branches = Branch::where('area_id', auth()->user()->area->id)
            ->orderBy('branch')
            ->where('id', '!=', auth()->user()->branch->id)
            ->get();
        if (auth()->user()->hasanyrole('Editor', 'Manager')) {
            $title = 'Warehouse Stock';
            return view('pages.stocks', compact('categories', 'service_units', 'customers', 'branches', 'title'));
        }
        return view('pages.stocks', compact('categories', 'service_units', 'customers', 'branches', 'title'));
    }

    public function repair_stocks()
    {
        if (!auth()->user()->hasanyrole( 'Repair')) {
            return redirect('/');
        }
        $title = 'Stocks';
        $categories = RepairCategory::orderBy('category')->get();
        // $service_units = RepairStock::where('branch_id', auth()->user()->branch->id)
        //     ->where('stocks.status', 'service unit')
        //     ->join('categories', 'stocks.category_id', '=', 'categories.id')
        //     ->get();
        // $customers = RepairStock::where('branch_id', auth()->user()->branch->id)
        //     ->where('stocks.status', 'service unit')
        //     ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
        //     ->join('categories', 'stocks.category_id', '=', 'categories.id')
        //     ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
        //     ->join('items', 'stocks.items_id', '=', 'items.id')
        //     ->get();
        // $branches = Branch::where('area_id', auth()->user()->area->id)
        //     ->orderBy('branch')
        //     ->where('id', '!=', auth()->user()->branch->id)
        //     ->get();
        return view('pages.repair-stocks', compact('categories'));
    }
    
    public function category(Request $request){
        $cat = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->where('customer_branches_id', $request->id)
            ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
            ->orderBy('category')
            ->get();
        return response()->json($cat);
    }
    public function bcategory(Request $request){
        $cat = Stock::select('stocks.category_id', 'categories.category')
            ->where('stocks.branch_id', $request->id)
            ->where('stocks.status', 'in')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->groupBy('stocks.category_id')
            ->orderBy('category')
            ->get();
        return response()->json($cat);
    }
    public function bitem(Request $request){
        $item = Stock::select('stocks.items_id', 'items.item')
            ->where('stocks.branch_id', $request->branchid)
            ->where('stocks.category_id', $request->catid)
            ->where('stocks.status', 'in')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->groupBy('stocks.items_id')
            ->orderBy('item')
            ->get();
        return response()->json($item);
    }
    public function bserial($id){
        $serial = Stock::select('items.item', 'stocks.*')
            ->where('stocks.branch_id', auth()->user()->branch->id)
            ->where('stocks.items_id', $id)
            ->where('stocks.status', 'in')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->orderBy('serial')
            ->get();
        return DataTables::of($serial)->make(true);
    }
    public function description(Request $request){
        $desc = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->where('customer_branches_id', $request->customerid)
            ->where('stocks.category_id', $request->categoryid)
            ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->orderBy('item')
            ->get();
        return response()->json($desc);
    }
    public function serial(Request $request){
        $serial = Stock::select('stocks.serial', 'stocks.id')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->where('customer_branches_id', $request->customerid)
            ->where('stocks.category_id', $request->categoryid)
            ->where('stocks.items_id', $request->descid)
            ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
            ->orderBy('serial')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->get();
        return response()->json($serial);
    }
    public function service()
    {
        $title = "Service Unit";
        $categories = Category::select('categories.id','category')
            ->orderBy('category')
            ->join('stocks', 'category_id','categories.id')
            ->where('status', 'in')
            ->where('branch_id', auth()->user()->branch->id)
            ->groupBy('categories.id')
            ->get();
        $pull_categories = Category::select('id','category')
            ->orderBy('category')
            ->get();
        if (!auth()->user()->hasanyrole('Head', 'Tech', 'Encoder', 'Warehouse Manager', 'Warehouse Administrator')) {
            return redirect('/');
        }
        return view('pages.service-unit', compact('title', 'categories', 'pull_categories'));
    }

    public function service_monitoring()
    {
        if (District::where('user_id', auth()->user()->id)->first() || auth()->user()->hasanyrole('Warehouse Manager', 'Manager', 'Editor', 'Warehouse Administrator')){
            $title = "Service Unit";
            return view('pages.service-monitoring', compact('title'));
        }
        return redirect('/');
        
    }

    public function delbill(Request $request)
    {
        $billable = Billable::where('branch_id', auth()->user()->branch->id)
            ->where('id', $request->billid)
            ->where('stocks_id', $request->stocksid)
            ->first();
        $billable->delete();
        $stock = Stock::where('id', $request->stocksid)
            ->where('status', 'billable')
            ->update(['status'=>'in']);
        return response()->json($stock);
    }
    public function approvebill(Request $request)
    {
        $billable = Billable::where('id', $request->billid)
            ->where('stocks_id', $request->stocksid)
            ->update(['status'=>'Approved', 'user_id'=>auth()->user()->id]);
        return response()->json($billable);
    }
    public function prcbill(Request $request)
    {   
        $billable = Billable::where('id', $request->billid)
            ->where('stocks_id', $request->stocksid)
            ->where('status','Approved')
            ->update(['status'=>$request->status, 'user_id'=>auth()->user()->id]);
        if ($request->status == "Completed") {
            $billable = Billable::where('id', $request->billid)
                ->where('stocks_id', $request->stocksid)
                ->where('status','Pending')
                ->update(['status'=>$request->status, 'user_id'=>auth()->user()->id]);

            $no = $request->billid;
            $bcc = \config('email.bcc');
            $excel = Excel::raw(new ExcelExport($request->billid, 'bill'), BaseExcel::XLSX);
            $data = array('office'=> auth()->user()->branch->branch, 'return_no'=>$request->billid, 'dated'=>Carbon::now()->toDateTimeString());
            if (env('MAIL') == 'yes') {
                Mail::send('del', $data, function($message) use($excel, $no, $bcc) {
                    $message->to(auth()->user()->email, auth()->user()->name)->subject
                        ('DR no. '.$no);
                    $message->attachData($excel, 'DR No. '.$no.'.xlsx');
                    $message->from('noreply@ideaserv.com.ph', 'BSMS');
                    $message->bcc($bcc);
                });
            }
        }
        return response()->json($billable);
        
    }
    public function bill()
    {
        $billable = Billable::query()->where('status', '!=', 'Completed')->where('branch_id', auth()->user()->branch->id)
                    ->get();
        if (auth()->user()->hasanyrole('Warehouse Manager')) {
            $billable = Billable::query()->where('status', '!=', 'Completed')->get();
        }
        return DataTables::of($billable)
        ->addColumn('date', function (Billable $request){
            return Carbon::parse($request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('description', function (Billable $request){
            $item = Item::where('id', $request->items_id)->first();
            return mb_strtoupper($item->item);
        })
        ->addColumn('serial', function (Billable $request){
            $serial = Stock::query()->select('serial')->where('id', $request->stocks_id)->first();
            return mb_strtoupper($serial->serial);
        })
        ->addColumn('client', function (Billable $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branch_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(mb_strtolower($client->customer.' - '.$client->customer_branch));
        })
        ->addColumn('client_name', function (Billable $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branch_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(mb_strtolower($client->customer));
        })
        ->addColumn('customer_name', function (Billable $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branch_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(mb_strtolower($client->customer_branch));
        })
        ->addColumn('serviceby', function (Billable $request){
            $user = User::select('name', 'middlename', 'lastname')->where('id', $request->user_id)->first();
            return ucwords(mb_strtolower($user->name.' '.$user->middlename.' '.$user->lastname));
        })
        ->make(true);
    }

    public function get_serial(Request $request)
    {
        $serials = Stock::where('items_id', $request->items_id)->where('status', 'in')->get()->pluck('serial')->toArray();
        return response()->json($serials);
    }

    public function return_to_branch(Request $request)
    {
        // return $request->serial;
        $stock = Stock::where('serial', $request->old)
            ->where('items_id', $request->items_id)
            ->where('status', 'pull out')
            ->first();
        $stock->status = 'returned';
        $stock->Save();
        $item = Item::where('id', $stock->items_id)->first();
        $customer = CustomerBranch::where('id', $stock->customer_branches_id)->first();
        $update = Stock::where('serial', $request->serial)
            ->where('items_id', $stock->items_id)
            ->where('status', 'in')
            ->update([
                'status' => $stock->id,
                'customer_branches_id' => $stock->customer_branches_id
            ]);

        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "SERVICE OUT $item->item with serial no. ".mb_strtoupper($request->serial)." to $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();

        return response()->json($update);
    }

    public function checkpullout(Request $request)
    {
        $serial = Stock::where('serial', $request->serial)
            ->where('status', 'in')
            ->where('branch_id', auth()->user()->branch_id)
            ->first();
        if ($serial) {
            return 'meron';
        }
        else{
            return 'wala';
        }
    }

    public function serviceMonitoring()
    {
        if (District::where('user_id', auth()->user()->id)->first()){
            if (auth()->user()->id == 53) {
                $stock = Stock::select(
                            'updated_at', 
                            'customer_branches_id', 
                            'items_id', 'serial', 
                            'status', 
                            'branch_id',
                            'user_id',
                        )
                        ->whereIn('status', ['service unit', 'pull out'])
                        ->whereHas('branch.area', function ($query) {
                            $query->whereIn('area_id', [1,2]);
                        })
                        ->with([
                            'branch' => function ($query) {
                                $query->select('id', 'branch', 'area_id')
                                ->with(
                                    [
                                        'area'=> function ($query) {
                                            $query->select('id','area');
                                        }
                                    ]
                                );
                            }
                        ])
                        ->with([
                            'user' => function ($query) {
                                $query->selectraw("id, CONCAT(name,' ',lastname) as name");
                            }
                        ])
                        ->with([
                            'item' => function ($query) {
                                $query->select('id', 'item');
                            }
                        ])
                        ->with([
                            'customerbranch' => function ($query) {
                                $query->select('id', 'customer_id', 'customer_branch')
                                ->with([
                                    'customer' => function ($query) {
                                        $query->select('id', 'customer');
                                    }
                                ]);
                            }
                        ])// Eager load the related models
                        ->get();
                return DataTables::of($stock)->make(true);
            }
            $stock = Stock::select(
                        'updated_at', 
                        'customer_branches_id', 
                        'items_id', 'serial', 
                        'status', 
                        'branch_id',
                        'user_id',
                    )
                    ->whereIn('status', ['service unit', 'pull out'])
                    ->whereHas('branch.area', function ($query) {
                        $query->where('area_id', auth()->user()->area_id);
                    })
                    ->with([
                        'branch' => function ($query) {
                            $query->select('id', 'branch', 'area_id')
                            ->with(
                                [
                                    'area'=> function ($query) {
                                        $query->select('id','area');
                                    }
                                ]
                            );
                        }
                    ])
                    ->with([
                        'user' => function ($query) {
                            $query->selectraw("id, CONCAT(name,' ',lastname) as name");
                        }
                    ])
                    ->with([
                        'item' => function ($query) {
                            $query->select('id', 'item');
                        }
                    ])
                    ->with([
                        'customerbranch' => function ($query) {
                            $query->select('id', 'customer_id', 'customer_branch')
                            ->with([
                                'customer' => function ($query) {
                                    $query->select('id', 'customer');
                                }
                            ]);
                        }
                    ])// Eager load the related models // Eager load the related models
                    ->get();
            return DataTables::of($stock)->make(true);
        }
        else if (auth()->user()->hasanyrole('Warehouse Manager', 'Manager', 'Editor', 'Warehouse Administrator')) {
            $stock = Stock::select(
                        'updated_at', 
                        'customer_branches_id', 
                        'items_id', 'serial', 
                        'status', 
                        'branch_id',
                        'user_id',
                    )
                    ->wherein('stocks.status', 
                        [
                            'service unit',
                            'pull out'
                        ]
                    )
                    ->with([
                        'user' => function ($query) {
                            $query->selectraw("id, CONCAT(name,' ',lastname) as name");
                        }
                    ])
                    ->with([
                        'branch' => function ($query) {
                            $query->select('id', 'branch', 'area_id')
                            ->with(
                                [
                                    'area'=> function ($query) {
                                        $query->select('id','area');
                                    }
                                ]
                            );
                        }
                    ])->with([
                        'item' => function ($query) {
                            $query->select('id', 'item');
                        }
                    ])
                    ->with([
                        'customerbranch' => function ($query) {
                            $query->select('id', 'customer_id', 'customer_branch')
                            ->with([
                                'customer' => function ($query) {
                                    $query->select('id', 'customer');
                                }
                            ]);
                        }
                    ])
                    ->get();
            return DataTables::of($stock)->make(true);
        }
    }

    public function serviceUnit()
    {
        
        // if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder', 'Warehouse Administrator')) {
        //     $stock = Stock::wherein('status', ['service unit', 'pull out'])
        //             ->where('branch_id', 2)
        //             ->get();
        // }
        
        $stock = Stock::wherein('status', ['service unit', 'pull out'])
            ->where('branch_id', auth()->user()->branch->id)
            ->get();

        return DataTables::of($stock)
        ->addColumn('date', function (Stock $request){
            return Carbon::parse($request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('items_id', function (Stock $request){
            return $request->items_id;
        })
        ->addColumn('status', function (Stock $request){
            return strtoupper($request->status);
        })
        ->addColumn('category', function (Stock $request){
            $cat = Category::find($request->category_id);
            return mb_strtoupper($cat->category);
        })
        ->addColumn('description', function (Stock $request){
            $item = Item::where('id', $request->items_id)->first();
            return mb_strtoupper($item->item);
        })
        ->addColumn('serial', function (Stock $request){
            return mb_strtoupper($request->serial);
        })
        ->addColumn('client', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            // if (!$client) {
            //     return 'wala--'.$request->customer_branches_id;
            // }
            return ucwords(mb_strtolower($client->customer.' - '.$client->customer_branch));
        })
        ->addColumn('client_name', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
                // if (!$client) {
                //     return 'wala--'.$request->customer_branches_id;
                // }
            return ucwords(mb_strtolower($client->customer));
        })
        ->addColumn('customer_name', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
                // if (!$client) {
                //     return 'wala--'.$request->customer_branches_id;
                // }
            return ucwords(mb_strtolower($client->customer_branch));
        })
        ->addColumn('serviceby', function (Stock $request){
            $user = User::select('name', 'middlename', 'lastname')->where('id', $request->user_id)->first();
            return ucwords(mb_strtolower($user->name.' '.$user->middlename.' '.$user->lastname));
        })
        ->make(true);
    }
    public function pmserviceUnit()
    {
        $stock = Pm::where('branch_id', auth()->user()->branch->id)->get();
        return DataTables::of($stock)
        ->addColumn('date', function (Pm $request){
            return Carbon::parse($request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('items_id', function (Pm $request){
            return $request->items_id;
        })
        ->addColumn('category', function (Pm $request){
            $cat = Category::find($request->category_id);
            return mb_strtoupper($cat->category);
        })
        ->addColumn('description', function (Pm $request){
            $item = Item::where('id', $request->items_id)->first();
            return mb_strtoupper($item->item);
        })
        ->addColumn('serial', function (Pm $request){
            return mb_strtoupper($request->serial);
        })
        ->addColumn('client', function (Pm $request){
            $clients = '';
            foreach (explode(',', $request->customer_ids) as $customer) {
                if ($clients == "") {
                    $client = CustomerBranch::select('customer_branch', 'customers.customer')
                        ->where('customer_branches.id', $customer)
                        ->join('customers', 'customer_id', '=', 'customers.id')
                        ->first();
                    if (!$client) {
                        return 'this==='.$customer;
                    }
                    $clients = ucwords(mb_strtolower($client->customer.' - '.$client->customer_branch));
                }else{
                    $client = CustomerBranch::select('customer_branch', 'customers.customer')
                        ->where('customer_branches.id', $customer)
                        ->join('customers', 'customer_id', '=', 'customers.id')
                        ->first();
                        if (!$client) {
                            return 'this==='.$customer;
                        }
                    $clients = $clients.', '.ucwords(mb_strtolower($client->customer.' - '.$client->customer_branch));
                }
            }
            return $clients;
        })
        /*->addColumn('client_name', function (Pm $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branch_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(mb_strtolower($client->customer));
        })
        ->addColumn('customer_name', function (Pm $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branch_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(mb_strtolower($client->customer_branch));
        })*/
        ->addColumn('serviceby', function (Pm $request){
            $user = User::select('name', 'lastname')->where('id', $request->user_id)->first();
            return ucwords(mb_strtolower($user->name.' '.$user->middlename.' '.$user->lastname));
        })
        ->make(true);
    }
    public function searchall(Request $request)
    {
        /*$search = Stock::select('branch', 'branch_id', 'categories.id as category_id', 'categories.category as category', 'stocks.items_id as items_id', 'items.item as description', 'serial')
            ->where('stocks.status', 'in')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('category', 'like', '%'.$request->search.'%')
            //->orwhere('description', 'like', '%'.$request->search.'%')
            //->orwhere('serial', 'like', '%'.$request->search.'%')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->join('branches', 'branches.id', '=', 'branch_id')
            ->get();*/
        /*$search1 = Stock::select('branch','branch_id','categories.id as category_id', 'categories.category as category', 'stocks.items_id as items_id', 'items.item as description', 'serial')
            ->where('stocks.status', 'in')
            ->where('branch_id', auth()->user()->branch->id)
            //->where('categories.category', 'like', '%'.$request->search.'%')
            ->where('items.item', 'like', '%'.$request->search.'%')
            //->orwhere('serial', 'like', '%'.$request->search.'%')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->join('branches', 'branches.id', '=', 'branch_id')
            ->get();*/
        $search2 = Stock::select('branch','branch_id','categories.id as category_id', 'categories.category as category', 'stocks.items_id as items_id', 'items.item as description', 'serial')
            ->where('stocks.status', 'in')
            ->where('branch_id', auth()->user()->branch->id)
            //->where('categories.category', 'like', '%'.$request->search.'%')
            //->where('items.item', 'like', '%'.$request->search.'%')
            //->where('serial', 'like', '%'.$request->search.'%')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->join('branches', 'branches.id', '=', 'branch_id')
            ->get();
        //dd($search);
        //$result = $search->merge($search1);
        //$result = $search2->merge($search1);
        return DataTables::of($search2)->make(true);
    }
    public function searchserial(Request $request)
    {
        $sserial = PreparedItem::all();
        //dd($sserial);
        return DataTables::of($sserial)
        ->addColumn('description', function (PreparedItem $PreparedItem){
            $desc = Item::where('id', $PreparedItem->items_id)->first();
            return mb_strtoupper($desc->item);
        })
        ->addColumn('branch', function (PreparedItem $PreparedItem){
            $branchname = Branch::where('id', $PreparedItem->branch_id)->first();
            return mb_strtoupper($branchname->branch);
        })
        ->addColumn('user', function (PreparedItem $PreparedItem){
            $username = User::where('id', $PreparedItem->user_id)->first();
            return $username->name.' '.$username->middlename.' '.$username->lastname;
        })
        ->make(true);
    }
    public function autocompleteCustomer(Request $request)
    {
        $customer = CustomerBranch::where("customer_branch", "LIKE", "%{$request->id}%")
            ->where('customer_id', $request->client)
            ->limit(10)
            ->get();
        return response()->json($customer);
    }
    
    public function autocompleteClient(Request $request)
    {
        $client = Customer::where("customer", "LIKE", "%{$request->id}%")
            ->limit(10)
            ->get();
        return response()->json($client);
    }
    public function pautocompleteCustomer(Request $request)
    {
        $pcustomer = Pullout::select('pullouts.customer_branch_id', 'pullouts.customer_id', 'customer_branches.customer_branch')
            ->join('customer_branches', 'pullouts.customer_branch_id', '=', 'customer_branches.id')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('pullouts.customer_id', $request->client)
            ->where("customer_branch", "LIKE", "%{$request->id}%")
            ->groupBy('pullouts.customer_branch_id')
            ->limit(10)
            ->get();
        return response()->json($pcustomer);
    }
    public function pautocompleteClient(Request $request)
    {
        $pclient = Pullout::select('pullouts.customer_id', 'customers.customer')
            ->where('branch_id', auth()->user()->branch->id)
            ->where("customer", "LIKE", "%{$request->id}%")
            ->join('customers', 'pullouts.customer_id', '=', 'customers.id')
            ->groupBy('pullouts.customer_id')
            ->limit(10)
            ->get();
        return response()->json($pclient);
    }
    
    public function pmautocompleteCustomer(Request $request)
    {
        $clients = array();
        foreach (explode(',', $request->customer_ids) as $customer) {
            $client = CustomerBranch::select('customer_id', 'customer_branches.id as id', 'customer_branch', 'customers.customer')
                ->where('customer_branches.id', $customer)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            array_push($clients, $client);
        }
        return response()->json($clients);
    }
    public function viewStocks(Request $request)
    {
        if ($request->data != 0) {
            // $category = Category::query()->get();
            $category = Category::query()
                ->selectRaw('id, UPPER(category) as category, id as category_id')
                ->get();
            /*$stock = Stock::select('category_id', 'category', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stockin'))
                ->where('branch_id', auth()->user()->branch->id)
                ->join('categories', 'category_id', '=', 'categories.id')
                ->groupBy('category')
                ->get();*/
            return DataTables::of($category)
            ->addColumn('stockout', function (Category $stock){
                $out = Stock::wherein('status', ['service unit', 'pm'])
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                return mb_strtoupper($out);
            })
            ->addColumn('defectives', function (Category $stock){
                $defective = Defective::where('status', 'For return')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                return mb_strtoupper($defective);
            })
            ->addColumn('total', function (Category $stock){
                $out = Stock::wherein('status', ['service unit', 'pm'])
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                $defective = Defective::where('status', 'For return')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                $in = Stock::where('status', 'in')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                return ($in+$out+$defective);
            })
            ->addColumn('stockin', function (Category $stock){
                $in = Stock::where('status', 'in')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('category_id', $stock->id)
                    ->count();
                return $in;
            })
            ->addColumn('alert', function (Category $stock){
                $items = Item::where('category_id', $stock->id)->get();
                $alert = 0;
                foreach ($items as $item) {
                    if ($alert == 0) {
                        $initials = Initial::select('qty')->where('branch_id', auth()->user()->branch->id)
                            ->where('items_id', $item->id)
                            ->first();
                        $itemstock = Stock::where('items_id', $item->id)
                            ->where('branch_id', auth()->user()->branch->id)
                            ->where('status', 'in')
                            ->count();
                        if ($initials) {
                            if ($initials->qty > $itemstock) {
                                $alert = 1;
                            }
                        }
                        // else{
                        //     $alert =  $item->id.'------';
                        // }
                    }
                }
                return $alert;
            })
            ->make(true);
        }else{
            $req = $request->reqno;
            $items = Item::query()
                ->select('items.*', 'category')
                ->where('categories.id', $request->category)
                ->join('categories', 'category_id', '=', 'categories.id')
                ->get();
            // return $items;
            /*$stock = Stock::select('UOM','categories.category', 'stocks.items_id as items_id', 'items.item as description', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stockin'))
                ->where('stocks.branch_id', auth()->user()->branch->id)
                ->where('categories.id', $request->category)
                ->join('categories', 'stocks.category_id', '=', 'categories.id')
                ->join('items', 'stocks.items_id', '=', 'items.id')
                ->groupBy('items_id')->get();*/
            return DataTables::of($items)
            ->addColumn('description', function (Item $items){
                return mb_strtoupper($items->item);
            })
            ->addColumn('items_id', function (Item $items){
                return mb_strtoupper($items->id);
            })
            ->addColumn('stockout', function (Item $items){
                $out = Stock::wherein('status', ['service unit', 'pm'])
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                return $out;
            })
            ->addColumn('stockin', function (Item $items){
                $in = Stock::where('status', 'in')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                return $in;
            })
            ->addColumn('defectives', function (Item $items){
                $defective = Defective::where('status', 'For return')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                return $defective;
            })
            ->addColumn('total', function (Item $items){
                $out = Stock::wherein('status', ['service unit', 'pm'])
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                $defective = Defective::where('status', 'For return')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                $in = Stock::where('status', 'in')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->count();
                return $in+$out+$defective;
            })
            ->addColumn('initial', function (Item $items){
                $initials = Initial::select('qty')->where('branch_id', auth()->user()->branch->id)
                    ->where('items_id', $items->id)
                    ->first();
                if (!$initials) {
                    return $items->id.' walangid';
                }
                return $initials->qty;
            })
            ->addColumn('request', function (Item $items) use ($req){
                $requestno = RequestedItem::where('items_id', $items->id)
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('request_no', $req)
                    ->first();
                if ($requestno) {
                    return 'meron';
                }else{
                    return 'wala';
                }
            })
            ->make(true);
        }
    }
    public function checkStocks(Request $request)
    {
        $initials = Initial::where('branch_id', auth()->user()->branch->id)
                    ->join('items', 'items.id', '=', 'items_id')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->orderBy('category')
                    ->get();
        $cat = array();
        foreach ($initials as $initial) {
            $count = Stock::where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('items_id', $initial->items_id)->count();
            if ($count < $initial->qty) {
                $category = Item::select('items.category_id as id', 'categories.category')
                    ->where('items.id', $initial->items_id)
                    ->join('categories', 'categories.id', '=', 'category_id')
                    ->first();
                if(!in_array($category, $cat)){
                    array_push($cat, $category);
                }
            }
        }
        return response()->json(array_filter($cat));
    }
    public function checkService(Request $request)
    {
        $initials = Initial::query()->where('branch_id', auth()->user()->branch->id)
                    ->join('items', 'items.id', '=', 'items_id')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->orderBy('category')
                    ->get();
        // dd($initials);
        $cat = array();
        foreach ($initials as $initial) {
            $count = Stock::query()->where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('items_id', $initial->items_id)->count();
            if ($count == "0") {
                $category = Item::query()->select('items.category_id as id', 'categories.category')
                    ->where('items.id', $initial->items_id)
                    ->join('categories', 'categories.id', '=', 'category_id')
                    ->first();
                if(!in_array($category, $cat)){
                    array_push($cat, $category);
                }
            }
        }
        return response()->json(array_filter($cat));
    }
    public function addItem(Request $request)
    {
        if ($request->user == 'repair') {
            $add = new RepairItem;
            $add->category_id = $request->cat;
            $add->item = ucwords($request->item);
            $add->UOM = ucfirst($request->uom);
            $add->n_a = 'no';
            $add->serialize = 'YES';
            $add->save();
            return response()->json($add);
        }
        else{
            $add = new Item;
            $add->category_id = $request->cat;
            $add->item = ucwords($request->item);
            $add->UOM = ucfirst($request->uom);
            $add->n_a = 'no';
            $add->serialize = 'YES';
            $add->save();
            $additem = new AddItem;
            $additem->category_id = $request->cat;
            $additem->item = ucwords($request->item);
            $additem->UOM = ucfirst($request->uom);
            $additem->n_a = 'no';
            $additem->serialize = 'YES';
            $additem->save();

            $stocks = new Buffersend;
            $stocks->item_id = $additem->id;
            $stocks->user_id = auth()->user()->id;
            $stocks->status = 'default';
            $stocks->qty = '1';
            $stocks->save();
            WarehouseInitial::create([
                'items_id'=>$add->id,
                'qty'=>10
            ]);
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                $initial = new Initial;
                $initial->items_id = $add->id;
                $initial->branch_id = $branchs->id;
                $initial->qty = 5;
                $data = $initial->save();
            }

            return response()->json($data);
        }
    }
    public function pmservicein(Request $request)
    {
        $pm = Pm::where('id', $request->id)->first();
        $stock = Stock::where('id', $pm->stocks_id)->first();
        $item = Item::where('id', $pm->items_id)->first();
        $customer = CustomerBranch::where('id', $request->customerid)->first();
        //return dd($stock);
        if ($request->status == 'defective') {
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            if ($request->stat == 'replace') {
                $defective->items_id = $request->ids;
            }else{
                $defective->items_id = $stock->items_id;
            }
            $defective->status = 'For return';
            $defective->category_id = $item->category_id;
            $defective->serial = mb_strtoupper($request->serial);
            $defective->save();
            $pmdb = Pm::where('id', $request->pmid)->first();
            $pmitem = Item::where('id', $pmdb->items_id)->first();
            $pmcustomer = CustomerBranch::where('id', $request->customerid)->first();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "PM SERVICE IN - SERVICE UNIT $pmitem->item(defective) with serial no. ".mb_strtoupper($request->serial)." from $pmcustomer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
        }else{
            $pmdb = Pm::where('id', $request->pmid)->first();
            $pmitem = Item::where('id', $pmdb->items_id)->first();
            $pmcustomer = CustomerBranch::where('id', $request->customerid)->first();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "PM SERVICE IN - SERVICE UNIT $pmitem->item(good) with serial no. ".mb_strtoupper($request->serial)." from $pmcustomer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
        }
        $stock->status = $request->status;
        $stock->customer_branches_id = $request->customerid;
        $pmupdate = Pm::where('id', $request->pmid)->first();
        $pmupdate->delete();
        $stock->user_id = auth()->user()->id;
        $data = $stock->save();
        return response()->json($data);
    }

    public function servicein(Request $request)
    {
        $stock = Stock::where('id', $request->id)->first();
        $item = Item::where('id', $stock->items_id)->first();
        $customer = CustomerBranch::where('id', $stock->customer_branches_id)->first();
        if ($stock->updated_at >= Carbon::now()->subMinutes(15)) {
            $data = "bawal";
            return response()->json($data);
        }
        if ($request->status == 'defective') {
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            if ($request->stat == 'replace') {
                $defective->items_id = $request->ids;
            }else{
                $defective->items_id = $stock->items_id;
            }
            $defective->status = 'For return';
            if ($request->remarks == 'pm') {
                $pmdb = Pm::where('id', $request->pmid)->first();
                $defective->category_id = $item->category_id;
            }else{
                $defective->category_id = $item->category_id;
            }
            $defective->serial = $request->serial;
            $defective->save();
            if ($request->remarks == 'pm') {
                $pmdb = Pm::where('id', $request->pmid)->first();
                $pmitem = Item::where('id', $pmdb->items_id)->first();
                $pmcustomer = CustomerBranch::where('id', $request->customerid)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "PM SERVICE IN - SERVICE UNIT $pmitem->item(defective) with serial no. ".mb_strtoupper($request->serial)." from $pmcustomer->customer_branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
            }else{
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "SERVICE IN - SERVICE UNIT $item->item(defective) with serial no. ".mb_strtoupper($request->serial)." from $customer->customer_branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
            }
        }else{
            if ($request->remarks == 'pm') {
                $pmdb = Pm::where('id', $request->pmid)->first();
                $pmitem = Item::where('id', $pmdb->items_id)->first();
                $pmcustomer = CustomerBranch::where('id', $request->customerid)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "PM SERVICE IN - SERVICE UNIT $pmitem->item(good) with serial no. ".mb_strtoupper($request->serial)." from $pmcustomer->customer_branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
            }else{
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = " PM SERVICE IN - SERVICE UNIT $item->item(good) with serial no. ".mb_strtoupper($stock->serial)." from $customer->customer_branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
            }
        }
        $stock->status = $request->status;
        if ($request->remarks == 'pm') {
            $stock->customer_branches_id = $request->customerid;
            $pmupdate = Pm::where('id', $request->pmid)->first();
            $pmupdate->delete();
        }
        $stock->user_id = auth()->user()->id;
        $data = $stock->save();
        return response()->json($data);
    }
    public function addCategory(Request $request){
        if ($request->user == 'repair') {
            $add = new RepairCategory;
            $add->category = ucwords($request->cat);
            $data = $add->save();
            return response()->json($data);
        }
        else{
            $add = new Category;
            $add->category = ucwords($request->cat);
            $data = $add->save();
            $add = new AddCategory;
            $add->category = ucwords($request->cat);
            return response()->json($data);
        }
    }
    public function pulldetails(Request $request, $id)
    {   
        $pullouts = Pullout::select('categories.category', 'items.item', 'pullouts.category_id', 'pullouts.created_at', 'pullouts.id', 'pullouts.items_id', 'pullouts.serial')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('customer_branch_id', $id)
                ->where('pullouts.status', 'pullout')
                ->join('categories', 'pullouts.category_id', '=', 'categories.id')
                ->join('items', 'pullouts.items_id', '=', 'items.id')
                ->get();
        return DataTables::of($pullouts)
        ->addColumn('date', function (Pullout $pullout){
            return $pullout->created_at->toFormattedDateString().' '.$pullout->created_at->toTimeString();
        })
        ->make(true);
    }
    public function pulldetails1(Request $request, $id)
    {   
        $pullouts = Pullout::select('categories.category', 'items.item', 'pullouts.category_id', 'pullouts.created_at', 'pullouts.id', 'pullouts.items_id', 'pullouts.serial')
                ->where('pullouts.id', $id)
                ->join('categories', 'pullouts.category_id', '=', 'categories.id')
                ->join('items', 'pullouts.items_id', '=', 'items.id')
                ->get();
        return DataTables::of($pullouts)
        ->addColumn('date', function (Pullout $pullout){
            return $pullout->created_at->toFormattedDateString().' '.$pullout->created_at->toTimeString();
        })
        ->make(true);
    }
    public function pullItemCode(Request $request)
    {
        $pullout = Pullout::select('pullouts.items_id', 'items.item')
            ->join('items', 'pullouts.items_id', '=', 'items.id')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('customer_branch_id', $request->custid)
            ->where('pullouts.category_id', $request->id)
            ->groupBy('pullouts.items_id')
            ->get();
        return response()->json($pullout);
    }
    public function pullOut(Request $request)
    {
        $item = Item::where('id', $request->item)->first();
        $customer = Customerbranch::where('id', $request->customer)->first();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "Pull-out $item->item(S/N: ".mb_strtoupper($request->serial).") from $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $pullout = new Pullout;
        $pullout->user_id = auth()->user()->id;
        $pullout->branch_id = auth()->user()->branch->id;
        $pullout->customer_id = $request->client;
        $pullout->customer_branch_id = $request->customer;
        $pullout->category_id = $request->cat;
        $pullout->items_id = $request->item;
        $pullout->serial = $request->serial;
        $pullout->status = 'pullout';
        $data = $pullout->save();
        return response()->json($data);
    }

    public function repaired(Request $request)
    {
        $defect = Defective::where('id', $request->id)->first();
        $addtostoc = new Stock;
        $addtostoc->user_id = auth()->user()->id;
        $addtostoc->category_id = $defect->category_id;
        $addtostoc->branch_id = auth()->user()->branch->id;
        $addtostoc->items_id = $defect->items_id;
        $addtostoc->serial = $defect->serial;
        $addtostoc->status = 'in';
        $addtostoc->save();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->activity = "Repaired $request->item(S/N: ".mb_strtoupper($defect->serial).").";
        $log->save();
        $data = $defect->delete();
        return response()->json($data);
    }
    public function pull(Request $request)
    {
        $pull = Stock::where('id', $request->id)->where('serial', $request->serial)->where('status', 'in')->first();
        $pullout = new Pullout;
        $pullout->user_id = auth()->user()->id;
        $pullout->branch_id = auth()->user()->branch->id;
        $pullout->category_id = $pull->category_id;
        $pullout->items_id = $pull->items_id;
        $pullout->serial = $request->serial;
        $pullout->status = "For pullout";
        $pull->status = "pullout";
        $pull->save();
        $item = Item::where('id', $pull->items_id)->first();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "PULLOUT $item->item(S/N: ".mb_strtoupper($request->serial).").";
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $data = $pullout->save();
        return response()->json($data);
    }
    public function def(Request $request)
    {
        $def = Stock::where('id', $request->id)->where('serial', $request->serial)->where('status', 'in')->first();
        $defective = new Defective;
        $defective->branch_id = auth()->user()->branch->id;
        $defective->user_id = auth()->user()->id;
        $defective->category_id = $def->category_id;
        $defective->items_id = $request->items_id;
        $defective->serial = $request->serial;
        $defective->status = 'For return';
        $defective->save();

        if ($request->replace == 1) {
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->activity = "Use $request->item(S/N: $request->serial) to repair $request->repairitem(S/N: $request->repairserial).";
            $log->save();
            $def->status = "use to $request->repairitem(S/N: ".mb_strtoupper($request->repairserial).")";
            $forrepair = Defective::select('serial', 'items.id as items_id', 'defectives.id as id', 'items.category_id')->where('defectives.id', $request->repairid)
                ->join('items', 'items.id', '=', 'items_id')
                ->first();
            $addtostock = new Stock;
            $addtostock->user_id = auth()->user()->id;
            $addtostock->category_id = $forrepair->category_id;
            $addtostock->branch_id = auth()->user()->branch->id;
            $addtostock->items_id = $forrepair->items_id;
            $addtostock->serial = $forrepair->serial;
            $addtostock->status = 'in';
            $addtostock->save();
            $forrepair->delete();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->activity = "Repaired $request->repairitem(S/N: ".mb_strtoupper($request->repairserial).").";
            $log->save();
            $data = $def->save();
        }else{
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->activity = "Marked $request->item(S/N: ".mb_strtoupper($request->serial).") as defective.";
            $log->save();
            $def->status = "defective";
            $data = $def->save();
        }
        return response()->json($data);
    }
    public function loan(Request $request)
    {
        $item = Item::where('id', $request->itemid)->first();
        $branch = Branch::where('id', $request->branchid)->first();
        $loan = new Loan;
        $loan->user_id = auth()->user()->id;
        $loan->from_branch_id = auth()->user()->branch->id;
        $loan->to_branch_id = $request->branchid;
        $loan->items_id = $request->itemid;
        $loan->status = 'pending';
        $loan->request_no = $request->reqno;
        $emails = User::select('email')
            ->where('branch_id', $request->branchid)
            ->get();
        $allemails = array();
        $allemails[] = 'jerome.lopez.ge2018@gmail.com';
        foreach ($emails as $email) {
            $allemails[]=$email->email;
        }
        $allemails = array_diff($allemails, array($branch->email));
        /*Mail::send('loan', ['reqitem'=>$item->item, 'branch'=>$branch],function( $message) use ($allemails, $branch){ 
            $message->to($branch->email, $branch->head)->subject 
                (auth()->user()->branch->branch); 
            $message->from('no-reply@ideaserv.com.ph', 'NO REPLY - '.auth()->user()->branch->branch); 
            $message->cc($allemails); 
        });*/
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "Request $item->item to $branch->branch." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $data = $loan->save();
        return response()->json($data);
    }
    public function serviceOut(Request $request)
    {
        $item = Item::where('id', $request->item)->first();
        $customer = CustomerBranch::where('id', $request->customer)->first();
        $client = Customer::where('id', $customer->customer_id)->first();
        if ($request->purpose == "pull out") {
            $stock = new Stock;
            $stock->user_id = auth()->user()->id;
            $stock->category_id = $item->category_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->items_id = $request->item;
            $stock->itemname = $item->item;
            $stock->serial = $request->serial;
            $stock->status = 'pull out';
            if ($request->remarks == "WARRANTY") {
                $stock->warranty = 1;
            }
            else{
                $stock->warranty = 0;
            }
            $stock->customer_branches_id = $request->customer;
            $stock->Save();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $request->item;
            $defective->serial = mb_strtoupper($request->serial);
            $defective->status = 'For return';
            if ($request->remarks == "WARRANTY") {
                $defective->remarks = 'PULL OUT - UNDER WARRANTY from '.$customer->customer_branch."($client->customer)";
            }
            else{
                $defective->remarks = 'PULL OUT - SAME S/N from '.$customer->customer_branch."($client->customer)";
            }
            $defective->save();
            $emailMessage = "The following units are service in pullout - $request->remarks:\n\n";
            $emailMessage .= "- " . $item->item . 'with serial '. $request->serial . "\n";

            // Send the email
            Mail::raw($emailMessage, function ($message) use ($item){
                $message->to('jolopez@ideaserv.com.ph')
                        ->subject('Service In Pullout '. $item->item);
            });

        }
        else{
            $stock = Stock::where('items_id', $request->item)
                ->where('branch_id', auth()->user()->branch->id)
                ->where('serial', $request->serial)
                ->where('status', 'in')
                ->first();
            $stock->status = $request->purpose;
            $stock->customer_branches_id = $request->customer;
            $stock->user_id = auth()->user()->id;
        }
        
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        if ($request->purpose == "pull out") {
            $log->activity = "SERVICE IN - Pullout only $item->item(S/N: ".mb_strtoupper($request->serial).") from $customer->customer_branch.";
            $serviceout = new ServiceOut;
            $serviceout->branch_id = auth()->user()->branch->id;
            $serviceout->user_id = auth()->user()->id;
            $serviceout->items_id = $request->item;
            $serviceout->stocks_id = $stock->id;
            $serviceout->status = 'pull out';
            $serviceout->customer_branch_id = $request->customer;
            $serviceout->save();
        }
        else if ($request->purpose == "billable") {
            $log->activity = "SERVICE OUT BILLABLE $item->item(S/N: ".mb_strtoupper($request->serial).") to $customer->customer_branch.";
            $serviceout = new Billable;
            $serviceout->branch_id = auth()->user()->branch->id;
            $serviceout->user_id = auth()->user()->id;
            $serviceout->items_id = $request->item;
            $serviceout->customer_branch_id = $request->customer;
            $serviceout->status = "For approval";
            $serviceout->stocks_id = $stock->id;
            $serviceout->save();
        }else{
            $log->activity = "SERVICE OUT $item->item(S/N: ".mb_strtoupper($request->serial).") to $customer->customer_branch.";
            $serviceout = new ServiceOut;
            $serviceout->branch_id = auth()->user()->branch->id;
            $serviceout->user_id = auth()->user()->id;
            $serviceout->items_id = $request->item;
            $serviceout->customer_branch_id = $request->customer;
            $serviceout->save();
        }
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $data = $stock->save();
        return response()->json($data);
    }

    public function pmOut(Request $request)
    {
        $stock = Stock::where('items_id', $request->item)
            ->where('branch_id', auth()->user()->branch->id)
            ->where('serial', $request->serial)
            ->where('status', 'in')
            ->first();
        $item = Item::where('id', $request->item)->first();
        $customer = CustomerBranch::where('id', $request->customer)->first();
        $stock->status = $request->purpose;
        $stock->user_id = auth()->user()->id;
        $preventive = new Pm;
        $preventive->stocks_id = $stock->id;
        $preventive->branch_id = auth()->user()->branch->id;
        $preventive->user_id = auth()->user()->id;
        $preventive->category_id = $stock->category_id;
        $preventive->items_id = $request->item;
        $customers = "";
        foreach ($request->customer as $key => $value) {
            if ($customers == "") {
                $customers = $value;
            }else{
                $customers = $customers.','.$value;
            }
            PmSched::where('customer_id', $value)
                ->where('branch_id', auth()->user()->branch->id)
                ->where('Status', '!=', 'Completed')
                ->update(['Status'=>'Completed', 'user_id'=>auth()->user()->id]);
        }
        $preventive->customer_ids = $customers;
        $preventive->serial = $request->serial;
        $preventive->save();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "PM SERVICE OUT $item->item(S/N: ".mb_strtoupper($request->serial).") to $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $data = $stock->save();
        return response()->json($log);
    }
    public function store(Request $request)
    {
        $item = Item::where('id', $request->item)->first();
        if (auth()->user()->branch->branch == 'Warehouse') {
            for ($i=1; $i <= $request->qty ; $i++) { 
                $add = new Warehouse;
                $add->category_id = $request->cat;
                $add->items_id = $request->item;
                $add->status = 'in';
                $add->serial = '-';
                $add->user_id = auth()->user()->id;
                $add->save();
            }
            if ($request->qty > 1) {
                $uom = $item->UOM.'s';
            }else{
                $uom = $item->UOM;
            }
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "ADD $request->qty $uom of $item->item to stocks." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
        }else{
            $add = new Stock;
            $add->category_id = $request->cat;
            $add->branch_id = auth()->user()->branch->id;
            $add->items_id = $request->item;
            $add->user_id = auth()->user()->id;
            $add->serial = strtoupper($request->serial);
            $add->status = 'in';
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "ADD $item->item with serial no. ".mb_strtoupper($request->serial)." to stocks" ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
            
            $data = $add->save();
        }
                
        return response()->json($data);
    }
    public function import(Request $request)
    {
        //get file
        $upload = $request->file('upload-file');
        $filePath = $upload->getRealPath();
        //open and read
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $escapedHeader=[];
        //validate
        foreach ($header as $key => $value) {
            $lheader = mb_strtolower($value);
            array_push($escapedHeader, $lheader);
        }
        //looping throu other columns
        $notfound=[];
        $duplicate=[];
        $dup = false;
        while ($columns = fgetcsv($file)) {
            $item = Item::where('item', $columns[0])->first();
            $serial = $columns[1];
            if ($columns[1] == "") {
                $serial = 'N/A';
            }else{
                $find = Stock::where('serial', $columns[1])
                    ->where('status', 'in')->first();
                if ($find) {
                    array_push($duplicate, $columns[1]);
                    $dup = true;
                }
            }
            $item = Item::where('id', $item->id)->first();
            if ($columns[0] != $item->item) {
                array_push($notfound, $columns[0]);
            }else {
                if ($dup == false) {
                    $stock = new Stock;
                    $stock->category_id = $item->category->id;
                    $stock->items_id = $item->id;
                    $stock->status = 'in';
                    $stock->branch_id = auth()->user()->branch_id;
                    $stock->serial = $serial;
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                    $log->activity = "IMPORT $item->item with serial no. $serial." ;
                    $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                    $log->save();
                    $stock->save();
                }
            }        
        }
        return redirect()->route('stocks.index');
    }
    public function uom(Request $request)
    {
        $uom = Item::select('UOM')->where('id', $request->id)->first();
        return response()->json($uom->UOM);
    }
    public function show(Request $request)
    {
        if ($request->data != 0) {
            $category = Category::query()->get();
            /*$stock = Warehouse::select('category_id', 'category', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as quantity'))
                ->join('categories', 'categories.id', '=', 'category_id')
                ->groupBy('category')
                ->get();*/
            return Datatables::of($category)
            ->addColumn('category_id', function (Category $request){
                return mb_strtoupper($request->id);
            })
            ->addColumn('category', function (Category $request){
                return mb_strtoupper($request->category);
            })
            ->addColumn('quantity', function (Category $request){
                $qty = Warehouse::where('category_id', $request->id)->count();
                return $qty;
            })
            ->make(true);
        }else{
            $items = Item::query()
                ->select('items.*', 'category')
                ->where('categories.id', $request->category)
                ->join('categories', 'category_id', '=', 'categories.id')
                ->get();

            $stock = Warehouse::select('warehouses.id as myid', 'items.UOM', 'warehouses.category_id','items_id', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stockIN'), \DB::raw('SUM(CASE WHEN status = \'sent\' THEN 1 ELSE 0 END) as stockOUT'))
                ->where('warehouses.category_id', $request->category)
                ->join('items', 'items_id', '=', 'items.id')
                ->groupBy('items_id')->get();

            return DataTables::of($items)
            ->addColumn('items_id', function (Item $request){
                return mb_strtoupper($request->id);
            })
            ->addColumn('category', function (Item $request){
                $cat = Category::find($request->category_id);
                return mb_strtoupper($cat->category);
            })
            ->addColumn('description', function (Item $request){
                return mb_strtoupper($request->item);
            })
            ->addColumn('StockIN', function (Item $request){
                $stockIN = Warehouse::query()->where('items_id', $request->id)
                    ->where('status', 'in')->count();
                $stockOUT = Warehouse::query()->where('items_id', $request->id)
                    ->where('status', 'sent')->count();
                return $stockIN+$stockOUT;
            })
            ->addColumn('StockOUT', function (Item $request){
                $stockOUT = Warehouse::query()->where('items_id', $request->id)
                    ->where('status', 'sent')->count();
                return $stockOUT;
            })
            ->addColumn('quantity', function (Item $request){
                $stockIN = Warehouse::query()->where('items_id', $request->id)
                    ->where('status', 'in')->count();
                return $stockIN;
            })
            ->addColumn('initial', function (Item $request){
                $initial = WarehouseInitial::query()->select('qty')->where('items_id', $request->id)->first();
                if (!$initial) {
                    $initial = new WarehouseInitial;
                    $initial->qty = 10;
                    $initial->items_id = $request->id;
                    $initial->save();
                }
                return $initial->qty;
            })
            ->addColumn('pending', function (Item $request){
                $initial = RequestedItem::query()
                    ->join('requests', 'requests.request_no', 'requested_items.request_no')
                    ->whereIN('requests.status', ['PENDING', 'SCHEDULED', 'PARTIAL PENDING', 'PARTIAL IN TRANSIT', 'IN TRANSIT', 'INCOMPLETE'])
                    ->where('requests.stat', '!=', 'COMPLETED')
                    ->where('requested_items.status','PENDING')
                    ->where('items_id', $request->id)->sum('requested_items.pending');
                return $initial;
            })
            ->make(true);
        }
    }

    public function repairshow(Request $request)
    {
        if ($request->data != 0) {
            $category = RepairCategory::query()->get();
            /*$stock = Warehouse::select('category_id', 'category', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as quantity'))
                ->join('categories', 'categories.id', '=', 'category_id')
                ->groupBy('category')
                ->get();*/
            return Datatables::of($category)
                ->addColumn('category_id', function (RepairCategory $request){
                    return mb_strtoupper($request->id);
                })
                ->addColumn('category', function (RepairCategory $request){
                    return mb_strtoupper($request->category);
                })
                ->addColumn('quantity', function (RepairCategory $request){
                    $qty = RepairStock::where('category_id', $request->id)
                            ->join('repair_items', 'repair_items.id', 'repair_stocks.item_id')
                            ->count();
                    return $qty;
                })
                ->make(true);
        }else{
            $items = RepairItem::query()
                ->select('repair_items.*', 'category')
                ->where('repair_categories.id', $request->category)
                ->join('repair_categories', 'category_id', '=', 'repair_categories.id')
                ->get();

            // $stock = Warehouse::select('warehouses.id as myid', 'repair_items.UOM', 'warehouses.category_id','repair_items_id', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stockIN'), \DB::raw('SUM(CASE WHEN status = \'sent\' THEN 1 ELSE 0 END) as stockOUT'))
            //     ->where('warehouses.category_id', $request->category)
            //     ->join('repair_items', 'items_id', '=', 'repair_items.id')
            //     ->groupBy('items_id')->get();

            return DataTables::of($items)
            ->addColumn('items_id', function (RepairItem $request){
                return mb_strtoupper($request->id);
            })
            ->addColumn('category', function (RepairItem $request){
                $cat = RepairCategory::find($request->category_id);
                return mb_strtoupper($cat->category);
            })
            ->addColumn('description', function (RepairItem $request){
                return mb_strtoupper($request->item);
            })
            ->addColumn('quantity', function (RepairItem $request){
                $stockIN = RepairStock::query()->where('item_id', $request->id)
                    ->where('status', 'in')->count();
                return $stockIN;
            })
            ->make(true);
        }
    }

    public function update(Request $request)
    {
        $update = Stock::where('id', $request->id)->first();
        $customer = CustomerBranch::where('id', $request->custid)->first();
        if ($update->updated_at >= Carbon::now()->subMinutes(15)) {
            $data = "bawal";
            return response()->json($data);
        }
        if ($request->stat == 'sunit') {
            $update->status = $request->status;
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $update->items_id)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $update->items_id;
            $defective->serial = mb_strtoupper($request->serial);
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "SERVICE IN - SERVICE UNIT $item->item(S/N: ".mb_strtoupper($request->serial).")(defective) from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
            return response()->json($data);
        }else if ($request->stat == 'replace') {
            $update->status = 'replacement';
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $request->ids)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $request->ids;
            $defective->serial = $request->serial;
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "SERVICE IN - REPLACEMENT $item->item(S/N: ".mb_strtoupper($request->serial).") from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
            return response()->json($data);
        }else if ($request->stat == 'replacement') {
            $item = Item::where('item', $request->ids)->first();
            if (!$item) {
                $data = "error";
                return response()->json($data);
            }
            $update->status = 'replacement';
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('item', $request->ids)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $item->id;
            $defective->serial = $request->serial;
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "SERVICE IN - REPLACEMENT $item->item(S/N: ".mb_strtoupper($request->serial).") from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
            return response()->json($data);
        }
    }
    public function PMupdate(Request $request)
    {
        $pm = Pm::where('id', $request->id)->first();
        $update = Stock::where('id', $pm->stocks_id)->first();
        $customer = CustomerBranch::where('id', $request->customerid)->first();
        if ($request->stat == 'sunit') {
            $update->status = $request->status;
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $update->items_id)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $update->items_id;
            $defective->serial = mb_strtoupper($request->serial);
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "PM SERVICE IN - SERVICE UNIT $item->item(S/N: ".mb_strtoupper($request->serial).")(defective) from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
            return response()->json($data);
        }else if ($request->stat == 'replace') {
            $update->status = 'replacement';
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $request->ids)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->category_id = $item->category_id;
            $defective->items_id = $request->ids;
            $defective->serial = mb_strtoupper($request->serial);
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "PM SERVICE IN - REPLACEMENT $item->item(S/N: ".mb_strtoupper($request->serial).") from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
            $pm->delete();
            return response()->json($data);
        }
    }

    public function verifyserial(Request $request)
    {
        $item = Stock::query()->where('serial', $request->serial)->where('Status', 'in')->first();
        $def = Defective::query()->where('serial', $request->serial)->where('Status', 'For return')->first();
        $prep = PreparedItem::query()->where('serial', $request->serial)->first();
        if ($item) {
            $data = "not allowed";
        }else if ($def) {
            $data = "not allowed";
        }else if ($prep) {
            $data = "not allowed";
        }else {
            $data = "allowed";
        }
        return response()->json($data);
    }
}