var r = 1;
var c = 1;
var y = 1;
var b = 1;
var sub = 0;
var cattable;
var table;
var reqno;

$(document).ready(function()
{
    $('#catTable').show();
    $('#itemsearch').hide();
    $.ajax({
        type:'get',
        url:'gen',
        success:function(result)
        {
            reqno = result;
            $.ajax({
                url: 'checkbuffer',
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

$(document).on('click', '.reqBtn', function(){
    
    var thisdata = table.row( $(this).parents('tr') ).data();
    $('#qtyModal').modal({backdrop: 'static', keyboard: false});
    $('#requestcategory').val(thisdata.category);
    $('#requestitem').val(thisdata.item);
    cat = thisdata.category_id;
    item = thisdata.items_id;
    qty = thisdata.initial - thisdata.quantity;
    $('#qty').attr({
        "min" : 0//qty
    });
    $('#qty').val(0);
});

$(document).on('click', '#req', function(){
    if ($('#qty').val() <= 0) {
        return false;
    }
    $('#loading').show();
    $('#qtyModal').toggle();
    $.ajax({
        url: 'bufferstore',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'POST',
        data: {
            item: item,
            qty: $('#qty').val(),
        },
        success: function(){
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

$(document).on("click", "#catTable tr", function () {
    var catdata = cattable.row(this).data();
    $('table.stockTable').dataTable().fnDestroy();
    $('#itemsearch').show();
    $('#catname').text(catdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#catname').show();
    $('#head').text(catdata.category.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#catTable').hide();
    $('#ctable').hide();
    $('#stockTable').show();
    if($('#userlevel').val() == 'Warehouse Manager' || $('#userid').val() == '283' || $('#userid').val() == '110') {
        table =
        $('table.stockTable').DataTable({ 
            "dom": 'rtip',
            "language": {
                "emptyTable": " "
            },
            "pageLength": 30,
            "order": [[ 1, "asc" ], [ 0, "asc" ]],
            "fnRowCallback": function(nRow, aData) {
                if (aData.initial > aData.quantity) {
                    $('td', nRow).css('color', 'red');
                    $('td', nRow).css('font-weight', 'bold');
                }
            },
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
                { data: 'pending', name:'pending'},
                { data: 'UOM', name:'UOM'},
                { data: null, "render": function (data) 
                    {
                        if($('#userlevel').val() == 'Warehouse Manager' || $('#userid').val() == '228' || $('#userid').val() == '110'){
                            if (data.initial > data.quantity) {
                                var items_id = data.id;
                                return '<button class="btn-primary reqBtn" req_id="'+items_id+'">REQUEST STOCK</button>';
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
    }else{
        table =
        $('table.stockTable').DataTable({ 
            "dom": 'rti',
            "language": {
                "emptyTable": " "
            },
            "pageLength": 30,
            "order": [[ 1, "asc" ], [ 0, "asc" ]],
            "fnRowCallback": function(nRow, aData) {
                if (aData.initial > aData.quantity) {
                    $('td', nRow).css('color', 'red');
                    $('td', nRow).css('font-weight', 'bold');
                }
            },
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
                { data: 'pending', name:'pending'},
                { data: 'UOM', name:'UOM'}
            ]
        });
    }
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