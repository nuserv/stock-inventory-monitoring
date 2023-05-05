<div id="updateModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">UPDATE 2NR PRINTER</h6>
                <button class="close cancel" aria-label="Close" hidden>
                </button>
            </div>
            <div class="modal-body" id="itemfield">
                <div class="row no-margin" id="row1">
                    <div class="col-md-12 form-group">
                        <select id="desc1" class="form-control desc" row_count="1" style="color: black">
                            <option selected disabled>select item description</option>
                            @foreach (App\Item::where('category_id', 7)->get() as $item)
                                @if ($item->item && strpos($item->item, '2NR Printer') !== false)
                                    <option value="{{ $item->id }}">{{ mb_strtoupper($item->item) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
                <input type="button" class="btn btn-primary sub_Btn" id="sub_Btn" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>