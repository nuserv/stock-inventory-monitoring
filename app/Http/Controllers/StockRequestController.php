<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Excel as BaseExcel;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\StockRequest;
use App\BufferNo;
use App\Buffer;
use App\Buffersend;
use App\RequestedItem;
use App\PreparedItem;
use App\Warehouse;
use App\Customer;
use App\CustomerBranch;
use App\Category;
use App\Item;
use App\Stock;
use App\Branch;
use App\User;
use App\Initial;
use App\UserLog;
use App\Defective;
use Illuminate\Support\Str;
use Mail;
use Auth;
class StockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        if (auth()->user()->hasanyrole('Repair', 'Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $title = 'Stock Request';
        $stocks = Warehouse::select('items_id', 'serial', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
            ->where('status', 'in')
            ->groupBy('items_id')->get();
        $categories = Category::all();
        return view('pages.stock-request', compact('stocks', 'categories', 'title'));
    }
    public function billable()
    {
        if (auth()->user()->hasanyrole('Repair', 'Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $title = 'Billable';
        $stocks = Warehouse::select('items_id', 'serial', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
            ->where('status', 'in')
            ->groupBy('items_id')->get();
        $categories = Category::all();
        return view('pages.billable', compact('stocks', 'categories', 'title'));
    }
    public function buffer(){
        $title = 'Buffer';
        return view('pages.buffer', compact('title'));
    }
    public function resolve()
    {
        if (auth()->user()->hasanyrole('Repair', 'Warehouse Administrator')) {
            return redirect('/');
        }
        $title = 'Stock Request';
        $stocks = Warehouse::select('items_id', 'serial', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
            ->where('status', 'in')
            ->groupBy('items_id')->get();
        $categories = Category::all();
        return view('pages.resolved', compact('stocks', 'categories', 'title'));
    }
    public function getItemCode(Request $request){
        $data = Item::select('id', 'item')->where('category_id', $request->id)->orderBy('item')->get();
        return response()->json($data);
    }
    public function getCode(Request $request){
        $initials = Initial::where('branch_id', auth()->user()->branch->id)
            ->join('items', 'items_id', '=', 'items.id')
            ->where('category_id', $request->id)
            ->orderBy('item')
            ->get();
        $icode = [];
        $itm =[];
        foreach ($initials as $initial) {
            $count = Stock::where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('items_id', $initial->items_id)
                ->count();
            if ($count < $initial->qty) {
                $itemcode = Item::select('id', 'item')->where('id', $initial->items_id)->first();
                if(!in_array($itemcode, $icode)){
                    array_push($icode, $itemcode);
                }
            }
        }
        return response()->json(array_filter($icode));
    }

    public function servicerequest(Request $request){
        $initials = Initial::where('branch_id', auth()->user()->branch->id)
            ->join('items', 'items_id', '=', 'items.id')
            ->where('category_id', $request->id)
            ->orderBy('item')
            ->get();
        $icode = [];
        $itm =[];
        foreach ($initials as $initial) {
            $count = Stock::where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('items_id', $initial->items_id)
                ->count();
            if ($count == "0") {
                $itemcode = Item::select('id', 'item')->where('id', $initial->items_id)->first();
                if(!in_array($itemcode, $icode)){
                    array_push($icode, $itemcode);
                }
            }
        }
        return response()->json(array_filter($icode));
    }
    public function getCatReq(Request $request){
        $catreqs = RequestedItem::select('categories.category', 'categories.id')
            ->where('request_no', $request->reqno)
            ->join('items', 'items.id', '=', 'requested_items.items_id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->get();
        return response()->json($catreqs);
    }
    public function getStock(Request $request){
        if (auth()->user()->branch->branch == 'Warehouse') {
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->groupBy('items_id')
                ->get();
        }else{
            $data = Stock::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->groupBy('items_id')
                ->get();
        }
        return response()->json($data);
    }
    public function getSerials(Request $request){
        if (auth()->user()->branch->branch == 'Warehouse') {
            $data = Warehouse::select('items_id', 'serial')
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->orderBy('serial')
                ->get();
        }else{
            $data = Stock::select('id', 'items_id', 'serial')
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->orderBy('serial')
                ->get();
        }
        return response()->json($data);
    }
    public function getcon(Request $request){
        $data = PreparedItem::select('id')->where('request_no', $request->reqno)->where('items_id', $request->itemsid)->get();
        return response()->json($data);
    }
    public function getintransitDetails(Request $request, $id){
        $consumable = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('request_no', $id)
            ->where('intransit', 'yes')
            ->whereNotin('uom', ['Unit'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(items_id) as quantity')
            ->groupBy('items_id')
            ->get();
        $unit = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('intransit', 'yes')
            ->where('request_no', $id)
            ->whereNotin('uom', ['Pc', 'Meter'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(prepared_items.id) as quantity')
            ->groupBy('prepared_items.id')
            ->get();
        $result = $unit->merge($consumable);
        return DataTables::of($result)
        ->addColumn('item_name', function (PreparedItem $PreparedItem){
            return mb_strtoupper($PreparedItem->items->item);
        })
        ->addColumn('serial', function (PreparedItem $PreparedItem){
            return mb_strtoupper($PreparedItem->serial);
        })
        ->addColumn('quantity', function (PreparedItem $PreparedItem){
            if ($PreparedItem->quantity != 1) {
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM.'s';
            }else{
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM;
            }
        })
        ->make(true);
    }
    public function getsendDetails(Request $request, $id){
        $consumable = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('intransit', 'no')
            ->where('request_no', $id)
            ->whereNotin('uom', ['Unit'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(items_id) as quantity')
            ->groupBy('items_id')
            ->get();
        $unit = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('intransit', 'no')
            ->where('request_no', $id)
            ->whereNotin('uom', ['Pc', 'Meter'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(prepared_items.id) as quantity')
            ->groupBy('prepared_items.id')
            ->get();
        $result = $unit->merge($consumable);
        return DataTables::of($result)
        ->addColumn('item_name', function (PreparedItem $PreparedItem){
            return mb_strtoupper($PreparedItem->items->item);
        })
        ->addColumn('serial', function (PreparedItem $PreparedItem){
            return mb_strtoupper($PreparedItem->serial);
        })
        ->addColumn('quantity', function (PreparedItem $PreparedItem){
            if ($PreparedItem->quantity != 1) {
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM.'s';
            }else{
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM;
            }
        })
        ->make(true);
    }
    public function generateRandomNumber() {
        $random = mt_rand(1, 999); 
        $today = Carbon::now()->format('d-m-Y');
        $number = $today.'-'.$random;
        if ($this->barcodeNumberExists($number)) {
            return generateBarcodeNumber();
        }
        return response()->json($number);
    }
    public function barcodeNumberExists($number) {
        return StockRequest::where('request_no', $number)->exists();
    }
    public function prepitemdetails(Request $request, $id)
    {
        return DataTables::of(PreparedItem::where('request_no', $id)->get())
        ->addColumn('item_name', function ($PreparedItem){

            return mb_strtoupper($PreparedItem->items->item);
        })
        ->make(true);
    }   
    public function getReqDetails(Request $request)
    {
        return response()->json(RequestedItem::whereNot('status', 'delivered')->where('request_no', $request->reqno)->get());
    }
    public function getuomq(Request $request)
    {
        return response()->json(RequestedItem::select('quantity', 'id')->where('request_no', $request->reqno)->where('items_id', $request->itemid)->first());
    }
    public function updateRequestDetails(Request $request, $id)
    {
        RequestedItem::where('request_no', $id)->where('items_id', $request->item)->decrement('pending', 1);
        $updated = RequestedItem::where('request_no', $id)->where('items_id', $request->item)->first();
        if ($updated->pending == 0) {
            $updated->status = 'COMPLETED';
            $data = $updated->save();
            return response()->json($data);
        }else{
            $data = $updated->status;
            return response()->json(true);
        }
    }
    public function getitems(Request $request)
    {
        $items = Warehouse::select('items_id', 'item')->where('warehouses.category_id', $request->catid)->where('Status', 'in')
            ->join('items', 'items.id', '=', 'items_id')->groupby('items_id')
            ->get();
        return response()->json($items);
    }
    public function requesteditems(Request $request)
    {
        $reqitems = RequestedItem::where('id', $request->id)->first();
        $item = Item::where('id', $reqitems->items_id)->first();

        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "DELETE requested $item->item.";
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $reqitems->delete();

        return response()->json($log);
    }
    public function getRequestDetails(Request $request, $id)
    {
        return DataTables::of(RequestedItem::where('request_no', $id)->where('pending', '!=', 0)->get())
        ->addColumn('item_name', function (RequestedItem $RequestedItem){
            return mb_strtoupper($RequestedItem->items->item);
        })
        ->addColumn('category', function (RequestedItem $RequestedItem){
            return mb_strtoupper($RequestedItem->items->category_id);
        })
        ->addColumn('cat_name', function (RequestedItem $RequestedItem){
            $category = Category::where('id', $RequestedItem->items->category_id)->first();
            return mb_strtoupper($category->category);
        })
        ->addColumn('cat_same', function (RequestedItem $RequestedItem){
            $category = Warehouse::where('status', 'in')->where('category_id', $RequestedItem->items->category_id)->count();
            return $category;
        })
        ->addColumn('uom', function (RequestedItem $RequestedItem){
            $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
            return $uom->uom;
        })
        ->addColumn('qty', function (RequestedItem $RequestedItem){
            if ($RequestedItem->pending != 1) {
                return $RequestedItem->pending. ' ' .$RequestedItem->items->UOM.'s';
            }else{
                return $RequestedItem->pending. ' ' .$RequestedItem->items->UOM;
            }
        })
        ->addColumn('validation', function (RequestedItem $RequestedItem){
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id',$RequestedItem->items->id)
                ->groupBy('items_id')
                ->first();
            if (!$data) {
                $stock = 0;
            }else{
                $stock = $data->stock;
            }
            if ($RequestedItem->quantity <= $stock) {
                return 'yes';
            }else{
                return 'no';
            }
        })
        ->addColumn('stock', function (RequestedItem $RequestedItem){
            $cat = Item::select('category_id','category')
                ->join('categories', 'categories.id', 'category_id')
                ->where('items.id', $RequestedItem->items->id)->first();
            $contains = Str::contains(strtolower($cat->category), 'kit');
            if ($contains) {
                $data = Warehouse::where('category_id', $cat->category_id)->where('status', 'in')->count();
                $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
                if ($data == 0) {
                    $stock = 0;
                }else{
                    $stock = $data;
                }
                return $stock;
            }

            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id',$RequestedItem->items->id)
                ->groupBy('items_id')
                ->first();
            if (!$data) {
                $stock = 0;
            }else{
                $stock = $data->stock;
            }
            return $stock;
        })
        ->addColumn('stockuom', function (RequestedItem $RequestedItem){
            $cat = Item::select('category_id','category')
                ->join('categories', 'categories.id', 'category_id')
                ->where('items.id', $RequestedItem->items->id)->first();
            $contains = Str::contains(strtolower($cat->category), 'kit');
            if ($contains) {
                $data = Warehouse::where('category_id', $cat->category_id)->where('status', 'in')->count();
                $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
                if ($data == 0) {
                    $stock = 0;
                }else{
                    $stock = $data;
                }
                if ($stock != 1) {
                    return $stock. ' ' . $uom->uom.'s';
                }else{
                    return $stock. ' ' . $uom->uom;
                }
            }
            $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id',$RequestedItem->items->id)
                ->groupBy('items_id')
                ->first();
            if (!$data) {
                $stock = 0;
            }else{
                $stock = $data->stock;
            }
            if ($stock != 1) {
                return $stock. ' ' . $uom->uom.'s';
            }else{
                return $stock. ' ' . $uom->uom;
            }
        })
        ->make(true);
    }   
    public function pcount(Request $request)
    {
        $stock = StockRequest::where('request_no', $request->reqno)
                ->first();
        return response()->json($stock);
    }
    public function getRequests()
    {
        $user = auth()->user()->branch->id;
        if (auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-Office'){
            $stock = StockRequest::wherein('status',  ['PARTIAL SCHEDULED', 'PARTIAL IN TRANSIT', 'PENDING', 'SCHEDULED', 'INCOMPLETE', 'RESCHEDULED', 'PARTIAL', 'IN TRANSIT'])
                ->where('stat', 'ACTIVE')
                ->where('branch_id', $user)
                ->get();
        }else if(auth()->user()->hasRole('Editor', 'Manager')){
            $stock = StockRequest::wherein('status',  ['PARTIAL SCHEDULED', 'PARTIAL IN TRANSIT', 'PENDING', 'SCHEDULED', 'INCOMPLETE', 'RESCHEDULED', 'UNRESOLVED', 'PARTIAL', 'IN TRANSIT'])
                ->where('stat', 'ACTIVE')
                ->get();
        }else{
            $stock = StockRequest::wherein('status',  ['PARTIAL SCHEDULED', 'PARTIAL IN TRANSIT', 'PENDING', 'SCHEDULED', 'INCOMPLETE', 'RESCHEDULED', 'UNRESOLVED', 'PARTIAL', 'IN TRANSIT'])
                ->where('stat', 'ACTIVE')
                ->get();
            //dd($stock);
        }
        return DataTables::of($stock)
        ->setRowData([
            'data-id' => '{{ $request_no }}',
            'data-status' => '{{ $status }}',
            'data-user' => '{{ $user_id }}',
        ])
        
        ->addColumn('sched', function (StockRequest $request){
            return $request->schedule;
        })
        ->addColumn('created_at', function (StockRequest $request){
            return Carbon::parse($request->created_at->toFormattedDateString().' '.$request->created_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('intransit', function (StockRequest $request){
            if ($request->intransit) {
                return Carbon::parse($request->intransit)->toFormattedDateString().' '.Carbon::parse($request->intransit)->toTimeString();
            }
        })
        ->addColumn('left', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInDays($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('leftcreatedhour', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->created_at)->addDays(1);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInHours($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('leftcreatedmin', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->created_at)->addDays(1);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInMinutes($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('minute', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInMinutes($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('hour', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInHours($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('reqBy', function (StockRequest $request){
            return mb_strtoupper($request->user->name);
        })
        ->addColumn('branch', function (StockRequest $request){
            return mb_strtoupper($request->branch->branch);
        })
        ->addColumn('customer', function (StockRequest $request){
            if ($request->type == 'Service') {
                $custname = CustomerBranch::where('id', $request->customer_branch_id)->first();
                return mb_strtoupper($custname->customer_branch);
            }else{
                return 'not urgent';
            }
        })
        ->addColumn('area', function (StockRequest $request){
            return mb_strtoupper($request->area->area);
        })
        ->addColumn('pending', function (StockRequest $request){
            return mb_strtoupper($request->pending);
        })
        ->addColumn('type', function (StockRequest $request){
            return mb_strtoupper($request->type);
        })
        ->addColumn('client', function (StockRequest $request){
            if ($request->type == "Service") {
                $client = Customer::select('customer')->where('id', $request->customer_id)->first()->customer;
            }else {
                $client = 'none';
            }
            return mb_strtoupper($client);
        })
        ->addColumn('customer', function (StockRequest $request){
            if ($request->type == "Service") {
                $customer = CustomerBranch::select('customer_branch')->where('id', $request->customer_branch_id)->first()->customer_branch;
            }else {
                $customer = 'none';
            }
            return mb_strtoupper($customer);
        })
        ->make(true);
    }
    public function getResolved()
    {
        $user = auth()->user()->branch->id;
       
        $stock = StockRequest::wherein('stat',  ['RESOLVED'])->get();
        return DataTables::of($stock)
        ->setRowData([
            'data-id' => '{{ $request_no }}',
            'data-status' => '{{ $status }}',
            'data-user' => '{{ $user_id }}',
        ])
        
        ->addColumn('sched', function (StockRequest $request){
            return $request->schedule;
        })
        ->addColumn('created_at', function (StockRequest $request){
            return $request->created_at->toFormattedDateString().' '.$request->created_at->toTimeString();
        })
        ->addColumn('intransit', function (StockRequest $request){
            if ($request->intransit) {
                return Carbon::parse($request->intransit)->toFormattedDateString().' '.Carbon::parse($request->intransit)->toTimeString();
            }
        })
        ->addColumn('left', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInDays($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('leftcreatedhour', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->created_at)->addDays(1);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInHours($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('leftcreatedmin', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->created_at)->addDays(1);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInMinutes($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('minute', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInMinutes($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('hour', function (StockRequest $request){
            //Carbon::now()->subDays($request->created_at))
            $dd = Carbon::parse($request->updated_at)->addDays(5);
            //$dd->year(date('Y'));
            return Carbon::now()->diffInHours($dd, false);//Carbon::now()->subDays(5);
        })
        ->addColumn('reqBy', function (StockRequest $request){
            return mb_strtoupper($request->user->name);
        })
        ->addColumn('branch', function (StockRequest $request){
            return mb_strtoupper($request->branch->branch);
        })
        ->addColumn('area', function (StockRequest $request){
            return mb_strtoupper($request->area->area);
        })
        ->addColumn('pending', function (StockRequest $request){
            return mb_strtoupper($request->pending);
        })
        ->addColumn('type', function (StockRequest $request){
            return mb_strtoupper($request->type);
        })
        ->addColumn('resolved_name', function (StockRequest $request){
            $name = User::where('id', $request->resolved_by)->first();
            return mb_strtoupper($name->name.' '.$name->lastname);
        })
        ->addColumn('client', function (StockRequest $request){
            if ($request->type == "Service") {
                $client = Customer::select('customer')->where('id', $request->customer_id)->first()->customer;
            }else {
                $client = 'none';
            }
            return mb_strtoupper($client);
        })
        ->addColumn('customer', function (StockRequest $request){
            if ($request->type == "Service") {
                $customer = CustomerBranch::select('customer_branch')->where('id', $request->customer_branch_id)->first()->customer_branch;
            }else {
                $customer = 'none';
            }
            return mb_strtoupper($customer);
        })
        ->make(true);
    }
    public function store(Request $request)
    {   
        if ($request->stat == 'ok') {
            $checkreq = StockRequest::where('request_no', $request->reqno)->first();
            if (!$checkreq) {
                $reqno = new StockRequest;
                $reqno->request_no = $request->reqno;
                $reqno->user_id = auth()->user()->id;
                $reqno->branch_id = auth()->user()->branch->id;
                $reqno->area_id = auth()->user()->area->id;
                $reqno->status = 'PENDING';
                $reqno->stat = 'ACTIVE';
                $reqno->customer_id = $request->clientid;
                $reqno->customer_branch_id = $request->customerid;
                $reqno->ticket = $request->ticket;
                $reqno->type = $request->type;
                if ($request->type == "Stock") {
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CREATE STOCK Request no. $request->reqno";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                }else{
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CREATE SERVICE STOCK Request no. $request->reqno";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                }
                $reqno->save();
            }else{
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "UPDATE Stock Request no. $request->reqno";
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            }
            $data = $log->save();
        }
        if ($request->stat == 'notok') {
            if ($request->check == "meron") {
                $meron = RequestedItem::query()->where('request_no', $request->reqno)->where('items_id', $request->item)->first();
                if ($meron) {
                    $meron->quantity = $meron->quantity+$request->qty;
                    $meron->pending = $meron->pending+$request->qty;
                    $data = $meron->save();
                }else{
                    $reqitem = new RequestedItem;
                    $reqitem->request_no = $request->reqno;
                    $reqitem->items_id = $request->item;
                    $reqitem->quantity = $request->qty;
                    $reqitem->pending = $request->qty;
                    $reqitem->branch_id = auth()->user()->branch->id;
                    $reqitem->status = 'PENDING';
                    $data = $reqitem->save();
                }
            }else {
                $reqitem = new RequestedItem;
                $reqitem->request_no = $request->reqno;
                $reqitem->items_id = $request->item;
                $reqitem->quantity = $request->qty;
                $reqitem->pending = $request->qty;
                $reqitem->status = 'PENDING';
                $data = $reqitem->save();
            }
        }
        return response()->json($data);
    }


    public function resolved(Request $request)
    {
        $resolve = StockRequest::where('request_no', $request->requestno)->first();
        $resolve->remarks = $request->remarks;
        $resolve->stat = 'RESOLVED';
        $resolve->resolve_by = auth()->user()->id;
        $data = $resolve->save();
        return response()->json($data);
    }

    public function prepitem(Request $request)
    {
        $preparedItem = PreparedItem::where('branch_id', $request->branchid)
            ->where('request_no', $request->reqno)
            ->first();

        if ($preparedItem) {
            $data = '1';
        }else{
            $data = '0';
        }
        return response()->json($data);
    }
    public function checkrequest(Request $request)
    {
        $requestno = StockRequest::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'PENDING')
            ->where('type', 'Stock')
            ->where('stat', 'ACTIVE')
            ->where( 'created_at', '>', Carbon::now()->subDays(7))
            ->first();
        if ($requestno) {
            $data = $requestno->request_no;
        }else{
            $data = "wala pa";
        }
        return response()->json($data);
    }

    public function checkbuffer(Request $request)
    {
        $requestno = StockRequest::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'PENDING')
            ->where('type', 'Stock')
            ->where('stat', 'ACTIVE')
            ->where( 'created_at', '>', Carbon::now()->subDays(7))
            ->first();
        if ($requestno) {
            $data = $requestno->request_no;
        }else{
            $data = "wala pa";
        }
        return response()->json($data);
    }
    public function checkrequestitem(Request $request)
    {
        $requestno = RequestedItem::where('items_id', $request->items_id)
            ->where('branch_id', auth()->user()->branch->id)
            ->where('request_no', $request->reqno)
            ->first();
        if ($requestno) {
            $data = 'meron';
        }else{
            $data = "wala pa";
        }
        return response()->json($data);
    }
    public function received(Request $request)
    {
        $data = '0';
        if ($request->Unit == 'yes') {
            foreach ($request->id as $del) {
                $preparedItems = PreparedItem::select('prepared_items.items_id as itemid', 'prepared_items.serial as serial')
                    ->join('items', 'prepared_items.items_id', '=', 'items.id')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('request_no', $request->reqno)
                    ->where('prepared_items.id', $del)
                    ->first();
                $prepared = PreparedItem::where('branch_id', auth()->user()->branch->id)
                    ->where('request_no', $request->reqno)
                    ->where('prepared_items.id', $del)
                    ->first();
                $stockreq = StockRequest::where('request_no', $request->reqno)->first();
                $items = Item::where('id', $preparedItems->itemid)->first();
                $stock = new Stock;
                $stock->category_id = $items->category_id;
                $stock->branch_id = auth()->user()->branch->id;
                $stock->items_id = $preparedItems->itemid;
                $stock->user_id = auth()->user()->id;
                $stock->serial = mb_strtoupper($preparedItems->serial);
                $stock->status = 'in';
                $stock->save();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RECEIVED $items->item(S/N: ".mb_strtoupper($preparedItems->serial).") with Request no. $request->reqno ";
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                if ($stockreq->type == "Service") {
                    $log->service = 'yes';
                    if (str_contains($stockreq->ticket, 'IDS')) {
                        $log->company = 'IDSI';
                    }else if (str_contains($stockreq->ticket, 'PLS')) {
                        $log->company = 'PLSI';
                    }else if (str_contains($stockreq->ticket, 'APS')) {
                        $log->company = 'APSOFT';
                    }
                }
                $log->save();
                $prepared->delete();
            }
        }else if ($request->Unit == 'no') {
            $count = 0;
            foreach ($request->id as $del) {
                $count += 1;
                $preparedItems = PreparedItem::select('prepared_items.items_id as itemid', 'prepared_items.serial as serial')
                    ->join('items', 'prepared_items.items_id', '=', 'items.id')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('request_no', $request->reqno)
                    ->where('prepared_items.id', $del)
                    ->first();
                $prepared = PreparedItem::where('branch_id', auth()->user()->branch->id)
                    ->where('request_no', $request->reqno)
                    ->where('prepared_items.id', $del)
                    ->first();
                $items = Item::where('id', $preparedItems->itemid)->first();
                $stock = new Stock;
                $stock->category_id = $items->category_id;
                $stock->branch_id = auth()->user()->branch->id;
                $stock->items_id = $preparedItems->itemid;
                $stock->user_id = auth()->user()->id;
                $stock->serial = mb_strtoupper($preparedItems->serial);
                $stock->status = 'in';
                $stock->save();
                $prepared->delete();
            }
            if ($count > 1) {
                $pcs = $count.' pcs.';
            }else{
                $pcs = $count.' pc.';
            }
            
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "RECEIVED $items->item($pcs) with Request no. $request->reqno ";
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
        }
        
        $preparedItem = PreparedItem::where('branch_id', auth()->user()->branch->id)
            ->where('request_no', $request->reqno)
            ->where('intransit', 'yes')
            ->first();
        if ($request->status == "COMPLETED") {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            if ($preparedItem) {
                $reqno->status = 'INCOMPLETE';
            }else{
                $reqno->stat = $request->status;
            }
        }
        if ($request->status  == "PARTIAL IN TRANSIT") {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $reqpending = RequestedItem::where('request_no', $request->reqno)->where('pending', '!=', '0')->first();
            if ($preparedItem) {
                $reqno->status = $request->status;
            }else{
                if ($reqpending) {
                    $reqno->status = 'PENDING';
                    $reqno->intransitval = '0';
                }else{
                    $reqno->stat = 'COMPLETED';
                }
            }  
        }
        $reqno->save();
        $data = '1';
        return response()->json($data);
        /*if ($preparedItem) {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            if ($reqno->status == 'PARTIAL IN TRANSIT') {
                $reqno->status = $request->status;
            }else{
                $reqno->status = 'INCOMPLETE';
            }
            $reqno->save();
            $data = '1';
        }else{
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            if ($reqno->status == 'PARTIAL IN TRANSIT') {
                $reqno->status = 'PARTIAL';
            }
            $reqno->stat = $request->status;
            $reqno->save();
        }*/
    }
    public function test(Request $request)
    {
        StockRequest::where('status', 4)->where( 'updated_at', '<', Carbon::now()->subDays(5))->update(['status' => 6]);
    }
    public function notreceived(Request $request)
    {
        $notrec = StockRequest::where('request_no', $request->reqno)->first();
        $notrec->status = "INCOMPLETE";
        $data = $notrec->save();
        return response()->json($data);
    }
    public function intransit(Request $request)
    {

        if($request->status == 'IN TRANSIT'){
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $reqno->status = $request->status;
            $reqno->intransit = Carbon::now()->toDateTimeString();;
            $intransits = PreparedItem::where('request_no', $request->reqno)->get();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "IN TRANSIT Request no. $request->reqno ";
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();

            PreparedItem::where('request_no', $request->reqno)->where('intransit', 'no')->update(['intransit' => 'yes']);
            $data = $reqno->save();
        }else if ($request->status == 'PARTIAL IN TRANSIT') {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $reqno->status = $request->status;
            $reqno->intransitval = '0';
            $reqno->intransit = Carbon::now()->toDateTimeString();
            $intransits = PreparedItem::where('request_no', $request->reqno)->get();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "PARTIAL IN TRANSIT Request no. $request->reqno ";
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
            PreparedItem::where('request_no', $request->reqno)->where('intransit', 'no')->update(['intransit' => 'yes']);
            $data = $reqno->save();
        }
        $no = $request->reqno;
        $branch = Branch::where('id', $reqno->branch_id)->first();
        $head = User::whereHas('roles', function($role) {
            $role->where('name', '=', 'Head');
        })->where('branch_id', $reqno->branch_id)->where('status', 1)->first();
        $bcc = \config('email.bcc');
        $excel = Excel::raw(new ExcelExport($request->reqno, 'DSR'), BaseExcel::XLSX);
        $data = array('office'=> $branch->branch, 'return_no'=>$request->reqno, 'dated'=>Carbon::now()->toDateTimeString());
        Mail::send('del', $data, function($message) use($excel, $no, $bcc, $head) {
            $message->to(auth()->user()->email, auth()->user()->name)->subject
                ('DR no. '.$no);
            $message->attachData($excel, 'DR No. '.$no.'.xlsx');
            $message->from('noreply@ideaserv.com.ph', 'BSMS');
            $message->bcc($bcc);
            $message->cc($head->email);

        });
        return response()->json($data);
    }
    public function upserial(Request $request)
    {   
        if ($request->new == "N/A") {
            $check = Stock::where('serial', 'walangserial')->where('status', 'in')->first();
            $checks = Defective::where('serial', 'walangserial')->where('status', 'For return')->first();
        }else{
            $check = Stock::where('serial', $request->new)->where('status', 'in')->first();
            $checks = Defective::where('serial', $request->new)->where('status', 'For return')->first();
        }
        if ($check) {
            $data = 'meron';
        }else if ($checks) {
            $data = 'meron';
        }else{
            $serial = PreparedItem::where('serial', $request->old)->where('request_no', $request->reqno)->where('items_id', $request->items_id)
            ->join('items', 'items.id', '=', 'items_id')
            ->first();
            $new = PreparedItem::where('serial', $request->old)->where('request_no', $request->reqno)->where('items_id', $request->items_id)->first();
            $new->serial = $request->new;
            $new->save();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "CHANGE $serial->item serial number from ".mb_strtoupper($serial->serial)." to ".mb_strtoupper($new->serial).".";
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $data = $log->save();
        }
        return response()->json($data);

    }
    public function update(Request $request)
    {
        if ($request->stat == 'ok') {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $schedby = StockRequest::query()->where('request_no', $request->reqno)->first();
            if ($reqno->status == 'PARTIAL IN TRANSIT') {
                $reqno->intransitval = '1';
            }else {
                if ($request->status == "PARTIAL SCHEDULED") {
                    $reqno->intransitval = '1';
                }else {
                    $reqno->intransitval = '0';
                }
            }
            if ($request->status == "COMPLETED" || $request->status == "RECOMPLETED") {
                $reqno->stat = 'COMPLETED';
            }else {
                $reqno->status = $request->status;
            }
            $reqno->schedule = $request->datesched;
            $reqno->schedby = auth()->user()->name.' '.auth()->user()->lastname;
            $reqno->save();
            $branch = StockRequest::query()->where('request_no', $request->reqno)
                ->join('branches', 'branches.id', 'branch_id')->first()->branch;
            if ($schedby->schedby) {
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "UPDATE PARTIAL SCHEDULED DELIVERY for $branch with Request no. $request->reqno ";
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            }else{
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "SCHEDULED DELIVERY for $branch with Request no. $request->reqno ";
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            }
            $data = $log->save();

            /*$prepitem = PreparedItem::select('items.item', 'serial', 'branch_id')
                ->where('request_no', $request->reqno)
                ->join('items', 'items.id', '=', 'prepared_items.items_id')
                ->get();
            $reqbranch = PreparedItem::select('branch_id')
                ->where('request_no', $request->reqno)
                ->first();
            //$branch = Branch::where('id', $request->branchid)->first();
            //$email = $branch->email;
            /*Mail::send('sched', ['prepitem' => $prepitem, 'sched'=>$request->datesched,'reqno' => $request->reqno,'branch' =>$branch],function( $message) use ($branch, $email){ 
                $message->to($email, $branch->head)->subject 
                    (auth()->user()->branch->branch); 
                $message->from('no-reply@ideaserv.com.ph', 'NO REPLY - Warehouse'); 
                $message->cc(['emorej046@gmail.com', 'gerard.mallari@gmail.com']); 
            });*/
            
        }else if($request->stat == 'resched'){
            if ($request->status == 'RESCHEDULED') {
                $reqno = StockRequest::where('request_no', $request->reqno)->first();
                $reqno->status = $request->status;
                $reqno->schedule = $request->datesched;
                PreparedItem::where('request_no', $request->reqno)->update(['schedule' => $request->datesched]);
                PreparedItem::where('request_no', $request->reqno)->update(['intransit' => 'no']);
                $branch = StockRequest::query()->where('request_no', $request->reqno)
                ->join('branches', 'branches.id', 'branch_id')->first()->branch;
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RESCHEDULED delivery for $branch with Request no. $request->reqno ";
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $data = $reqno->save();
            }else if($request->status == 'UNRESOLVED') {
                $reqno = StockRequest::where('request_no', $request->reqno)->first();
                $reqno->status = $request->status;
                $data = $reqno->save();
            }
        }else{
            $item = Warehouse::where('status', 'in')
                ->where('items_id', $request->item)
                ->first();
                $item->status = 'sent';
                $item->request_no = $request->reqno;
                $item->branch_id = $request->branchid;
                $item->schedule = $request->datesched;
                $item->serial = mb_strtoupper($request->serial);
                $item->user_id = auth()->user()->id;
                $item->save();
                $prep = new PreparedItem;
                $prep->items_id = $request->item;
                $prep->request_no = $request->reqno;
                $prep->serial = mb_strtoupper($request->serial);
                $prep->branch_id = $request->branchid;
                $prep->schedule = $request->datesched;
                $prep->intransit = 'no';
                $prep->user_id = auth()->user()->id;
                $data = $prep->save();          
        }
        return response()->json($data);
    }
    
    public function dest(Request $request)
    {
        $delete = StockRequest::where('request_no', $request->reqno)->where('status', 'PENDING')->first();
        $delete->status = 'DELETED';
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
        $log->activity = "DELETE request no. $request->reqno" ;
        $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $data = $delete->save();
        return response()->json($data);
    }
    public function bufferviewlist()
    {
        $title = 'Buffer Request list';
        return view('pages.warehouse.buffer', compact('title'));
    }
    public function bufferupdate(Request $request)
    {
        if ($request->send == 1) {
            $buffer = Buffer::query()
            ->where('status', 'request')
            ->update(['status' => 'For approval', 'buffers_no' => $request->retno]);
            $buffer = new BufferNo;
            $buffer->user_id = auth()->user()->id;
            $buffer->status = 'For approval';
            $buffer->buffers_no = $request->retno;
            $buffer->save();
            $bcc = \config('email.bcc');
            $no = $buffer->buffers_no;
            $table = Buffer::query()->select('category', 'item', 'qty')
                ->where('buffers_no', $request->retno)
                ->where('status', 'For approval')
                ->join('categories', 'categories.id', 'buffers.category_id')
                ->join('items', 'items.id', 'buffers.items_id')
                ->orderBy('category', 'ASC')
                ->orderBy('item', 'ASC')
                ->get()->all();
            //$excel = Excel::raw(new ExcelExport($buffer->buffers_no, 'PR'), BaseExcel::XLSX);
            $clients = User::whereHas('roles', function($role) {
                $role->where('name', '=', 'Warehouse Administrator');
            })->first();
            $data = array('table'=> $table, 'RM'=>$clients->name, 'reference'=>$no, 'role'=>'rm');
            Mail::send('buffer', $data, function($message) use($no, $bcc) {
                $message->to('jolopez@ideaserv.com.ph', auth()->user()->name)->subject
                    ('BR no. '.$no);
                //$message->attachData($excel, 'BR No. '.$no.'.xlsx');
                $message->from('noreply@ideaserv.com.ph', 'BSMS');
            });

            return response()->json($buffer);
        }
    }
    public function bufferreceived(Request $request)
    {
        $count = Buffersend::where('buffers_no', $request->buffers_no)
            ->where('items_id', $request->items_id)
            ->where('status', 'For receiving')->count();
        Buffersend::where('buffers_no', $request->buffers_no)
            ->where('items_id', $request->items_id)
            ->where('status', 'For receiving')->update(['status'=>'Received']);
        for ($i=0; $i < $count ; $i++) { 
            Warehouse::create([
                'user_id' => auth()->user()->id,
                'category_id' => $request->category_id,
                'items_id' => $request->items_id,
                'status' => 'in',
            ]);
        }
        $go = 'not ok';
        $check = Buffersend::query()->where('buffers_no', $request->buffers_no)->where('status', 'For receiving')->first();
        if (!$check) {
            BufferNo::query()->where('buffers_no', $request->buffers_no)->where('status', 'For receiving')->update(['status'=> 'Received']);
            $go = 'ok';
        }
        if ($count > 1) {
            $itemcount = $count.'pcs.';
        }else{
            $itemcount = $count.'pc.';
        }
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "RECEIVED $itemcount $request->item from Main Warehouse." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        return response()->json($go);

    }
    public function buffersenditems(Request $request)
    {
        $buffer = Buffersend::query()->select('buffersend.*', 'item', 'category','category_id')
                ->where('buffers_no', $request->buffers_no)
                ->where('status', 'For receiving')
                ->join('items', 'items.id', 'items_id')
                ->join('categories', 'categories.id', 'category_id')
                ->groupBy('items_id');
        return DataTables::of($buffer)
            ->addColumn('updated_at', function (Buffersend $buffer){
                return Carbon::parse($buffer->updated_at->toFormattedDateString().' '.$buffer->updated_at->toTimeString())->isoFormat('lll');
            })
            ->addColumn('qty', function (Buffersend $buffer){
                $qty = Buffersend::query()->where('status', 'For receiving')->where('buffers_no', $buffer->buffers_no)->where('items_id', $buffer->items_id)->count();
                return $qty;
            })
            ->addColumn('item', function (Buffersend $buffer){
                
                return strtoupper($buffer->item);
            })
            ->make(true);
        
    }
    public function buffersend(Request $request)
    {
        $buffer = Buffer::query()
                    ->where('buffers_no', $request->buffers_no)
                    ->where('items_id', $request->items_id)->decrement('pending', $request->qty);
        for ($i=0; $i < $request->qty ; $i++) { 
            $buffersend = new Buffersend;
            $buffersend->user_id = auth()->user()->id;
            $buffersend->items_id = $request->items_id;
            $buffersend->status = 'For receiving';
            $buffersend->buffers_no = $request->buffers_no;
            $buffersend->save();
        }
        $item = Item::query()->where('id', $request->items_id)->first();
        $check = Buffer::query()
                    ->where('buffers_no', $request->buffers_no)
                    ->where('pending', '!=', 0)->first();
            
        if ($check) {
            BufferNo::query()
                ->where('buffers_no', $request->buffers_no)
                ->update(['status'=>'Partial']);
        }else{
            BufferNo::query()
                ->where('buffers_no', $request->buffers_no)
                ->update(['status'=>'For receiving']);
        }
        if ($request->qty > 1) {
            $itemcount = $request->qty.'pcs.';
        }else{
            $itemcount = $request->qty.'pc.';
        }
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "DELIVERED $itemcount $item->item to Warehouse." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        return response()->json(true);
        
    }
    public function bufferstore(Request $request)
    {
        $item = Item::where('id', $request->item)->first();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "ADD $item->item to buffer stock request." ;
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $check = Buffer::query()->where('items_id', $item->id)->where('status', 'request')->first();
        if ($check) {
            $check->qty = $check->qty+$request->qty;
            $check->user_id = auth()->user()->id;
            $data = $check->save();
        }else{
            $buffer = new Buffer;
            $buffer->user_id = auth()->user()->id;
            $buffer->category_id = $item->category_id;
            $buffer->pending = $request->qty;
            $buffer->items_id = $request->item;
            $buffer->qty = $request->qty;
            $buffer->status = 'request';
            $data = $buffer->save();
        }
        return response()->json($data);
    }
    public function bufferdelete(Request $request)
    {
        $item = Buffer::query()->where('id',$request->reqid)->first();
        $item->delete();
        return response()->json($item);
    }
    public function bufferget(Request $request)
    {
        if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder')) {
            $buffer = Buffer::query()
                ->select('category', 'item', 'buffers.updated_at', 'qty', 'buffers.created_at', 'buffers.id as reqid')
                ->join('categories', 'categories.id', 'buffers.category_id')
                ->join('items', 'items.id', 'items_id')
                ->wherein('buffers.status', ['request'])
                ->get();
            return DataTables::of($buffer)
                ->addColumn('updated_at', function (Buffer $buffer){
                    return Carbon::parse($buffer->updated_at->toFormattedDateString().' '.$buffer->updated_at->toTimeString())->isoFormat('lll');
                })
                ->addColumn('item', function (Buffer $buffer){
                    return strtoupper($buffer->item);
                })
                ->make(true);
        }
    }
    public function bufferitem(Request $request)
    {
        $buffers = Buffer::query()
            ->select('buffers.id', 'category', 'item', 'qty', 'buffers.category_id as cat_id', 'items_id', 'pending')
            ->join('categories', 'categories.id', 'buffers.category_id')
            ->join('items', 'items.id', 'items_id')
            ->where('pending', '!=', 0)
            ->wherein('status', ['For approval', 'Approved'])
            ->where('buffers_no', $request->buffers_no)
            ->get();
        return DataTables::of($buffers)
        
        ->addColumn('item', function (Buffer $buffer){
            return strtoupper($buffer->item);
        })
        ->make(true);
    }
    public function bufferapproved(Request $request)
    {
        BufferNo::where('status', 'For approval')->where('buffers_no', $request->buffers_no)->update(['status' => 'Approved']);
        Buffer::where('status', 'For approval')->where('buffers_no', $request->buffers_no)->update(['status'=>'Approved']);
        $buffer = Buffer::where('status', 'Approved')->where('buffers_no', $request->buffers_no)->get()->all();
        $bcc = \config('email.bcc');
        $no = $request->buffers_no;
        $table = Buffer::query()->select('category', 'item', 'qty')
            ->where('buffers_no', $request->buffers_no)
            ->where('status', 'Approved')
            ->join('categories', 'categories.id', 'buffers.category_id')
            ->join('items', 'items.id', 'buffers.items_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->get()->all();
        //$excel = Excel::raw(new ExcelExport($buffer->buffers_no, 'PR'), BaseExcel::XLSX);
        $clients = User::whereHas('roles', function($role) {
            $role->where('name', '=', 'Main Warehouse Manager');
        })->first();
        $data = array('table'=> $table, 'RM'=>'test', 'reference'=>$no, 'role'=>'mwm');
        Mail::send('buffer', $data, function($message) use($no, $bcc) {
            $message->to('jolopez@ideaserv.com.ph', auth()->user()->name)->subject
                ('BR no. '.$no);
            //$message->attachData($excel, 'BR No. '.$no.'.xlsx');
            $message->from('noreply@ideaserv.com.ph', 'BSMS');
        });

        return response()->json(true);

    }
    public function bufferlist(Request $request)
    {
        if (auth()->user()->hasanyrole('Warehouse Manager', 'Warehouse Administrator') || auth()->user()->id == 228 || auth()->user()->id == 110) {
            $buffer = BufferNo::query()
                ->wherein('status', ['For approval', 'Approved', 'Partial', 'For receiving'])
                ->get();
            return DataTables::of($buffer)
                ->addColumn('updated_at', function (BufferNo $buffer){
                    return Carbon::parse($buffer->updated_at->toFormattedDateString().' '.$buffer->updated_at->toTimeString())->isoFormat('lll');
                })
                ->addColumn('user', function (BufferNo $buffer){
                    return strtoupper(User::query()->where('id', $buffer->user_id)->first());
                })
                ->make(true);
        }
        if (auth()->user()->hasanyrole('Main Warehouse Manager')) {
            $buffer = BufferNo::query()
                ->wherein('status', ['Approved', 'Partial', 'For receiving', 'For Approval'])
                ->get();
            return DataTables::of($buffer)
                ->addColumn('updated_at', function (BufferNo $buffer){
                    return Carbon::parse($buffer->created_at->toFormattedDateString().' '.$buffer->created_at->toTimeString())->isoFormat('lll');
                })
                ->addColumn('user', function (BufferNo $buffer){
                    return strtoupper(User::query()->where('id', $buffer->user_id)->first()->name.' '.User::query()->where('id', $buffer->user_id)->first()->lastname);
                })
                ->make(true);
        }
    }
    public function checkserial(Request $request)
    {
        if ($request->type == 'na') {
            $item = Item::where('id', $request->item)->first();
            if ($item->n_a == "yes") {
                $data = "allowed";
            }else {
                $data = "not allowed";
            }
        }else{
            $stock = Stock::where('serial', $request->serial)->where('status', 'in')->first();
            $def = Defective::where('serial', $request->serial)->wherein('status', ['For return', 'For add stock', 'For receiving', 'For repair', 'Repaired'])->first();
            $meron = 0;
            $checks = 'wala';
            if ($request->reqno) {
                $checks = PreparedItem::query()->where('request_no', $request->reqno)->get();
                foreach ($checks as $check) {
                    if ($check->serial != 'N/A') {
                        $stock = Stock::where('serial', $check->serial)->where('status', 'in')->first();
                        $defs = Defective::where('serial', $check->serial)->wherein('status', ['For return', 'For add stock', 'For receiving', 'For repair', 'Repaired'])->first();
                        if ($defs) {
                            $meron = 1;
                            $serial = $check->serial;
                        }else if ($stock) {
                            $meron = 1;
                            $serial = $check->serial;
                        }
                    }else{
                        $meron = 0;
                    }
                }
            }
            if ($checks != 'wala') {
                if ($checks){
                    if($meron == 1){
                        $data = ['data' =>"not allowed", 'serial'=>$serial];
                    }else{
                        $data = ['data' =>"allowed"];
                    }
                }
            }else if ($stock) {
                $data = "not allowed";
            }else if ($def) {
                $data = ['data'=>"not allowed", 'retno'=>$def->return_no];
            }else{
                $data = "allowed";
            }
        }
        
        return response()->json($data);
    }
}