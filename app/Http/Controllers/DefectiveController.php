<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use Route;
use App\Defective;
use App\Branch;
use App\Item;
use App\Warehouse;
use App\Category;
use Carbon\Carbon;
use App\UserLog;
use DB;
use Auth;
class DefectiveController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('auth');
    }
    public function index()
    {
        
        $title = 'Defective Unit/Parts';
        $users = User::all();
        if (auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }

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
    public function table()
    {
        $defective = Defective::query()->select('defectives.updated_at', 'defectives.category_id', 'branch_id as branchid', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('branch_id', auth()->user()->branch->id)
            ->where('status', 'For return')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->wherein('defectives.status', ['For return', 'For receiving']);
        $waredef =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'Repaired')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
        $main =Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['Repaired', 'For Repair'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
        $repair = Defective::query()->select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['For receiving', 'For repair', 'Repaired'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id');
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
                $items = Item::where('id', $updates->items_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RETURN defective $items->item(S/N: $updates->serial) to warehouse." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $updates->save();
            }
            return response()->json($updates);
        }else{
            if ($request->status == 'Received') {
                $update = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
                    ->where('status', 'For receiving')
                    ->first();
                $item = Item::where('id', $update->items_id)->first();
                $branch = Branch::where('id', $update->branch_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "RECEIVED defective $item->item($update->serial) from $branch->branch." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $log->save();
                $update->status = "For repair";
                $update->user_id = auth()->user()->id;
                $data = $update->save();
                return response()->json($data);
            }
            if ($request->status == 'Repaired') {
                $repaired = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
                    ->where('status', 'For repair')
                    ->first();
                $repaired->status = "Repaired";
                $repaired->save();
                $item = Item::where('id', $repaired->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();
                $log = new UserLog;
                $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
                $log->activity = "REPAIRED $item->item($repaired->serial) and send to Warehouse." ;
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
                $log->activity = "ADD $item->item($pending->serial) from Repair to Stock." ;
                $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
                $pending->save();
                $data = $log->save();
                return response()->json($data);
            }
            if ($request->status == 'Unrepairable approval') {
                $unreapairable = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
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