<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;
use App\Mail\EmailForQueuing;
use Route;
use Validator;
use App\User;
use App\Bstock;
use App\Branch;
use App\Responder;
use App\Item;
use App\Loan;
use App\Pullout;
use App\Initial;
use App\Warehouse;
use App\StockRequest;
use App\PreparedItem;
use App\Category;
use App\Stock;
use App\Defective;
use App\Customer;
use App\CustomerBranch;
use App\RepairedNo;
use DB;
use App\UserLog;
use Carbon\Carbon;
use Mail;
use Auth;
use Config;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function report()
    {
        $title = 'Report a problem';
        return view('report', compact('title'));
    }

    public function item()
    {
        $title = 'Item List';
        return view('pages.item', compact('title'));
    }
    public function items()
    {
        $item = Item::select('items.*', 'category')
            ->join('categories', 'category_id', '=', 'categories.id');
        return DataTables::of($item)->make(true);
    }
    public function itemsedit(Request $request)
    {
        $items = Item::where('id', $request->id)->first();
        $item = Item::where('id', $request->id)->first();
        $item->item = $request->item;
        $item->save();
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
        $log->activity = 'UPDATE '.$items->item.' to '.$item->item.'.';
        $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $data = $log->save();
        return response()->json($data);

    }
    public function itemsUpdate(Request $request)
    {   
        $items = Item::where('id', $request->item)->first();
        $item = Item::where('id', $request->item)->update(['n_a' => $request->stat]);
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
        $log->activity = 'UPDATE '.$items->item.'.';
        $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $data = $log->save();
        return response()->json($data);
    }
    public function reportAproblem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module' => ['required', 'string', 'min:5', 'max:255'],
            'remarks' => ['required', 'string', 'min:10'],
        ]);
        if ($validator->passes()) {
            $user = auth()->user()->name.' '.auth()->user()->lastname;
            $branch = auth()->user()->branch->branch;
            $email = auth()->user()->email;
            $config = array(
                'driver'     => env('MAIL_DRIVER', 'smtp'),
                'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
                'port'       => env('MAIL_PORT', 587),
                'from'       => array('address' => 'bsms.support@ideaserv.com.ph', 'name' => 'support'),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username'   => env('BSMS_USERNAME'),
                'password'   => env('BSMS_PASSWORD'),
            );
            Config::set('mail', $config);
            
            $send = Mail::send('report-a-problem', 
                [
                'branch'=>auth()->user()->branch->branch,
                'module'=>$request->input('module'),
                'email'=>auth()->user()->email,
                'remarks'=>$request->input('remarks')
                ],
                function( $message) use($user, $branch, $email){ 
                $message->to('bsms.support@ideaserv.com.ph', 'bsms.support@ideaserv.com.ph')->subject('Report A Problem'); 
                $message->from($email, 'Report A Problem - '.$user. ' - '.$branch);
            });
            $responder = new Responder;
            $responder->branch_id = auth()->user()->branch->id;
            $responder->user_id = auth()->user()->id;
            $responder->email = auth()->user()->email;
            $responder->save();
            /*Mail::later(15, 'responder', 
                [
                'fullname'=>auth()->user()->name.' '.auth()->user()->lastname,
                ],
                function( $message) use($email, $name){ 
                $message->to($email, $name)->subject('Report A Problem'); 
                $message->from('bsms.support@ideaserv.com.ph', 'BSMS Support Team');
            });*/
        
            return redirect()->back()->with('success', 'Thank you '.$user.'! Your report has been successfully sent. Thank you for contacting us.');
        }
    }

    public function responder()
    {
        $config = array(
            'driver'     => env('MAIL_DRIVER', 'smtp'),
            'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port'       => env('MAIL_PORT', 587),
            'from'       => array('address' => 'bsms.support@ideaserv.com.ph', 'name' => 'support'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('BSMS_USERNAME'),
            'password'   => env('BSMS_PASSWORD'),
        );
        Config::set('mail', $config);
        $email = auth()->user()->email;
        $name = auth()->user()->name. ' '. auth()->user()->lastname;
        $data = Mail::send('responder', function( $message) use($email, $name){ 
            $message->to($email, $name)->subject('Report A Problem'); 
            $message->from('bsms.support@ideaserv.com.ph', 'BSMS Support Team');
        });
        return response()->json($data);
    }
    public function pending()
    {
        $title = "Pending";
        return view('pages.pending', compact('title'));
    }
    public function index()
    {
        if (auth()->user()->status == '0') {
            return redirect('logout');
        }
        
        $route = Route::current()->getname();
        if ($route == 'disposed') {
            $title = 'Disposed';
            return view('pages.disposed', compact('title'));
        }
        
        $title = 'Service Center Stock Monitoring System';
        $mail = StockRequest::wherein('status', ['4', 'INCOMPLETE'])->where( 'updated_at', '<', Carbon::now()->subDays(5))->first();
        //dd($mail);
        if ($mail) {
            $branch = Branch::where('id', $mail->branch_id)->first();
            $count = PreparedItem::where('request_no', $mail->request_no)->count();
            $items = PreparedItem::where('request_no', $mail->request_no)->get();
            //return $count;
            if ($count) {
                //return $items;
                foreach ($items as $item) {
                    $missing = Item::where('id', $item->items_id)->first();
                    $email = 'jerome.lopez.ge2018@gmail.com';
                    $gomail = Mail::send('unresolved', ['item'=>$missing->item, 'RDate'=>$mail->created_at, 'intransit'=>$mail->intransit, 'branch'=>$branch->branch],function( $message){ 
                        $message->to('jerome.lopez.ge2018@gmail.com', 'Jerome Lopez')->subject 
                            ('Unresolved Issue Notification'); 
                        $message->from('noreply@ideaserv.com.ph', 'Unresolved - NO-REPLY'); 
                    });
                    if ($gomail){
                        return 'tama';
                    }
                }
            }
        }
        StockRequest::wherein('status', ['4', 'INCOMPLETE'])->where( 'updated_at', '<', Carbon::now()->subDays(5))->update(['status' => 'UNRESOLVED']);
        $responder = Responder::select('responders.*', 'users.name', 'users.lastname')->where('responders.branch_id', auth()->user()->branch->id)
                    ->join('users', 'user_id', '=', 'users.id')
                    ->first();
        if ($responder) {
            $config = array(
                'driver'     => env('MAIL_DRIVER', 'smtp'),
                'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
                'port'       => env('MAIL_PORT', 587),
                'from'       => array('address' => 'bsms.support@ideaserv.com.ph', 'name' => 'support'),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username'   => env('BSMS_USERNAME'),
                'password'   => env('BSMS_PASSWORD'),
            );
            Config::set('mail', $config);
            $email = $responder->email;
            $name = $responder->name. ' '. $responder->lastname;
            Mail::send('responder',['email'=>'email'], function( $message) use($email, $name){ 
                $message->to($email, $name)->subject('Report A Problem'); 
                $message->from('bsms.support@ideaserv.com.ph', 'BSMS Support Team');
            });
            $responder->delete();
        }

        if (auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return view('pages.home', compact('title'));
            //return view('pages.pending', compact('title'));
        }

        if (auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != 'Main-Office' && !auth()->user()->hasanyrole('Repair', 'Warehouse Administrator')) {
            $units = Stock::where('status', 'in')->where('branch_id', auth()->user()->branch->id)->count();
            $returns = Defective::wherein('status', ['For return', 'For receiving'])->where('branch_id', auth()->user()->branch->id)->count();
            $stockreq = StockRequest::where('branch_id', auth()->user()->branch->id)
                ->wherein('status', ['PENDING', 'SCHEDULED'])
                ->where('stat', '=', 'ACTIVE')
                ->count();
            $sunits = Stock::where('status', 'service unit')->where('branch_id', auth()->user()->branch->id)->count();
            $loans = Loan::where('status', 'pending')->where('to_branch_id', auth()->user()->branch->id)->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'sunits', 'title', 'loans'));
        }else if (auth()->user()->hasrole('Repair')){
            return view('pages.warehouse.return', compact('title'));
        }else if (auth()->user()->hasrole('Warehouse Administrator')){
            return view('pages.unrepair', compact('title'));
        }else{
            $stockreq = StockRequest::where('status', 'PENDING')
                ->where('stat', '=', 'ACTIVE')
                ->count();
            $units = Warehouse::where('status', 'in')->count();
            $returns = RepairedNo::where('status', 'For receiving')->count();
            $unresolved = StockRequest::where('status', 'UNRESOLVED')->where('stat', 'ACTIVE')->count();
            $resolved = StockRequest::where('status', 'UNRESOLVED')->where('stat', 'RESOLVED')->count();
            $resolved = StockRequest::where('status', 'UNRESOLVED')->where('stat', 'RESOLVED')->count();
            $pullout = Pullout::where('status', 'For receiving')->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'title', 'unresolved', 'resolved', 'pullout'));
        }
    }
    public function log()
    {   
        $title = 'Activities';
        return view('pages.home', compact('title'));
    }
    public function myapi(Request $request)
    {
        $stockreq = StockRequest::where('id', $request->id)
                ->wherein('status', ['PENDING', 'SCHEDULED'])
                ->where('stat', '=', 'ACTIVE')
                ->update(['stat' => 'mytest']);
        $mystock = StockRequest::where('requests.id', $request->id)
        ->wherein('requests.status', ['PENDING', 'SCHEDULED'])
        ->join('branches', 'branches.id', 'branch_id')
        ->first();
        return $mystock->branch;
    }
    public function unrepair()
    {
        $title = 'Unrepairable items';
        return view('pages.unrepair', compact('title'));
    }
    public function print($id)
    {
        sleep(2);
        $request = StockRequest::query()
            ->where('request_no', $id)->first();
        if ($request->type == "Service") {
            $request = StockRequest::query()
            ->select('requests.*', 'customer_branch')
            ->where('request_no', $id)
            ->join('customer_branches', 'customer_branches.id', 'customer_branch_id')
            ->first();
        }
        // if ($request->) {
        //     # code...
        // }
        $title = 'Print Preview';
        return view('pages.warehouse.print', compact('request', 'title'));
    }
    public function printDefective()
    {
        $request = Defective::query()->where('branch_id', auth()->user()->branch->id)
            ->where('status', 'For receiving')
            ->where('return_no', '!=', '0')
            ->groupBy('return_no')
            ->get();
        $title = 'Return - For receiving';
        return view('pages.branch.print', compact('title'));
    }

    public function showret()
    {
        $title = 'Return - For receiving';
        return view('pages.branch.ret', compact('title'));
    }
    public function initial($id)
    {
        if ($id == 'logss') {
            $users = Userlog::query()->where('user_id', '!=', '0')->get();
            foreach ($users as $user) {
                $tech = User::query()->select('branch_id')->where('id', $user->user_id)->first();
                $update = Userlog::query()->where('id', $user->id)->first();
                $update->branch_id = $tech->branch_id;
                $update->save();
            }
            dd($users);
        }

        if ($id == 'shadow046') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    $initial = new Initial;
                    $initial->items_id = $item->id;
                    $initial->branch_id = $branchs->id;
                    $initial->qty = 5;
                    $initial->save();
                }
            }
        }
        if ($id == 'del') {
            $items = Item::all();
            $branches = Branch::wherein('id', [18,17,40,41,14,37,19,15,16])->get();
            foreach ($branches as $branchs) {
                $del = initial::where('branch_id', $branchs)->get();
                foreach ($del as $dl) {
                    $dl->delete();
                }
            }
        }
        if ($id == 'ini') {
            $length = 10;
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $items = Item::all();
            $branches = Branch::where('area_id', 6)->get();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    $series = $item->id;
                    $results = mb_strtolower(($series).substr(str_shuffle($permitted_chars), 0, $length));
                    if ($branchs->id != 1) {
                        $stock = new Stock;
                        $stock->category_id = $item->category_id;
                        $stock->branch_id = $branchs->id;
                        $stock->items_id = $item->id;
                        $stock->serial = $results;
                        $stock->status = 'in'; 
                        $stock->save();
                    }
                }
            }
        }
        if ($id == 'add') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    if ($branchs->id != 1) {
                        $initial = new Initial;
                        $initial->branch_id = $branchs->id;
                        $initial->items_id = $item->id;
                        $initial->qty = '9';
                        $initial->save();
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
                        $inia = Initial::where('items_id', $item->id)
                            ->where('branch_id', $branchs->id)
                            ->first();
                        $inia->qty = 9;
                        $inia->save();
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
        if ($id == 'logs') {
            $logs = UserLog::query()->orderBy('id', 'DESC');
                foreach ($logs as $log) {
                    $newlog = UserLog::where('id', $log->id)->first();
                    //dd($newlog);
                    $newlog->fullname = User::where('id', $log->user_id)->first()->name.' '.User::where('id', $log->user_id)->first()->middlename.' '.User::where('id', $log->user_id)->first()->lastname;
                    $newlog->branch = Branch::where('id', $log->branch_id)->first()->branch;
                    $newlog->save();
                }
        }

        if ($id == 'caps') {
            $stocks = Warehouse::all();
            foreach ($stocks as $stock) {
                $stock->serial = strtoupper($stock->serial);
                $stock->save();
            }
        }

        if ($id == 'wareini') {
            $items = Item::all();
            foreach ($items as $item) {
                $ini = new \App\WarehouseInitial;
                $ini->items_id = $item->id;
                $ini->save();
            }
        }

        if ($id == 'count') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($items as $item) {
                foreach ($branches as $branch) {
                    $stock = Stock::where('items_id', $item->id)->where('status', 'in')->where('branch_id', $branch->id)->count();
                    $bstock = new Bstock;
                    $bstock->branch_id = $branch->id;
                    $bstock->itemname = $item->item;
                    $bstock->items_id = $item->id;
                    $bstock->category_id = $item->category_id;
                    $bstock->count = $stock;
                    $bstock->save();
                }
            }
        }
        dd(Stock::all());
    }
    public function activity()
    {   
        if (auth()->user()->hasRole('Viewer PLSI')) {
            $acts = UserLog::query()->get();
            $act = [];
            foreach ($acts as $acs) {
                if(str_contains(strtolower($acs->activity), 'mercury drug')){
                    array_push($act, $acs);
                }
                if ($acs->service == "yes" && $acs->company == "PLSI") {
                    array_push($act, $acs);
                }
            }
        }
        if (auth()->user()->hasanyRole('Viewer IDSI', 'Viewer')) {
            $acts = UserLog::query()->where('activity', 'not like', '%mercury drug%' )->get();
            $act = [];
            foreach ($acts as $acs) {
                if(str_contains($acs->activity, 'SERVICE OUT') || str_contains($acs->activity, 'SERVICE IN')){
                    array_push($act, $acs);
                }
                if(str_contains($acs->activity, 'REPLACED')){
                    array_push($act, $acs);
                }
                if ($acs->service == "yes" && $acs->company == "IDSI") {
                    array_push($act, $acs);
                }
                if ($acs->service == "yes" && $acs->company == "APSOFT") {
                    array_push($act, $acs);
                }
            }
        }
        if (auth()->user()->hasAnyRole('Editor',  'Manager')) {
            $act = UserLog::query();
                /*
                ->take(1000)
                ->get();*/
        }
        if (auth()->user()->hasAnyRole('Head',  'Warehouse Manager')) {
            $myuser = [];
            $user = User::query()->where('branch_id', auth()->user()->branch->id)->get();
            foreach ($user as $user) {
                $myuser[] = $user->id;
            }
            
            $act = Userlog::query()
                ->wherein('user_id', $myuser);
            //$act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'desc')->take(1000)->get();
        }
        if (auth()->user()->hasAnyRole('Warehouse Administrator')) {
            $users = User::query()->whereHas('roles', function($q){
                $q->where('name', 'Repair');
            })->get();
            $myuser = [];
            array_push($myuser, auth()->user()->id);
            foreach ($users as $user) {
                $myuser[] = $user->id;
            }
            $logs = Userlog::query()
                ->wherein('user_id', $myuser)->get();
            $acts = Userlog::query()->where('activity', 'LIKE', 'RECEIVED REPAIRED%')->get();
            $acts2 = Userlog::query()->where('activity', 'LIKE', '%buffer stock request%')->get();
            $acts3 = Userlog::query()->where('activity', 'LIKE', 'RECEIVED%from Main Warehouse%')->get();
            $acts4 = Userlog::query()->where('activity', 'LIKE', 'DELIVERED%to Warehouse%')->get();

            //$act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'desc')->take(1000)->get();
            $act = [];
            foreach ($logs as $log) {
                $act[] = $log;
            }
            foreach ($acts as $acs) {
                array_push($act, $acs);
            }
            foreach ($acts2 as $acs) {
                array_push($act, $acs);
            }
            foreach ($acts3 as $acs) {
                array_push($act, $acs);
            }
            foreach ($acts4 as $acs) {
                array_push($act, $acs);
            }
            $act = collect($act)->sortBy('id')->all();
        }
        if (auth()->user()->hasAnyRole('Repair')) {
            $users = User::query()->whereHas('roles', function($q){
                $q->where('name', 'Repair');
            })->get();
            $myuser = [];
            array_push($myuser, auth()->user()->id);
            foreach ($users as $user) {
                $myuser[] = $user->id;
            }
            $logs = Userlog::query()
                ->wherein('user_id', $myuser)->get();
            $acts = Userlog::query()->where('activity', 'LIKE', 'RECEIVED REPAIRED%')->get();
            //$act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'desc')->take(1000)->get();
            $act = [];
            foreach ($logs as $log) {
                $act[] = $log;
            }
            foreach ($acts as $acs) {
                array_push($act, $acs);
            }
            $act = collect($act)->sortBy('id')->all();
        }
        if (auth()->user()->hasAnyRole('Tech', 'Encoder')) {
            $act = UserLog::query()
                ->where('user_id', auth()->user()->id);
        }
        if (auth()->user()->hasAnyRole('Main Warehouse Manager')) {
           
            $logs = Userlog::query()
                ->where('user_id', auth()->user()->id)->get();
            $acts = Userlog::query()->where('activity', 'LIKE', '%from Main Warehouse.')->get();
            //$act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'desc')->take(1000)->get();
            $act = [];
            foreach ($logs as $log) {
                $act[] = $log;
            }
            foreach ($acts as $acs) {
                array_push($act, $acs);
            }
            $act = collect($act)->sortBy('id')->all();
        }
        
        return DataTables::of($act)
        // ->addColumn('date', function (UserLog $request){
        //     return $request->created_at;
        // })
        /*->addColumn('time', function (UserLog $request){
            return $request->created_at->toTimeString();
        })
        /*->addColumn('branch', function (UserLog $request){
            //$branch = User::where('id', $request->user_id)->first();
            return $request->branch;
        })
        ->addColumn('fullname', function (UserLog $request){
            //$username = User::where('id', $request->user_id)->first();
            return $request->name.' '.$request->middlename.' '. $request->lastname;
        })
        ->addColumn('userlevel', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->roles->first()->name;
        })*/
        ->make(true);
    }
    public function service_units()
    {
        $users = User::all();
        return view('pages.service-units', compact('users'));
    }

    public function imp()
    {
        $sources = DB::table('customer_branch')->get();
            foreach ($sources as $source) {
                $customer = Customer::where('code', $source->custcode)->first();
                if ($customer) {
                    $branch = CustomerBranch::where('customer_id', $customer->id)->where('code', $source->brchcode)->first();
                    if (!$branch) {
                        $add = new CustomerBranch;
                        $add->customer_branch = $source->brchname;
                        $add->address = $source->addr1;
                        $add->code = $source->brchcode;
                        $add->customer_id = $customer->id;
                        if ($source->contact_number) {
                            $add->contact = $source->contact_number;
                        }
                        if ($source->contact_person) {
                            $add->cperson = $source->contact_person;
                        }
                        $add->status = '1';
                        $add->save();
                    }
                }
            }
        return dd(CustomerBranch::all());
    }
    public function preventive()
    {
        $title = "Preventive Maintenance";
        $categories = Category::orderBy('category')->get();
        if (!auth()->user()->hasanyrole('Head', 'Tech')) {
            return redirect('/');
        }
        return view('pages.pm', compact('title', 'categories'));
    }
    public function getprint(Request $request, $id)
    {
        $stock = StockRequest::where('request_no', $id)->first();
        $consumable = PreparedItem::select('item', 'uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('branch_id', $stock->branch_id)
            ->where('request_no', $stock->request_no)
            ->where('schedule', $request->schedule)
            ->whereNotin('uom', ['Unit'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(items_id) as quantity')
            ->groupBy('items_id')
            ->get();
        $unit = PreparedItem::select('item', 'uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('branch_id', $stock->branch_id)
            ->where('request_no', $id)
            ->where('schedule', $request->schedule)
            ->whereNotin('uom', ['Pc', 'Meter'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(prepared_items.id) as quantity')
            ->groupBy('prepared_items.id')
            ->get();
        $result = $unit->merge($consumable);
        return DataTables::of($result)
        ->addColumn('quantity', function (PreparedItem $PreparedItem){
            if ($PreparedItem->quantity != 1) {
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM.'s';
            }else{
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM;
            }
        })
        ->make(true);
    }
    public function save(Request $request){
        Storage::disk('open')->put('company_info/description.txt', $request->description);
    }
    public function convert(){
        return view('pages.test');
    }
}