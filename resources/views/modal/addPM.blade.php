<div id="PmModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="head-title">ADD PM BRANCH</h4>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mod">
                <form id="customerForm">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label for="customer_code" class="col-md-4 col-form-label text-md-right">{{ __('Customer Code') }}</label>
                        <div class="col-md-6">
                            <input id="customer_code" maxlength="4" type="text" class="form-control" name="customer_code" style="color: black;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Customer_name" class="col-md-4 col-form-label text-md-right">{{ __('Customer Name') }}</label>
                        <div class="col-md-6">
                            <input id="customer_name" type="text" class="form-control text-uppercase" name="customer_name" style="color: black;" readonly>
                        </div>
                    </div>
                    <center><b><p id="note" style="color:red"></p></b></center>
                    <div class="form-group row" id="branchesDiv" style="display:none">
                        <label for="branches" class="col-md-4 col-form-label text-md-right">{{ __('Office Name') }}</label>
                        <div class="col-md-6">
                            <select id="branches" name="branches" class="form-control intype" style="color: black;">
                                <option selected value="" disabled>select office</option>
                                @foreach (App\Branch::whereNotIn('id', [1, 38, 40, 41, 42, 43, 44])->get() as $branch)
                                    <option value="{{$branch->id}}">{{$branch->branch}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input id="subBtn" class="btn btn-primary" value="ADD" disabled>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>