var y = 1;
var r = 1;
var b =1;
var table;
var branchid;
var stock;
var cattable;
var sub = 0;
var reqno;
var repretselected;
var qty;
var cat;
var check;
var item;
var checkrequest = 'wala pa'

$(document).ready(function()
{
    branchid = $('#branchid').attr('branchid');
    $('#catTable').show();
    $('#itemsearch').hide();
    $.ajax({
        type:'get',
        url:'gen',
        success:function(result)
        {
            reqno = result;
            $.ajax({
                url: 'checkrequest',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'GET',
                async:false,
                data: {
                    reqno : reqno,
                },
                success: function(data){
                    if(data != "wala pa"){
                        reqno = data;
                        checkrequest = 'meron';
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                    alert(data.responseText);
                }
            });
        },
    });
    cattable =
    $('table.catTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No stock found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
        },
        "pageLength": 50,
        "fnRowCallback": function(nRow, aData) {
            if (aData.alert > 0) {
                $('td', nRow).css('color', 'red');
                $('td', nRow).css('font-weight', 'bold');
            }
        },
        processing: true,
        serverSide: false,
        ajax: {
            "url": 'viewStock',
            "data": {
                "data": 1
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        
        columns: [
            { data: 'category', name:'category'},
            { data: 'stockin', name:'stockin'},
            { data: 'stockout', name:'stockout'},
            { data: 'defectives', name:'defectives'},
            { data: 'total', name:'total'}
        ]
    });
   
    searchtable =
    $('table.searchtable').DataTable({ 
        "dom": 'lp',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 25,
        "order": [[ 1, "asc" ]],
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'searchall',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'category', name:'category'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'}
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

$(document).on("keyup", "#searchall", function () {
    if ($('#searchall').val()) {
        searchtable.search(this.value).draw();
        $('#searchtable').show();
        $('#salltable').show();
        $('#catTable').hide();
        $('#ctable').hide();
    }else{
        $('#searchtable').hide();
        $('#salltable').hide();
        $('#catTable').show();
        $('#ctable').show();
    }
});

$(document).on("click", "#searchtable tr", function () {
    var trdata = searchtable.row(this).data();
    var id = trdata.items_id;
    $('table.stockDetails').dataTable().fnDestroy();
    $('#head').text(trdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    stock = 
    $('table.stockDetails').DataTable({ 
        "dom": 'rt',
        "language": {
            "emptyTable": "No Stock Available for this Item"
        },
        processing: true,
        serverSide: true,
        ajax: {
            "url": "/bserial/"+id,
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
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

$(document).on("click", "#catTable tbody td", function () {
    var catdata = cattable.row(this).data();
    $('table.stockTable').dataTable().fnDestroy();
    $('#searchall').hide()
    $('#itemsearch').show();
    $('#catname').text(catdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#catname').show();
    $('#head').text(catdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#catTable').hide();
    $('#ctable').hide();
    $('#stockTable').show();

    table =
    $('table.stockTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
            "emptyTable": " "
        },
        "pageLength": 25,
        "order": [[ 1, "asc" ]],
        "fnRowCallback": function(nRow, aData) {
            if (aData.initial > aData.stockin) {
                $('td', nRow).css('color', 'red');
                $('td', nRow).css('font-weight', 'bold');
            }
        },
        processing: true,
        serverSide: false,
        ajax: {
            "url": 'viewStock',
            "data": {
                "data": 0,
                "category": catdata.category_id
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'description', name:'description'},
            { data: 'stockin', name:'stockin'},
            { data: 'stockout', name:'stockout'},
            { data: 'defectives', name:'defectives'},
            { data: 'total', name:'total'},
            { data: 'UOM', name:'UOM'},
            { data: null, "render": function ( data, type, row, meta) 
                {
                    if($('#userlevel').val() == 'Head'){
                        if (data.initial > data.stockin) {
                            var items_id = data.id;
                            var stockqty = data.stockin;
                            var initial = data.initial;
                            $.ajax({
                                url: 'checkrequestitemqty',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                                },
                                dataType: 'json',
                                type: 'GET',
                                async:false,
                                data: {
                                    items_id: items_id
                                },
                                success: function(thisdata){
                                    if(thisdata == 'yes'){
                                        check = 'sobra';
                                    }else{
                                        check = 'kulang';
                                    }
                                },
                                error: function (thisdata) {
                                    if(thisdata.status == 401) {
                                        window.location.href = '/login';
                                    }
                                    alert(thisdata.responseText);
                                }
                            });
                            if (check == 'sobra') {
                                return '';
                            }else{
                                return '<button class="btn-primary reqBtn" req_id="'+items_id+' test="'+check+'">REQUEST STOCK</button>';
                            }
                        }else{
                            return '';
                        }
                    }else{
                        return '';
                    }
                }
            }
        ]
    });
});

$(document).on('click', '#addStockBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});
});

var reqcode;

$(document).on('click', '.reqBtn', function(){
    $.ajax({
        type:'get',
        url:'reqcode',
        success:function(result)
        {
            reqcode = result;
        },
    });
    var thisdata = table.row( $(this).parents('tr') ).data();
    $('#qtyModal').modal({backdrop: 'static', keyboard: false});
    $('#requestcategory').val(thisdata.category);
    $('#requestitem').val(thisdata.item);
    cat = thisdata.category_id;
    item = thisdata.items_id;
    qty = thisdata.initial - thisdata.stockin;
    $('#qty').attr({
        "min" : qty,
        "max" : thisdata.initial
    });
    $('#qty').val(qty);
});

// $(document).on('keyup', '#qty', function(){
//     if ($(this).val()) {
//         if ($(this).val() < qty) {
//             $(this).val(qty);
//         }
//         $('#req').prop('disabled', false);
//     }else{
//         $('#req').prop('disabled', true);
//     }
// });
// $(document).on('click', '#qty', function(){
//     if ($(this).val()) {
//         if ($(this).val() < qty) {
//             $(this).val(qty);
//         }
//         $('#req').prop('disabled', false);
//     }else{
//         $('#req').prop('disabled', true);
//     }
// });

$(document).on('click', '#req', function(){
    var proceed = false;
    $.ajax({
        type:'get',
        url:'checkreqcode',
        async: false,
        data:{
            reqcode: reqcode
        },
        success:function(result)
        {
            if (result == 'ok') {
                proceed = true;
            }
        },
    });
    if (!proceed) {
        alert('Request Code not match. Please Contact System Administrator.');
        return proceed;
    }
    $('#loading').show();
    $('#qtyModal').toggle();
    $.ajax({
        url: 'storerequest',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'POST',
        data: {
            reqno : reqno,
            item: item,
            qty: $('#qty').val(),
            check: checkrequest,    
            stat: 'notok'                   
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
    $.ajax({
        url: 'storerequest',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'POST',
        data: {
            reqno : reqno,
            clientid : null,  
            customerid : null,  
            ticket : null,  
            type : 'Stock',  
            stat: 'ok'                     
        },
        success: function(){
            if (checkrequest == 'meron') {
                alert('ATTENTION: Your new stock request was added to your previous pending request with Request No. '+reqno);
            }else{
                alert('You have successfully submitted a stock request. You may check your request under STOCK REQUEST tab.');
            }
            window.location.href = 'stocks';
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
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
        if ($('#serial'+ rowcount).val().toLowerCase() != 'n/a') {
            if ($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, '')) {
                $.ajax({
                    url: 'verifyserial',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'get',
                    async: false,
                    data: {
                        serial: $('#serial'+ rowcount).val().toLowerCase()
                    },
                    success: function (data) {
                        if (data != "allowed") {
                            console.log(data);
                            alert('Serial Number already exist!');
                            $('#serial'+ rowcount).val('');
                            return false;
                        }else{
                            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                                y++;
                                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                                $('#category'+ rowcount).prop('disabled', true);
                                $('#desc'+ rowcount).prop('disabled', true);
                                $('#serial'+ rowcount).val($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, ''));
                                $('#serial'+ rowcount).prop('disabled', true);
                                if (r < 20 ) {
                                    $('#reqfield').append(additem);
                                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                                    $('#itemdiv'+ y).hide();
                                    r++;
                                }
                            }
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                        return false;
                    }
                });
            }
        }else{
            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#serial'+ rowcount).val($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, ''));
                $('#serial'+ rowcount).prop('disabled', true);
                if (r < 20 ) {
                    $('#reqfield').append(additem);
                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                    $('#itemdiv'+ y).hide();
                    r++;
                }
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            $('#itemdiv'+ y).hide();
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#desc'+rowcount).val('select item description');
        $('#serial'+rowcount).val('select serial');
        $('#category'+rowcount).prop('disabled', false);
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
        location.reload();
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
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
        location.reload();
    }
});

$(document).on('click', '.cancel', function(){
    location.reload();
});
var reprettable =
    $('table.replace-return').DataTable({ 
        "dom": 'lrtp',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No item/s for return found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Defectives\" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
            select: {
                rows: {
                    _: "You have selected %d Defective Units",
                    0: "Select Defective Units to Return",
                    1: "Only %d Defective Unit Selected"
                }
            }
        },
        "pageLength": 25,
        columnDefs: [
            {
            orderable: false,
            className: 'select-checkbox',      
            targets: 0
            },
            {
                "targets": [ 0 ],
                "visible": true
            }
        ],
        ajax: {
            url: '/return-table',
            async: false,
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: null, defaultContent: ''},
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ],
        select: {
            style: 'single',
            selector: 'td:first-child'
        }
    });

$(document).on('click', '.repret_sub_Btn', function(){
    var repretrow = reprettable.rows( { selected: true } ).data();
    if (repretrow.length == 0) {
        alert('Please Select Item!');
    }else{
        console.log(repretrow);
        $.ajax({
            url: 'def',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'DELETE',
            data: {
                id: repretselected[0].id,
                serial: repretselected[0].serial,
                items_id: repretselected[0].items_id,
                item: repretselected[0].item,
                replace: 1,
                repairitem: repretrow[0].item,
                repairserial: repretrow[0].serial,
                repairid:  repretrow[0].id
            },
            success: function(){
                location.reload(); 
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }
});
         
$(document).on('click', '#use_Btn', function(){
    repretselected = stock.rows( { selected: true } ).data();
    console.log(repretselected);
    $('#replace-item').val(repretselected[0].item);
    var rowcount = reprettable.data().count();
    var status = new Array();
    for(var i=0;i<rowcount;i++){
        if (reprettable.rows( i ).data()[0].status == 'For receiving')
        {
            status.push(i);
        }
    }
    if (rowcount == status.length) {
        return false;
    }
    if (rowcount != status.length) {
        $('#loading').show();
        reprettable.rows( status ).remove().draw();
        $('#loading').hide();
    }
    $('#stockModal').hide();
    $('#replace-return').modal({backdrop: 'static', keyboard: false});
});


$('table.stockDetails').DataTable().on('select', function () {
    var rowselected = stock.rows( { selected: true } ).data();
    if(rowselected.length > 0){
        $('#def_Btn').prop('disabled', false);
        $('#pull_Btn').prop('disabled', false);
        $('#use_Btn').prop('disabled', false);
    }
});

$('table.stockDetails').DataTable().on('deselect', function () {
    var rowselected = stock.rows( { selected: true } ).data();
    if(rowselected.length == 0){
        $('#def_Btn').prop('disabled', true);
        $('#use_Btn').prop('disabled', true);
        $('#pull_Btn').prop('disabled', true);
    }
});

$(document).on("click", "#def_Btn", function () {
    $('#passwordModal').modal('show');
});

$(document).on("click", "#pull_Btn", function () {
    var datas = stock.rows( { selected: true } ).data();
    $.ajax({
        url: 'pullout',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: datas[0].id,
            serial: datas[0].serial,
            items_id: datas[0].items_id,
            replace: 0,
            item: datas[0].item
        },
        success: function(){
            stock.rows( { selected: true } ).remove().draw(false);
            //location.reload(); 
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on("click", "#confirm_Btn", function () {
    var datas = stock.rows( { selected: true } ).data();
    if ($('#email').val() && $('#password').val()){
        $.ajax({
            url: 'confirm',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            data: {
                password: $('#password').val(),
                email: $('#email').val()
            },
            success: function(data){
                if (data == 'success') {
                    $('#passwordModal').modal('hide');
                    $('#stockModal').modal('hide');
                    $('#loading').show();
                    $.ajax({
                        url: 'def',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'DELETE',
                        data: {
                            id: datas[0].id,
                            serial: datas[0].serial,
                            items_id: datas[0].items_id,
                            replace: 0,
                            item: datas[0].item
                        },
                        success: function(){
                            location.reload(); 
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }else{
                    alert(data);
                }
                
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }
});

$(document).on("click", "#stockTable tbody td", function () {
    if ( $(this).index() < 6 ) {
        var trdata = table.row(this).data();
        var id = trdata.items_id;
        $('table.stockDetails').dataTable().fnDestroy();
        $('#head').text(trdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
        stock = 
        $('table.stockDetails').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No Stock Available for this Item"
            },
            processing: true,
            serverSide: false,
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
        $('#stockModal').modal('show');
    }
});

function checkserial(ex) {
    //var mycount = document.getElementById(ex.id).row_count.value;
    var myval = ex.id;
    var slicena = myval.slice(6)
    console.log(slicena);
    if ($('#serial'+slicena).val().toLowerCase().includes('n/a')) {
        $.ajax({
            url: 'checkserial',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            async: false,
            data: {
                item: $('#desc'+slicena).val(),
                type: 'na'
            },
            success: function (data) {
                if (data != "allowed") {
                    $('#serial'+slicena).val('');
                    alert('This item requires a valid serial number. If the item does not contain a serial number please contact the main office to generate a new one.');
                }
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }else{
        $.ajax({
            url: 'checkserial',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            async: false,
            data: {
                serial: $('#serial'+slicena).val(),
                type: 'check'
            },
            success: function (data) {
                console.log(data);
                if (data != "allowed") {
                    $('#serial'+slicena).val('');
                    alert('The serial number you selected is already existing. Please contact the administrator.');
                }
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
}