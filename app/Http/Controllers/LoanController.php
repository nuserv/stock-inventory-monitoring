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
use App\User;
use Carbon\Carbon;
use Mail;
class LoanController extends Controller
{
    public function __construct()
    {
        
        $this->middleware('auth');
    }
    public function index()
    {
        if (!auth()->user()->hasanyrole('Head', 'Tech')) {
            return redirect('/');
        }
        $title = 'Loans';
        $branches = Branch::where('area_id', auth()->user()->area->id)
            ->where('id', '!=', auth()->user()->branch->id)
            ->orderBy('branch')
            ->get();
        return view('pages.loan', compact('branches', 'title'));
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
        $myloans = Loan::select('lastname', 'name', 'user_id', 'loans.id', 'loans.items_id', 'loans.to_branch_id', 'loans.from_branch_id', 'branches.id as branchid', 'branches.branch', 'loans.updated_at', 'items.item', 'loans.status', 'loans.updated_at')
            ->where('loans.from_branch_id', auth()->user()->branch->id)
            ->where('loans.status', '!=', 'completed')
            ->where('loans.status', '!=', 'deleted')
            ->join('users', 'users.id', '=', 'user_id')
            ->join('branches', 'loans.to_branch_id', '=', 'branches.id')
            ->join('items', 'loans.items_id', '=', 'items.id')
            ->get();
        $loans = Loan::select('lastname', 'name', 'user_id', 'loans.id', 'loans.items_id', 'loans.to_branch_id', 'loans.from_branch_id', 'branches.id as branchid', 'branches.branch', 'loans.updated_at', 'items.item', 'loans.status', 'loans.updated_at')
            ->where('loans.to_branch_id', auth()->user()->branch->id)
            ->where('loans.status', '!=', 'completed')
            ->where('loans.status', '!=', 'deleted')
            ->join('users', 'users.id', '=', 'user_id')
            ->join('branches', 'loans.from_branch_id', '=', 'branches.id')
            ->join('items', 'loans.items_id', '=', 'items.id')
            ->get();
        $merge = $loans->merge($myloans);
        return Datatables::of($merge)
        ->addColumn('date', function (Loan $request){
            return Carbon::parse($request->updated_at->toFormattedDateString().' '.$request->updated_at->toTimeString())->isoFormat('lll');
        })
        ->addColumn('stat', function (Loan $request){
            if ($request->to_branch_id == auth()->user()->branch->id) {
                return 'IN-BOUND';
            }else{
                return 'OUT-BOUND';
            }
        })
        ->addColumn('requestby', function (Loan $request){
            return $request->name.' '.$request->lastname;
        })
        ->make(true);
    }
    public function stock(Request $request)
    {
        $stock = Stock::where('id', $request->item)->first();
        $item = Item::where('id', $stock->items_id)->first();
        $update = Stock::where('id', $request->item)->where('status', 'in')->where('branch_id', auth()->user()->branch->id)->first();
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
        $branch = Branch::where('id', $request->branch)->first();
        $emails = User::select('email')
            ->where('branch_id', $request->branch)
            ->get();
        $allemails = array();
        $allemails[] = 'jerome.lopez.ge2018@gmail.com';
        foreach ($emails as $email) {
            $allemails[]=$email->email;
        }
        $allemails = array_diff($allemails, array($branch->email));
        /*Mail::send('approved', ['reqitem'=>$item->item, 'serial'=>$stock->serial, 'branch'=>$branch],function( $message) use ($allemails, $branch){ 
            $message->to($branch->email, $branch->head)->subject 
                (auth()->user()->branch->branch); 
            $message->from('no-reply@ideaserv.com.ph', 'NO REPLY - '.auth()->user()->branch->branch); 
            $message->cc($allemails); 
        });*/
        $data = $add->save();
        return response()->json($data);
    }

    public function stockUpdate(Request $request)
    {
        $update = Stock::where('branch_id', auth()->user()->branch->id)
            ->where('status', $request->id)
            ->where('id_branch', $request->branch)
            ->first();
        $item = Item::where('id', $update->items_id)->first();
        $branch = Branch::where('id', $update->id_branch)->first();
        $update->status = 'in';
        $update->user_id = auth()->user()->id;
        /*$log = new UserLog;
$log->branch_id = auth()->user()->branch->id;
        $log->activity = "Received request $item->item from $branch->branch." ;
        $log->user_id = auth()->user()->id;
        $log->save();*/
        $data = $update->save();
        return response()->json($data);
    }

    public function getitem(Request $request)
    {
        $serial = Stock::where('id_branch', auth()->user()->branch->id)
            ->where('status', 'loan'.$request->id)
            ->where('branch_id', $request->branch)
            ->first();
        return response()->json($serial);
    }

    public function update(Request $request)
    {
        $loan = Loan::where('id', $request->id)->first();
        $branch = Branch::where('id', $loan->from_branch_id)->first();
        $item = Item::where('id', $loan->items_id)->first();
        $loan->status = $request->status;
        $loan->approved_by = auth()->user()->id;
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        if ($request->status == "completed") {
            $log->activity = "RECEIVED request $item->item from $branch->branch." ;
        }else{
            $log->activity = "APPROVED request $item->item from $branch->branch" ;
        }
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
        $log->branch_id = auth()->user()->branch->id;
        $log->activity = "\DELETE $item->item loan request to $branch->branch";
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $delete->save();
        return response()->json($data);
    }
}