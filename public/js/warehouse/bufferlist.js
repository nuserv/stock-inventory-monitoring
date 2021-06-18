var table;
var send = 1;
var retno;
var rowcount;
var pullout;
var items;
var pull_no;
var reqDetails;
var reqDate;
var senditems;
var buffersenditems;
$(document).ready(function()
{
    table =
    $('table.bufferTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No request list found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'bufferlist',
            data:{
                list: 'list'
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'buffers_no', name:'buffers_no'},
            { data: 'status', name:'status'}
        ]
    });
    
});
$(document).on("click", "#bufferTable tr", function () {
    var data = table.row(this).data();
    reqDate = data.updated_at;
    $('#head').text('Request no. '+data.buffers_no);
    $('#bufferModal').modal({backdrop: 'static', keyboard: false});
    buffers_no = data.buffers_no;
    
    if ($('#level').val() == 'Returns Manager') {
        if (data.status != "For approval") {
            $('#approvedBtn').hide();
        }
        Promise.all([bufferitems(), buffersend()]).then(() => { 
            if (items > 0) {
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
            }else{
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
                $('table.bufferitems').dataTable().fnDestroy();
                $('#pending').hide();
            }
            if (senditems > 0) {
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                    //$('#receiving').show();
                    //$('#prcBtn').hide();
            }else{
                $('#receiving').hide();
            }
        });
    }else if ($('#level').val() == 'Main Warehouse Manager') {
        if (data.status == 'For approval') {
            $('#prcBtn').hide();
        }
        Promise.all([bufferitems(), buffersend()]).then(() => { 
            if (items != 0) {
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
            }else{
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
                $('table.bufferitems').dataTable().fnDestroy();
                $('#pending').hide();
                $('#prcBtn').hide();
            }
            if (senditems > 0 ) {
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                    $('#receiving').show();
            }else{
                $('#receiving').hide();
            }
        });
        
    }else if ($('#level').val() == 'Warehouse Manager') {
        Promise.all([bufferitems(), buffersend()]).then(() => { 
            if (items != 0) {
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
            }else{
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'},
                        ]
                    });
                $('table.bufferitems').dataTable().fnDestroy();
                $('#pending').hide();
            }
            if (senditems > 0 ) {
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'},
                            { data: null, "render": function (data) 
                                {
                                    return '<button class="btn-primary recBtn" req_id="'+data.items_id+'">ADD TO STOCK</button>';
                                }
                            }
                        ]
                    });
                    $('#receiving').show();
                    //$('#prcBtn').hide();
            }else{
                $('#receiving').hide();
            }
        });
    }
    
    
    /*if ($('#level').val() != "Main Warehouse Manager") {
        Promise.all([buffersend()]).then(() => { 
            if (senditems != 0) {
            console.log('dito');
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'},
                            { data: null, "render": function (data) 
                                {
                                    return '<button class="btn-primary recBtn" req_id="'+data.items_id+'">ADD TO STOCK</button>';
                                }
                        }
                        ]
                    });
            }else{
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.buffers_no,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'},
                            { data: null, "render": function (data) 
                                {
                                    return '<button class="btn-primary recBtn" req_id="'+data.items_id+'">ADD TO STOCK</button>';
                                }
                        }
                        ]
                    });
                $('table.buffersend').dataTable().fnDestroy();
                $('#receiving').hide();
            }
        });
        
    }*/
    
    function bufferitems() {
        return $.ajax({
            type:'get',
            url: "bufferitem",
            data: {
                buffers_no: data.buffers_no,
            },
            success:function(data)
            {
                items = data.data.length;
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
    function buffersend() {
        return $.ajax({
            type:'get',
            url: "buffersenditems",
            data: {
                buffers_no: data.buffers_no,
            },
            success:function(data)
            {
                senditems = data.data.length;
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
    
});
$(document).on('click', '.recBtn', function(){
    var thisdata = buffersenditems.row( $(this).parents('tr') ).data();
    var row =  $(this).parents('tr');
    $('#loading').show();
    $.ajax({
        url: 'bufferreceived',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'put',
        data: {
            buffers_no : buffers_no,
            items_id: thisdata.items_id,
            qty: thisdata.qty,
            item: thisdata.item,
            category_id: thisdata.category_id
        },
        success: function(){
            $('#loading').hide();
            buffersenditems
                .row(row)
                .remove().draw( false );
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('click', '#approvedBtn', function(){
    $('#bufferModal').toggle();
    $('#loading').show();
    $.ajax({
        url: 'bufferapproved',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'put',
        data: {
            buffers_no : buffers_no,
        },
        success: function(){
            location.reload();
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});

$(document).on('click', '#prcBtn', function(){
    $("#bufferModal .closes").click();
    $('#loading').show();
    $('#sreqno').val(buffers_no);
    $('#sdate').val(reqDate);
    $('table.sendDetails').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        async:false,
        "language": {
            "emptyTable": "No item found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "order": [ 0, "asc" ],
        "pageLength": 25,
        ajax: {
            url: 'bufferitem',
            data: {
                buffers_no: buffers_no,
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'pending', name:'pending'},
        ]
    });
    bufferdata = buffer.rows().data()
    for (let index = 0; index < items; index++) {
        var additem = '<div class="row no-margin" id="row'+index+'"><div class="col-md-2 form-group"><select id="categ'+index+'" disabled class="form-control categ" row_count="'+index+'" style="color:black;-webkit-appearance: none;"><option selected value="'+bufferdata[index].cat_id+'">'+bufferdata[index].category+'</option></select></div>  <div class="col-md-7 form-group"><select id="desc'+index+'" disabled class="form-control desc" row_count="'+index+'" style="color:black;-webkit-appearance: none;"><option selected value="'+bufferdata[index].items_id+'">'+bufferdata[index].item+'</option></select></div><div class="col-md-1 form-group"><input type="number" class="form-control qty" row_count="'+index+'" id="qty'+index+'" min="0" max="'+bufferdata[index].pending+'" style="color:black" autocomplete="off" placeholder="input quantity" value="0"></div></div>'
        $('#reqfield').append(additem);
    }
    $('#loading').hide();
    $('#sendModal').modal('show');
});

$(document).on('keydown', '.qty', function(){
    var qty = $(this).attr('max');
    if (!$(this).val() || (parseInt($(this).val()) <= qty && parseInt($(this).val()) >= 0))
    $(this).data("old", $(this).val());
});
$(document).on('keyup', '.qty', function(){
    var qty = $(this).attr('max');
    if (!$(this).val() || (parseInt($(this).val()) <= qty && parseInt($(this).val()) >= 0))
      ;
    else
      $(this).val($(this).data("old"));
});

$(document).on('click', '.sub_Btn', function(){
    $('#sendModal').toggle();
    $('#loading').show();
    for (let index = 0; index < items; index++) {
        qty = $('#qty'+index).val();
        if (!$('#qty'+index).val()) {
            qty = 0;
            console.log('pumasok');
        }
        $.ajax({
            url: 'buffersend',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            async:false,
            type: 'PUT',
            data: {
                items_id: $('#desc'+index).val(),
                buffers_no: $('#sreqno').val(),
                qty: qty
            },
            success: function(){
                location.reload();
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
});

$(document).on('click', '.cancel', function(){
    $('#loading').show();
    location.reload();
});