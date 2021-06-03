<div id="returnModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Return details</h4>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mod">
                <form id="requestForm">
                    {{ csrf_field() }}
                    <input type="hidden" id="myid">
                    <input type="hidden" id="branch_id">
                    <input type="hidden" id="return_id">
                    <input type="hidden" id="return_name">
                    <div class="form-group row">
                        <label for="date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }}</label>
                        <div class="col-md-6">
                            <input id="date" style="color: black" type="text" class="form-control" name="date" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Item Description') }}</label>
                        <div class="col-md-6">
                            <input id="description" style="color: black" type="text" class="form-control" name="description" disabled>
                        </div>
                    </div>
                    <div class="form-group row" id="serials">
                        <label for="serial" class="col-md-4 col-form-label text-md-right">{{ __('Serial') }}</label>
                        <div class="col-md-6">
                            <input id="serial" style="color: black" type="text" class="form-control" name="serial" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                        <div class="col-md-6">
                            <input id="status" style="color: black" type="text" class="form-control" name="status" disabled>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    @if(auth()->user()->hasrole('Repair'))
                    <input type="button" class="btn btn-primary mr-auto" id="unrepair_Btn" value="Unrepairable">
                    @endif
                    <input type="button" class="btn btn-primary" id="submit_Btn" value="Return">
                </div>
            </div>
        </div>
    </div>
</div>