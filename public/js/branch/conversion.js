$(document).on('click', '#addBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});
});
$(document).on("click", "#drTable tr", function () {
    trdata = table.row(this).data();
    $('#pulloutdate').val(trdata.pullout_date);
    $('#pulloutby').val(trdata.branch);
    $('#client').val(trdata.customer_branch);
    $('#refno').val(trdata.drno.toUpperCase());
    $('#returnModal').modal({backdrop: 'static', keyboard: false});
    conversion =
        $('table.converitonitems').DataTable({ 
            "dom": 'Blrtip',
            processing: true,
            serverSide: false,
            destroy: true,
            "language": {
                "emptyTable": "No item/s for return found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "pageLength": 25,
            ajax: {
                url: '/convertion-table',
                async: false,
                data:{
                    drno:trdata.drno
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
                { data: 'serial', name:'serial'}
            ],
            buttons: {
                    dom: {
                        button: {
                            className: 'btn btn-primary' //Primary class for all buttons
                        }
                    },
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-primary',
                            titleAttr: 'Submit and print preview',
                            enabled: true,
                            autoPrint: false,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> SUBMIT</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                /*$(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    .prepend('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$('#userlog').val()+'</div>')
                                    .prepend('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;">Received By: _____________________</div>')
                                    .prepend('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;">Received Date: _____________________</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                $(doc.document.body).find('table')            			
                                        .removeClass('dataTable')
                                .css('font-size','12px') 
                                        .css('margin-top','85px')
                                .css('margin-bottom','120px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','14px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });*/
                                $(doc.document.body)
                                .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                .prepend('<div style="position:absolute; top:90; width:100%;left:30%;font-size:28px;font-weight: bold">CONVERSION DELIVERY RECEIPT</div>')
                                //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Prepared by:</b> '+$('#userlog').val()+'</div>')
                                //.prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#areaname').val()+'</div>')
                                .prepend('<div style="position:absolute; top:140;left:50%;font-size:24px"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                //.prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch address:&nbsp;&nbsp;</b></label><textarea id="textbranch" style="vertical-align: top;resize: none;background: transparent;border:0 none" rows="3" cols="80" readonly>'+$('#addr').val()+'</textarea></div>')
                                .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Branch: </b> '+trdata.branch+'</div>')
                                .prepend('<div style="position:absolute; top:170;left:50%;font-size:24px"><b>Client name: </b> '+trdata.customer_branch+'</div>')
                                .prepend('<div style="position:absolute; top:200;font-family: arial;font-size:24px"><b>DR Reference no.: </b>'+trdata.drno+'</div>')
                                .prepend('<div style="position:absolute; top:200;left:50%;font-size:24px"><b>Date Pullout: </b> '+trdata.pullout_date+'</div>')
                                .prepend('<div style="position:absolute; top:230;font-family: arial;font-size:24px"><b>Received By:</b> _____________________</div>')
                                .prepend('<div style="position:absolute; top:260;font-family: arial;font-size:24px"><b>Received Date:</b> _____________________</div>')
                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                            //jsDate.toString()
                                // $(doc.document.body)
                                    //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                    //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    // .append('<div style="position:absolute; bottom:40; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    // .append('<div style="position:absolute; bottom:10; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                                    .css('font-size','24px') 
                                    .css('margin-top','310px')
                                    .css('margin-bottom','310px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','26px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });   
                            },
                            title:'',
                            exportOptions: {
                                rows: function (idx) {
                                    var dt = new $.fn.dataTable.Api('#defectiveTable' );
                                    var selected = dt.rows( { selected: true } ).indexes().toArray();
                                
                                    if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                                    return true;
                                    return false;
                                }
                                // columns: [ 1, 2 ]
                            },
                            init: function(node) {$(node).removeClass('dt-button')},
                        }
                    ]
                }
        });
    conversion.buttons().container().appendTo('.convertionprintBtn');
});

$(document).on('click', '.convertionprintBtn', function () {
    // var data = table.rows( { selected: true } ).data()
    var id = new Array();
    conversion.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
        id.push(this.data().id);
    });
    $('#loading').show();
    $.ajax({
        url: 'return-update',
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: id,
            ret: retno
        },
        success:function()
        {
            $('#loading').show();
            setTimeout(function() { window.location.href = 'return';}, 1000);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    }); 
});

$(document).on('click', '.sub_Btn', function(){
    $('#loading').show();
    if ($('#branchlist').is(":visible")) {
        alert('Invalid client name!');
        $('#loading').hide();
        return false;
    }
    if (branch != "ok") {
        alert('Invalid branch name!');
        $('#loading').hide();
        return false;
    }
    if ($('#drno').val() == "") {
        alert('Invalid DR reference no.!');
        $('#loading').hide();
        return false;
    }
    if ($('#datepullout').val() == "") {
        alert('Invalid pullout date!');
        $('#loading').hide();
        return false;
    }
    var cat = "";
    var item = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                $('.sub_Btn').prop('disabled', true)
                cat = $('#category'+q).val();
                item = $('#desc'+q).val();
                serial = $('#serial'+q).val();
                drno = $('#drno').val();
                cname = $('#incustomer').val();
                branch_id = $('#branch').val();
                $.ajax({
                    url: 'conversion',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        category: cat,
                        serial: serial,
                        item: item,
                        details: 'items',
                        drno: drno,
                        cname: cname,
                        branch_id: branch_id,
                        pulldate: $('#datepullout').val()
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            }
        }
    }
    
    if (check > 1) {
        location.reload();
    }
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if ($('#serial'+ rowcount).val()) {
            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-3 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-4 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text"id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#serial'+ rowcount).prop('disabled', true);
                if (r < 30 ) {
                    $('#reqfield').append(additem);
                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                    $('#itemdiv'+ y).hide();
                    r++;
                }
            }
            if (r > 1) {
                $('#sub_Btn').prop('disabled', false);
            }
        }
    }else{
        if (r == 30) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-3 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-4 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text"id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            // var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-3 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-4 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="number" min="0" id="qty'+y+'" class="form-control qty" row_count="'+y+'" placeholder="qty" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            // var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            $('#itemdiv'+ y).hide();
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#desc'+rowcount).val('select item description');
        $('#category'+rowcount).prop('disabled', false);
        $('#desc'+rowcount).prop('disabled', false);
        $('#serial'+rowcount).prop('disabled', false);
        $('#row'+rowcount).hide();
        $(this).val('Add Item');
        r--;
        if (r > 1) {
            $('#sub_Btn').prop('disabled', false);
        }
    }
    
});

$(document).on('click', '.addpos', function(){
    var rowcount = $(this).attr('pos_count');
    if ($(this).val() == 'Add POS') {
        if ($('#posserial'+ rowcount).val().toLowerCase() != 'n/a') {
            if($('#posserial'+ rowcount).val()) {
                posy++;
                var additem = '<div class="row no-margin" id="posrow'+posy+'"><div class="col-md-3 form-group"><select style="color: black" class="form-control form-control-sm " id="possel'+posy+'"><option selected disabled>Select POS Model</option><option value="4800-722">4800-722</option><option value="4800-723">4800-723</option><option value="4800-743">4800-743</option><option value="4900-745">4900-745</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control form-control-sm serial" id="posserial'+posy+'" placeholder="Serial number" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="btn btn-xs btn-primary form-control-sm addpos" pos_count="'+posy+'" value="Add POS"></div></div>';
                $('.addpos[pos_count=\''+rowcount+'\']').val('Remove');
                $('#possel'+ rowcount).prop('disabled', true);
                $('#posserial'+ rowcount).prop('disabled', true);
                if (posr < 12 ) {
                    $('#posreqfield').append(additem);
                    posr++;
                }
                if (posr > 1) {
                    $('#sub_Btn').prop('disabled', false);
                }
            }
        }
    }else{
        if (posr == 12) {
            posy++;
            var additem = '<div class="row no-margin" id="posrow'+posy+'"><div class="col-md-3 form-group"><select style="color: black" class="form-control form-control-sm " id="possel'+posy+'"><option selected disabled>Select POS Model</option><option value="4800-722">4800-722</option><option value="4800-723">4800-723</option><option value="4800-743">4800-743</option><option value="4900-745">4900-745</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control form-control-sm serial" id="posserial'+posy+'" placeholder="Serial number" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="btn btn-xs btn-primary form-control-sm addpos" pos_count="'+posy+'" value="Add POS"></div></div>';
            $('#posreqfield').append(additem);
            posr++;
        }
        $('#posrow'+rowcount).hide();
        posr--;
        if (posr > 1) {
            $('#sub_Btn').prop('disabled', false);
        }
    }
});

$(document).on('change', '.category', function(){
    var codeOp = " ";
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#serial' + count).prop('disabled', true);
    $('#serial' + count).val('');
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
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
            $("#desc" + count).find('option').remove().end().append(descOp);
        },
    });
});

$(document).on('change', '#branch', function(){
    branch = 'ok';
});

$(document).on('change', '.desc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#serial' + count).prop('disabled', false);
    $('#serial' + count).val('');
});
$('input[type="text"]').keyup(function() {
    $(this).val().replace('-','');
});
function checkserial(ex) {
    //var mycount = document.getElementById(ex.id).row_count.value;
    var myval = ex.id;
    var slicena = myval.slice(6)
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
$(document).on('keyup', '#incustomer', function(){ 
    var withclient = 'yes';
    var clientname = 'MERCURY DRUG';
    var query = $(this).val();
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"pulloutclient",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
                });
                $('#branchlist').fadeIn();  
                $('#branchlist').html(ul);
                // $('#out_sub_Btn').prop('disabled', true);
            }
        });
    }
});

$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    $('#incustomer').val($(this).text());  
    $('#branchlist').fadeOut();  
});