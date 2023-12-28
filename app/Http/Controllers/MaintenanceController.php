<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Imports\ItemsImport;
use App\Models\Stock;
use App\Item;
use App\Category;
use App\Models\BSMS_Item;
use App\Models\BSMS_Category;
use App\Models\BSMS_Branch;
use App\Models\BSMS_Initial_Warehouse;
use App\Models\BSMS_Initial_Branch;
use App\Models\Location;
use App\Models\Location2;
use App\Models\Supplier;
use App\Models\Warranty;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Models\UserLogs;
use App\Mail\requestLocation;
use App\Mail\requestStatusChange;
use Yajra\Datatables\Datatables;

class MaintenanceController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function maintenance(Request $request){
        
        $categories = Category::select('id','category')->where('id','!=','0')->where('id','!=','1')->get()->sortBy('category');
        $toAssy = Item::select('items.id','item')
                    ->join('categories', 'categories.id', 'category_id')
                    ->where('category_id', 36)
                    ->where('assemble', 'NO')
                    ->get();
        // return $toAssy;
        return view('/pages/maintenance/maintenance', compact('categories', 'toAssy'));
    }

    public function fm_items(){
        $list = Item::select('items.id', 'items.item', 'items.prodcode', 'items.specs', 'categories.category', 'items.category_id', 'items.UOM AS uom', 'items.serialize', 'items.minimum')
            ->where('items.assemble', 'NO')
            ->join('categories', 'categories.id', 'category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_assembled(){
        $list = Item::select('items.id', 'items.item', 'items.prodcode', 'categories.category', 'items.category_id', 'items.UOM AS uom', 'items.minimum')
            ->where('items.assemble', 'YES')
            ->join('categories', 'categories.id', 'category_id')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_bundled(){
        $list = Item::select('items.id', 'items.item', 'items.prodcode')
            ->where('items.assemble', 'BUNDLE')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_categories(){
        $list = Category::select('id', 'category')->orderBy('category', 'ASC')->orderBy('id', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_locations(){
        $list = Location::select('id AS location_id', 'location', 'status')->orderBy('location', 'ASC')->orderBy('id', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_suppliers(){
        return DataTables::of(Supplier::where('id', '!=', '0')->get())->make(true);
    }

    public function fm_customers(){
        $list = Customer::select('id', 'code AS customer_code', 'customer AS customer_name')->orderBy('customer_name', 'ASC')->orderBy('id', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_branches(Request $request){
        $list = Branch::selectRaw('code AS branch_code, customer_branch AS branch_name, address, tin_number, cperson AS contact_person, position, contact AS contact_number, email_address')
            ->where('customer_id', $request->customer_id)
            ->orderBy('customer_branch', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function listItems(Request $request){
        $data = Item::select('items.id', 'items.item', 'items.prodcode', 'items.specs', 'categories.category')
            ->where('items.assemble', 'NO')
            ->join('categories', 'categories.id', 'category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();

        return DataTables::of($data)->make(true);
    }

    public function fm_items_reload(){
        if(Item::count() == 0){
            return 'NULL';
        }
        $data_update = Item::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_assembled_reload(){
        if(Item::count() == 0){
            return 'NULL';
        }
        $data_update = Item::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_bundled_reload(){
        if(Item::count() == 0){
            return 'NULL';
        }
        $data_update = Item::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_categories_reload(){
        if(Category::count() == 0){
            return 'NULL';
        }
        $data_update = Category::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_locations_reload(){
        if(Location::count() == 0){
            return 'NULL';
        }
        $data_update = Location::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_suppliers_reload(){
        if(Supplier::count() == 0){
            return 'NULL';
        }
        $data_update = Supplier::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_warranty_reload(){
        if(Warranty::count() == 0){
            return 'NULL';
        }
        $data_update = Warranty::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_customers_reload(){
        if(Customer::count() == 0){
            return 'NULL';
        }
        $data_update = Customer::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_branches_reload(){
        if(Branch::count() == 0){
            return 'NULL';
        }
        $data_update = Branch::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function import(Request $request){
        $file = $request->file('xlsx');
        $import = new ItemsImport;
        $data = Excel::toArray($import, $file);
        if(count($data[0]) == 0){
            return redirect()->to('/maintenance?import=failed');
        }
        $failed_rows = [];
        $row_num = 2;
        foreach($data[0] as $key => $value){
            if(!$value['category_name'] && !$value['item_code'] && !$value['item_description'] && !$value['min_stock'] && !$value['uom'] && !$value['serial_y_n']){
                echo(null);
            }
            else{
                $category = Category::select('id','category')
                    ->where('category', $value['category_name'])
                    ->get();
                $item = Item::query()->select()
                    ->whereRaw('LOWER(item) = ?', strtolower($value['item_description']))
                    ->count();
                $itemcode = Item::query()->select()
                    ->where('prodcode', strtoupper($value['item_code']))
                    ->count();
                if(!$value['category_name'] || !$value['item_code'] || !$value['item_description'] || !$value['min_stock'] || !$value['uom'] || !$value['serial_y_n']){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Fill Required Fields!】');
                }
                else if(!$category){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Invalid Category!】');
                }
                else if($value['min_stock'] < 1){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Invalid Quantity!】');
                }
                else if($item > 0){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Duplicate Product Description!】');
                }
                else if($itemcode > 0){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Duplicate Product Code!】');
                }
                else if($value['uom'] != 'Unit' && $value['serial_y_n'] == 'Y'){
                    array_push($failed_rows, '【Row: '.$row_num.' => Error: Serial not allowed!】');
                }
                else{
                    if($value['serial_y_n'] == 'Y'){
                        $hasSerial = 'YES';
                        $n_a = 'no';
                    }
                    else{
                        $hasSerial = 'NO';
                        $n_a = 'yes';
                    }
                    $item_name = ucwords($value['item_description']);
                    $category_name = $category[0]['category'];

                    $items = new Item;
                    $items->item = $item_name;
                    $items->prodcode = strtoupper($value['item_code']);
                    $items->category_id = $category[0]['id'];
                    $items->minimum = $value['min_stock'];
                    $items->UOM = $value['uom'];
                    $items->assemble = 'NO';
                    $items->serialize = $hasSerial;
                    $items->n_a = $n_a;
                    $sql = $items->save();
                    $id = $items->id;
                    if(!$sql){
                        array_push($failed_rows, '【Row: '.$row_num.', Error: Save Failed!】');
                    }
                    else{
                        $stocks = new Stock;
                        $stocks->item_id = $id;
                        $stocks->user_id = auth()->user()->id;
                        $stocks->status = 'default';
                        $stocks->qty = '1';
                        $stocks->save();

                        $items = new BSMS_Item;
                        $items->item = $item_name;
                        $items->prodcode = strtoupper($value['item_code']);
                        $items->category_id = $category[0]['id'];
                        $items->minimum = $value['min_stock'];
                        $items->UOM = $value['uom'];
                        $items->assemble = 'NO';
                        $items->serialize = $hasSerial;
                        $items->n_a = $n_a;
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

                        $userlogs = new UserLogs;
                        $userlogs->user_id = auth()->user()->id;
                        $userlogs->activity = "ITEM ADDED: User successfully saved new Item '$item_name' with ItemID#$id under Category '$category_name'.";
                        $userlogs->save();
                    }
                }
            }
            $row_num++;
        }
        if(count($failed_rows) == count($data[0])){
            $errors = implode(', ', $failed_rows);
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEMS FILE IMPORT [FAILED]: User attempt failed to import file data into Items with the following errors: $errors.";
            $userlogs->save();

            return redirect()->to('/maintenance?import=failed');
        }
        else if(count($failed_rows) == 0){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEMS FILE IMPORT [NO ERRORS]: User successfully imported file data into Items without any errors.";
            $userlogs->save();

            return redirect()->to('/maintenance?import=success_without_errors');
        }
        else{
            $errors = implode(', ', $failed_rows);
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEMS FILE IMPORT [WITH ERRORS]: User successfully imported file data into Items with the following errors: $errors.";
            $userlogs->save();

            return redirect()->to('/maintenance?import=success_with_errors');
        }
    }

    public function saveItem(Request $request){
        $item = Item::query()->select()
            ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
            ->count();
        if($item > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }

        if($request->prodcode != '[BLANK]'){
            $itemcode = Item::query()->select()
                ->whereRaw('UPPER(prodcode) = ?',strtoupper($request->prodcode))
                ->count();
            if($itemcode > 0){
                $data = array('result' => 'duplicatecode');
                return response()->json($data);
            }
        }

        $item_name = ucwords($request->item_name);
        $n_a = $request->serialize == 'YES' ? 'no' : 'yes';

        $items = new Item;
        $items->item = $item_name;
        $items->prodcode = $request->prodcode;
        $items->specs = ucwords($request->specs);
        $items->category_id = $request->item_category;
        $items->minimum = $request->minimum;
        $items->UOM = $request->item_uom;
        $items->assemble = 'NO';
        $items->serialize = $request->serialize;
        $items->n_a = $n_a;
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
            $items->item = $item_name;
            $items->prodcode = $request->prodcode;
            $items->specs = ucwords($request->specs);
            $items->category_id = $request->item_category;
            $items->minimum = $request->minimum;
            $items->UOM = $request->item_uom;
            $items->assemble = 'NO';
            $items->serialize = $request->serialize;
            $items->n_a = $n_a;
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

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEM ADDED: User successfully saved new Item '$item_name' with ItemID#$id under Category '$request->category_name'.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function updateItem(Request $request){
        if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
                ->count();
        }
        else{
            $item = 0;
        }
        if($item > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }

        if($request->prodcode != '[BLANK]'){
            if(strtoupper($request->prodcode) != strtoupper($request->prodcode_original)){
                $itemcode = Item::query()->select()
                    ->whereRaw('UPPER(prodcode) = ?',strtoupper($request->prodcode))
                    ->count();
            }
            else{
                $itemcode = 0;
            }
            if($itemcode > 0){
                $data = array('result' => 'duplicatecode');
                return response()->json($data);
            }
        }

        $item_name = ucwords($request->item_name);
        $n_a = $request->serialize == 'YES' ? 'no' : 'yes';

        $items = Item::find($request->input('item_id'));
        $items->item = $item_name;
        $items->prodcode = $request->prodcode;
        $items->specs = ucwords($request->specs);
        $items->category_id = $request->item_category;
        $items->minimum = $request->minimum;
        $items->UOM = $request->item_uom;
        $items->serialize = $request->serialize;
        $items->n_a = $n_a;
        $sql = $items->save();
        $id = $items->id;

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $items = BSMS_Item::find($request->input('item_id'));
            $items->item = $item_name;
            $items->prodcode = $request->prodcode;
            $items->specs = ucwords($request->specs);
            $items->category_id = $request->item_category;
            $items->minimum = $request->minimum;
            $items->UOM = $request->item_uom;
            $items->serialize = $request->serialize;
            $items->n_a = $n_a;
            $items->save();

            if($request->item_category != $request->item_category_original){
                $category_name = "【Category Name: FROM '$request->category_name_original' TO '$request->category_name'】";
            }
            else{
                $category_name = NULL;
            }
            if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
                $item_desc = "【Product Description: FROM '$request->item_name_original' TO '$item_name'】";
            }
            else{
                $item_desc = NULL;
            }
            if($request->prodcode != $request->prodcode_original){
                $prodcode = "【Product Code: FROM '$request->prodcode_original' TO '$request->prodcode'】";
            }
            else{
                $prodcode = NULL;
            }
            if($request->minimum != $request->minimum_original){
                $minimum = "【Minimum Stock: FROM '$request->minimum_original' TO '$request->minimum'】";
            }
            else{
                $minimum = NULL;
            }
            if($request->item_uom != $request->item_uom_original){
                $item_uom = "【Unit of Measure (UOM): FROM '$request->item_uom_original' TO '$request->item_uom'】";
            }
            else{
                $item_uom = NULL;
            }
            if($request->item_uom == 'Unit' && ($request->serialize != $request->serialize_original)){
                $serial = "【Has Serial? (YES/NO): FROM '$request->serialize_original' TO '$request->serialize'】";
            }
            else{
                $serial = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEM UPDATED: User successfully updated details of '$request->item_name_original' with the following CHANGES: $category_name $item_desc $prodcode $minimum $item_uom $serial.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function saveCategory(Request $request){
        $category = Category::query()->select()
            ->where('category',strtoupper($request->category))
            ->count();
        if($category > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $categories = new Category;
            $categories->category = strtoupper($request->category);
            $sql = $categories->save();
            $id = $categories->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $categories = new BSMS_Category;
                $categories->category = strtoupper($request->category);
                $categories->save();
            }

            $data = array('result' => $result, 'id' => $id, 'category' => strtoupper($request->category));
            return response()->json($data);
        }
    }

    public function logNewCategory(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "CATEGORY ADDED: User successfully saved new Category '$request->category' with CategoryID#$request->id.";
        $userlogs->save();

        return response('true');
    }

    public function updateCategory(Request $request){
        if(strtoupper($request->category_details) != strtoupper($request->category_original)){
            $category = Category::query()->select()
                ->where('category',strtoupper($request->category_details))
                ->count();
        }
        else{
            $category = 0;
        }
        if($category > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $categories = Category::find($request->input('category_id'));
            $categories->category = strtoupper($request->category_details);
            $sql = $categories->save();

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $categories = BSMS_Category::find($request->input('category_id'));
                $categories->category = strtoupper($request->category_details);
                $categories->save();
            }

            $data = array('result' => $result, 'category_id' => $request->category_id, 'category_details' => strtoupper($request->category_details), 'category_original' => strtoupper($request->category_original));
            return response()->json($data);
        }
    }

    public function logUpdateCategory(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "CATEGORY UPDATED: User successfully updated Category FROM '$request->category_original' TO '$request->category_details' with CategoryID#$request->category_id.";
        $userlogs->save();

        return response('true');
    }

    public function saveLocation(Request $request){
        $location = Location::query()->select()
            ->whereRaw('LOWER(location) = ?',strtolower($request->location))
            ->count();
        if($location > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $id = '';
            $location_name = strtoupper($request->location);

            $locations = new Location;
            $locations->location = $location_name;
            $locations->status = 'PENDING';
            $sql = $locations->save();
            $id = $locations->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $locations2 = new Location2;
                $locations2->id = $id;
                $locations2->location = $location_name;
                $locations2->save();
            }

            $data = array('result' => $result, 'id' => $id, 'location' => $location_name);
            return response()->json($data);
        }
    }

    public function logNewLocation(Request $request){
        $error = '.';
        try{
            $subject = 'NEW LOCATION REQUEST: '.$request->location;
            $details = [
                'location' => $request->location,
                'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'requested_by' => auth()->user()->name
            ];
            Mail::to(env('MAIL_TO_SUPPORT'))->send(new requestLocation($details, $subject));
        }
        catch(\Exception $e){
            $error = ' but was UNABLE TO SEND EMAIL.';
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "LOCATION REQUESTED: User successfully requested new Location '$request->location' with LocationID#$request->id".$error;
        $userlogs->save();

        return response('true');
    }

    public function updateLocation(Request $request){
        if($request->status != $request->status_original){
            do{
                $locations = Location::find($request->input('location_id'));
                $locations->status = $request->status_original.' - CHANGE REQUESTED';
                $sql = $locations->save();
            }
            while(!$sql);

            $data = array(
                'result' => 'request',
                'id' => $request->location_id,
                'location' => strtoupper($request->location_details),
                'status_original' => $request->status_original,
                'status' => $request->status
            );
            return response()->json($data);
        }
        if(strtoupper($request->location_details) != strtoupper($request->location_original)){
            $location = Location::query()->select()
                ->where('location',strtoupper($request->location_details))
                ->count();
        }
        else{
            $location = 0;
        }
        if($location > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $location_details = strtoupper($request->location_details);

            $locations = Location::find($request->input('location_id'));
            $locations->location = $location_details;
            $sql = $locations->save();
            $id = $locations->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $locations2 = Location2::find($request->input('location_id'));
                $locations2->location = $location_details;
                $locations2->save();

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "LOCATION UPDATED: User successfully updated Location FROM '$request->location_original' TO '$location_details' with LocationID#$id.";
                $userlogs->save();
            }

            $data = array('result' => $result);
            return response()->json($data);
        }
    }

    public function requestStatusChange(Request $request){
        $error = '.';
        try{
            $subject = 'LOCATION STATUS CHANGE REQUEST: '.$request->location;
            $details = [
                'location' => $request->location,
                'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'requested_by' => auth()->user()->name,
                'status_original' => $request->status_original,
                'status' => $request->status
            ];
            Mail::to(env('MAIL_TO_SUPPORT'))->send(new requestStatusChange($details, $subject));
        }
        catch(\Exception $e){
            $error = ' but was UNABLE TO SEND EMAIL.';
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "LOCATION STATUS CHANGE REQUESTED: User successfully requested Location Status Change of '$request->location' FROM '$request->status_original' TO '$request->status' with LocationID#$request->id".$error;
        $userlogs->save();

        return response('true');
    }

    public function saveSupplier(Request $request){
        $supplier = Supplier::query()->select()
            ->whereRaw('LOWER(supplier_name) = ?',strtolower($request->supplier_name))
            ->count();
        if($supplier > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }

        $suppliercode = Supplier::query()->select()
            ->whereRaw('UPPER(supplier_code) = ?',strtoupper($request->supplier_code))
            ->count();
        if($suppliercode > 0){
            $data = array('result' => 'duplicatecode');
            return response()->json($data);
        }

        $supplier_name = ucwords($request->supplier_name);

        $suppliers = new Supplier;
        $suppliers->supplier_code = strtoupper($request->supplier_code);
        $suppliers->supplier_name = $supplier_name;
        $suppliers->address = ucwords($request->address);
        $suppliers->contact_person = ucwords($request->contact_person);
        $suppliers->contact_number = $request->contact_number;
        $suppliers->email = strtolower($request->email);
        $sql = $suppliers->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "SUPPLIER ADDED: User successfully saved new Supplier '$supplier_name' with Supplier Code '$request->supplier_code'.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function updateSupplier(Request $request){
        if(strtoupper($request->supplier_name) != strtoupper($request->supplier_name_orig)){
            $supplier = Supplier::query()->select()
                ->whereRaw('LOWER(supplier_name) = ?',strtolower($request->supplier_name))
                ->count();
            if($supplier > 0){
                $data = array('result' => 'duplicate');
                return response()->json($data);
            }
        }

        if(strtoupper($request->supplier_code) != strtoupper($request->supplier_code_orig)){
            $suppliercode = Supplier::query()->select()
                ->whereRaw('UPPER(supplier_code) = ?',strtoupper($request->supplier_code))
                ->count();
            if($suppliercode > 0){
                $data = array('result' => 'duplicatecode');
                return response()->json($data);
            }
        }

        $supplier_code = strtoupper($request->supplier_code);
        $supplier_name = ucwords($request->supplier_name);
        $address = ucwords($request->address);
        $contact_person = ucwords($request->contact_person);
        $contact_number = $request->contact_number;
        $email = strtolower($request->email);

        $suppliers = Supplier::find($request->input('id'));
        $suppliers->supplier_code = $supplier_code;
        $suppliers->supplier_name = $supplier_name;
        $suppliers->address = $address;
        $suppliers->contact_person = $contact_person;
        $suppliers->contact_number = $contact_number;
        $suppliers->email = $email;
        $sql = $suppliers->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            if($supplier_code != $request->supplier_code_orig){
                $supplier_code_change = "【Supplier Code: FROM '$request->supplier_code_orig' TO '$supplier_code'】";
            }
            else{
                $supplier_code_change = NULL;
            }
            if(strtoupper($supplier_name) != strtoupper($request->supplier_name_orig)){
                $supplier_name_change = "【Supplier Name: FROM '$request->supplier_name_orig' TO '$supplier_name'】";
            }
            else{
                $supplier_name_change = NULL;
            }
            if(strtoupper($address) != strtoupper($request->address_orig)){
                $address_change = "【Supplier Address: FROM '$request->address_orig' TO '$address'】";
            }
            else{
                $address_change = NULL;
            }
            if(strtoupper($contact_person) != strtoupper($request->contact_person_orig)){
                $contact_person_change = "【Contact Person: FROM '$request->contact_person_orig' TO '$contact_person'】";
            }
            else{
                $contact_person_change = NULL;
            }
            if(strtoupper($contact_number) != strtoupper($request->contact_number_orig)){
                $contact_number_change = "【Contact Number: FROM '$request->contact_number_orig' TO '$contact_number'】";
            }
            else{
                $contact_number_change = NULL;
            }
            if(strtoupper($email) != strtoupper($request->email_orig)){
                $email_change = "【Email Address: FROM '$request->email_orig' TO '$email'】";
            }
            else{
                $email_change = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "SUPPLIER UPDATED: User successfully updated details of '$request->supplier_name_orig' with the following CHANGES: $supplier_code_change $supplier_name_change $address_change $contact_person_change $contact_number_change $email_change.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function saveCustomer(Request $request){
        $customers = new Customer;
        $customers->code = ucwords($request->customer_code);
        $customers->customer = ucwords($request->customer_name);
        $sql = $customers->save();
        $id = $customers->id;

        if(!$sql){
            $result = '0';
        }
        else{
            $result = $id;

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "CUSTOMER ADDED: User successfully saved new Customer '$customers->code: $customers->customer' with CustomerID#$id.";
            $userlogs->save();
        }
        return $result;
    }

    public function updateCustomer(Request $request){
        $customer_code_orig = Customer::where('id', $request->customer_id)->first()->code;
        $customer_name_orig = Customer::where('id', $request->customer_id)->first()->customer;

        $customer_code_new = strtoupper($request->customer_code);
        $customer_name_new = ucwords($request->customer_name);

        $changes = 0;
        if($customer_code_orig != $customer_code_new){
            $customer_code_change = "【Customer Code: FROM '$customer_code_orig' TO '$customer_code_new'】";
            $changes++;
        }
        else{
            $customer_code_change = NULL;
        }
        if($customer_name_orig != $customer_name_new){
            $customer_name_change = "【Customer Name: FROM '$customer_name_orig' TO '$customer_name_new'】";
            $changes++;
        }
        else{
            $customer_name_change = NULL;
        }
        if($changes == 0){
            return 'NO CHANGES';
        }
        else{
            $sql = Customer::where('id', $request->customer_id)
                        ->update([
                            'code' => $customer_code_new,
                            'customer' => $customer_name_new
                        ]);
    
            if(!$sql){
                $result = '0';
            }
            else{
                $result = $request->customer_id;
    
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "CUSTOMER DETAILS UPDATED: User successfully updated Customer Details of '$customer_name_orig' with CustomerID#$request->customer_id with the following CHANGES: $customer_code_change $customer_name_change";
                $userlogs->save();
            }
            return $result;
        }
    }

    public function saveBranch(Request $request){
        $customer_name = Customer::where('id', $request->customer_id)->first()->customer;

        $branches = new Branch;
        $branches->customer_id = $request->customer_id;
        $branches->code = 'XX'.$branches->id; //BACKLOG
        $branches->customer_branch = ucwords($request->branch_name);
        $branches->address = ucwords($request->address);
        $branches->tin_number = $request->tin_number;
        $branches->cperson = ucwords($request->contact_person);
        $branches->position = ucwords($request->position);
        $branches->contact = $request->contact_number;
        $branches->email_address = strtolower($request->email_address);
        $sql = $branches->save();
        $id = $branches->id;

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "BRANCH ADDED: User successfully saved new Branch '$branches->customer_branch' to '$customer_name' with BranchID#$id.";
            $userlogs->save();
        }
        return $result;
    }

    public function updateBranch(Request $request){
        $customer_name = Customer::where('id', $request->customer_id)->first()->customer;
        $branch_orig = Branch::where('id', $request->branch_id)->first();

        $branch_name_orig = $branch_orig->customer_branch;
        $address_orig = $branch_orig->address;
        $tin_number_orig = $branch_orig->tin_number;
        $contact_person_orig = $branch_orig->cperson;
        $position_orig = $branch_orig->position;
        $contact_number_orig = $branch_orig->contact;
        $email_address_orig = $branch_orig->email_address;

        $branch_name_new = ucwords($request->branch_name);
        $address_new = ucwords($request->address);
        $tin_number_new = $request->tin_number;
        $contact_person_new = ucwords($request->contact_person);
        $position_new = ucwords($request->position);
        $contact_number_new = $request->contact_number;
        $email_address_new = strtolower($request->email_address);

        $changes = 0;
        if($branch_name_orig != $branch_name_new){
            $branch_name_change = "【Branch Name: FROM '$branch_name_orig' TO '$branch_name_new'】";
            $changes++;
        }
        else{
            $branch_name_change = NULL;
        }
        if($address_orig != $address_new){
            $address_change = "【Branch Address: FROM '$address_orig' TO '$address_new'】";
            $changes++;
        }
        else{
            $address_change = NULL;
        }
        if($tin_number_orig != $tin_number_new){
            $tin_number_change = "【T.I.N.: FROM '$tin_number_orig' TO '$tin_number_new'】";
            $changes++;
        }
        else{
            $tin_number_change = NULL;
        }
        if($contact_person_orig != $contact_person_new){
            $contact_person_change = "【Contact Person: FROM '$contact_person_orig' TO '$contact_person_new'】";
            $changes++;
        }
        else{
            $contact_person_change = NULL;
        }
        if($position_orig != $position_new){
            $position_change = "【Position: FROM '$position_orig' TO '$position_new'】";
            $changes++;
        }
        else{
            $position_change = NULL;
        }
        if($contact_number_orig != $contact_number_new){
            $contact_number_change = "【Contact Number: FROM '$contact_number_orig' TO '$contact_number_new'】";
            $changes++;
        }
        else{
            $contact_number_change = NULL;
        }
        if($email_address_orig != $email_address_new){
            $email_address_change = "【Email Address: FROM '$email_address_orig' TO '$email_address_new'】";
            $changes++;
        }
        else{
            $email_address_change = NULL;
        }

        if($changes == 0){
            return 'NO CHANGES';
        }
        else{
            $branches = Branch::find($request->branch_id);
            $branches->customer_id = $request->customer_id;
            $branches->customer_branch = $branch_name_new;
            $branches->address = $address_new;
            $branches->tin_number = $tin_number_new;
            $branches->cperson = $contact_person_new;
            $branches->position = $position_new;
            $branches->contact = $contact_number_new;
            $branches->email_address = $email_address_new;
            $sql = $branches->save();
            $id = $branches->id;

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "BRANCH DETAILS UPDATED: User successfully updated Branch Details of '$branches->customer_branch' under '$customer_name' with BranchID#$id with the following CHANGES: $branch_name_change $address_change $tin_number_change $contact_person_change $position_change $contact_number_change $email_address_change";
                $userlogs->save();
            }
            return $result;
        }
    }

    public function GetWarranty(){
        return DataTables::of(Warranty::all())->make(true);
    }

    public function AddWarranty(Request $request){
        $inclusive = implode(", ",$request->inclusive);
        $sql = Warranty::create([
            'Warranty_Name' => $request->warranty,
            'Duration' => $request->duration,
            'Inclusive' => $inclusive
        ]);

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "WARRANTY ADDED: User successfully saved new Warranty '$request->warranty' with Duration '$request->duration-Month/s' and Inclusive: [$inclusive].";
            $userlogs->save();
        }

        return response($result);
    }

    public function UpdateWarranty(Request $request){
        $inclusive = implode(", ",$request->inclusive);
        $sql = Warranty::where('id', $request->id)->update([
            'Warranty_Name' => $request->warranty,
            'Duration' => $request->duration,
            'Inclusive' => $inclusive
        ]);

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "WARRANTY UPDATED: User successfully updated details of Warranty '$request->warranty' with Duration '$request->duration-Month/s' and Inclusive: [$inclusive].";
            $userlogs->save();
        }

        return response($result);
    }

    public function item_barcode(Request $request){
        $generator = new BarcodeGeneratorHTML;
        return $generator->getBarcode($request->barcode, $generator::TYPE_CODE_128);
    }
}