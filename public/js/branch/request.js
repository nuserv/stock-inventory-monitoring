var y = 1;
var table;
var schedtable;
var dtdata;
var sub = 0;
var add = 0;
var pendingreq;
$(document).ready(function()
{
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 25,
        "language": {
            "emptyTable": 'No stock request found.',
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
        },
        processing: true,
        serverSide: true,
        ajax: 'requests',
        columns: [
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'request_no', name:'request_no', "width": "14%"},
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"}
        ]
    });

    $('#requestTable tbody').on('click', 'tr', function () { 
        var trdata = table.row(this).data();
        dtdata = table.row(this).data();;
        $('#date').val(trdata.created_at);
        $('#reqno').val(trdata.request_no);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();

        if (trdata.status == 'PENDING') {
            $('table.schedDetails').hide();
            $('table.requestDetails').show();
            $('.sched').hide();
            $('#del_Btn').show();
            $('#msg').hide();
            $('#rec_Btn').hide();
            $('#del_Btn').attr('reqno', trdata.request_no);
            Promise.all([pendingrequest()]).then(() => { 
                if (pendingreq.data().count() <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'}
                        ]
                    });
                }else if (pendingreq.data().count() > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'}
                        ]
                    });
                }
            });
            function pendingrequest() {
                return pendingreq = 
                $('table.requestDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/requests/"+trdata.request_no,
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'quantity', name:'quantity'}
                    ]
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            Promise.all([sched()]).then(() => { 
                if (schedtable.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (schedtable.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function sched() {
                return schedtable = 
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    
                    columns: [
                        { data: 'schedule', name:'schedule'},
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            Promise.all([incompleteschedtable()]).then(() => { 
                if (schedtable.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (schedtable.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function incompleteschedtable() {
                return schedtable = 
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    
                    columns: [
                        { data: 'schedule', name:'schedule'},
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            Promise.all([rescheduleschedtable()]).then(() => { 
                if (schedtable.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (schedtable.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function rescheduleschedtable() {
                return schedtable = 
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    
                    columns: [
                        { data: 'schedule', name:'schedule'},
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }else if(trdata.status == 'PARTIAL'){
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            $('.sched').hide();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            Promise.all([partialschedtable()]).then(() => { 
                if (schedtable.data().count() == 0) {
                    $('.sched').hide();
                    $('#msg').hide();
                    $('table.schedDetails').dataTable().fnDestroy();
                    $('table.schedDetails').hide();
                    $('table.requestDetails').show();
                    Promise.all([partialrequesttable()]).then(() => {
                        if (requesttable.data().count() <= 10) {
                            $('table.requestDetails').dataTable().fnDestroy();
                            requesttable = $('table.requestDetails').DataTable({ 
                                "dom": 'rt',
                                "language": {
                                    "emptyTable": " "
                                },
                                processing: true,
                                serverSide: true,
                                ajax: "/requests/"+trdata.request_no,
                                columns: [
                                    { data: 'items_id', name:'items_id'},
                                    { data: 'item_name', name:'item_name'},
                                    { data: 'quantity', name:'quantity'}
                                ]
                            });
                        }else if (requesttable.data().count() > 10){
                            $('table.requestDetails').dataTable().fnDestroy();
                            requesttable = $('table.requestDetails').DataTable({ 
                                "dom": 'lrtip',
                                "language": {
                                    "emptyTable": " "
                                },
                                processing: true,
                                serverSide: true,
                                ajax: "/requests/"+trdata.request_no,
                                columns: [
                                    { data: 'items_id', name:'items_id'},
                                    { data: 'item_name', name:'item_name'},
                                    { data: 'quantity', name:'quantity'}
                                ]
                            });
                        }
                    });
                    function partialrequesttable() {
                        return requesttable = $('table.requestDetails').DataTable({ 
                            "dom": 'lrtip',
                            "language": {
                                "emptyTable": " "
                            },
                            processing: true,
                            serverSide: true,
                            ajax: "/requests/"+trdata.request_no,
                            columns: [
                                { data: 'items_id', name:'items_id'},
                                { data: 'item_name', name:'item_name'},
                                { data: 'quantity', name:'quantity'}
                            ]
                        });
                    }
                    $('#rec_Btn').hide();
                }else if (schedtable.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (schedtable.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtip',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });

            function partialschedtable() {
                return schedtable = 
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtip',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    
                    columns: [
                        { data: 'schedule', name:'schedule'},
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }
        $('#requestModal').modal('show');
    });

    $('table.schedDetails').DataTable().on('select', function () {
        var rowselected = schedtable.rows( { selected: true } ).data();
        if(rowselected.length > 0){
            $('#rec_Btn').prop('disabled', false);
        }
    });
    $('table.schedDetails').DataTable().on('deselect', function () {
        var rowselected = schedtable.rows( { selected: true } ).data();
        if(rowselected.length == 0){
            $('#rec_Btn').prop('disabled', true);
        }
    });
    
});

$(document).on('click', '#del_Btn', function(){
    var reqno = $(this).attr('reqno');
    $('#loading').show();
    $.ajax({
        url: 'remove',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'DELETE',
        data: {
            reqno : reqno                     
        },
        success: function(){
            location.reload();
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '#rec_Btn', function(){
    var reqno = $('#reqno').val();
    var sched = $('#sched').val();
    $('#loading').show();
    if(dtdata.status == "SCHEDULED"){
        var status = "2";
    }else if(dtdata.status == "RESCHEDULED"){
        var status = "7";
    }else if(dtdata.status == "PARTIAL"){
        var status = "8";
    }
    var datas = schedtable.rows( { selected: true } ).data();
    var id = [];
    if(datas.length > 0){
        for(var i=0;i<datas.length;i++){
            id.push(datas[i].id);
        }    
        $.ajax({
            url: 'storerreceived',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            type: 'POST',
            async: false,
            data: {
                reqno : reqno,
                id: id,
                status: status,
                sched: sched
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

$(document).on('click', '#reqBtn', function(){
    
    $.ajax({
        type:'get',
        url:'gen',
        success:function(result)
        {
            $('#sreqno').val(result);
        },
    });
    $('#loading').show()
    var catop;
    $.ajax({
        type:'get',
        url:'checkStock',
        success:function(data)
        {
            var category = $.map(data, function(value, index) {
                return [value];
            });
            catop+='<option selected value="select" disabled>select category</option>';
            /*for(var i=0;i<data.length;i++){
            catop+='<option value="'+data[i].id+'">'+data[i].category.toUpperCase()+'</option>';
            }*/
            category.forEach(value => {
                catop+='<option value="'+value.id+'">'+value.category.toUpperCase()+'</option>';
            });
            $("#category1").find('option').remove().end().append(catop);
            $('#sendrequestModal').modal({backdrop: 'static', keyboard: false});
            $('#loading').hide()
        },
    });
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#qty'+rowcount).val() != 0){
            if($('#item'+rowcount).val()){
                y++;
                add++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black;" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" style="color: black;" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" style="color: black;" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-1 form-group"><input type="number" min="0" class="form-control" style="color: black; width: 6em" name="qty'+y+'" id="qty'+y+'" placeholder="0" disabled></div><div class="col-md-2 form-group text-center"><input type="text" class="form-control" name="uom'+y+'" id="uom'+y+'" style="color:black;"readonly></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
                $(this).val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#item'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#qty'+ rowcount).prop('disabled', true);
                $('#reqfield').append(additem);
                $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            }else{
                alert("Please Select Item!");
            }
        }else{
            alert("Invalid Quantity value!");
        }
    }else{
        add--;
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
    }
});

$(document).on('click', '.send_sub_Btn', function(){
    if (add == 0 || sub > 0) {
        alert('Please add item/s.');
        return false;
    }
    var item = "";
    var qty = "";
    var stat = "notok";
    var reqno = $('#sreqno').val();
    $('#sendrequestModal').modal('toggle');
    $('#loading').show();
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                sub++;
                cat = $('#category'+q).val();
                item = $('#item'+q).val();
                desc = $('#desc'+q).val();
                qty = $('#qty'+q).val();
                $.ajax({
                    url: 'storerequest',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        reqno : reqno,
                        item: item,
                        qty: qty,
                        stat: stat                           
                    },
                });
            }
        }
        if (q == y) {
            stat = "ok";
            $.ajax({
                url: 'storerequest',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                type: 'POST',
                data: {
                    reqno : reqno,  
                    stat: stat                     
                },
                success: function(){
                    window.location.href = 'request';
                },
                error: function (data) {
                    alert(data.responseText);
                }
            });
        }
    }
});

$(document).on('change', '.desc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#item' + count).val(id);
    $('#qty'+count).val('0');
    $('#qty'+count).prop('disabled', false);
    $.ajax({
        type:'get',
        url:'uom',
        data:{
            id: id
        },
        success:function(data)
        {
            console.log(data);
            $('#uom'+count).val(data);
        },
    });
});

$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#desc' + count).val(id);
    $('#qty'+count).prop('disabled', false);
    $('#qty'+count).val('0');
    $.ajax({
        type:'get',
        url:'uom',
        data:{
            id: id
        },
        success:function(data)
        {
            console.log(data);
            $('#uom'+count).val(data);
        },
    });
});

$(document).on('change', '.category', function(){
    var codeOp = " ";
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#stock' + count).val('Stock');
    $.ajax({
        type:'get',
        url:'getcode',
        data:{'id':id},
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            codeOp+='<option selected value="select" disabled>select item code</option>';
            descOp+='<option selected value="select" disabled>select description</option>';
            itemcode.forEach(value => {
                codeOp+='<option value="'+value.id+'">'+value.id+'</option>';
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#item" + count).find('option').remove().end().append(codeOp);
            $("#desc" + count).find('option').remove().end().append(descOp);
        },
    });
    
    $('#item' + count).val('select item code');
    $('#desc' + count).val('select description');
    $('#qty'+count).val('0');
});

$(document).on('click', '.close', function(){
    window.location.href = 'request';
});

$(document).on('click', '.cancel', function(){
    window.location.href = 'request';
});
