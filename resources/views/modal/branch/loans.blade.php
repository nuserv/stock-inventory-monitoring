<div id="loansModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Request details</h4>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mod">
                <form id="requestForm">
                    {{ csrf_field() }}
                    <input type="hidden" id="myid">
                    <input type="hidden" id="branch_id">
                    <div class="form-group row">
                        <label for="date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }}</label>
                        <div class="col-md-6">
                            <input id="date" style="color: black" type="text" class="form-control" name="date" readonly="read-only">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="branch" class="col-md-4 col-form-label text-md-right">{{ __('Branch') }}</label>
                        <div class="col-md-6">
                            <input id="branch" style="color: black" type="text" class="form-control" name="branch" readonly="read-only">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Item Description') }}</label>
                        <div class="col-md-6">
                            <input id="description" style="color: black" type="text" class="form-control" name="description" readonly="read-only">
                        </div>
                    </div>
                    <div class="form-group row" id="serials">
                        <label for="serial" class="col-md-4 col-form-label text-md-right">{{ __('Serial') }}</label>
                        <div class="col-md-6">
                            <input id="serial" style="color: black" type="text" class="form-control" name="serial" readonly="read-only">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                        <div class="col-md-6">
                            <input id="status" style="color: black" type="text" class="form-control" name="status" readonly="read-only">
                        </div>
                    </div>
                </form>
                <hr>
                <div class="row no-margin" id="loanrow1">
                    <div class="col-md-5 form-group">
                        <select id="loanserial1" class="form-control loanserial" row_count="1">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" id="submit_Btn" value="Approved request">
                    <input type="button" class="btn btn-primary" id="received_Btn" value="Received">
                    <input type="button" class="btn btn-primary" id="del_Btn" value="Delete request">
                </div>
            </div>
        </div>
    </div>
</div>