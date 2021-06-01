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
use App\Branch;
use App\Item;
use App\Warehouse;
use App\Category;
use Carbon\Carbon;
use App\UserLog;
use App\Retno;
use App\Retmail;
use DB;
use Mail;
use Auth;
class DefectiveController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('auth');
    }
    public function returnview()
    {
        $title = 'Defectives';
        return view('pages.return', compact('title'));
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
    }

    public function returnitem(Request $request)
    {
        $return = Defective::query()
            ->select('defectives.id', 'category', 'item', 'serial')
            ->join('categories', 'categories.id', 'defectives.category_id')
            ->join('items', 'items.id', 'items_id')
            ->wherein('status', ['For receiving'])
            ->where('return_no', $request->retno)
            ->get();
        return DataTables::of($return)->make(true);
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
        if (auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $tosend = Retmail::select('return_to_mail.*', 'branch')
            ->where('return_no', '!=', '0')
            ->join('branches', 'branches.id', 'branch_id')
            ->first();
        

       // dd(storage_path('app/public/excel'.'/'.$attach));
       /*Mail::send('returncopy', $data, function($message) use($attach) {
        $message->to('jolopez@ideaserv.com.ph', 'BSMS')->subject
            ($attach);
        $message->attach(public_path().'/storage/excel/'.$attach);
        $message->from('noreply@ideaserv.com.ph', 'NO REPLY - Create Customer Branch');
        $message->cc(['jerome.lopez.ge2018@gmail.com']);
        });
        if ($tosend) {
            $attach = $tosend->branch.'-'.$tosend->return_no.'.xlsx';
            $excel = Excel::raw(new ExcelExport($tosend->return_no), BaseExcel::XLSX);
            $data = array('office'=> $tosend->branch, 'return_no'=>$tosend->return_no, 'dated'=>$tosend->created_at);
            $send = Mail::send('returncopy', $data, function($message) use($attach, $excel, $tosend) {
                $message->to('jolopez@ideaserv.com.ph', 'BSMS')->subject
                    ($attach);
                $message->attachData($excel, $attach);
                $message->from('noreply@ideaserv.com.ph', 'Defective delivery receipt no.: '.$tosend->return_no);
                $message->cc(['emorej046@gmail.com', 'jerome.lopez.aks2018@gmail.com']);
            });
            //dd($send);
        }*/
        if (auth()->user()->branch->branch == 'Main-Office'){
            return view('pages.warehouse.return', compact('users', 'title'));
        }else if (auth()->user()->branch->branch != 'Warehouse') {
            if (auth()->user()->hasrole('Tech')) {
                return redirect('/');
            }
            return view('pages.branch.return', compact('users', 'title'));
        }else{
            return view('pages.warehouse.return', compact('users', 'title'));
        }
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
    public function table()
    {
        $defective = Defective::query()->select('defectives.updated_at', 'defectives.category_id', 'branch_id as branchid', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('status', 'For return')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->wherein('defectives.status', ['For return', 'For receiving'])->get();
        $waredef =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'Repaired')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        $main =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['Repaired', 'For Repair'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        $repair = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['For receiving', 'For repair', 'Repaired'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')->get();
        if (auth()->user()->branch->branch == 'Warehouse' && !auth()->user()->hasanyrole('Repair', 'Returns Manager')) {
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
        ->make(true);
    }
    public function unrepairable()
    {
        $repair = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['Unrepairable', 'Unrepairable approval'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
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
            $retno = new Retno;
            $retno->user_id = auth()->user()->id;
            $retno->branch_id = auth()->user()->branch->id;
            $retno->status = 'For receiving';
            $retno->return_no = $request->ret;
            $retno->save();
            $excel = Excel::raw(new ExcelExport($request->ret), BaseExcel::XLSX);
            $attach = $branch->branch.'-'.$retno->return_no;
            $data = array('office'=> $branch->branch, 'return_no'=>$retno->return_no, 'dated'=>$retno->created_at);
            Mail::send('returncopy', $data, function($message) use($attach, $excel, $retno) {
                $message->to(auth()->user()->email, auth()->user()->name)->subject
                    ($attach);
                $message->attachData($excel, 'DDR No. '.$retno->return_no.'.xlsx');
                $message->from('noreply@ideaserv.com.ph', 'BSMS');
                $message->cc(['jolopez@ideaserv.com.ph','mallarig@apsoft.com.ph','jerome.lopez.aks2018@gmail.com']);
            });
            //Excel::store(new ExcelExport($request->ret), 'excel/'.auth()->user()->branch->branch.'-'.$request->ret.'.xlsx', 'public');
            $retmail = new Retmail;
            $retmail->branch_id = auth()->user()->branch->id;
            $retmail->user_id = auth()->user()->id;
            $retmail->return_no = $request->ret;
            $retmail->save();

            return response()->json($updates);
        }else{
            if ($request->status == 'Received') {
                $update = Defective::where('id', $request->id)
                    ->where('status', 'For receiving')
                    ->first();
                $item = Item::where('id', $update->items_id)->first();
                $branch = Branch::where('id', $update->branch_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RECEIVED defective $item->item(".mb_strtoupper($update->serial).") from $branch->branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $update->status = "For repair";
                $update->user_id = auth()->user()->id;
                $data = $update->save();

                $check = Defective::where('return_no', $update->return_no)->wherein('status', ['For receiving', 'Incomplete'])->first();
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