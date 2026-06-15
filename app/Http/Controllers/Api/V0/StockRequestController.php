<?php

namespace App\Http\Controllers\Api\V0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Initial;
use App\Stock;
use App\Item;
use App\StockRequest;
use Carbon\Carbon;
use App\UserLog;
use App\Branch;
use App\User;
use App\RequestedItem;

class StockRequestController extends Controller
{
    public function getCategories(Request $request)
    {
        $initials = Initial::query()->where('branch_id', $request->branch_id)
                    ->join('items', 'items.id', '=', 'items_id')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->orderBy('category')
                    ->get();
        // dd($initials);
        $cat = array();
        foreach ($initials as $initial) {
            $count = Stock::query()->where('stocks.status', 'in')
                ->where('branch_id', $request->branch_id)
                ->where('items_id', $initial->items_id)->count();
            if ($count == "0") {
                $category = Item::query()->select('items.category_id as id', 'categories.category')
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

    public function getItems(Request $request){
        $initials = Initial::where('branch_id', $request->branch_id)
            ->join('items', 'items_id', '=', 'items.id')
            ->where('category_id', $request->category_id)
            ->orderBy('item')
            ->get();
        $icode = [];
        $itm =[];
        foreach ($initials as $initial) {
            $count = Stock::where('stocks.status', 'in')
                ->where('branch_id', $request->branch_id)
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

    public function checkTicket(Request $request)
    {
        \Log::info('Checking ticket.', ['ticket' => $request->ticket]);
        $requestno = StockRequest::where('branch_id', $request->branch_id)
            ->where('status', 'PENDING')
            ->where('type', 'Service')
            ->where('stat', 'ACTIVE')
            ->where('ticket', $request->ticket)
            ->where( 'created_at', '>', Carbon::now()->subDays(60))
            ->first();

        return response()->json([
            'ticket' => $requestno ? $requestno->ticket : null
        ]);
    }

    public function storeRequestService(Request $request){
        // Find if the request already exists to prevent duplicates
        $stockRequest = StockRequest::where('branch_id', $request->branch_id)
            ->where('request_no', $request->request_no)
            ->first();
    
        if($stockRequest){
            return response()->json([
                'error' => 'Duplicate request number.',
                'request_no' => $stockRequest->request_no
            ], 409); // Use 409 Conflict for duplicates
        }
    
        // Since the request is new, create it.
        $newStockRequest = StockRequest::create([
            'request_no' => $request->request_no,
            'user_id' => $request->user_id,
            'branch_id' => $request->branch_id,
            'area_id' => $request->area_id,
            'status' => 'PENDING',
            'stat' => 'ACTIVE',
            'customer_id' => $request->customer_id,
            'customer_branch_id' => $request->customer_branch_id,
            'ticket' => $request->ticket,
            'type' => 'Service',
        ]);
    
        if($newStockRequest){
            // Log the creation of the main request
            $user = User::find($request->user_id);
            $branch = Branch::find($request->branch_id);
            if ($user && $branch) {
                $log = new UserLog;
                $log->branch_id = $request->branch_id;
                $log->branch = $branch->branch;
                $log->activity = "CREATE SERVICE STOCK Request no. $request->request_no";
                $log->user_id = $request->user_id;
                $log->fullname = trim($user->name.' '.$user->middlename.' '.$user->lastname);
                $log->save();
            }
    
            // Loop through the submitted items and create a RequestedItem for each one.
            foreach ($request->items as $item) {
                $reqitem = new RequestedItem;
                $reqitem->request_no = $request->request_no;
                $reqitem->items_id = $item['items_id']; 
                $reqitem->quantity = $item['quantity'];
                $reqitem->pending = $item['quantity'];
                $reqitem->branch_id = $request->branch_id;
                $reqitem->status = 'PENDING';
                $reqitem->save();
            }
        } else {
            return response()->json(['error' => 'Failed to create stock request.'], 500);
        }
        return response()->json([
            'message' => 'Stock request created successfully.',
            'request_no' => $newStockRequest->request_no
        ], 201);
    }

    public function getStockRequest(Request $request)
    {
        $stockRequest = StockRequest::where('branch_id', $request->branch_id)
            ->where('status', '!=', 'DELETED')
            ->where('stat', 'ACTIVE')
            ->where('type', 'Service')
            ->get();
        return response()->json($stockRequest);
    }

    public function getStockRequestItems(Request $request)
    {
        $stockRequestItems = RequestedItem::where('request_no', $request->request_no)
            ->where('status', '!=', 'DELETED')
            ->get();
        return response()->json($stockRequestItems);
    }
    //get item by id
    public function getItem(Request $request)
    {
        $item = Item::where('id', $request->id)
            ->first();
        return response()->json($item);
    }
}
