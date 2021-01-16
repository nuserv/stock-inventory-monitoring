var y = 1;
var r = 1;
var b =1;
var table;
var branchid;
var stock;
var cattable;
var sub = 0;
$(document).ready(function()
{
    branchid = $('#branchid').attr('branchid');
    $('#catTable').show();
    $('#itemsearch').hide();
    cattable =
    $('table.catTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 50,
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'viewStock',
            "data": {
                "data": 1
            },
            error: function (data) {
                alert(data.responseText);
            }
        },
        
        columns: [
            { data: 'category', name:'category'},
            { data: 'quantity', name:'quantity'}
        ]
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=6 ; i++ ) {
            
            $('.fl-'+i).val('').change();
            table
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
        
    });

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});

$(document).on("click", "#catTable tr", function () {
    var catdata = cattable.row(this).data();
    $('table.stockTable').dataTable().fnDestroy();
    $('#itemsearch').show();
    $('#catname').text(catdata.category.replace(/&amp;/g, '&'));
    $('#catname').show();
    $('#head').text(catdata.category.replace(/&amp;/g, '&'));
    $('#catTable').hide();
    $('#ctable').hide();
    $('#stockTable').show();

    table =
    $('table.stockTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 15,
        "order": [[ 1, "asc" ]],
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'viewStock',
            "data": {
                "data": 0,
                "category": catdata.category_id 
            },
            error: function (data) {
                alert(data.responseText);
            }
        },
        columns: [
            { data: 'description', name:'description'},
            { data: 'quantity', name:'quantity'}
        ]
    });

});

$(document).on('click', '#addStockBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});

});

$(document).on('click', '#importBtn', function(){
    $('#importModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '#in_Btn', function(){
    if ($('#check').val() == '[]') {
        $('.defective').prop('disabled', true)
        $('.good').prop('disabled', true)
    }else{
        $('.defective').prop('disabled', false)
        $('.good').prop('disabled', false)
    }
    $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '#out_Btn', function(){
    $('#outOptionModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#category'+ rowcount).val() && $('#item'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $(this).val('Remove');
            $('#category'+ rowcount).prop('disabled', true);
            $('#item'+ rowcount).prop('disabled', true);
            $('#desc'+ rowcount).prop('disabled', true);
            $('#serial'+ rowcount).prop('disabled', true);
            if (r < 20 ) {
                $('#reqfield').append(additem);
                $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                r++;
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#item'+rowcount).val('select item code');
        $('#desc'+rowcount).val('select description');
        $('#serial'+rowcount).val('select serial');
        $('#category'+rowcount).prop('disabled', false);
        $('#item'+rowcount).prop('disabled', false);
        $('#desc'+rowcount).prop('disabled', false);
        $('#serial'+rowcount).prop('disabled', false);
        $('#row'+rowcount).hide();
        $(this).val('Add Item');
        r--;
    }
});

$(document).on('click', '#addCatBtn', function(){
    $("#addModal .close").click();
    $('#categoryModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '#addCodeBtn', function(){
    $("#addModal .close").click();
    $('#itemModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.add_cat', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add') {
        if($('#cat'+ rowcount).val()){
            y++;
            var additem = '<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
            $(this).val('Remove');
            $('#cat'+ rowcount).prop('disabled', true);
        }
        if (c < 10 ) {
            $('#catfield').append(additem);
            $('#cat'+ rowcount).find('option').clone().appendTo('#cat'+y);
            c++;
        }
    }else{
        if (c == 10) {
            y++;
            var additem = '<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
            $('#catfield').append(additem);
            $('#cat'+ rowcount).find('option').clone().appendTo('#cat'+y);
            c++;
        }
        $('#cat'+rowcount).val('');
        $('#catrow'+rowcount).hide();
        $(this).val('Add');
        c--;
    }
});

$(document).on('click', '.add_item-desc', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add') {
        if($('#item-desc'+ rowcount).val() && $('#itemcat'+ rowcount).val()){
            y++;
            var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
            $(this).val('Remove');
            $('#item-desc'+ rowcount).prop('disabled', true);
            $('#itemcat'+ rowcount).prop('disabled', true);
        }
        if (b < 10 ) {
            $('#itemfield').append(additem);
            $('#itemcat'+ rowcount).find('option').clone().appendTo('#itemcat'+y);
            b++;
        }
    }else{
        if (b == 10) {
            y++;
            var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
            $('#itemfield').append(additem);
            $('#itemcat'+ rowcount).find('option').clone().appendTo('#itemcat'+y);
            b++;
        }
        $('#itemcat'+rowcount).val('');
        $('#itemrow'+rowcount).hide();
        $(this).val('Add');
        b--;
    }
});

$(document).on('click', '#sub_cat_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var cat = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#catrow'+q).is(":visible")) {
            if ($('.add_cat[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                sub++;
                $('#sub_cat_Btn').prop('disabled', true)
                cat = $('#cat'+q).val();
                $.ajax({
                    url: 'addcategory',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        cat : cat
                    },
                });
            }
        }
    }
    if (check > 1) {
        window.location.href = 'stocks';
    }
});

$(document).on('click', '#sub_item_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var cat = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#itemrow'+q).is(":visible")) {
            if ($('.add_item-desc[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                sub++;
                $('#sub_item_Btn').prop('disabled', true);
                cat = $('#itemcat'+q).val();
                item = $('#item-desc'+q).val();
                $.ajax({
                    url: 'additem',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        cat : cat,
                        item : item
                    },
                });
            }
        }
    }
    if (check > 1) {
        window.location.href = 'stocks';
    }
});

$(document).on('click', '.cancel', function(){
    window.location.href = 'stocks';
});

$('table.stockDetails').DataTable().on('select', function () {
    var rowselected = stock.rows( { selected: true } ).data();
    //console.log(rowselected[0]);
    if(rowselected.length > 0){
        $('#def_Btn').prop('disabled', false);
    }
});

$('table.stockDetails').DataTable().on('deselect', function () {
    var rowselected = stock.rows( { selected: true } ).data();
    //console.log(rowselected[0]);
    if(rowselected.length == 0){
        $('#def_Btn').prop('disabled', true);
    }
});

$(document).on("click", "#def_Btn", function () {
    var data = stock.rows( { selected: true } ).data();
    $.ajax({
        url: 'def',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'DELETE',
        data: {
            id: data[0].id,
            serial: data[0].serial,
            items_id: data[0].items_id,
            item: data[0].item
        },
        success: function(){
            setTimeout(function(){// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
            }, 1000);
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on("click", "#stockTable tr", function () {
    var trdata = table.row(this).data();
    var id = trdata.items_id;
    $('table.stockDetails').dataTable().fnDestroy();
    $('#head').text(trdata.category.replace(/&amp;/g, '&'));
    stock = 
    $('table.stockDetails').DataTable({ 
        "dom": 'rt',
        "language": {
            "emptyTable": "No Stock Available for this Item"
        },
        processing: true,
        serverSide: true,
        ajax: "/bserial/"+id,
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'}
        ],
        select: {
            style: 'single'
        }
    });
    $('#stockModal').modal();
});
