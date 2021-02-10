var r = 1;
var y = 1;
var c = 1;
var w = 0;
var uomcount = 0;
var uomarray = new Array();
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
                    { data: 'qty', name:'qty'},
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
            var pendreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (pendreq <= 10) {
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (pendreq > 10) {
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        pendreq = data.data.length;
                    },
                });
            }
        }else if (trdata.status == 'PARTIAL') {
            $('#printBtn').hide();
            $('table.schedDetails').hide();
            $('#unresolveBtn').hide();
            $('table.requestDetails').show();
            var partreq;
            Promise.all([partialrequest()]).then(() => { 
                if (partreq <= 10) {
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (partreq > 10) {
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        partreq = data.data.length;
                        console.log(partreq);
                    },
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var schedreq;
            Promise.all([schedrequest()]).then(() => {
                if (schedreq <= 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (schedreq > 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function schedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var resched;
            Promise.all([reschedrequest()]).then(() => {
                if (resched <= 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (resched > 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function reschedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        resched = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('#printBtn').show();
            $('#printBtn').val("RESOLVE");
            $('#unresolveBtn').show();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            var incomp;
            Promise.all([incompleterequest()]).then(() => {
                if (incomp <= 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (incomp > 10) {
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
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function incompleterequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        incomp = data.data.length;
                    },
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
            { data: 'qty', name:'qty'},
            { data: 'stockuom', name:'stockuom'}
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
        if (rowselected[i].uom == 'Unit') {
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
        }else{
            if (rowselected[i].quantity <= rowselected[i].stock) {
                var maxqty = rowselected[i].quantity;
            }else{
                var maxqty = rowselected[i].stock;
            }
            w++;
            uomcount = w;
            uomarray.push(w);
            var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected disabled>select item code</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control inputqty" row_count="'+w+'" id="inputqty'+w+'" maxlength="2" min="0" max="'+maxqty+'" value="0" style="color:black" onkeypress="return event.charCode >= 48 && event.charCode <= 57" onkeyup=imposeMinMax(this)></div><div class="col-md-2 form-group"><input type="text" class="form-control uom" row_count="'+w+'" id="uom'+w+'" value="'+rowselected[i].uom+'" style="color:black" autocomplete="off" disabled></div></div>'
            $('#reqfield').append(additem);
            $('#item'+w).val(rowselected[i].items_id);
            $('#desc'+w).val(rowselected[i].items_id);
        }
    }
    $('#loading').hide();
    console.log('my'+w);
    $('#sendModal').modal('show');

});


