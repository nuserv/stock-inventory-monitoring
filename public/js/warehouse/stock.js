var r = 1;
var c = 1;
var y = 1;
var b = 1;
var sub = 0;
var cattable;
var table;
$(document).ready(function()
{
    $('#catTable').show();
    $('#itemsearch').hide();
    cattable =
    $('table.catTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No stock found!"
        },
        "pageLength": 50,
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'show',
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

    $('#search-ic').on("click", function (event) { 
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
    $('#catname').text(catdata.category.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&'));
    $('#catname').show();
    $('#head').text(catdata.category.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&'));
    $('#catTable').hide();
    $('#ctable').hide();
    $('#stockTable').show();
    table =
    $('table.stockTable').DataTable({ 
        "dom": 'rti',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 30,
        "order": [[ 1, "asc" ], [ 0, "asc" ]],
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'show',
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
            { data: 'StockIN', name:'StockIN'},
            { data: 'StockOUT', name:'StockOUT'},
            { data: 'quantity', name:'quantity'},
            { data: 'UOM', name:'UOM'}
        ]
    });
});


$(document).on('click', '#addStockBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();        
    $('#desc' + count).val(id);
    $.ajax({
        type:'get',
        url:'uom',
        data:{
            id: id
        },
        success:function(data)
        {
            $('#uom'+count).val(data);
        },
    });
});

$(document).on('change', '.desc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#item' + count).val(id);
    $.ajax({
        type:'get',
        url:'uom',
        data:{
            id: id
        },
        success:function(data)
        {
            $('#uom'+count).val(data);
        },
    });
});

$(document).on('change', '.category', function(){
    var codeOp = " ";
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            codeOp+='<option selected disabled>select item code</option>';
            descOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                codeOp+='<option value="'+value.id+'">'+value.id+'</option>';
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#item" + count).find('option').remove().end().append(codeOp);
            $("#desc" + count).find('option').remove().end().append(descOp);
        },
    });
    
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#category'+ rowcount).val() && $('#item'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#qty'+ rowcount).val() && $('#qty'+ rowcount).val() != 0 ) {
            y++;
            $('#sub_Btn').prop('disabled', false);
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" style="color: black" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'" style="color: black"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'" style="color: black"><option selected disabled>select item description</option></select></div><div class="col-md-1 form-group text-center"><input type="number" class="form-control" min="0" name="qty'+y+'" id="qty'+y+'" placeholder="0" style="color:black; width: 6em"></div><div class="col-md-2 form-group text-center"><input type="text" class="form-control" name="uom'+y+'" id="uom'+y+'" style="color:black;"readonly></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
            $(this).val('Remove');
            $('#category'+ rowcount).prop('disabled', true);
            $('#item'+ rowcount).prop('disabled', true);
            $('#desc'+ rowcount).prop('disabled', true);
            $('#qty'+ rowcount).prop('disabled', true);
            $('#uom'+ rowcount).prop('disabled', true);

            if (r < 20 ) {
                $('#reqfield').append(additem);
                $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                r++;
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" style="color: black" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'" style="color: black"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'" style="color: black"><option selected disabled>select item description</option></select></div><div class="col-md-1 form-group text-center"><input type="number" class="form-control" min="0" name="qty'+y+'" id="qty'+y+'" placeholder="0" style="color:black; width: 6em"></div><div class="col-md-2 form-group text-center"><input type="text" class="form-control" name="uom'+y+'" id="uom'+y+'" style="color:black;"readonly></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#item'+rowcount).val('select item code');
        $('#desc'+rowcount).val('select item description');
        $('#category'+rowcount).prop('disabled', false);
        $('#item'+rowcount).prop('disabled', false);
        $('#desc'+rowcount).prop('disabled', false);
        $('#row'+rowcount).hide();
        $(this).val('Add Item');
        r--;
        if (r == 1) {
            $('#sub_Btn').prop('disabled', true);
        }
    }
});

$(document).on('click', '.sub_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var cat = "";
    var item = "";
    var qty = "";
    var check = 1;
    $('#loading').show();
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                sub++;
                $('.sub_Btn').prop('disabled', true)
                cat = $('#category'+q).val();
                item = $('#item'+q).val();
                qty = $('#qty'+q).val();
                $.ajax({
                    url: 'store',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    async: false,
                    data: {
                        item: item,
                        qty: qty,
                        cat : cat
                    }
                });
            }
        }
    }
    if (check > 1) {
        location.reload();
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
        if($('#item-desc'+ rowcount).val() && $('#itemcat'+ rowcount).val() && $('#itemuom'+rowcount).val()){
        y++;
            var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-2"><select id="itemuom'+y+'" class="form-control item-uom" row_count="'+y+'"><option selected disabled>select uom</option><option value="Meter">Meter</option><option value="Unit">Unit</option><option value="Pc">Pc</option></select></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
            $(this).val('Remove');
            $('#item-desc'+ rowcount).prop('disabled', true);
            $('#itemcat'+ rowcount).prop('disabled', true);
            $('#itemuom'+ rowcount).prop('disabled', true);
            if (b < 10 ) {
            $('#itemfield').append(additem);
                $('#itemcat'+ rowcount).find('option').clone().appendTo('#itemcat'+y);
                b++;
            }
        }
    }else{
        if (b == 10) {
            y++;
            var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-2"><select id="itemuom'+y+'" class="form-control item-uom" row_count="'+y+'"><option selected disabled>select uom</option><option value="Meter">Meter</option><option value="Unit">Unit</option><option value="Pc">Pc</option></select></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
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
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
        alert("Category added!");
        location.reload();
    }
});

$(document).on('click', '#sub_item_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var cat = "";
    var uom = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#itemrow'+q).is(":visible")) {
            if ($('.add_item-desc[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                sub++;
                $('#sub_item_Btn').prop('disabled', true);
                cat = $('#itemcat'+q).val();
                item = $('#item-desc'+q).val();
                uom = $('#itemuom'+q).val();
                $.ajax({
                    url: 'additem',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        cat : cat,
                        uom : uom,
                        item : item
                    },
                });
            }
        }
    }
    if (check > 1) {
        alert("Item added!");
        location.reload();
    }
});

$(document).on('click', '.cancel', function(){
    location.reload();
});

$(document).on('click', '#importBtn', function(){
    $('#importModal').modal({backdrop: 'static', keyboard: false});
});