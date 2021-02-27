<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Warehouse;
use App\Item;
use App\Category;
use App\Stock;
use App\PreparedItem;
use App\CustomerBranch;
use App\Customer;
use App\Pullout;
use App\Loan;
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
        $this->middleware('auth');
    }
    public function index()
    {
        if (auth()->user()->hasanyrole('Viewer', 'Repair')) {
            return redirect('/');
        }
        $title = 'Stocks';
        $categories = Category::all();
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
            ->where('id', '!=', auth()->user()->branch->id)
            ->get();
        return view('pages.stocks', compact('categories', 'service_units', 'customers', 'branches', 'title'));
    }
    public function category(Request $request){
        $cat = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('stocks.status', 'service unit')
            ->where('customer_branches_id', $request->id)
            ->join('customer_branches', 'stocks.customer_branches_id', '=', 'customer_branches.id')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->join('customers', 'customer_branches.customer_id', '=', 'customers.id')
            ->get();
        return response()->json($cat);
    }
    public function bcategory(Request $request){
        $cat = Stock::select('stocks.category_id', 'categories.category')
            ->where('stocks.branch_id', $request->id)
            ->where('stocks.status', 'in')
            ->join('categories', 'stocks.category_id', '=', 'categories.id')
            ->groupBy('stocks.category_id')
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
            ->get();
        return response()->json($item);
    }
    public function bserial($id){
        $serial = Stock::select('items.item', 'stocks.*')
            ->where('stocks.branch_id', auth()->user()->branch->id)
            ->where('stocks.items_id', $id)
            ->where('stocks.status', 'in')
            ->join('items', 'stocks.items_id', '=', 'items.id')
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
            ->join('items', 'stocks.items_id', '=', 'items.id')
            ->get();
        return response()->json($serial);
    }
    public function service()
    {
        $title = "Service Unit";
        $categories = Category::all();
        if (auth()->user()->hasrole('Administrator')) {
            return redirect('/');
        }
        return view('pages.service-unit', compact('title', 'categories'));
    }
    public function serviceUnit()
    {
        $stock = Stock::where('status', 'service unit')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->get();
        return DataTables::of($stock)
        ->addColumn('date', function (Stock $request){
            return $request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString();
        })
        ->addColumn('items_id', function (Stock $request){
            return $request->items_id;
        })
        ->addColumn('category', function (Stock $request){
            $cat = Category::find($request->category_id);
            return strtoupper($cat->category);
        })
        ->addColumn('description', function (Stock $request){
            $item = Item::where('id', $request->items_id)->first();
            return strtoupper($item->item);
        })
        ->addColumn('serial', function (Stock $request){
            return strtoupper($request->serial);
        })
        ->addColumn('client', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(strtolower($client->customer.' - '.$client->customer_branch));
        })
        ->addColumn('client_name', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(strtolower($client->customer));
        })
        ->addColumn('customer_name', function (Stock $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(strtolower($client->customer_branch));
        })
        ->addColumn('serviceby', function (Stock $request){
            $user = User::select('name', 'lastname')->where('id', $request->user_id)->first();
            return ucwords(strtolower($user->name.' '.$user->lastname));
        })
        ->make(true);
    }
    public function pmserviceUnit()
    {
        $stock = Pm::where('branch_id', auth()->user()->branch->id)->get();
        return DataTables::of($stock)
        ->addColumn('date', function (Pm $request){
            return $request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString();
        })
        ->addColumn('items_id', function (Pm $request){
            return $request->items_id;
        })
        ->addColumn('category', function (Pm $request){
            $cat = Category::find($request->category_id);
            return strtoupper($cat->category);
        })
        ->addColumn('description', function (Pm $request){
            $item = Item::where('id', $request->items_id)->first();
            return strtoupper($item->item);
        })
        ->addColumn('serial', function (Pm $request){
            return strtoupper($request->serial);
        })
        ->addColumn('client', function (Pm $request){
            $clients = '';
            foreach (explode(',', $request->customer_ids) as $customer) {
                if ($clients == "") {
                    $client = CustomerBranch::select('customer_branch', 'customers.customer')
                        ->where('customer_branches.id', $customer)
                        ->join('customers', 'customer_id', '=', 'customers.id')
                        ->first();
                    $clients = ucwords(strtolower($client->customer.' - '.$client->customer_branch));
                }else{
                    $client = CustomerBranch::select('customer_branch', 'customers.customer')
                        ->where('customer_branches.id', $customer)
                        ->join('customers', 'customer_id', '=', 'customers.id')
                        ->first();
                    $clients = $clients.', '.ucwords(strtolower($client->customer.' - '.$client->customer_branch));
                }
            }
            return $clients;
        })
        /*->addColumn('client_name', function (Pm $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(strtolower($client->customer));
        })
        ->addColumn('customer_name', function (Pm $request){
            $client = CustomerBranch::select('customer_branch', 'customers.customer')
                ->where('customer_branches.id', $request->customer_branches_id)
                ->join('customers', 'customer_id', '=', 'customers.id')
                ->first();
            return ucwords(strtolower($client->customer_branch));
        })*/
        ->addColumn('serviceby', function (Pm $request){
            $user = User::select('name', 'lastname')->where('id', $request->user_id)->first();
            return ucwords(strtolower($user->name.' '.$user->lastname));
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
            return strtoupper($desc->item);
        })
        ->addColumn('branch', function (PreparedItem $PreparedItem){
            $branchname = Branch::where('id', $PreparedItem->branch_id)->first();
            return strtoupper($branchname->branch);
        })
        ->addColumn('user', function (PreparedItem $PreparedItem){
            $username = User::where('id', $PreparedItem->user_id)->first();
            return $username->name.' '.$username->lastname;
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
            $stock = Stock::select('category_id', 'category', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as quantity'))
                ->where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->join('categories', 'category_id', '=', 'categories.id')
                ->groupBy('category')
                ->get();
            return DataTables::of($stock)
            ->addColumn('category', function (Stock $stock){
                return strtoupper($stock->category);
            })
            ->make(true);
        }else{
            $stock = Stock::select('UOM','categories.category', 'stocks.items_id as items_id', 'items.item as description', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as quantity'))
                ->where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('categories.id', $request->category)
                ->join('categories', 'stocks.category_id', '=', 'categories.id')
                ->join('items', 'stocks.items_id', '=', 'items.id')
                ->groupBy('items_id')->get();
            return DataTables::of($stock)
            ->addColumn('description', function (Stock $stock){
                return strtoupper($stock->description);
            })
            ->make(true);
        }
    }
    public function checkStocks(Request $request)
    {
        $initials = Initial::where('branch_id', auth()->user()->branch->id)->get();
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
    public function addItem(Request $request)
    {
        $add = new Item;
        $add->category_id = $request->cat;
        $add->item = ucfirst($request->item);
        $add->UOM = ucfirst($request->uom);
        $add->save();
        $branches = Branch::all();
        foreach ($branches as $branchs) {
            $initial = new Initial;
            $initial->items_id = $add->id;
            $initial->branch_id = $branchs->id;
            $initial->qty = 1;
            $data = $initial->save();
        }
        return response()->json($data);
    }
    public function servicein(Request $request)
    {
        $stock = Stock::where('id', $request->id)->first();
        $item = Item::where('id', $stock->items_id)->first();
        $customer = CustomerBranch::where('id', $stock->customer_branches_id)->first();
        if ($request->status == 'defective') {
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->id;
            $defective->items_id = $stock->items_id;
            $defective->status = 'For return';
            $defective->serial = $stock->serial;
            $defective->category_id = $stock->category_id;
            $defective->save();
            $log = new UserLog;
            $log->activity = "Service in $item->item(defective) with serial no. $stock->serial from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->save();
        }else{
            $log = new UserLog;
            $log->activity = "Service in $item->item(good) with serial no. $stock->serial from $customer->customer_branch." ;
            $log->user_id = auth()->user()->id;
            $log->save();
        }
        $stock->status = $request->status;
        if ($request->remarks == 'pm') {
            $stock->customer_branches_id = $request->customerid;
        }
        $stock->user_id = auth()->user()->id;
        $data = $stock->save();
        return response()->json($data);
    }
    public function addCategory(Request $request){
        $add = new Category;
        $add->category = ucfirst($request->cat);
        $data = $add->save();
        return response()->json($data);
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
        $log->activity = "Pull-out $item->item(S/N: $request->serial) from $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
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
        $log->user_id = auth()->user()->id;
        $log->activity = "Repaired $request->item(S/N: $defect->serial).";
        $data = $defect->delete();
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
            $log->user_id = auth()->user()->id;
            $log->activity = "Use $request->item(S/N: $request->serial) to repair $request->repairitem(S/N: $request->repairserial).";
            $log->save();
            $def->status = "use to $request->repairitem(S/N: '$request->repairserial)";
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
            $log->user_id = auth()->user()->id;
            $log->activity = "Repaired $request->repairitem(S/N: $request->repairserial).";
            $data = $def->save();
        }else{
            $log = new UserLog;
            $log->user_id = auth()->user()->id;
            $log->activity = "Marked $request->item(S/N: $request->serial) as defective.";
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
        $log->activity = "Request $item->item to $branch->branch." ;
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $loan->save();
        return response()->json($data);
    }
    public function serviceOut(Request $request)
    {
        $stock = Stock::where('items_id', $request->item)
            ->where('branch_id', auth()->user()->branch->id)
            ->where('serial', $request->serial)
            ->where('status', 'in')
            ->first();
        $item = Item::where('id', $request->item)->first();
        $customer = CustomerBranch::where('id', $request->customer)->first();
        $stock->status = $request->purpose;
        $stock->customer_branches_id = $request->customer;
        $stock->user_id = auth()->user()->id;
        $log = new UserLog;
        $log->activity = "Service out $item->item(S/N: $request->serial) to $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
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
        }
        $preventive->customer_ids = $customers;
        $preventive->serial = $request->serial;
        $preventive->save();
        $log = new UserLog;
        $log->activity = "PM Service out $item->item(S/N: $request->serial) to $customer->customer_branch." ;
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $stock->save();
        return response()->json($data);
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
                $log = new UserLog;
                $log->activity = "Add $item->item to stocks." ;
                $log->user_id = auth()->user()->id;
                $log->save();
                $data = $add->save();
            }
        }else{
            $add = new Stock;
            $add->category_id = $request->cat;
            $add->branch_id = auth()->user()->branch->id;
            $add->items_id = $request->item;
            $add->user_id = auth()->user()->id;
            $add->serial = $request->serial;
            $add->status = 'in';
            $log = new UserLog;
            $log->activity = "Add $item->item with serial no. $request->serial to stocks" ;
            $log->user_id = auth()->user()->id;
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
            $lheader = strtolower($value);
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
                    $log->activity = "Import $item->item with serial no. $serial." ;
                    $log->user_id = auth()->user()->id;
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
            $stock = Warehouse::select('category_id', 'category', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as quantity'))
                ->where('status', 'in')
                ->join('categories', 'categories.id', '=', 'category_id')
                ->groupBy('category')
                ->get();
            return Datatables::of($stock)->make(true);
        }else{
            $stock = Warehouse::select('items.UOM', 'warehouses.category_id','items_id', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('warehouses.category_id', $request->category)
                ->join('items', 'items_id', '=', 'items.id')
                ->groupBy('items_id')->get();
            return DataTables::of($stock)
            ->addColumn('items_id', function (Warehouse $request){
                return $request->items_id;
            })
            ->addColumn('category', function (Warehouse $request){
                $cat = Category::find($request->category_id);
                return $cat->category;
            })
            ->addColumn('description', function (Warehouse $request){
                $item = Item::where('id', $request->items_id)->first();
                return $item->item;
            })
            ->addColumn('quantity', function (Warehouse $request){
                return $request->stock;
            })
            ->make(true);
        }
    }
    public function update(Request $request)
    {
        if ($request->stat == 'sunit') {
            $update = Stock::where('id', $request->id)->first();
            $update->status = $request->status;
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $update->items_id)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->branch->id;
            $defective->category_id = $update->category_id;
            $defective->items_id = $update->items_id;
            $defective->serial = $request->serial;
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->activity = "Replaced $item->item(S/N: $request->serial)." ;
            $log->user_id = auth()->user()->id;
            $data = $log->save();
            return response()->json($data);
        }else if ($request->stat == 'replace') {
            $update = Stock::where('id', $request->id)->first();
            $update->status = 'replacement';
            $update->user_id = auth()->user()->id;
            $update->save();
            $item = Item::where('id', $update->items_id)->first();
            $defective = new Defective;
            $defective->branch_id = auth()->user()->branch->id;
            $defective->user_id = auth()->user()->branch->id;
            $defective->category_id = $update->category_id;
            $defective->items_id = $request->ids;
            $defective->serial = $request->serial;
            $defective->status = 'For return';
            $defective->save();
            $log = new UserLog;
            $log->activity = "Replaced $item->item(S/N: $request->serial)." ;
            $log->user_id = auth()->user()->id;
            $data = $log->save();
            return response()->json($data);
        }
    }
}