<?php

namespace App\Http\Controllers;

use Auth;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Item;
use App\Models\BSMS_Item;
use App\Models\BSMS_Branch;
use App\Models\BSMS_Initial_Warehouse;
use App\Models\BSMS_Initial_Branch;
use App\Models\Part;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\Transfer;
use App\Models\RequestTransfer;
use App\Models\Prepare;
use App\Models\User;
use App\Models\UserLogs;
use App\Models\Temp;
use Yajra\Datatables\Datatables;

class AssemblyController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function assembly(){
        $items = Item::select('id','item')->where('assemble','YES')->get()->sortBy('item');
        return view('/pages/assembly/assembly', compact('items'));
    }

    public function itemsAssembly(Request $request){
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }

    public function uomAssembly(Request $request){
        $data = Item::selectRaw('UOM as uom, prodcode')
            ->where('id',$request->item_id)
            ->get();

        return response($data);
    }

    public function saveReqNum(Request $request){
        do{
            $requests = new Requests;
            $requests->request_number = $request->request_number;
            $requests->requested_by = auth()->user()->id;
            $requests->needdate = $request->needdate;
            $requests->request_type = $request->request_type;
            $requests->status = '1';
            $requests->item_id = $request->item_id;
            $requests->qty = $request->qty;
            $sql = $requests->save();
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }

        return response($result);
    }

    public function saveRequest(Request $request){
        do{
            $stockRequest = new StockRequest;
            $stockRequest->request_number = $request->request_number;
            $stockRequest->item = $request->item;
            $stockRequest->quantity = $request->quantity * $request->qty;
            $stockRequest->served = '0';
            $stockRequest->pending = $request->quantity * $request->qty;
            $sql = $stockRequest->save();
        }
        while(!$sql);

        return response('true');
    }

    public function logSave(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW ASSEMBLY JOB ORDER: User successfully requested for assembly $request->qty-Unit/s of '$request->item_desc' under Assembly Job Order No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function request_data(){
        $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%b. %d, %Y") AS reqdatetime, DATE_FORMAT(requests.needdate, "%b. %d, %Y") AS needdatetime, DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, needdate, requests.item_id AS item_id, items.item AS item_desc, qty, assembly_reqnum, cancelled_by, verify')
            ->where('requests.requested_by', auth()->user()->id)
            ->whereIn('requests.request_type', ['4','5'])
            // ->whereNotIn('requests.status', ['7','8','14','19','26'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->join('items', 'items.id', '=', 'requests.item_id')
            ->orderBy('reqdate', 'DESC')
            ->orderBy('requests.needdate', 'DESC')
            ->orderBy('requests.id', 'DESC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function reload(){
        if(Requests::count() == 0){
            return 'NULL';
        }
        $data_update = Requests::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function receiveRequest(Request $request){
        if($request->inc == 'true'){
            if($request->request_type == '4'){
                Requests::where('request_number', $request->request_number)
                    ->update(['status' => '15']);
                Requests::where('request_number', $request->assembly_reqnum)
                    ->update(['status' => '23']);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '15']);
                }
                while(!$sql);
            }
        }
        else{
            if($request->request_type == '4'){
                Requests::where('request_number', $request->request_number)
                    ->update(['status' => '19', 'verify' => 'Confirmed']);
                Requests::where('request_number', $request->assembly_reqnum)
                    ->update(['status' => '12']);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '12']);
                }
                while(!$sql);
            }
        }

        return response('true');
    }

    public function receiveItems(Request $request){
        if($request->type == 'single'){
            if($request->status == '3'){
                Stock::where('id', $request->id)
                    ->update(['status' => 'received', 'user_id' => auth()->user()->id]);
            }
            if($request->status == '17'){
                Stock::where('id', $request->id)
                    ->update(['status' => 'assembly', 'user_id' => auth()->user()->id]);
            }
            if($request->request_type == '4'){
                $transfer = new Transfer;
                $transfer->request_number = $request->request_number;
                $transfer->stock_id = $request->id;
                $transfer->save();
            }
        }
        else{
            foreach($request->items_id as $key => $value){
                if($key == 'itemQty'){
                    $itemQty = $value;
                }
                if($key == 'item_id'){
                    $item_id = $value;
                }
            }
            $include[] = $request->request_number;
            if($request->status == '3'){
                $stocks = Stock::where('item_id', $item_id)
                    ->whereIn('status', ['prep'])
                    ->whereIn('request_number', $include)
                    ->whereIn('location_id', ['1','2','3','4'])
                    ->orderBy('location_id','ASC')
                    ->limit($itemQty)
                    ->get();
                foreach($stocks as $stock){
                    $stock->status = 'received';
                    $stock->user_id = auth()->user()->id;
                    $stock->save();
                    if($request->request_type == '4'){
                        if(Transfer::where('stock_id', $stock->id)->where('request_number', $request->request_number)->count() == 0){
                            $transfer = new Transfer;
                            $transfer->request_number = $request->request_number;
                            $transfer->stock_id = $stock->id;
                            $transfer->save();
                        }
                    }
                }
            }
            if($request->status == '17'){
                $stocks = Stock::where('item_id', $item_id)
                    ->whereIn('status', ['incomplete'])
                    ->whereIn('request_number', $include)
                    ->whereIn('location_id', ['1','2','3','4'])
                    ->orderBy('location_id','ASC')
                    ->limit($itemQty)
                    ->get();
                foreach($stocks as $stock){
                    $stock->status = 'assembly';
                    $stock->user_id = auth()->user()->id;
                    $stock->save();
                    if($request->request_type == '4'){
                        if(Transfer::where('stock_id', $stock->id)->where('request_number', $request->request_number)->count() == 0){
                            $transfer = new Transfer;
                            $transfer->request_number = $request->request_number;
                            $transfer->stock_id = $stock->id;
                            $transfer->save();
                        }
                    }
                }
            }
        }
        return response('true');
    }

    public function logReceive(Request $request){
        if($request->request_type == '4'){
            $request_number_orig = Requests::where('request_number', $request->request_number)->first()->assembly_reqnum;
            if(Requests::where('assembly_reqnum', $request_number_orig)->count() > 0){
                $include = Requests::where('assembly_reqnum', $request_number_orig)->get('request_number');
                $include = explode(',',str_replace(']','',(str_replace('[','',(str_replace('"}','',(str_replace('{"request_number":"','',$include))))))));
            }
            $include[] = $request_number_orig;
        }
        else{
            $include[] = $request->request_number;
        }
        if($request->status == '3'){
            Stock::whereIn('request_number', $include)
                ->where('status', '=', 'prep')
                ->update(['status' => 'incomplete', 'user_id' => auth()->user()->id]);

            Stock::whereIn('request_number', $include)
                ->where('status', '=', 'received')
                ->update(['status' => 'assembly', 'user_id' => auth()->user()->id]);
        }
        Stock::whereIn('request_number', $include)
            ->whereIn('status', ['assembly'])
            ->where('batch', 'new')
            ->update(['batch' => 'old']);
        Stock::whereIn('request_number', $include)
            ->whereIn('status', ['assembly'])
            ->where('batch', '')
            ->update(['batch' => 'new']);
        Stock::whereIn('request_number', $include)
            ->whereIn('status', ['incomplete'])
            ->update(['batch' => '']);

        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE ASSEMBLY JOB ORDER: User successfully received incomplete needed parts of Assembly Job Order No. $request->request_number.";
            $userlogs->save();
        }
        else{
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE ASSEMBLY JOB ORDER: User successfully received complete needed parts of Assembly Job Order No. $request->request_number.";
            $userlogs->save();
        }
        return response('true');
    }

    public function defectiveRequest(Request $request){
        $status = Requests::where('request_number', $request->request_number)
            ->update(['status' => '18']);

        $needdate = Requests::where('request_number', $request->request_number)->first()->needdate;
        $orderID = Requests::where('request_number', $request->request_number)->first()->request_type == '8' ? 'SALES' : '';

        $requests = new Requests;
        $requests->request_number = $request->generatedReqNum;
        $requests->assembly_reqnum = $request->request_number;
        $requests->orderID = $orderID;
        $requests->requested_by = auth()->user()->id;
        $requests->needdate = $needdate;
        $requests->request_type = '4';
        $requests->status = '1';
        $requests->item_id = '0';
        $sql = $requests->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }

        return response($result);
    }

    public function defectiveItems(Request $request){
        if($request->type == 'single'){
            Stock::where('id', $request->id)
                ->update(['status' => 'defective', 'batch' => '', 'warranty_id' => '', 'user_id' => auth()->user()->id]);
        }
        else{
            foreach($request->items_id as $key => $value){
                if($key == 'itemQty'){
                    $itemQty = $value;
                }
                if($key == 'item_id'){
                    $item_id = $value;
                }
            }
            if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
                $include = Requests::where('assembly_reqnum', $request->request_number)->get('request_number');
                $include = explode(',',str_replace(']','',(str_replace('[','',(str_replace('"}','',(str_replace('{"request_number":"','',$include))))))));
            }
            $include[] = $request->request_number;
            $sql = Stock::where('item_id', $item_id)
                ->whereIn('status', ['staging','assembly'])
                ->whereIn('request_number', $include)
                ->whereIn('location_id', ['1','2','3','4'])
                ->orderBy('location_id','ASC')
                ->limit($itemQty)
                ->update(['status' => 'defective', 'batch' => '', 'warranty_id' => '', 'user_id' => auth()->user()->id]);
        }
        return response('true');
    }

    public function logDefective(Request $request){
        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['out','staging','asset','demo','assembly','assembled'])
            ->update(['batch' => '']);

        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include = Requests::where('assembly_reqnum', $request->request_number)->get('request_number');
            $include = explode(',',str_replace(']','',(str_replace('[','',(str_replace('"}','',(str_replace('{"request_number":"','',$include))))))));
        }
        $include[] = $request->request_number;

        do{
            $list = Stock::select('request_number', 'item_id', 'warranty_id',
                DB::raw
                    (
                        "SUM(CASE WHEN stocks.status = 'defective' THEN 1 ELSE 0 END) as quantity"
                    )
                )
                ->whereIn('request_number', $include)
                ->where('status', 'defective')
                ->groupby('request_number', 'item_id', 'warranty_id')
                ->get();
        }
        while(!$list);

        foreach($list as $key){
            do{
                $stockRequest = new StockRequest;
                $stockRequest->request_number = $request->generatedReqNum;
                $stockRequest->item = $key->item_id;
                $stockRequest->warranty = $key->warranty_id;
                $stockRequest->quantity = $key->quantity;
                $stockRequest->served = '0';
                $stockRequest->pending = $key->quantity;
                $dump = $stockRequest->save();
            }
            while(!$dump);
        }

        if(Requests::where('request_number', $request->request_number)->first()->request_type == 8){
            $reqtype = 'For Staging';
            $items = 'items';
            $info = strtoupper($reqtype);
        }
        else{
            $reqtype = 'Assembly';
            $items = 'parts';
            $info = strtoupper($reqtype);
        }
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "REQUESTED $info DEFECTIVE REPLACEMENTS: User successfully requested replacements for defective $items of $reqtype Job Order No. $request->request_number under Replacement Job Order No. $request->generatedReqNum.";
        $userlogs->save();

        return response('true');
    }

    public function assembleRequest(Request $request){
        do{
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '13']);
        }
        while(!$sql);

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "ASSEMBLED FOR RECEIVING: User successfully Assembled Item for receiving Assembly Job Order No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function receiveAssembled(Request $request){
        $sql = Requests::where('request_number', $request->request_number)
            ->update(['status' => '14', 'verify' => 'Confirmed']);

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
            Temp::where('request_number', $request->request_number)->delete();

            if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
                $include = Requests::where('assembly_reqnum', $request->request_number)->get('request_number');
                $include = explode(',',str_replace(']','',(str_replace('[','',(str_replace('"}','',(str_replace('{"request_number":"','',$include))))))));
            }
            $include[] = $request->request_number;

            Stock::whereIn('request_number', $include)
                ->where('status', 'assembly')
                ->update(['status' => 'assembled', 'user_id' => auth()->user()->id]);
        }

        return response($result);
    }

    public function addAssembled(Request $request){
        do{
            $stocks = new Stock;
            $stocks->request_number = $request->request_number;
            $stocks->assembly_reqnum = $request->request_number;
            $stocks->drnumber = 'N/A';
            $stocks->item_id = $request->item_id;
            $stocks->user_id = auth()->user()->id;
            $stocks->location_id = $request->location_id;
            $stocks->supplier_id = '0';
            $stocks->status = 'in';
            $stocks->serial = strtoupper($request->serial);
            $stocks->rack = 'N/A';
            $stocks->row = 'N/A';
            $sql = $stocks->save();
        }
        while(!$sql);

        return response('true');
    }

    public function logAssembled(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "RECEIVED ASSEMBLED ITEM: User successfully received into warehouse stocks '$request->item_name' with Assembly Job Order No. $request->request_number.";
        $userlogs->save();

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "ADDED STOCK: User successfully added $request->qty-Unit/s Stock of '$request->item_name' to $request->location_name with Serial Number '$request->serial'.";
        $userlogs->save();

        return response('true');
    }

    public function saveAssemblyItem(Request $request){
        if(trim($request->item) != ''){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item))
                ->count();
        }
        else{
            $item = 0;
        }
        if(trim($request->prodcode) != ''){
            $code = Item::query()->select()
                ->whereRaw('LOWER(prodcode) = ?',strtolower($request->prodcode))
                ->count();
        }
        else{
            $code = 0;
        }
        if($item > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else if($code > 0){
            $data = array('result' => 'dupecode');
            return response()->json($data);
        }
        else{
            $items = new Item;
            $items->item = ucwords($request->item);
            $items->prodcode = $request->prodcode;
            $items->category_id = '0';
            $items->minimum = $request->minimum;
            $items->UOM = 'Unit';
            $items->assemble = 'YES';
            $items->serialize = 'YES';
            $items->n_a = 'no';
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $stocks = new Stock;
                $stocks->item_id = $id;
                $stocks->user_id = auth()->user()->id;
                $stocks->status = 'default';
                $stocks->qty = '1';
                $stocks->save();

                $items = new BSMS_Item;
                $items->item = ucwords($request->item);
                $items->prodcode = $request->prodcode;
                $items->category_id = '0';
                $items->minimum = $request->minimum;
                $items->UOM = 'Unit';
                $items->assemble = 'YES';
                $items->serialize = 'YES';
                $items->n_a = 'no';
                $items->save();

                $initialwh = new BSMS_Initial_Warehouse;
                $initialwh->items_id = $id;
                $initialwh->qty = 10;
                $initialwh->save();

                $branches = BSMS_Branch::all();
                foreach($branches as $branch){
                    $initial = new BSMS_Initial_Branch;
                    $initial->items_id = $id;
                    $initial->branch_id = $branch->id;
                    $initial->qty = 5;
                    $initial->save();
                }
            }
            $data = array('result' => $result, 'id' => $id);
            return response()->json($data);
        }
    }

    public function saveParts(Request $request){
        $parts = new Part;
        $parts->item_id = $request->item_id;
        $parts->part_id = $request->part_id;
        $parts->quantity = $request->quantity;
        $parts->priority = $request->priority;
        $sql = $parts->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }

        return response($result);
    }

    public function logItem(Request $request){
        $item = ucwords($request->item);

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "ASSEMBLED ITEM ADDED: User successfully saved new Assembled Item '$item' with ItemID#$request->item_id.";
        $userlogs->save();

        return response('true');
    }

    public function itemDetails(Request $request){
        $itemDetails = Part::query()->select('items.id AS item_id','items.item','items.UOM AS uom','quantity')
            ->join('items', 'items.id', 'parts.part_id')
            ->where('parts.item_id',$request->item_id)
            ->orderBy('priority','ASC')
            ->get();

        return response()->json($itemDetails);
    }

    public function updateAssemblyItem(Request $request){
        if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
                ->count();
        }
        else{
            $item = 0;
        }
        if(strtoupper($request->item_code) != strtoupper($request->item_code_original)){
            $code = Item::query()->select()
                ->whereRaw('LOWER(prodcode) = ?',strtolower($request->item_code))
                ->count();
        }
        else{
            $code = 0;
        }
        if($item > 0){
            $result = 'duplicate';
            return response($result);
        }
        else if($code > 0){
            $result = 'dupecode';
            return response($result);
        }
        else{
            $item_name = ucwords($request->item_name);

            $items = Item::find($request->item_id);
            $items->item = $item_name;
            $items->prodcode = $request->item_code;
            $items->category_id = '0';
            $items->minimum = $request->minimum;
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $items = BSMS_Item::find($request->item_id);
                $items->item = $item_name;
                $items->prodcode = $request->item_code;
                $items->category_id = '0';
                $items->minimum = $request->minimum;
                $sql = $items->save();

                if($item_name != $request->item_name_original){
                    $item_desc = "【Product Description: FROM '$request->item_name_original' TO '$item_name'】";
                }
                else{
                    $item_desc = NULL;
                }
                if($request->item_code != $request->item_code_original){
                    $item_code = "【Product Code: FROM '$request->item_code_original' TO '$request->item_code'】";
                }
                else{
                    $item_code = NULL;
                }
                if($request->minimum != $request->minimum_original){
                    $minimum = "【Minimum Stock: FROM '$request->minimum_original' TO '$request->minimum'】";
                }
                else{
                    $minimum = NULL;
                }
                if($request->edited_parts == 'true'){
                    Part::where('item_id', $request->item_id)->delete();
                    $edited_parts = "【Parts Details: Assembled Item Part/s have been changed.】";
                }
                else{
                    $edited_parts = NULL;
                }

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "ASSEMBLED ITEM UPDATED: User successfully updated details of '$request->item_name_original' with the following CHANGES: $edited_parts $item_desc $item_code $minimum.";
                $userlogs->save();
            }

            return response($result);
        }
    }

    public function partsDetails(Request $request){
        $partsDetails = Part::query()->select('items.prodcode','items.item','items.UOM AS uom','quantity','items.id AS item_id','items.category_id AS category_id')
            ->join('items', 'items.id', 'parts.part_id')
            ->where('parts.item_id',$request->item_id)
            ->orderBy('item','ASC')
            ->get();

        return DataTables::of($partsDetails)
        ->addColumn('main', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('balintawak', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['5'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('malabon', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['6'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->make(true);
    }

    public function saveBundledItems(Request $request){
        if(trim($request->item) != ''){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item))
                ->count();
        }
        else{
            $item = 0;
        }
        if($item > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $items = new Item;
            $items->item = ucwords($request->item);
            $items->prodcode = $request->prodcode;
            $items->category_id = '1';
            $items->minimum = $request->minimum;
            $items->UOM = 'Unit';
            $items->assemble = 'BUNDLE';
            $items->serialize = 'YES';
            $items->n_a = 'no';
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $stocks = new Stock;
                $stocks->item_id = $id;
                $stocks->user_id = auth()->user()->id;
                $stocks->status = 'default';
                $stocks->qty = '1';
                $stocks->save();

                $items = new BSMS_Item;
                $items->item = ucwords($request->item);
                $items->prodcode = $request->prodcode;
                $items->category_id = '1';
                $items->minimum = $request->minimum;
                $items->UOM = 'Unit';
                $items->assemble = 'BUNDLE';
                $items->serialize = 'YES';
                $items->n_a = 'no';
                $items->save();

                $initialwh = new BSMS_Initial_Warehouse;
                $initialwh->items_id = $id;
                $initialwh->qty = 10;
                $initialwh->save();

                $branches = BSMS_Branch::all();
                foreach($branches as $branch){
                    $initial = new BSMS_Initial_Branch;
                    $initial->items_id = $id;
                    $initial->branch_id = $branch->id;
                    $initial->qty = 5;
                    $initial->save();
                }
            }
            $data = array('result' => $result, 'id' => $id);
            return response()->json($data);
        }
    }

    public function updateBundledItems(Request $request){
        if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
                ->count();
        }
        else{
            $item = 0;
        }
        if(strtoupper($request->item_code) != strtoupper($request->item_code_original)){
            $code = Item::query()->select()
                ->whereRaw('LOWER(prodcode) = ?',strtolower($request->item_code))
                ->count();
        }
        else{
            $code = 0;
        }
        if($item > 0){
            $result = 'duplicate';
            return response($result);
        }
        else if($code > 0){
            $result = 'dupecode';
            return response($result);
        }
        else{
            $item_name = ucwords($request->item_name);
            $prod_code = strtoupper($request->item_code);

            $items = Item::find($request->item_id);
            $items->item = $item_name;
            $items->prodcode = $request->item_code;
            $items->category_id = '1';
            $items->minimum = $request->minimum;
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $items = BSMS_Item::find($request->item_id);
                $items->item = $item_name;
                $items->prodcode = $request->item_code;
                $items->category_id = '1';
                $items->minimum = $request->minimum;
                $sql = $items->save();

                if($prod_code != $request->item_code_original){
                    $item_code = "【Product Code: FROM '$request->item_code_original' TO '$prod_code'】";
                }
                else{
                    $item_code = NULL;
                }
                if($item_name != $request->item_name_original){
                    $item_desc = "【Product Description: FROM '$request->item_name_original' TO '$item_name'】";
                }
                else{
                    $item_desc = NULL;
                }
                if($request->edited_parts == 'true'){
                    Part::where('item_id', $request->item_id)->delete();
                    $edited_parts = "【Bundle Details: Bundle Inclusive/s have been changed.】";
                }
                else{
                    $edited_parts = NULL;
                }

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "BUNDLED ITEMS UPDATED: User successfully updated details of '$request->item_name_original' with the following CHANGES: $edited_parts $item_code $item_desc.";
                $userlogs->save();
            }

            return response($result);
        }
    }

    public function saveInclusives(Request $request){
        $parts = new Part;
        $parts->item_id = $request->item_id;
        $parts->part_id = $request->part_id;
        $parts->quantity = $request->quantity;
        $parts->priority = $request->priority;
        $sql = $parts->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }

        return response($result);
    }

    public function logBundle(Request $request){
        $item = ucwords($request->item);

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "BUNDLED ITEMS ADDED: User successfully saved new Bundle '$item' with ItemID#$request->item_id.";
        $userlogs->save();

        return response('true');
    }

    public function setBundle(Request $request){
        $setBundle = Part::query()->select('items.prodcode','items.item','parts.part_id AS item_id','items.UOM AS uom','quantity AS qty')
            ->join('items', 'items.id', 'parts.part_id')
            ->where('parts.item_id',$request->item_id)
            ->orderBy('priority','ASC')
            ->get();

        return DataTables::of($setBundle)->toJson();
    }

    public function setSpecs(Request $request){
        if(Item::where('items.id', $request->item_id)->first()->assemble == 'YES' && $request->include != 'NO'){
            $list = '';
            $parts = Part::select('items.item','quantity AS qty')
                ->join('items', 'items.id', 'parts.part_id')
                ->where('item_id', $request->item_id)
                ->get();
            foreach($parts as $part) {
                $list = $list.$part->qty.'x '.$part->item.', ';
            }
            return rtrim($list, ', ');
        }
        else{
            return Item::where('items.id', $request->item_id)->first()->specs;
        }
    }
}