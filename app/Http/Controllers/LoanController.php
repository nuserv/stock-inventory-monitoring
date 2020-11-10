<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Loan;
use Auth;
use App\Item;
use App\Branch;
use App\Stock;
use App\UserLog;
class LoanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->hasrole('Administrator')) {
            return redirect('/');
        }

        $branches = Branch::where('area_id', auth()->user()->area->id)
            ->where('id', '!=', auth()->user()->branch->id)
            ->get();

        return view('pages.loan', compact('branches'));
    }

    public function getItemCode(Request $request)
    {
        $cat = Item::select('category_id')
            ->where('items.id', $request->id)
            ->first();
        $items = Item::select('item', 'id')
            ->where('category_id', $cat->category_id)
            ->get();
        return response()->json($items);
    }

    public function table(Request $request)
    {
        $myloans = Loan::select('loans.id', 'loans.items_id', 'loans.to_branch_id', 'loans.from_branch_id', 'branches.id as branchid', 'branches.branch', 'loans.updated_at', 'items.item', 'loans.status', 'loans.updated_at')
            ->where('loans.from_branch_id', auth()->user()->branch->id)
            ->where('loans.status', '!=', 'completed')
            ->where('loans.status', '!=', 'deleted')
            ->join('branches', 'loans.to_branch_id', '=', 'branches.id')
            ->join('items', 'loans.items_id', '=', 'items.id')
            ->get();
        $loans = Loan::select('loans.id', 'loans.items_id', 'loans.to_branch_id', 'loans.from_branch_id', 'branches.id as branchid', 'branches.branch', 'loans.updated_at', 'items.item', 'loans.status', 'loans.updated_at')
            ->where('loans.to_branch_id', auth()->user()->branch->id)
            ->where('loans.status', '!=', 'completed')
            ->where('loans.status', '!=', 'deleted')
            ->join('branches', 'loans.from_branch_id', '=', 'branches.id')
            ->join('items', 'loans.items_id', '=', 'items.id')
            ->get();

        $merge = $loans->merge($myloans);
        return Datatables::of($merge)

        ->addColumn('date', function (Loan $request){

            return $request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString();
        })

        ->addColumn('stat', function (Loan $request){
            
            if ($request->to_branch_id == auth()->user()->branch->id) {
                return 'IN-BOUND';
            }else{
                return 'OUT-BOUND';
            }
        })

        ->make(true);
    }
    public function stock(Request $request)
    {
        $stock = Stock::where('id', $request->item)->first();
        $item = Item::where('id', $stock->items_id)->first();

        $update = Stock::where('id', $request->item)->where('branch_id', auth()->user()->branch->id)->first();
        $update->status = 'loan'.$request->id;
        $update->id_branch = $request->branch;
        $update->user_id = auth()->user()->id;
        $update->save();

        $add = new Stock;
        $add->category_id = $item->category_id;
        $add->branch_id = $request->branch;
        $add->items_id = $item->id;
        $add->user_id = auth()->user()->id;
        $add->serial = $stock->serial;
        $add->status = $request->id;
        $add->id_branch = auth()->user()->branch->id;
        $data = $add->save();

        return response()->json($data);
    }

    public function stockUpdate(Request $request)
    {
        $update = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('status', $request->id)
            ->where('id_branch', $request->branch)
            ->first();
        //dd($update);
        $item = Item::where('id', $update->items_id)->first();
        //dd($item);
        $branch = Branch::where('id', $update->id_branch)->first();
        $update->status = 'in';
        $update->user_id = auth()->user()->id;
        $log = new UserLog;
        $log->activity = "Received request $item->item from $branch->branch." ;
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $update->save();
        return response()->json($data);
    }

    public function getitem(Request $request)
    {
        $serial = Stock::where('id_branch', auth()->user()->branch->id)
            ->where('status', 'loan'.$request->id)
            ->where('branch_id', $request->branch)
            ->first();
            //dd($serial->serial);
        return response()->json($serial);
    }

    public function update(Request $request)
    {
        $loan = Loan::where('id', $request->id)->first();
        $branch = Branch::where('id', $loan->from_branch_id)->first();
        $item = Item::where('id', $loan->items_id)->first();
        $loan->status = $request->status;
        $loan->user_id = auth()->user()->id;
        $log = new UserLog;
        $log->activity = "Approved request $item->item from $branch->branch" ;
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $loan->save();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $delete = Loan::where('id', $request->id)->first();
        $item = Item::where('id', $delete->items_id)->first();
        $branch = Branch::where('id', $delete->to_branch_id)->first();
        $delete->status = $request->status;
        $log = new UserLog;
        $log->activity = "Delete $item->item loan request to $branch->branch";
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $delete->save();
        return response()->json($data);
    }
}
