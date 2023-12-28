<div class="modal fade in" id="newAssembly">
    <div class="modal-dialog modal-xl">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW ASSEMBLY REQUEST</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Created</label>
                <input class="form-control form-control-sm" id="reqdate" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Assembled Item Name</label>
                <select class="form-select form-control-sm required_field" id="assembly" style="font-size: .85rem; padding: 0.25rem 0.5rem; height: 30px !important; width: 647px; margin-right: 10px;" required tabindex="2">
                    <option value="" selected disabled>Select Assembled Item</option>
                    @foreach($items as $item)
                        <option value="{{$item->id}}">{{strtoupper($item->item)}}</option>
                    @endforeach
                </select>
                <input class="form-control form-control-sm" id="qty" value="1" type="hidden">
                <button type="button" id="btnAssemblyProceed" class="btn btn-primary bp" style="zoom: 80%;" disabled tabindex="3">PROCEED</button>
            </div>
            <div class="header_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="assemblypartsDetails" style="display: none;">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">NEEDED PARTS DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <div class="container-fluid mt-2" id="#divPartsDetails">
                    <table id='tblPartsDetails' class="table tblPartsDetails" style="cursor: pointer; font-size: 12px; width: 100%;">
                        <thead>
                            <tr>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>CATEGORY ID</th>
                                <th>ITEM ID</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <hr>
                <button type="button" id="btnAssemblyBack" class="btn btn-primary bp">BACK</button>
                <button type="button" id="btnAssemblySave" class="btn btn-primary float-end bp" disabled tabindex="4">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>