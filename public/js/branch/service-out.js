var replaceTable;
var repdata;
var outsub = 0;
var r = 1;
var y = 1;
var clientselected = 'yes';

$(document).on('change', '.replacementdesc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    var serialOp = " ";
    $.ajax({
        type:'get',
        url:'getserials',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            var serial = $.map(data, function(value, index) {
                return [value];
            });
            serialOp+='<option selected disabled>select serial</option>';
            serial.forEach(value => {
                serialOp+='<option value="'+value.serial+'">'+value.serial+'</option>';
            });
            $("#replacementserial" + count).find('option').remove().end().append(serialOp);
        },
    });
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
    if(confirm('Please make sure you selected the CORRECT ITEM and SERIAL NUMBER.\nClick CANCEL to review your entry.\nClick OK if you are sure that your entry is correct to SUBMIT.')) {
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
                    purpose = 'service unit';
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
                            console.log(data.id);
                            console.log(data.customer_id);
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
                                    purpose: 'service unit',
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
                window.location.href = 'service-unit';
            }
        }
    }
    /*}else{
        alert("Invalid Customer Name!");
        return false;
    }*/
    
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


$(document).on('click', '.replacement_next_Btn', function(){
    if ($('#replacementcustomer-id').val()) {
        var id = $('#replacementcustomer-id').val();
        $("#replacementModal .closes").click();
        $('table.replacementDetails').dataTable().fnDestroy();
        replaceTable =
        $('table.replacementDetails').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": " "
            },
            processing: true,
            serverSide: true,
            ajax: "/pull-details/"+id,
            
            columns: [
                { data: 'date', name:'date'},
                { data: 'category', name:'category'},
                { data: 'items_id', name:'items_id'},
                { data: 'item', name:'item'},
                { data: 'serial', name:'serial'}
            ]
        });
        $('#replacementTableModal').modal({backdrop: 'static', keyboard: false});
        $('#replacecustomer').val($('#replacementcustomer').val());
        $('#replaceclient').val($('#replacementclient').val());
    }

});

$(document).on("click", "#replacementDetails tr", function () {
    var trdata = replaceTable.row(this).data();
    var catid = trdata.category_id;
    var id = trdata.id;
    repdata = trdata.id;
    var repOp = " ";
    $("#replacementTableModal .closes").click();
    $('#replaceselectcustomer').val($('#replacementcustomer').val());
    $('#replaceselectclient').val($('#replacementclient').val());
    $('table.replacement1Details').DataTable({ 
        "dom": 'rt',
        "language": {
            "emptyTable": " "
        },
        processing: true,
        serverSide: true,
        ajax: "/pull-details1/"+id,
        columnDefs: [
            {"className": "dt-center", "targets": "_all"}
        ],
        columns: [
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'items_id', name:'items_id'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'}
        ]
    });
    $('#replacementSelectModal').modal({backdrop: 'static', keyboard: false});

    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':catid},
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            repOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                repOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#repdesc1").find('option').remove().end().append(repOp);
        },
    });
});

$(document).on('change', '#repdesc1', function(){
    var id = $(this).val();
    var serialOp = " ";
    $.ajax({
        type:'get',
        url:'getserials',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            var serial = $.map(data, function(value, index) {
                return [value];
            });
            serialOp+='<option selected disabled>select serial</option>';
            serial.forEach(value => {
                serialOp+='<option value="'+value.id+'">'+value.serial+'</option>';
            });
            $("#repserial1").find('option').remove().end().append(serialOp);
        },
    });
});

$(document).on('click', '.rep_sub_Btn', function(){

    if ($('#repserial1').val()) {
        var item = $('#repserial1').val();
        var custid = $('#replacementcustomer-id').val();
        $.ajax({
            url: 'rep-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            async: false,
            data: {
                item: item,
                repdata: repdata,
                custid : custid
            },
            success:function()
            {
                location.reload();
            },
            error: function (data) {
                alert(data.responseText);
            }
        });

        

    }
});

$(document).on('keyup', '#replacementclient', function(){
    var id = $(this).val();
    var op = " ";
    $('#replacementcustomer').val('');
    $("#replacementcustomer-name").find('option').remove();
    $.ajax({
        type:'get',
        url:'pclient-autocomplete',
        data:{
            'id':id
        },
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            itemcode.forEach(value => {
                op+='<option data-value="'+value.customer_id+'" value="'+value.customer.toUpperCase()+'"></option>'; 
            });
            $("#replacementclient-name").find('option').remove().end().append(op);
            
            $('#replacementclient-id').val($('#replacementclient-name [value="'+$('#replacementclient').val()+'"]').data('value'));
        },
    });
});

$(document).on('keyup', '#replacementcustomer', function(){
    var id = $(this).val();
    var op = " ";
    if ($('#replacementclient-id').val()) {
        var client = $('#replacementclient-id').val();
    }else{
        alert("Incomplete Client Name!");
        return false;
    }
    $.ajax({
        type:'get',
        url:'pcustomer-autocomplete',
        async: false,
        data:{
            'id':id,
            'client':client
        },
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            itemcode.forEach(value => {
                op+='<option data-value="'+value.customer_branch_id+'" value="'+value.customer_branch.toUpperCase()+'"></option>';
            });
            $("#replacementcustomer-name").find('option').remove().end().append(op);
            $('#replacementcustomer-id').val($('#replacementcustomer-name [value="'+$('#replacementcustomer').val()+'"]').data('value'));
        },
    });
    
});

/*$(document).on('keyup', '#client', function(){
    var id = $(this).val();
    var op = " ";
    $('#customer').val('');
    $('#customer-id').val('');
    $("#customer-name").find('option').remove();
    $.ajax({
        type:'get',
        url:'client-autocomplete',
        data:{
            'id':id
        },
        success:function(data)
        {
            var customer = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            customer.forEach(value => {
                op+='<option data-value="'+value.id+'" value="'+value.customer.toUpperCase()+'"></option>';
            });
            $("#client-name").find('option').remove().end().append(op);
            
            $('#client-id').val($('#client-name [value="'+$('#client').val()+'"]').data('value'));
        },
    });
});

/*$(document).on('keyup', '#customer', function(){
    var id = $(this).val();
    var op = " ";
    if ($('#client-id').val()) {
        var client = $('#client-id').val();
    }else{
        alert("Incomplete Client Name!");
        return false;
    }
    $.ajax({
        type:'get',
        url:'customer-autocomplete',
        data:{
            'id':id,
            'client':client
        },
        success:function(data)
        {
            var customer = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            customer.forEach(value => {
                op+='<option data-value="'+value.id+'" value="'+value.customer_branch.toUpperCase()+'"></option>';
            });
            $("#customer-name").find('option').remove().end().append(op);
            $('#customer-id').val($('#customer-name [value="'+$('#customer').val()+'"]').data('value'));
        }
    });
});*/
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