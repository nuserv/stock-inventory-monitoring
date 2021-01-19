var r = 1;
var y = 1;
var c = 1;
var bID;
var sub = 0;
var save = 0;
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
            "emptyTable": " "
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
                    { data: 'purpose', name:'purpose'},
                    { data: 'stock', name:'stock'}
                ]
            });
        }else if(trdata.status == 'SCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
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
        }else if(trdata.status == 'RESCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            $('#unresolveBtn').hide();
            $('table.schedDetails').show();
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
        }else if(trdata.status == 'INCOMPLETE'){
            $('#printBtn').show();
            $('#printBtn').val("RESOLVE");
            $('#unresolveBtn').show();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
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
        }
        
        $('#requestModal').modal('show');
    });
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
    var reqno = $('#reqno').val();
    $("#requestModal .closes").click();
    $('#sdate').val($('#date').val());
    $('#sreqno').val($('#reqno').val());
    $('#sbranch').val($('#branch').val());
    $('#sname').val($('#name').val());
    $('#sendModal').modal('show');
    var catop = " ";
    for(var i=1;i<=y;i++){
        if (i != 1) {
            $('#row'+i).hide();
        }
        $('#stock'+i).css("border", "");
        $('#qty'+i).css('border', '');
        $('#category'+i).val('select category');
        $('#item'+i).val('select item code');
        $('#desc'+i).val('select description');
        $('#qty'+i).val('Qty');
        $('#stock'+i).val('');
    }
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
            { data: 'purpose', name:'purpose'},
            { data: 'stock', name:'stock'}
        ]
    });
    $('table.prepDetails').dataTable().fnDestroy();
    $.ajax({
        url: 'prepitem',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'get',
        async:false,
        data: {
            reqno: reqno,
            branchid: bID
        },
        success:function(data)
        {
            if (data == 1) {
                $('#prepitem').show();
                $('#preptable').show();
                $('table.prepDetails').DataTable({ 
                    "dom": 'rtp',
                    "language": {
                        "emptyTable": " "
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "/prep/"+$('#reqno').val(),
                    columns: [
                        { data: 'items_id', name:'items_id'},
                        { data: 'item_name', name:'item_name'},
                        { data: 'serial', name:'serial'}
                    ]
                });

            }else{
                $('#preptable').hide();
                $('#prepitem').hide();
            }
        },
        error: function (data) {
            alert(data.responseText);
        }
    });

    $.ajax({
        url: 'getcatreq',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'get',
        async:false,
        data: {
            reqno: reqno,
        },
        success:function(data)
        {
            catop+='<option selected value="select" disabled>select category</option>';
            for(var i=0;i<data.length;i++){
                catop+='<option value="'+data[i].id+'">'+data[i].category+'</option>';
            }
            $("#category1").find('option').remove().end().append(catop);
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        var x = 0;
        c++;
        if($('#category'+ rowcount).val()) {
            if($('#desc'+ rowcount).val()) {
                if ($('#serial'+ rowcount).val()) {
                    var id = $('#item'+ rowcount).val();
                    $.ajax({
                        type:'get',
                        url:'getstock',
                        async: false,
                        data:{'id':id},
                        success:function(data)
                        {
                            if (data != "") {
                                var curstock = data[0].stock;
                                for(var i=1;i<=y;i++){
                                    if (i != rowcount) {
                                        if ($('#item'+i).val() == $('#item'+ rowcount).val()) {
                                            if ($('#serial'+i).val() == $('#serial'+ rowcount).val()) {
                                                $('#serial' + rowcount).css('color', 'red');
                                                $('#serial' + rowcount).css("border", "5px solid red");
                                                x++;
                                            }else{
                                                $('#serial' + rowcount).css('color', 'black');
                                                $('#serial' + rowcount).css("border", "");
                                            }
                                            if ($('#item'+i).prop('disabled')) {
                                                $('#stock'+i).val(curstock);
                                                curstock--;
                                            }
                                            
                                        }
                                    }
                                }
                                if (i == y) {
                                    $('#stock'+rowcount).val(curstock);
                                    if (curstock <= 0) {
                                        $('#stock' + rowcount).css('color', 'red');
                                        $('#stock' + rowcount).css("border", "5px solid red");
                                        x++;
                                    }
                                    $('#stock' + rowcount).css('color', 'black');
                                    $('#stock' + rowcount).css("border", "");
                                }
                            }
                        },
                    });
                }else{
                    x++;
                    alert('Input Serial number!');
                    return false; 
                }
            }else{
                x++;
                alert('Select Item!');
                return false; 
            }
        }else{
            x++;
            alert('Select Category!');
            return false; 
        }
        if (x == 0) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+y+'" name="serial1" id="serial'+y+'" placeholder="input serial"></div><div class="col-md-2 form-group"><input type="number" class="form-control" name="stock'+y+'" id="stock'+y+'" placeholder="0" style="width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
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
        }else{
            return false;
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+y+'" name="serial1" id="serial'+y+'" placeholder="input serial"></div><div class="col-md-2 form-group"><input type="number" class="form-control" name="stock'+y+'" id="stock'+y+'" placeholder="0" style="width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            r++;
        }
        c--;
        var id = $('#item'+rowcount).val();
        var istock = 0;
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
        $.ajax({
            type:'get',
            url:'getstock',
            data:{'id':id},
            async: false,
            success:function(data)
            {
                if (data != "") {
                    for(var i=1;i<=y;i++){
                        if (i != rowcount) {
                            if ($('#item'+i).val() == id) {
                                $('#stock'+i).val(data[0].stock - istock);
                                istock++;
                            }
                        }
                    }
                }
                
            },
        });
    }
});


$(document).on('click', '.sub_Btn', function(){
    var item = "";
    var stat = "notok";
    var reqno = $('#sreqno').val();
    var check = 1;
    branchid = bID;
    datesched = $('#datesched').val();
    if ($('#datesched').val()) {
        if (sub > 0) {
            return false;
        }
        for(var q=1;q<=y;q++){
            if ($('#row'+q).is(":visible")) {
                check++;
                sub++;
                if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                    check++;
                    cat = $('#category'+q).val();
                    item = $('#item'+q).val();
                    desc = $('#desc'+q).val();
                    serial = $('#serial'+q).val();
                    $.ajax({
                        url: 'update',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'PUT',
                        data: {
                            item: item,
                            serial: serial,
                            reqno: reqno,
                            branchid: branchid
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
                    var stat = "ok";
                    var status = "1";
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
                            branchid: branchid,
                            status: status
                        },
                        dataType: 'json',
                        success:function()
                        {
                            window.location.href = '/print/'+reqno;
                        },
                        error: function (data) {
                            alert(data.responseText);
                            return false;
                        }
                    });
                }
            }
        }
    }else{
        alert("Please select schedule date!");
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
    var stockCount = 0;
    var serialOp = " ";
    $('#item' + count).val(id);
    for(var i=1;i<=y;i++){
        if (i != count ) {
            if ($('#desc'+i).val() == $(this).val()) {
                stockCount++;
            }
        }
    }
    $.ajax({
        type:'get',
        url:'getstock',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            if (data != "") {
                $('#stock' + count).val(data[0].stock - stockCount);
                $('#stock' + count).css('color', 'black');
                $('#stock' + count).css("border", "");
                if ($('#stock' + count).val() <= 0) {
                    $('#stock' + count).css('color', 'red');
                    $('#stock' + count).css("border", "5px solid red");
                }
            }else{
                $('#stock' + count).val('0');
                $('#stock' + count).css('color', 'red');
                $('#stock' + count).css("border", "5px solid red");
            }
        },
    });

    $.ajax({
        type:'get',
        url:'getserials',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            serialOp+='<option selected value="select" disabled>select serial</option>';
            for(var i=0;i<data.length;i++){
                serialOp+='<option value="'+data[i].serial+'">'+data[i].serial+'</option>';
            }
            $("#serial" + count).find('option').remove().end().append(serialOp);
        },
    });
    for(var i=1;i<=y;i++){
        if ($('#desc'+i).val() == $(this).val()) {
            rmserial = $('#serial'+i).val();
            $("#serial"+count+" option[value=\'"+rmserial+"\']").remove();
        }
    }
});

$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    var stockCount = 0;
    var serialOp = " ";
    var rmserial = "";
    $('#desc' + count).val(id);
    for(var i=1;i<=y;i++){
        if (i != count ) {
            if ($('#item'+i).val() == $(this).val()) {
                stockCount++;
            }
        }
    }

    $.ajax({
        type:'get',
        url:'getstock',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            if (data != "") {
                $('#stock' + count).val(data[0].stock - stockCount);
                $('#stock' + count).css('color', 'black');
                $('#stock' + count).css("border", "");
                if (($('#stock' + count).val() < 0) || ($('#stock' + count).val() == 0)) {
                    $('#stock' + count).css('color', 'red');
                    $('#stock' + count).css("border", "5px solid red");
                }
            }else{
                $('#stock' + count).val('0');
                $('#stock' + count).css('color', 'red');
                $('#stock' + count).css("border", "5px solid red");
            }
            
        },
    });

    $.ajax({
        type:'get',
        url:'getserials',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            serialOp+='<option selected value="select" disabled>select serial</option>';
            for(var i=0;i<data.length;i++){
                serialOp+='<option value="'+data[i].serial+'">'+data[i].serial+'</option>';
            }
            $("#serial" + count).find('option').remove().end().append(serialOp);
        },
    });

    for(var i=1;i<=y;i++){
        if ($('#item'+i).val() == $(this).val()) {
            rmserial = $('#serial'+i).val();
            $("#serial"+count+" option[value=\'"+rmserial+"\']").remove();
        }
    }
});

$(document).on('change', '.category', function(){
    var codeOp = " ";
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#stock' + count).val('0');
    
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            codeOp+='<option selected value="select" disabled>select item code</option>';
            descOp+='<option selected value="select" disabled>select description</option>';
            for(var i=0;i<data.length;i++){
                codeOp+='<option value="'+data[i].id+'">'+data[i].id+'</option>';
                descOp+='<option value="'+data[i].id+'">'+data[i].item.toUpperCase()+'</option>';
            }
            $("#item" + count).find('option').remove().end().append(codeOp);
            $("#desc" + count).find('option').remove().end().append(descOp);
        },
    });
    $('#item' + count).val('select itemcode');
    $('#desc' + count).val('select description');
    $('#stock' + count).css("border", "");
    $('#item' + count).css("border", "");
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