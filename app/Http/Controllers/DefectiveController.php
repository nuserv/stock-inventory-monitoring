<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use App\Defective;
use App\Branch;
use App\Item;
use App\Warehouse;
use App\Category;
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
        if (auth()->user()->branch->branch != 'Warehouse') {
            return view('pages.branch.return', compact('users', 'title'));
        }else{
            return view('pages.warehouse.return', compact('users', 'title'));
        }
    }

    public function table()
    {
        $defective = Defective::select('defectives.updated_at', 'defectives.category_id', 'branch_id as branchid', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('branch_id', auth()->user()->branch->id)
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->wherein('status', ['For return', 'For receiving'])
            ->get();
            
        $waredef =Defective::select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['For receiving', 'Repaired', 'For Repair'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')
            ->get();

        $repair = Defective::select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->wherein('defectives.status', ['For repair', 'Repaired'])
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')
            ->get();
        
        if (auth()->user()->branch->branch == 'Warehouse' && !auth()->user()->hasrole('Repair')) {
            $data = $waredef;
        }else if (auth()->user()->branch->branch == 'Warehouse' && auth()->user()->hasrole('Repair')){
            $data = $repair;
        }else{
            $data = $defective;
        }
        

        //dd($data);
        return DataTables::of($data)
        
        ->addColumn('date', function (Defective $data){
            return $data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString();
        })

        ->addColumn('category', function (Defective $data){
            //dd($data->itemid);
            $cat = Category::where('id', $data->category_id)->first();
            //dd($data);
            return $cat->category;
        })


        ->make(true);
    }

    public function unrepairable()
    {
        $repair = Defective::select('branches.branch', 'defectives.category_id', 'branches.id as branchid', 'defectives.updated_at', 'defectives.id as id', 'items.item', 'items.id as itemid', 'defectives.serial', 'defectives.status')
            ->where('defectives.status', 'unrepairable')
            ->join('items', 'defectives.items_id', '=', 'items.id')
            ->join('branches', 'defectives.branch_id', '=', 'branches.id')
            ->get();
        
        //dd($data);
        return DataTables::of($repair)
        
        ->addColumn('date', function (Defective $data){
            return $data->updated_at->toFormattedDateString().' '.$data->updated_at->toTimeString();
        })

        ->addColumn('category', function (Defective $data){
            //dd($data->itemid);
            $cat = Category::where('id', $data->category_id)->first();
            //dd($data);
            return $cat->category;
        })
        
        ->make(true);
    }

    public function update(Request $request)
    {
        if (auth()->user()->branch->branch != 'Warehouse') {

            $updates = Defective::where('branch_id', auth()->user()->branch->id)
                ->where('id', $request->id)
                ->where('status', 'For return')
                ->first();
            $updates->status = 'For receiving';
            $updates->user_id = auth()->user()->id;


            $items = Item::where('id', $updates->items_id)->first();
            $branch = Branch::where('id', auth()->user()->branch->id)->first();
            
            $log = new UserLog;
            $log->activity = "Return defective $items->item($updates->serial) to warehouse." ;
            $log->user_id = auth()->user()->id;
            $log->save();
            
            $data = $updates->save();

        }else{
            if ($request->status == 'Received') {
                $update = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
                    ->where('status', 'For receiving')
                    ->first();
                $item = Item::where('id', $update->items_id)->first();
                $branch = Branch::where('id', $update->branch_id)->first();

                $log = new UserLog;
                $log->activity = "Received defective $item->item($update->serial) from $branch->branch." ;
                $log->user_id = auth()->user()->id;
                $log->save();
                $update->status = "For repair";
                $update->user_id = auth()->user()->id;
                $data = $update->save();
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
                $log->activity = "Repaired $item->item($repaired->serial) and send to Warehouse." ;
                $log->user_id = auth()->user()->id;
                $repaired->save();

                $data = $log->save();
            }

            if ($request->status == 'Repaired') {
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
                $log->activity = "Add $item->item($pending->serial) from Repair to Stock." ;
                $log->user_id = auth()->user()->id;
                $pending->save();

                $data = $log->save();
            }

            if ($request->status == 'unrepairable') {
                $unreapairable = Defective::where('id', $request->id)
                    ->where('branch_id', $request->branch)
                    ->where('status', 'For repair')
                    ->first();
                $unreapairable->status = "unrepairable";
                $unreapairable->save();

                $item = Item::where('id', $unreapairable->items_id)->first();
                $cat = Category::where('id', $item->category_id)->first();

                $log = new UserLog;
                $log->activity = "Marked $item->item($unreapairable->serial) as unreapairabled." ;
                $log->user_id = auth()->user()->id;
                $unreapairable->save();

                $data = $log->save();
            }
        }
        return response()->json($data);

    }
}
