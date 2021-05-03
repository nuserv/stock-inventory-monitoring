<div id="branchModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100 text-center">Service center inventory</h4>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="branchForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="myid" id="myid">
                    <div class="row no-margin">
                        <div class="col-md-3 form-group">
                            <input type="text" class="form-control form-control-sm " name="branch_name" style="color: black;" id="branch_name" placeholder="Enter Branch Name" disabled>
                        </div>
                        <div class="col-md-3 form-group">
                            <select name="area" id="area" class="form-control form-control-sm area @error('area') is-invalid @enderror" style="color: black;" disabled>
                                <option selected disabled>select area</option>
                                @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" class="form-control form-control-sm" name="address" id="address" style="color: black;" placeholder="Enter address" disabled>
                        </div>
                    </div>
                    <div class="row no-margin">
                        <div class="col-md-3 form-group">
                            <input type="text" class="form-control form-control-sm" name="contact_person" id="contact_person" style="color: black;" placeholder="Enter Contact person" disabled>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="mobile" id="mobile" style="color: black;" placeholder="Enter mobile number" disabled>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="email" id="email" style="color: black;" placeholder="Enter email address" disabled>
                        </div>
                        <div class="col-md-3">
                            <select name="status" id="status" class="form-control form-control-sm status @error('status') is-invalid @enderror" style="color: black;" disabled>
                                <option selected disabled>select status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div id="table">
                        <center><h5 id="catname"></h5></center>
                        <table class="table branchDetails" id="branchDetails" style="width: 100%;font-size:80%">
                            <thead class="thead-dark">
                                <tr class="tbsearchm">
                                    <td>
                                        <input type="text" placeholder="Search item.." class="form-control mfilter-input fl-m" data-column="0" style="border: 1px solid black;"/>
                                    </td>
                                <tr>
                                    <th>DESCRIPTION</th>
                                    @if (auth()->user()->hasanyrole('Editor', 'Manager', 'Warehouse Manager', 'Encoder'))
                                        <th>INITIAL STOCK</th>
                                        <th>DEFECTIVE</th>
                                    @endif
                                    <th>AVAILABLE</th>
                                    <th>OUT</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div id="cattable">
                        <table class="table catbranchDetails" id="catbranchDetails" style="width: 100%;font-size:80%">
                            <thead class="thead-dark">
                                <tr class="tbsearchm">
                                    <td>
                                        <input type="text" placeholder="Search category.." class="form-control cfilter-input fl-c" data-column="0" style="border: 1px solid black;"/>
                                    </td>
                                <tr>
                                    <th>Category</th>
                                    @if (auth()->user()->hasanyrole('Editor', 'Manager', 'Warehouse Manager', 'Encoder'))
                                        <th>DEFECTIVE</th>
                                    @endif
                                    <th>AVAILABLE</th>
                                    <th>OUT</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @if(auth()->user()->hasanyrole('Manager', 'Editor'))
                    <div class="modal-footer">
                        <input type="button" id="editBtn" class="btn btn-primary" value="Edit">
                        <input type="submit" id="saveBtn" class="btn btn-primary" value="Save">
                        <input type="button" class="btn btn-primary mr-auto" data-dismiss="modal" value="Cancel">
                    </div>
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" id="catBtn" class="btn btn-xs btn-primary" value="Back to Category">
            </div>
        </div>
    </div>
</div>