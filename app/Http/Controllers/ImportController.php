<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\WarehouseImport;
use App\Imports\BranchImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Item;
use App\Warehouse;
use App\Stock;

class ImportController extends Controller
{
    public function branchstore(Request $request)
    {
        $file = $request->file('upload');
        $import = new BranchImport;
        $data = Excel::toArray($import, $file);
        $error = 0;
        $itemswitherror = [];
        foreach ($data[0] as $key => $value) {
            $items = Item::where('item', $value[0])->first();
            if ($items) {
                $add = new Stock;
                $add->user_id = auth()->user()->id;
                $add->category_id = $items->category_id;
                $add->branch_id = auth()->user()->branch->id;
                $add->items_id = $items->id;
                if (!$value[1]) {
                    $add->serial = 'N/A';
                }else{
                    $add->serial = $value[1];
                }
                $add->status = 'in';
                $add->save();
            }elseif (!$items) {
                $error = 1;
                array_push($itemswitherror, $value[0]);
            }
        }
        if ($error == 1) {
            return back()->withErrors([$itemswitherror]);
        }else{
            return back()->withStatus('Excel File Imported Successfully');
        }
    }
    public function warestore(Request $request)
    {
        $file = $request->file('upload');
        $import = new WarehouseImport;
        $data = Excel::toArray($import, $file);
        $error = 0;
        $itemswitherror = [];
        foreach ($data[0] as $key => $value) {
            $items = Item::where('item', $value[0])->first();
            if ($items) {
                if ($value[1] || $value[1] != 0) {
                    for ($i=1; $i <= $value[1]; $i++) { 
                        $add = new Warehouse;
                        $add->user_id = auth()->user()->id;
                        $add->category_id = $items->category_id;
                        $add->items_id = $items->id;
                        $add->serial = '-';
                        $add->status = 'in';
                        $add->save();
                    }
                }
            }elseif (!$items) {
                $error = 1;
                array_push($itemswitherror, $value[0]);
            }
        }
        if ($error == 1) {
            return back()->withErrors([$itemswitherror]);
        }else{
            return back()->withStatus('Excel File Imported Successfully');
        }
    }
}