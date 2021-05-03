<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Branch;
use App\Area;
use App\Stock;
use App\Initial;
use App\Item;
use App\Category;
use App\Defective;
use App\Warehouse;
use Auth;
use DB;
use Validator;
use App\UserLog;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        if (auth()->user()->hasanyrole('Repair', 'Returns Manager', 'Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $branch = Branch::all()->sortBy('branch');
        $areas = Area::all();
        $title = 'Offices';
        return view('pages.branch', compact('branch', 'areas', 'title'));
    }

    public function showModal()
    {
        $branch = Branch::all()->sortBy('branch');
        $areas = Area::all();
        return view('modal.add-branch', compact('branch', 'areas'));
    }

    public function getStocks(Request $request, $id)
    {
        $details = DB::table('items')
            ->select(
                'stocks.id',
                'stocks.items_id',
                'items.item',
                'stocks.branch_id as branchid',
                DB::raw
                (
                    'SUM(CASE WHEN stocks.status = \'in\' THEN 1 ELSE 0 END) as available'
                ),
                DB::raw
                (
                    'SUM(CASE WHEN stocks.status = \'service unit\' THEN 1 ELSE 0 END) as stock_out'
                ),
                DB::raw
                (
                    'SUM(CASE WHEN initials.qty = \'0\' THEN 0 ELSE initials.qty END) as initial'
                )
            )
            ->join('stocks', 'stocks.items_id', '=', 'items.id')
            ->join('initials', 'initials.items_id', '=', 'items.id')
            ->where('stocks.branch_id', $id)
            ->groupBy('stocks.items_id')
            ->get();
        if ($request->data != 1) {
            return DataTables::of(Category::query()->orderBy('category')->get())
            ->addColumn('stock_out', function ($category) use ($id){
                    
                if (auth()->user()->branch->branch == 'Warehouse' && $id == 1) {
                    $stock_out = 0;
                }else{
                    $stock_out = Stock::wherein('status', ['service unit', 'pm'])
                        ->where('branch_id', $id)
                        ->where('category_id', $category->id)
                        ->count();
                }
                return $stock_out;
            })
            ->addColumn('available', function ($category) use ($id){
                if (auth()->user()->branch->id == 1 && $id == 1) {
                    $avail = Warehouse::where('status', 'in')
                        ->where('category_id', $category->id)
                        ->count();
                }else{
                    $avail = Stock::where('status', 'in')
                        ->where('branch_id', $id)
                        ->where('category_id', $category->id)
                        ->count();
                }
                return $avail;
            })
            ->addColumn('defectives', function ($category) use ($id){
                if (auth()->user()->branch->id == 1 && $id == 1) {
                    $avail = Warehouse::where('status', 'in')
                        ->where('category_id', $category->id)
                        ->count();
                }else{
                    $avail = Defective::where('status', 'For return')
                        ->where('branch_id', $id)
                        ->where('category_id', $category->id)
                        ->count();
                }
                return $avail;
            })
            ->make(true);
        }else{ 
            $stock = Item::query()->where('category_id', $request->category)->get();
            return DataTables::of($stock)
            ->addColumn('available', function ($item) use ($id){
                if (auth()->user()->branch->id == 1 && $id == 1) {
                    $avail = Warehouse::where('status', 'in')
                        ->where('items_id', $item->id)
                        ->count();
                }else{
                    $avail = Stock::where('status', 'in')
                        ->where('branch_id', $id)
                        ->where('items_id', $item->id)
                        ->count();
                }
                return $avail.' '.$item->UOM;
            })
            ->addColumn('defectives', function ($item) use ($id){
                if (auth()->user()->branch->id == 1 && $id == 1) {
                    $avail = Warehouse::where('status', 'in')
                        ->where('items_id', $item->id)
                        ->count();
                }else{
                    $avail = Defective::where('status', 'For return')
                        ->where('branch_id', $id)
                        ->where('items_id', $item->id)
                        ->count();
                }
                return $avail.' '.$item->UOM;
            })
            ->addColumn('stock_out', function ($item) use ($id){
                if (auth()->user()->branch->id == 1 && $id == 1) {
                    $stock_out = 0;
                }else{
                    $stock_out = Stock::wherein('status', ['service unit', 'pm'])
                        ->where('branch_id', $id)
                        ->where('items_id', $item->id)
                        ->count();
                }
                return $stock_out.' '.$item->UOM;
            })
            ->addColumn('initial', function ($item) use ($id){
                
                $ini = Initial::select('qty')
                    ->where('items_id', $item->id)
                    ->where('branch_id', $id)
                    ->first();
                return $ini->qty.' '.$item->UOM;
            })
            ->make(true);
        }
    }
    public function getBranches()
    {
        if (auth()->user()->hasrole('Warehouse Manager')) {
            $branches = Branch::query()->select('branches.*', 'areas.area')
                ->where('branches.id', '!=', auth()->user()->branch->id)
                ->where('branches.branch', '!=', 'Main-office')
                ->join('areas', 'areas.id', '=', 'branches.area_id')->get();
        }else if (auth()->user()->hasanyrole('Editor', 'Manager', 'Encoder')){
            $branches = Branch::query()->select('branches.*', 'areas.area')
                ->where('branches.id', '!=', auth()->user()->branch->id)
                ->where('branches.branch', '!=', 'Warehouse')
                ->join('areas', 'areas.id', '=', 'branches.area_id')->get();
        }else if (auth()->user()->hasanyrole('Head', 'Tech')){
            $branches = Branch::query()->select('branches.*', 'areas.area')
                ->whereNotin('branches.id', [auth()->user()->branch->id, '1'])
                ->where('branches.branch', '!=', 'Main-office')
                ->where('branches.area_id', '=', auth()->user()->area->id)
                ->join('areas', 'areas.id', '=', 'branches.area_id')->get();
        }
        return DataTables::of($branches)
        ->setRowData([
            'data-id' => '{{$id}}',
            'data-status' => '{{ $status }}',
        ])
        ->addColumn('address', function (Branch $branch){
            return ucwords(mb_strtolower($branch->address));
        })
        ->addColumn('status', function (Branch $branch){

            if ($branch->status == 1) {
                return 'Active';
            } else {
                return 'Inactive';
            }
        })
        ->make(true);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => ['required', 'string', 'min:5', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:10', 'max:255'],
            'area' => ['required', 'string', 'min:1', 'max:255'],
            'contact_person' => ['required', 'string', 'min:3', 'max:255'],
            'mobile' => ['required', 'string', 'min:8', 'max:255'],
            'status' => ['required', 'string', 'min:1', 'max:255'],
        ]);
        if ($validator->passes()) {
            $items = Item::all();
            $branch = new Branch;
            $branch->branch = ucwords(mb_strtolower($request->input('branch_name')));
            $branch->email = mb_strtolower($request->input('email'));
            $branch->address = $request->input('address');
            $branch->area_id = $request->input('area');
            $branch->head = ucwords(mb_strtolower($request->input('contact_person')));
            $branch->phone = $request->input('mobile');
            $branch->status = $request->input('status');
            $branch->save();
            foreach ($items as $item) {
                $initial = new Initial;
                $initial->items_id = $item->id;
                $initial->branch_id = $branch->id;
                $initial->qty = 0;
                $initial->save();
            }
            $data = 'save';
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }
    public function initial(Request $request)
    {
        $initial = Initial::where('items_id', $request->itemid)
            ->where('branch_id', $request->branchid)
            ->first();
        $initial->qty = $request->qty;
        $data = $initial->save();
        return response()->json($data);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:10', 'max:255'],
            'area' => ['required', 'string', 'min:1', 'max:255'],
            'contact_person' => ['required', 'string', 'min:3', 'max:255'],
            'mobile' => ['required', 'string', 'min:8', 'max:255'],
            'status' => ['required', 'string', 'min:1', 'max:255'],
        ]);
        if ($validator->passes()) {
            $branch = Branch::find($id);
            $branch->branch = ucwords(mb_strtolower($request->input('branch_name')));
            $branch->email = $request->input('email');
            $branch->address = $request->input('address');
            $branch->area_id = $request->input('area');
            $branch->head = ucwords(mb_strtolower($request->input('contact_person')));
            $branch->phone = $request->input('mobile');
            $branch->status = $request->input('status');
            $data = $branch->save();
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }
}