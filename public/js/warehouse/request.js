var r = 1;
var y = 1;
var c = 1;
var w = 0;
var bID;
var sub = 0;
var save = 0;
var pcount = 0;
var requestdetails;
var stat = "notok";
var pending = 0;
var check = false;
var requestgo;

$(document).ready(function()
{
    $("#datesched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
    $("#resched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    var table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 50,
        "language": {
            "emptyTable": "No stock request found!"
        },
        "order": [[ 5, 'asc'], [ 0, 'desc']],
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'requests',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'id', name:'id'},
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'request_no', name:'request_no', "width": "14%"},
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'area', name:'area',"width": "14%"},
            { data: 'status', name:'status', "width": "14%"}
        ]
    });

    $('#requestTable tbody').on('click', 'tr', function () { 
        var trdata = table.row(this).data();
        bID = trdata.branch_id
        if (trdata.status == 'SCHEDULED') {
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if (trdata.status == 'RESCHEDULED') {
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if(trdata.status == 'PENDING'){
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#save_Btn').show();
        }else if(trdata.status == 'UNRESOLVED'){
            $('#printBtn').show();
            $('#printBtn').val('PRINT');
            $('table.requestDetails').hide();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.schedDetails').show();
            $('table.schedDetails').DataTable({ 
                "dom": 'rt',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/send/"+trdata.request_no,
                columnDefs: [
                    {"className": "dt-center", "targets": "_all"}
                ],
                columns: [
                    { data: 'items_id', name:'items_id'},
                    { data: 'item_name', name:'item_name'},
                    { data: 'serial', name:'serial'}
                ]
            });
            $('#unresolveBtn').hide();
            $('#prcBtn').hide();
        }else if(trdata.status == 'PARTIAL'){
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#save_Btn').show();
        }else if(trdata.status == 'INCOMPLETE'){
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }
        $('#date').val(trdata.created_at);
        $('#reqno').val(trdata.request_no);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();

        if (trdata.status == 'PENDING') {
            $('#printBtn').hide();
            $('table.schedDetails').hide();
            $('#unresolveBtn').hide();
            $('table.requestDetails').show();
            Promise.all(pendingrequest()).then(() => { 
                if (requestdetails.data().count() <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'stock', name:'stock'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (requestdetails.data().count() > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'stock', name:'stock'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function pendingrequest() {
                return requestdetails = 
                $('table.requestDetails').DataTable({ 
                    "dom": 'lrtip',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: false,
                    ajax: "/requests/"+trdata.request_no,
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'quantity', name:'quantity'},
                        { data: 'stock', name:'stock'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }else if (trdata.status == 'PARTIAL') {
            $('#printBtn').hide();
            $('table.schedDetails').hide();
            $('#unresolveBtn').hide();
            $('table.requestDetails').show();
            Promise.all(partialrequest()).then(() => { 
                if (requestdetails.data().count() <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'stock', name:'stock'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (requestdetails.data().count() > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'stock', name:'stock'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialrequest() {
                return requestdetails = 
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
                        { data: 'quantity', name:'quantity'},
                        { data: 'stock', name:'stock'}
                    ],
                    select: {
                        style: 'multi'
                    }
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
            Promise.all(schedrequest()).then(() => {
                if (scheddetails.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (scheddetails.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function schedrequest() {
                return scheddetails =
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ]
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
            Promise.all(reschedrequest()).then(() => {
                if (scheddetails.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (scheddetails.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function reschedrequest() {
                return scheddetails =
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ]
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('#printBtn').show();
            $('#printBtn').val("RESOLVE");
            $('#unresolveBtn').show();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            Promise.all(incompleterequest()).then(() => {
                if (scheddetails.data().count() <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (scheddetails.data().count() > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function incompleterequest() {
                return scheddetails =
                $('table.schedDetails').DataTable({ 
                    "dom": 'lrtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/send/"+trdata.request_no,
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ]
                });
            }
        }
        
        $('#requestModal').modal('show');
    });
});

$('table.requestDetails').DataTable().on('select', function () {
    var rowselected = requestdetails.rows( { selected: true } ).data();
    var rowcount = requestdetails.rows( { selected: true } ).count();
    if(rowselected.length > 0){
        for(var i=0;i<rowcount;i++){
            if (rowselected[i].stock == 0) {
                $('#prcBtn').prop('disabled', true);
                requestdetails.rows( { selected: true } ).deselect();
                alert(rowselected[i].item_name+' is of stock!')
                return false;
            }else{
                $('#prcBtn').prop('disabled', false);
            }
        }  
    }
});

$('table.requestDetails').DataTable().on('deselect', function () {
    var rowselected = requestdetails.rows( { selected: true } ).data();
    if(rowselected.length == 0){
        $('#prcBtn').prop('disabled', true);
    }    
});

$(document).on('change', '#datesched', function(){
    var seldate = new Date($('#datesched').val());
    var dd = String(seldate.getDate()).padStart(2, '0');
    var mm = String(seldate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = seldate.getFullYear();
    seldate = yyyy + '-' + mm + '-' + dd;
    var today = new Date();
    var datval = moment(seldate, 'YYYY-MM-DD', true).isValid();
    seldate = seldate.split("-");
    var newdate = new Date(seldate[2], seldate[0], seldate[1]);
    if (datval) {
        if(newdate < today) {
            alert('Invalid Date!');
        }
    }else{
        alert('Invalid Date!');
    }
});

$(document).on('change', '#resched', function(){
    var seldate = new Date($('#resched').val());
    var dd = String(seldate.getDate()).padStart(2, '0');
    var mm = String(seldate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = seldate.getFullYear();
    seldate = yyyy + '-' + mm + '-' + dd;
    var today = new Date();
    var datval = moment(seldate, 'YYYY-MM-DD', true).isValid();
    seldate = seldate.split("-");
    var newdate = new Date(seldate[2], seldate[0], seldate[1]);
    if (datval) {
        if(newdate < today) {
            alert('Invalid Date!');
        }
    }else{
        alert('Invalid Date!');
    }
});

$(document).on('click', '#prcBtn', function(){
    $("#requestModal .closes").click();
    $('#loading').show();
    $('#sdate').val($('#date').val());
    $('#sreqno').val($('#reqno').val());
    $('#sbranch').val($('#branch').val());
    $('#sname').val($('#name').val());
    $('table.sendDetails').dataTable().fnDestroy();
    $('table.sendDetails').DataTable({ 
        "dom": 'rtp',
        "language": {
            "emptyTable": " "
        },
        processing: true,
        serverSide: true,
        ajax: "/requests/"+$('#sreqno').val(),
        columns: [
            { data: 'items_id', name:'items_id'},
            { data: 'item_name', name:'item_name'},
            { data: 'quantity', name:'quantity'},
            { data: 'stock', name:'stock'}
        ]
    });
    var rowselected = requestdetails.rows( { selected: true } ).data();
    var rowcount = requestdetails.rows( { selected: true } ).count();
    var requestcount = requestdetails.data().count();
    if (rowcount < requestcount) {
        requestgo = false;
    }else{
        requestgo = true;
    }
 
    for(var i=0;i<rowcount;i++){
        if (rowselected[i].quantity <= rowselected[i].stock) {
            for(var e=0;e<rowselected[i].quantity;e++){
                w++;
                var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected disabled>select item code</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode != 32"></div></div>'
                $('#reqfield').append(additem);
                $('#item'+w).val(rowselected[i].items_id);
                $('#desc'+w).val(rowselected[i].items_id);
            }
        }else if(rowselected[i].quantity > rowselected[i].stock){
            for(var e=0;e<rowselected[i].stock;e++){
                w++;
                var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected disabled>select item code</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode != 32"></div></div>'
                $('#reqfield').append(additem);
                $('#item'+w).val(rowselected[i].items_id);
                $('#desc'+w).val(rowselected[i].items_id);
            }
        }
    }
    $('#loading').hide();
    console.log('my'+w);
    $('#sendModal').modal('show');

    /*$('table.prepDetails').dataTable().fnDestroy();
    $.ajax({
        url: "/getrequests/",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'get',
        dataType: 'json',
        async:false,
        data: {
            reqno: $('#reqno').val(),
        },
        success:function(data)
        {
            if (pcount != 0) {
                for(var x=0;x<pcount;x++){
                    w++;
                    var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="category'+w+'" class="form-control category" row_count="'+w+'" style="color:black"></select></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" name="serial1" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+w+'" value="Add Item"></div></div>'
                    $('#reqfield').append(additem);
                    var catop = " ";
                    $.ajax({
                        url: 'getcatreq',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'get',
                        async: false,
                        data: {
                            reqno: $('#reqno').val(),
                        },
                        success:function(data)
                        {
                            catop+='<option selected disabled>select category</option>';
                            for(var i=0;i<data.length;i++){
                                catop+='<option value="'+data[i].id+'">'+data[i].category+'</option>';
                            }
                            $("#category"+w).find('option').remove().end().append(catop);
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }
            }else{
                for(var v=0;v<data.length;v++){
                    for(var x=0;x<data[v].quantity;x++){
                        w++;
                        var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="category'+w+'" class="form-control category" row_count="'+w+'" style="color:black"></select></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" name="serial'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+w+'" value="Add Item"></div></div>'
                        $('#reqfield').append(additem);
                        var catop = " ";
                        $.ajax({
                            url: 'getcatreq',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            type: 'get',
                            async: false,
                            data: {
                                reqno: $('#reqno').val(),
                            },
                            success:function(data)
                            {
                                catop+='<option selected disabled>select category</option>';
                                for(var i=0;i<data.length;i++){
                                    catop+='<option value="'+data[i].id+'">'+data[i].category+'</option>';
                                }
                                $("#category"+w).find('option').remove().end().append(catop);
                            },
                            error: function (data) {
                                alert(data.responseText);
                            }
                        });
                    }
                }
            }
        },
        error: function (data) {
            alert(data.responseText);
        }
    });*/
});


$(document).on('click', '.sub_Btn', function(){
    if ($('#datesched').val()) {
        $('#sendModal').toggle();
        $('#loading').show();
        for(var q=1;q<=w;q++){
            if ($('#serial'+q).val()) {
                $.ajax({
                    url: 'update',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        item: $('#item'+q).val(),
                        serial: $('#serial'+q).val(),
                        reqno: $('#sreqno').val(),
                        branchid: bID,
                        datesched: $('#datesched').val(),
                        stat: "notok"
                    },
                    error: function (data) {
                        alert(data.responseText);
                        return false;
                    }
                });
                $.ajax({
                    url: 'update/'+$('#sreqno').val(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        item: $('#item'+q).val(),
                    },
                    error: function (data) {
                        alert(data.responseText);
                        return false;
                    }
                });
            }
        }

        if (pending != 0) {
            var status = '8';
        }else{
            if (requestgo == true) {
                var status = '1';
            }else{
                var status = '8';
            }
        }

        $.ajax({
            url: 'update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'PUT',
            data: { 
                reqno: $('#sreqno').val(),
                datesched: $('#datesched').val(),
                stat: "ok",
                branchid: bID,
                status: status
            },
            dataType: 'json',
            success:function()
            {
                console.log('success');
                return window.location.href = '/print/'+$('#sreqno').val();
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }else{
        alert("Please select schedule date!");
    }

    
    //var self = $(clicked_element);
    /*for(var i = 0; i <= 10; i++)
    {
        var random_string = generateRandomString(4);
        console.log(random_string);
    }
    function generateRandomString(string_length)
    {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var string = 'HPH1005';
        

        for(var i = 0; i <= string_length; i++)
        {
            var rand = Math.round(Math.random() * (characters.length - 1));
            var character = characters.substr(rand, 1);
            string = string+character;
        }

        return string;
    }*/
});

$(document).on('keyup', '.serial', function () {
    pending = 0;
    for(q=1;q<=w;q++){
        if (!$('#serial'+q).val()) {
            pending++;
            $('#sub_Btn').prop('disabled', true);
            check = false;
            if (pending != w) {
                check = true;
                $('#sub_Btn').prop('disabled', false);
            }
        }
        if (w == 1 && !$('#serial'+q).val()) {
            $('#sub_Btn').prop('disabled', true);
        }else if (w == 1 && $('#serial'+q).val()){
            $('#sub_Btn').prop('disabled', false);
        }
    }
});

$(document).on('click', '#save_Btn', function(){
    if (c == 1) {
        alert('Add Item/s');
        return false;
    }
    if (save > 0) {
        return false;
    }
    var item = "";
    var reqno = $('#sreqno').val();
    var check = 1;
    var q;
    for(q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            save++;
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                cat = $('#category'+q).val();
                item = $('#item'+q).val();
                desc = $('#desc'+q).val();
                serial = $('#serial'+q).val();
                branchid = bID;
                $.ajax({
                    url: 'update',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    async: false,
                    data: {
                        item: item,
                        serial: serial,
                        reqno: reqno,
                        branchid: branchid
                    },
                    success:function()
                    {
                    },
                    error: function (data) {
                        alert(data.responseText);
                        return false;
                    }
                });
            }
        }
        if (q == y) {
            if (check > 1) {
                window.location.href = '/request';
            }
        }
    }
    
});


$(document).on('change', '.desc', function(){
    
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#item' + count).val(id);
});

$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#desc' + count).val(id);
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

$(document).on('click', '.cancel', function(){
    window.location.href = 'request';
});

$(document).on('click', '#printBtn', function(){
    if ($('#printBtn').val() == "PRINT") {
        window.location.href = '/print/'+$('#reqno').val();
    }else if($('#printBtn').val() == "RESOLVE"){
        $('#reschedModal').modal('show');
    }
});

$(document).on('click', '#unresolveBtn', function(){
    var status = "6";
    var reqno = $('#reqno').val();
    var stat = "resched";
    $.ajax({
        url: 'update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'PUT',
        data: { 
            reqno: reqno,
            stat: stat,
            status: status
        },
        dataType: 'json',
        success:function()
        {
            window.location.href = '/print/'+$('#reqno').val();
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '#resched_btn', function(){
    var datesched = $('#resched').val();
    var reqno = $('#reqno').val();
    var stat = "resched";
    var status = "5";
    $.ajax({
        url: 'update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'PUT',
        data: { 
            reqno: reqno,
            datesched: datesched,
            stat: stat,
            status: status
        },
        dataType: 'json',
        success:function()
        {
            window.location.href = '/print/'+$('#reqno').val();
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});