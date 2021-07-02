var replaceTable;
var repdata;
var outsub = 0;
var r = 1;
var y = 1;
var clientselected = 'yes';
var billid;
var stocksid;

$(document).ready(function()
{
    sunit = $('table.billableTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No data found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
        },
        processing: true,
        serverSide: true,
        ajax: 'bill',
        columns: [
            { data: 'date', name:'date'},
            { data: 'client', name:'client'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ]
    });
});

$(document).on('click', '#reqBtn', function(){
    $('#service-unitModal').modal({backdrop: 'static', keyboard: false}); 
});

$(document).on('click', '#delBtn', function(e){
    if(confirm('Are you sure you want to delete this request?')) {
        e.preventDefault();
        $.ajax({
            type:'put',
            url:'delbill',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                billid:billid,
                stocksid:stocksid
            },
            success:function()
            {
                location.reload();
            },
        });
    }
});

$(document).on('click', '#doneBtn', function(e){
    $.ajax({
        type:'put',
        url:'prcbill',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            billid:billid,
            stocksid:stocksid,
            status: 'Completed'
        },
        success:function()
        {   
            location.reload();
        },
    });
});
$(document).on('click', '.prcBtn', function(e){
    $.ajax({
        type:'put',
        url:'prcbill',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            billid:billid,
            stocksid:stocksid,
            status: 'Pending'
        },
        success:function()
        {   
            setTimeout(function () {    
                location.reload();
            }, 1000)
        },
    });
});

$(document).on('click', '#approveBtn', function(e){
    if(confirm('Are you sure you want to approve this request?')) {
        e.preventDefault();
        $.ajax({
            type:'put',
            url:'approvebill',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                billid:billid,
                stocksid:stocksid
            },
            success:function()
            {
                location.reload();
            },
        });
    }
});

$(document).on("click", "#billableTable tr", function () {
    trdata = sunit.row(this).data();
    if (trdata.user_id != $('#userid').val()) {
        if ($('#userlevel').val() != 'Warehouse Manager') {
            if ($('#userlevel').val() != 'Head') {
                return false;
            }
        }
    }
    billid = trdata.id;
    stocksid = trdata.stocks_id;
    $('#service-inModal').modal({backdrop: 'static', keyboard: false}); 
    $('#inclient').val(trdata.client_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#incustomer').val(trdata.customer_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#outitem').val(trdata.description.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#outserial').val(trdata.serial.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#indate').val(trdata.date);
    $('#outstatus').val(trdata.status);
    if ($('#userlevel').val() == 'Warehouse Manager') {
        if (trdata.status == "Approved") {
            $('#approveBtn').remove();
        }
    }else{
        if (trdata.status == "Approved") {
            $('#delBtn').remove();
            $('#doneBtn').remove();
            $('#printBtn').remove();
            $('.prcBtn').show();
            var data = [
                [
                    trdata.description,
                    trdata.serial
                ]
            ]
            table = $('table.billitemTable').DataTable({ 
                "dom": 'Brtip',
                serverSide: false,
                destroy: true,
                data:data,
                buttons: {
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-primary btn-icon-split',
                            titleAttr: 'PRINT',
                            enabled: false,
                            autoPrint: true,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> Proceed</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                
                                $(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>DELIVERY RECEIPT<b></b></div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Date:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-size:24px"><b>Client Name:</b> '+$('#incustomer').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><label for="textbranch"><b>Service By: '+$('#branchname').val()+' - '+$('#userlog').val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Prepared By: '+$("#userlog").val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                // $(doc.document.body)
                                //     //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                //     //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Date: _____________________</div>')
                                $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                                    .css('font-size','24px') 
                                    .css('margin-top','270px')
                                    .css('margin-bottom','250px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','26px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });                
                            },
                            title:'',
                            exportOptions: {
                                rows: function ( idx, data, node ) {
                                    var dt = new $.fn.dataTable.Api('#billitemTable');
                                    var selected = dt.rows( { selected: true } ).indexes().toArray();
                                
                                    if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                                    return true;

                                    return false;
                                }
                            },
                            init: function(api, node, config) {$(node).removeClass('dt-button')}    
                        }
                    ]
                }
            });
            table.buttons().container().appendTo('.prcBtn');
            table.button( 0 ).enable( true );
        }
        if (trdata.status == "Pending") {
            $('#delBtn').remove();
            $('.prcbtn').remove();
            var data = [
                [
                    trdata.description,
                    trdata.serial
                ]
            ]
            table = $('table.billitemTable').DataTable({ 
                "dom": 'Brtip',
                serverSide: false,
                destroy: true,
                data:data,
                buttons: {
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-primary btn-icon-split',
                            titleAttr: 'PRINT',
                            enabled: false,
                            autoPrint: true,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> Reprint</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                
                                $(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>DELIVERY RECEIPT<b></b></div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Date:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-size:24px"><b>Client Name:</b> '+$('#incustomer').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><label for="textbranch"><b>Service By: '+$('#branchname').val()+' - '+$('#userlog').val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Prepared By: '+$("#userlog").val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                // $(doc.document.body)
                                //     //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                //     //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Date: _____________________</div>')
                                $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                                    .css('font-size','24px') 
                                    .css('margin-top','270px')
                                    .css('margin-bottom','250px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','26px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });                
                            },
                            title:'',
                            exportOptions: {
                                rows: function ( idx, data, node ) {
                                    var dt = new $.fn.dataTable.Api('#billitemTable');
                                    var selected = dt.rows( { selected: true } ).indexes().toArray();
                                
                                    if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                                    return true;

                                    return false;
                                }
                            },
                            init: function(api, node, config) {$(node).removeClass('dt-button')}    
                        }
                    ]
                }
            });
            table.buttons().container().appendTo('.printBtn');
            table.button( 0 ).enable( true );
        }
        if (trdata.status == "For approval") {
            $('.prcBtn').remove();
            $('#doneBtn').remove();
            $('#printBtn').remove();
        }
    }
});
$(document).on('click', '#clientdiv', function () {
   $('#client').prop('disabled', false);
   if ($('#client').is(':disabled')) { 
        clientselected = 'no';
   }
});

$(document).on('keyup', '#customer', function(){ 
    var withclient = 'no';
    var clientname = "";
    $('#clientlist').fadeOut();  
    if ($('#client').is(':enabled')) {
        if ($('#client').val()) {
            withclient = 'yes';
            clientname = $('#client').val();
            if (clientselected != "yes") {
                alert("Incorrect Client Name!");
            }
        }else{
            $('#client').val('');
        }
    }
    var query = $(this).val();
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
                withclient: withclient,
                clientname: clientname,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
                });
                console.log(ul);
                $('#branchlist').fadeIn();  
                $('#branchlist').html(ul);
                $('#out_sub_Btn').prop('disabled', true);
            }
        });
    }
});
$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    if (id == 'licustomer') {
        $('#customer').val($(this).text());  
        $('#branchlist').fadeOut();  
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                client:'yes',
                branch: select.trim()
            },
            success:function(data){
                if (data) {
                    $('#client').val(data);  
                    if (r == 1 || outsub > 0) {
                        $('#out_sub_Btn').prop('disabled', true);
                    }else{
                        $('#out_sub_Btn').prop('disabled', false);
                    }
                }else{
                    $('#client').val('');  
                    $('#out_sub_Btn').prop('disabled', true);
                }
            }
        });
    }else{
        clientselected = "yes";
        $('#client').val($(this).text());  
        $('#clientlist').fadeOut();
        $('#out_sub_Btn').prop('disabled', true);
    }
    
});
$(document).on('keyup', '#client', function(){ 
    var query = $(this).val();
    clientselected = 'no';
    $('#branchlist').fadeOut();  
    $('#out_sub_Btn').prop('disabled', true);
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"getclient",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="liclient">'+value.customer+'</li>';
                });
                $('#clientlist').fadeIn();  
                $('#clientlist').html(ul);
                $('#customer').val('');  
            }
        });
    }
});

$(document).on('change', '.outcategory', function(){
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            descOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#outdesc" + count).find('option').remove().end().append(descOp);
        },
    });
});

$(document).on('change', '.outitem', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();        
    $('#outdesc' + count).val(id);
});

$(document).on('change', '.outdesc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    var stockCount = 0;
    var serialOp = " ";
    
    for(var i=1;i<=y;i++){
        if (i != count ) {
            if ($('#outdesc'+i).val() == $(this).val()) {
                stockCount++;
            }
        }
    }
    Promise.all([ajaxCall1(), ajaxCall2()]).then(() => { // try removing ajax 1 or replacing with ajax2
        for(var i=1;i<=y;i++){
            if ($('#outdesc'+i).val() == $(this).val()) {
                rmserial = $('#outserial'+i).val();
                //$("#outserial"+count+" option[value=\'"+rmserial+"\']").remove();
            }
        }
    });
    
    function ajaxCall1() {
        return $.ajax({
            type:'get',
            url:'getstock',
            data:{'id':id},
            success:function(data)
            {
                if (data != "") {
                    $('#outstock' + count).val(data[0].stock - stockCount);
                    $('#outstock' + count).css('color', 'black');
                    $('#outstock' + count).css("border", "");
                    if ($('#outstock' + count).val() <= 0) {
                        $('#outstock' + count).css('color', 'red');
                        $('#outstock' + count).css("border", "5px solid red");
                    }
                }else{
                    $('#outstock' + count).val('0');
                    $('#outstock' + count).css('color', 'red');
                    $('#outstock' + count).css("border", "5px solid red");
                }
            },
        });
    }
    function ajaxCall2() {
        return $.ajax({
            type:'get',
            url:'getserials',
            data:{'id':id},
            success:function(data)
            {
                var serial = $.map(data, function(value, index) {
                    return [value];
                });
                serialOp+='<option selected disabled>select serial</option>';
                for(var i=1;i<=y;i++){
                    if ($('#outdesc'+i).val() == id) {
                        rmserial = $('#outserial'+i).val();
                        $.each(serial, function(idx, item) {
                            if (item.serial == rmserial) {
                                serial.splice(idx, 1); // Remove current item
                                return false; // End the loop
                            }
                        });
                    }
                }
                serial.forEach(value => {
                    serialOp+='<option value="'+value.serial+'">'+value.serial+'</option>';
                });
                $("#outserial" + count).find('option').remove().end().append(serialOp);
            },
        });
    }
    
});

$(document).on('click', '.out_add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#outcategory'+ rowcount).val() && $('#outdesc'+ rowcount).val() && $('#outserial'+ rowcount).val()) {
            y++;
            var additem = '<div class="row no-margin" id="outrow'+y+'"><div class="col-md-2 form-group"><select style="color:black" id="outcategory'+y+'" class="form-control outcategory" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select style="color:black" id="outdesc'+y+'" class="form-control outdesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="outserial'+y+'" class="form-control outserial" row_count="'+y+'" style="color: black;"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="number" class="form-control" min="0" name="outstock'+y+'" id="outstock'+y+'" placeholder="0" style="color:black; width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="out_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $(this).val('Remove');
            $('#outcategory'+ rowcount).prop('disabled', true);
            $('#outdesc'+ rowcount).prop('disabled', true);
            $('#outserial'+ rowcount).prop('disabled', true);
            if (r < 20 ) {
                $('#outfield').append(additem);
                $('#outcategory'+ rowcount).find('option').clone().appendTo('#outcategory'+y);
                r++;
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="outrow'+y+'"><div class="col-md-2 form-group"><select id="outcategory'+y+'" class="form-control outcategory" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="outdesc'+y+'" class="form-control outdesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="outserial'+y+'" class="form-control outserial" row_count="'+y+'" style="color: black;"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="number" class="form-control" min="0" name="outstock'+y+'" id="outstock'+y+'" placeholder="0" style="color:black; width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="out_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#outfield').append(additem);
            $('#outcategory'+ rowcount).find('option').clone().appendTo('#outcategory'+y);
            r++;
        }
        $('#outcategory'+rowcount).val('select category');
        $('#outdesc'+rowcount).val('select item description');
        $('#outserial'+rowcount).val('select serial');
        $('#outcategory'+rowcount).prop('disabled', false);
        $('#outdesc'+rowcount).prop('disabled', false);
        $('#outserial'+rowcount).prop('disabled', false);
        $('#outrow'+rowcount).hide();
        $(this).val('Add Item');
        r--;
    }
    if (r == 1) {
        $('#out_sub_Btn').prop('disabled', true);
    }else{
        if ($('#cleint').val() == '') {
            $('#out_sub_Btn').prop('disabled', true);
        }else{
            $('#out_sub_Btn').prop('disabled', false);
        }
    }
});
$(document).on('click', '.out_sub_Btn', function(){
    if ($('#client').val() == "") {
        alert('Invalid Client Name!');
        return false;
    }
    if (r == 1 || outsub > 0) {
        alert('Please add item/s.');
        return false;
    }
    var cat = "";
    var item = "";
    var check = 1;
    //if ($('#customer-id').val() != "") {
        $('#service-unitModal').modal('toggle');
        $('#loading').show();
        for(var q=1;q<=y;q++){
            if ($('#outrow'+q).is(":visible")) {
                if ($('.out_add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                    check++;
                    outsub++;
                    $('.out_sub_Btn').prop('disabled', true)
                    cat = $('#outcategory'+q).val();
                    item = $('#outdesc'+q).val();
                    serial = $('#outserial'+q).val();
                    purpose = 'billable';
                    $.ajax({
                        url:"getcustomerid",
                        type:"get",
                        async:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        data:{
                            customer:$('#customer').val(),
                        },
                        success:function(data){
                            var customer = data.id;
                            var client = data.customer_id;
                            $.ajax({
                                url: 'service-out',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                                },
                                async:false,
                                dataType: 'json',
                                type: 'PUT',
                                data: {
                                    item: item,
                                    cat : cat,
                                    purpose: purpose,
                                    serial: serial,
                                    customer: customer,
                                    client: client
                                },
                                error: function (data) {
                                    alert(data.responseText);
                                }
                            });
                        }
                    });
                    
                }
            }
            if (q==y) {
                window.location.href = 'billable';
            }
        }
    /*}else{
        alert("Invalid Customer Name!");
        return false;
    }*/
    
});
$(document).on('click', '.close', function(){
    location.reload();
});