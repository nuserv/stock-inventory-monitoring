$(document).on('click', '.sub_Btn', function(){
    if ($('#datesched').val()) {
        $('#sendModal').toggle();
        $('#loading').show();
        pending = 0;
        for(var q=1;q<=w;q++){
            if (q<=w) {
                if ($.inArray(q, uomarray) == -1){
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
                    }else{
                        pending++;
                    }
                }else{
                    var ins = $('#inputqty'+q).val();
                    $.ajax({
                        url: 'getuomq',
                        dataType: 'json',
                        async: false,
                        type: 'GET',
                        data: {
                            reqno: $('#sreqno').val(),
                            itemid: $('#item'+q).val()
                        },
                        success:function(data)
                        {
                            console.log('dito');
                            if( ins < data.quantity){
                                pending++;
                                console.log('im '+pending);
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                    for(var f=1;f<=$('#inputqty'+q).val();f++){
                        $.ajax({
                            url: 'update',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            type: 'PUT',
                            data: {
                                item: $('#item'+q).val(),
                                serial: "N/A",
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
            }
            if (q == w) {
                console.log('this ' +pending);
                if (pending != 0) {
                    var status = '8';
                }else{
                    if (requestgo == true) {
                        var status = '1';
                    }else{
                        var status = '8';
                    }
                }
                console.log(status);
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
            }
        }
    }else{
        alert("Please select schedule date!");
    }
});

$(document).on('keyup', '.serial', function () {
    for(q=1;q<=w;q++){
        if (q <= w) {
            pending = 0;
            if ($.inArray(q, uomarray) == -1){
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
            }else{
                var ins = $('#inputqty'+q).val();
                $.ajax({
                    url: 'getuomq',
                    dataType: 'json',
                    type: 'GET',
                    data: {
                        reqno: $('#sreqno').val(),
                        itemid: $('#item'+q).val()
                    },
                    success:function(data)
                    {
                        if( ins < data.quantity){
                            pending++;
                            console.log('me'+pending);
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
                if ($('#inputqty'+q).val() == 0) {
                    $('#sub_Btn').prop('disabled', true);
                    check = false;
                    if (pending != w) {
                        check = true;
                        $('#sub_Btn').prop('disabled', false);
                    }
                }
                if (w == 1 && $('#inputqty'+q).val() == 0) {
                    $('#sub_Btn').prop('disabled', true);
                }else if (w == 1 && $('#inputqty'+q).val() != 0){
                    $('#sub_Btn').prop('disabled', false);
                }
            }
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

function imposeMinMax(el){
    if(el.value != ""){
      if(parseInt(el.value) < parseInt(el.min)){
        el.value = el.min;
      }
      if(parseInt(el.value) > parseInt(el.max)){
        el.value = el.max;
      }
    }
    for(q=1;q<=w;q++){
        if (q <= w) {
            pending = 0;
            if ($.inArray(q, uomarray) == -1){
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
            }else{
                var ins = $('#inputqty'+q).val();
                $.ajax({
                    url: 'getuomq',
                    dataType: 'json',
                    type: 'GET',
                    data: {
                        reqno: $('#sreqno').val(),
                        itemid: $('#item'+q).val()
                    },
                    success:function(data)
                    {
                        if( ins < data.quantity){
                            pending++;
                            console.log('me'+pending);
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
                if ($('#inputqty'+q).val() == 0) {
                    $('#sub_Btn').prop('disabled', true);
                    check = false;
                    if (pending != w) {
                        check = true;
                        $('#sub_Btn').prop('disabled', false);
                    }
                }
                if (w == 1 && $('#inputqty'+q).val() == 0) {
                    $('#sub_Btn').prop('disabled', true);
                }else if (w == 1 && $('#inputqty'+q).val() != 0){
                    $('#sub_Btn').prop('disabled', false);
                }
            }
        }
    }

    return event.charCode >= 48 && event.charCode <= 57;

  }