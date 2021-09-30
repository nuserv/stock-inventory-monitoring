<div id="exportModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">GENERATE PM REPORTS</h6>
                <button class="close" data-dismiss="modal" aria-label="Close" hidden>

                </button>
            </div>
            <div class="modal-body" id="import">
                <div class="row no-margin">
                    <div class="col-md-3 form-group">
                        <select id="yearselect" style="color: black">
                            <option selected disabled>select year</option>
                        </select>
                    </div>
                </div><hr>
                <div class="row no-margin">
                    <div class="col-md-3 form-group">
                        <select id="monthselect" style="color: black" disabled>
                            <option selected disabled>select month</option>
                        </select>
                    </div>
                    <div class="col-md-5 form-group">
                        <b>to</b> &nbsp;&nbsp;&nbsp;
                        <select id="monthto" style="color: black" disabled>
                            <option selected disabled>select month</option>
                        </select>
                    </div>
                </div>
                @if (auth()->user()->hasanyrole('Manager', 'Editor'))
                <hr>
                <div class="row no-margin">
                    <div class="col-md-4 form-group">
                        <select id="select_area" style="color: black" disabled>
                            <option selected disabled>select area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 form-group">
                        <select id="select_branch" style="color: black" disabled>
                            <option selected disabled>select branch</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel mr-auto" value="Cancel">
                <input type="button" class="btn btn-primary ml-auto" id="genBtn" value="Generate">
            </div>
        </div>
    </div>
</div>