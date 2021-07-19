<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Excel as BaseExcel;
use Route;
use App\Defective;
use App\CustomerBranch;
use App\ConversionPos;
use App\Conversion;
use App\Branch;
use App\Item;
use App\Warehouse;
use App\Category;
use Carbon\Carbon;
use App\UserLog;
use App\Retno;
use App\RepairedNo;
use App\Retmail;
use DB;
use Mail;
use Auth;
class DefectiveController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function returnview()
    {
        $title = 'Defectives';
        return view('pages.return', compact('title'));
    }
    public function repaired()
    {
        $title = 'Repaired';
        return view('pages.repaired', compact('title'));
    }
    public function repairedlist()
    {
        $title = 'Repaired-list';
        return view('pages.repaired-list', compact('title'));
    }

    public function returnget()
    {
        if (auth()->user()->hasanyrole('Repair')) {
            $return = Retno::query()
                ->select('returns_no.updated_at', 'returns_no.status', 'return_no', 'branch', 'returns_no.status')
                ->wherein('returns_no.status', ['For receiving', 'Incomplete'])
                ->join('branches', 'branches.id', 'branch_id')
                ->get();
            return DataTables::of($return)
                ->addColumn('updated_at', function (Retno $return){
                    return Carbon::parse($return->updated_at->toFormattedDateString().' '.$return->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
        if (auth()->user()->hasanyrole('Head')) {
            $return = Retno::query()
                ->select('returns_no.updated_at', 'returns_no.status', 'return_no', 'branch', 'returns_no.status')
                ->wherein('returns_no.status', ['For receiving', 'Incomplete'])
                ->where('branch_id', auth()->user()->branch->id)
                ->join('branches', 'branches.id', 'branch_id')
                ->get();
            return DataTables::of($return)
                ->addColumn('updated_at', function (Retno $return){
                    return Carbon::parse($return->updated_at->toFormattedDateString().' '.$return->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
    }

    public function returnitem(Request $request)
    {
        $return = Defective::query()
            ->select('defectives.id', 'category', 'item', 'serial', 'items_id')
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')
            ->wherein('status', ['For receiving'])
            ->where('return_no', $request->retno)
            ->get();
        return DataTables::of($return)
        ->addColumn('serial', function (Defective $data){
            return strtoupper($data->serial);
        })
        ->make(true);
    }
    public function repaireditem(Request $request)
    {
        $repaired = Defective::query()
            ->select('defectives.id', 'category', 'item', 'serial')
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')
            ->where('status', 'For add stock')
            ->where('repaired_no', $request->repaired_no)
            ->get();
        return DataTables::of($repaired)->make(true);
    }
    public function repairednr(Request $request)
    {
        $repaired = RepairedNo::where('status', 'For receiving')
            ->where('repaired_no', $request->repaired_no)
            ->update(['status' => 'Incomplete']);
        return response()->json($repaired);

    }
    public function repairedrec(Request $request)
    {
        foreach ($request->id as $id) {
            $repaired_no= Defective::where('status', 'For add stock')
                ->where('id', $id)
                ->where('repaired_no', $request->repaired_no)->first();
            $repaired_no->status = 'Received';
            $repaired_no->save();
                //->update(['status' => 'Received']);
            $warehouse = new Warehouse;
            $warehouse->items_id = $repaired_no->items_id;
            $warehouse->serial = $repaired_no->serial;
            $warehouse->category_id = $repaired_no->category_id;
            $warehouse->status = 'in';
            $warehouse->user_id = auth()->user()->id;
            $warehouse->save();
            $items = Item::where('id', $repaired_no->items_id)->first();
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "RECEIVED REPAIRED $items->item(S/N: ".mb_strtoupper($repaired_no->serial).") from repair." ;
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
        }
        $check = Defective::where('status', 'For add stock')
            ->where('repaired_no', $request->repaired_no)->first();
        if ($check) {
            RepairedNo::wherein('status', ['For receiving', 'Incomplete'])->where('repaired_no', $request->repaired_no)->update(['status' => 'Incomplete']);
        }else{
            RepairedNo::wherein('status', ['For receiving', 'Incomplete'])->where('repaired_no', $request->repaired_no)->update(['status' => 'Completed']);
        }
        return response()->json($warehouse);

    }
    public function repairedget(Request $request)
    {
        if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder')) {
            $repaired = RepairedNo::query()
                ->select('created_at', 'repaired_no', 'status')
                ->wherein('status', ['For receiving', 'Incomplete'])
                ->get();
            return DataTables::of($repaired)
                ->addColumn('created_at', function (RepairedNo $repaired){
                    return Carbon::parse($repaired->created_at->toFormattedDateString().' '.$repaired->created_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
        if (auth()->user()->hasanyrole('Repair')) {
            if ($request->list == 'list') {
                $repaired = RepairedNo::query()
                ->select('created_at', 'repaired_no', 'status')
                ->wherein('status', ['For receiving', 'Incomplete'])
                ->get();
                return DataTables::of($repaired)
                    ->addColumn('created_at', function (RepairedNo $repaired){
                        return Carbon::parse($repaired->created_at->toFormattedDateString().' '.$repaired->created_at->toTimeString())->isoFormat('lll');
                    })
                    ->make(true);
            }
            $repaired = Defective::query()
                ->select('defectives.updated_at', 'item', 'serial', 'category')
                ->where('status', 'Repaired')
                ->join('items', 'items.id', 'items_id')
                ->join('categories', 'categories.id', 'defectives.category_id')
                ->get();
            return DataTables::of($repaired)
                ->addColumn('updated_at', function (Defective $repaired){
                    return Carbon::parse($repaired->updated_at->toFormattedDateString().' '.$repaired->updated_at->toTimeString())->isoFormat('lll');
                })
                ->make(true);
        }
        
    }
    public function repairedupdate(Request $request)
    {
        if ($request->send == 1) {
            $defective = Defective::query()
            ->where('status', 'Repaired')
            ->update(['status' => 'For add stock', 'repaired_no' => $request->retno]);
            $repaired = new RepairedNo;
            $repaired->user_id = auth()->user()->id;
            $repaired->status = 'For receiving';
            $repaired->repaired_no = $request->retno;
            $repaired->save();
            $bcc = \config('email.bcc');
            $no = $repaired->repaired_no;
            $excel = Excel::raw(new ExcelExport($repaired->repaired_no, 'RR'), BaseExcel::XLSX);
            $data = array('office'=> auth()->user()->branch->branch, 'return_no'=>$repaired->repaired_no, 'dated'=>Carbon::now()->toDateTimeString());
            Mail::send('rr', $data, function($message) use($excel, $no, $bcc) {
                $message->to(auth()->user()->email, auth()->user()->name)->subject
                    ('RR no. '.$no);
                $message->attachData($excel, 'RR No. '.$no.'.xlsx');
                $message->from('noreply@ideaserv.com.ph', 'BSMS');
                $message->bcc($bcc);
            });

            return response()->json($repaired);
        }
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
        }
        $check = Pullout::where('status', 'For receiving')
            ->where('pullout_no', $request->pull_no)->first();
        if ($check) {
           Pullno::where('status', 'For receiving')->where('pullout_no', $request->pull_no)->update(['status' => 'Incomplete']);
        }else{
           Pullno::where('status', 'For receiving')->where('pullout_no', $request->pull_no)->update(['status' => 'Completed']);
        }
        return response()->json($pullout);

    }
    public function index()
    {
        
        $title = 'Defective Unit/Parts';
        $users = User::all();
        $categories = Category::orderBy('category')->get();
        if (auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $tosend = Retmail::select('return_to_mail.*', 'branch')
            ->where('return_no', '!=', '0')
            ->join('branches', 'branches.id', 'branch_id')
            ->first();
        
        if (auth()->user()->branch->branch == 'Main-Office'){
            return view('pages.warehouse.return', compact('users', 'title'));
        }else if (auth()->user()->branch->branch == 'Conversion') {
            if (auth()->user()->hasrole('Tech')) {
                return redirect('/');
            }
            $branches = Branch::query()->orderBy('branch', 'asc')->get();
            return view('pages.branch.conversion', compact('users', 'title', 'categories', 'branches'));
        }else if (auth()->user()->branch->branch != 'Warehouse') {
            if (auth()->user()->hasrole('Tech')) {
                return redirect('/');
            }
            $branches = Branch::query()->orderBy('branch', 'asc')->get();
            return view('pages.branch.return', compact('users', 'title', 'categories', 'branches'));
        }else{
            return view('pages.warehouse.return', compact('users', 'title'));
        }
    }
    public function conversion(Request $request)
    {
        $item = Item::select('item')->where('id', $request->item)->first();
        $cid = CustomerBranch::query()->where('customer_branch', $request->cname)->first();
        $defective = new Defective;
        $defective->branch_id = auth()->user()->branch->id;
        $defective->user_id = auth()->user()->id;
        $defective->category_id = $request->category;
        $defective->items_id = $request->item;
        $defective->serial = $request->serial;
        $defective->status = 'For return';
        $defective->drno = $request->drno;
        $defective->customer_branches_id = $cid->id;
        $defective->bid = $request->branch_id;
        $defective->pullout_date = $request->pulldate;
        $defective->save();

        if (!Conversion::query()->where('drno', $request->drno)->first()) {
            $conversion = new Conversion;
            $conversion->user_id = auth()->user()->id;
            $conversion->drno = $request->drno;
            $conversion->pullout_date = $request->pulldate;
            $conversion->branch_id = $request->branch_id;
            $conversion->customer_branches_id = $cid->id;
            $conversion->status = "For return";
            $conversion->save();
        }
        // $log = new UserLog;
        // $log->branch_id = auth()->user()->branch->id;
        // $log->branch = auth()->user()->branch->branch;
        // $log->user_id = auth()->user()->id;
        // $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        // $log->activity = "ADD $item->item from Conversion.";
        // $log->save();

        return response()->json($defective);
    }
    public function printtable()
    {
        $defective = Defective::select('defectives.updated_at', 'defectives.category_id', 'branch_id as branchid', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('branch_id', auth()->user()->branch->id)
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->where('status', 'For receiving')
            ->get();
        return DataTables::of($defective)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('category', function (Defective $data){
            $cat = Category::where('id', $data->category_id)->first();
            return $cat->category;
        })
        ->make(true);
    }

    public function returntable()
    {
        $request = Defective::query()->where('branch_id', auth()->user()->branch->id)
            ->where('status', 'For receiving')
            ->where('return_no', '!=', '0')
            ->groupBy('return_no')
            ->get();
        return DataTables::of($request)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->created_at)->isoFormat('lll');
        })
        ->make(true);
        
    }
    public function pos()
    {
        $title = 'POS';
        $users = User::all();
        return view('pages.branch.pos', compact('users', 'title'));
    }
    public function postable()
    {
        $pos = ConversionPos::all();
        return DataTables::of($pos)
        ->addColumn('date', function (ConversionPos $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('customer', function (ConversionPos $data){
            $customer  = CustomerBranch::query()->where('id', $data->customer_branches_id)->first();
            return $customer->customer_branch;
        })
        ->make(true);

    }
    public function convertiontable(Request $request)
    {
        $item = Defective::query()->select('defectives.id', 'item', 'category', 'serial', 'defectives.status')
            ->where('drno', $request->drno)
            ->where('status', 'For return')
            ->join('items', 'items_id', 'items.id')
            ->join('categories', 'defectives.category_id', 'categories.id')
            ->get();
        return DataTables::of($item)->make(true);
    }
    public function table()
    {
        if (auth()->user()->branch->branch == 'Conversion') {
            $defective = Conversion::query()->where('status', 'For return')->get();
            return DataTables::of($defective)
                ->addColumn('date', function (Conversion $data){
                    return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
                })
                ->addColumn('pullout_date', function (Conversion $data){
                    return Carbon::parse($data->pullout_date)->formatLocalized('%A, %B  %d, %Y');
                })
                ->addColumn('branch', function (Conversion $data){
                    $branch = Branch::query()->where('id', $data->branch_id)->first();
                    return $branch->branch;
                })
                ->addColumn('customer_branch', function (Conversion $data){
                    $cbranch = CustomerBranch::query()->where('id', $data->customer_branches_id)->first();
                    return $cbranch->customer_branch;
                })
                ->addColumn('drno', function (Conversion $data){
                    return strtoupper($data->drno);
                })
                ->make(true);
        }
        $defective = Defective::query()->select('defectives.updated_at', 'defectives.category_id', 'branch_id as branchid', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('defectives.status', 'For return')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->wherein('defectives.status', ['For return', 'For receiving'])
            ->get();
        
        $waredef =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'Repaired')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        $main =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['Repaired', 'For Repair'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        $repair = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['For repair', 'Repaired', 'Conversion'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')
            ->get();
        if (auth()->user()->branch->branch == 'Warehouse' && !auth()->user()->hasanyrole('Repair', 'Warehouse Administrator')) {
            $data = $waredef;
        }else if (auth()->user()->branch->branch == 'Warehouse' && auth()->user()->hasrole('Repair')){
            $data = $repair;
        }else if (auth()->user()->branch->branch == 'Main-Office'){
            $data = $main;
        }else{
            $data = $defective;
        }

        
        return DataTables::of($data)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('category', function (Defective $data){
            $cat = Category::where('id', $data->category_id)->first();
            return $cat->category;
        })
        ->addColumn('status', function (Defective $data){
            return $data->status;
        })
        ->addColumn('serial', function (Defective $data){
            return strtoupper($data->serial);
        })
        ->make(true);
    }
    public function unrepairable()
    {
        $repair = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['Unrepairable', 'Unrepairable approval'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        return DataTables::of($repair)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('category', function (Defective $data){
            $cat = Category::where('id', $data->category_id)->first();
            return $cat->category;
        })
        ->make(true);
    }
    public function sdisposed($request)
    {
        $disposed = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'Disposed')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
        return DataTables::of($disposed)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('mydate', function (Defective $data){

            return Carbon::parse($data->updated_at)->format('Y/m/d');
        })
        ->addColumn('category', function (Defective $data){
            $cat = Category::where('id', $data->category_id)->first();
            return $cat->category;
        })
        ->make(true);
    }
    public function disposed()
    {
        $disposed = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'Disposed')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
        return DataTables::of($disposed)
        ->addColumn('date', function (Defective $data){
            return Carbon::parse($data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('mydate', function (Defective $data){

            return Carbon::parse($data->updated_at)->format('m/d/Y');
        })
        ->addColumn('category', function (Defective $data){
            $cat = Category::where('id', $data->category_id)->first();
            return $cat->category;
        })
        ->make(true);
    }
    public function update(Request $request)
    {
        if (auth()->user()->branch->branch != 'Warehouse') {
            $branch = Branch::where('id', auth()->user()->branch->id)->first();
            foreach ($request->id as $id) {
                $updates = Defective::where('branch_id', auth()->user()->branch->id)
                    ->where('id', $id)
                    ->where('status', 'For return')
                    ->first();
                $updates->status = 'For receiving';
                $updates->user_id = auth()->user()->id;
                $updates->return_no = $request->ret;
                $items = Item::where('id', $updates->items_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RETURN defective $items->item(S/N: ".mb_strtoupper($updates->serial).") to warehouse." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $updates->save();
            }
            Conversion::where('drno', $updates->drno)->update(['status'=>'submitted']);
            $retno = new Retno;
            $retno->user_id = auth()->user()->id;
            $retno->branch_id = auth()->user()->branch->id;
            $retno->status = 'For receiving';
            $retno->return_no = $request->ret;
            $retno->save();
            $bcc = \config('email.bcc');
            if (auth()->user()->branch->branch != "Conversion") {
                $excel = Excel::raw(new ExcelExport($request->ret, 'DDR'), BaseExcel::XLSX);
                $data = array('office'=> $branch->branch, 'return_no'=>$retno->return_no, 'dated'=>$retno->created_at);
                Mail::send('returncopy', $data, function($message) use($excel, $retno, $bcc) {
                    $message->to(auth()->user()->email, auth()->user()->name)->subject
                        ('DDR No. '.$retno->return_no);
                    $message->attachData($excel, 'DDR No. '.$retno->return_no.'.xlsx');
                    $message->from('noreply@ideaserv.com.ph', 'BSMS');
                    $message->bcc($bcc);
                });
            }else{
                $excel = Excel::raw(new ExcelExport($request->ret.'/'.$updates->drno, 'CDR'), BaseExcel::XLSX);
                $data = array('office'=> $branch->branch, 'return_no'=>$retno->return_no, 'dated'=>$retno->created_at);
                Mail::send('returncopy', $data, function($message) use($excel, $retno, $bcc) {
                    $message->to(auth()->user()->email, auth()->user()->name)->subject
                        ('CDR No. '.$retno->return_no);
                    $message->attachData($excel, 'CDR No. '.$retno->return_no.'.xlsx');
                    $message->from('noreply@ideaserv.com.ph', 'BSMS');
                    $message->bcc($bcc);
                });
            }

            //Excel::store(new ExcelExport($request->ret), 'excel/'.auth()->user()->branch->branch.'-'.$request->ret.'.xlsx', 'public');
            $retmail = new Retmail;
            $retmail->branch_id = auth()->user()->branch->id;
            $retmail->user_id = auth()->user()->id;
            $retmail->return_no = $request->ret;
            $retmail->save();

            return response()->json($updates);
        }else{
            if ($request->edit == "yes") {
                $old = Defective::query()->where('id', $request->id)
                    ->where('status', 'For receiving')
                    ->where('serial', $request->old)
                    ->first();
                $defective = Defective::query()->where('id', $request->id)
                    ->where('status', 'For receiving')
                    ->where('serial', $request->old)
                    ->first();
                if ($request->type == "get") {
                    $items = Item::query()->select('id','item')->where('category_id', $defective->category_id)->get();
                    return response()->json($items);
                }
                $defective->serial = $request->new;
                $defective->items_id = $request->itemid;
                $defective->save();
                $branch = Branch::query()->select('branch')->where('id', $defective->branch_id)->first();
                if ($old->serial != $request->new && $old->items_id != $request->itemid) {
                    $itemold = Item::query()->where('id', $old->items_id)->first();
                    $itemnew = Item::query()->where('id', $request->itemid)->first();
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CHANGE $branch->branch $itemold->item serial number from ".mb_strtoupper($request->old)." to ".mb_strtoupper($request->new).".";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                    $log->save();
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CHANGE $branch->branch $itemold->item(".mb_strtoupper($request->new).") item description to $itemnew->item.";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                    $log->save();
                    return response()->json($log);
                }

                if ($old->serial != $request->new) {
                    $itemold = Item::query()->where('id', $old->items_id)->first();
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CHANGE $branch->branch $itemold->item serial number from ".mb_strtoupper($old->serial)." to ".mb_strtoupper($request->new).".";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                    $data = $log->save();
                    return response()->json($data);
                }

                if ($old->items_id != $request->itemid) {
                    $itemold = Item::query()->where('id', $old->items_id)->first();
                    $itemnew = Item::query()->where('id', $request->itemid)->first();
                    $log = new UserLog;
                    $log->branch_id = auth()->user()->branch->id;
                    $log->branch = auth()->user()->branch->branch;
                    $log->activity = "CHANGE $branch->branch $itemold->item(".mb_strtoupper($request->old).") item description to $itemnew->item.";
                    $log->user_id = auth()->user()->id;
                    $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                    $log->save();
                    return response()->json($log);
                }
            }
            if ($request->status == 'Received') {
                $update = Defective::where('id', $request->id)
                    ->where('status', 'For receiving')
                    ->first();
                
                $item = Item::where('id', $update->items_id)->first();
                $branch = Branch::where('id', $update->branch_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                if ($branch->branch == "Conversion") {
                    $log->activity = "RECEIVED $item->item(".mb_strtoupper($update->serial).") from $branch->branch." ;
                    $update->status = "Conversion";
                }else{
                    $log->activity = "RECEIVED defective $item->item(".mb_strtoupper($update->serial).") from $branch->branch." ;
                    $update->status = "For repair";
                }
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $update->user_id = auth()->user()->id;
                $data = $update->save();

                $check = Defective::where('return_no', $update->return_no)->where('status', 'For receiving')->first();
                if (!$check) {
                    Retno::where('return_no', $update->return_no)->update(['status'=>'Received']);
                }else{
                    Retno::where('return_no', $update->return_no)->update(['status'=>'Incomplete']);
                }
                return response()->json($data);
            }
            if ($request->status == 'Repaired') {
                $repaired = Defective::where('id', $request->id)
                    ->where('status', 'For repair')
                    ->first();
                $repaired->status = "Repaired";
                $repaired->save();
                $item = Item::where('id', $repaired->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "REPAIRED $item->item(".mb_strtoupper($repaired->serial).") and send to Warehouse." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $repaired->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'warehouse') {
                $pending = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
                    ->where('status', 'Repaired')
                    ->first();
                $stock = new Warehouse;
                $stock->user_id = auth()->user()->id;
                $stock->category_id = $pending->category_id;
                $stock->items_id = $pending->items_id;
                $stock->serial = '-';
                $stock->status = 'in';
                $stock->save();
                $pending->status = "warehouse";
                $item = Item::where('id', $pending->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "ADD $item->item(".($pending->serial).") from Repair to Stock." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $pending->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'Unrepairable approval') {
                $unreapairable = Defective::where('id', $request->id)
                    ->where('status', 'For repair')
                    ->first();
                $unreapairable->status = "Unrepairable approval";
                $unreapairable->save();
                $item = Item::where('id', $unreapairable->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "MARKED $item->item($unreapairable->serial) as unreapairable and subject for approval." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $unreapairable->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'approved') {
                $unreapairable = Defective::where('id', $request->id)
                    ->first();
                $unreapairable->status = "Unrepairable";
                $unreapairable->save();
                $item = Item::where('id', $unreapairable->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "MARKED $item->item($unreapairable->serial) as unreapairable and ready to dispose." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $unreapairable->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'dispose') {
                $unreapairable = Defective::where('id', $request->id)
                    ->first();
                $unreapairable->status = "Disposed";
                $unreapairable->save();
                $item = Item::where('id', $unreapairable->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "MARKED $item->item($unreapairable->serial) as dispose" ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $unreapairable->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'return') {
                $unreapairable = Defective::where('id', $request->id)
                    ->first();
                $unreapairable->status = "For repair";
                $unreapairable->save();
                $item = Item::where('id', $unreapairable->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RETURN $item->item($unreapairable->serial) to Repair" ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $unreapairable->save();
                $data = $log->save();
                return response()->json($data);
            }
        }
    }
}