$(document).on('click', '#intransitBtn', function(){
    if ($('#status').val() == 'SCHEDULED' || $('#status').val() == 'RESCHEDULED') {
        $.ajax({
            url: 'intransit',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            type: 'PUT',
            data: { 
                reqno: reqnumber,
                status: 'IN TRANSIT'
            },
            dataType: 'json',
            success:function()
            {
                location.reload();
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }else if ($('#status').val() == 'PARTIAL SCHEDULED' || $('#status').val() == 'PARTIAL IN TRANSIT') {
        $.ajax({
            url: 'intransit',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            type: 'PUT',
            data: { 
                reqno: reqnumber,
                status: 'PARTIAL IN TRANSIT'
            },
            dataType: 'json',
            success:function()
            {
                location.reload();
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
});

$(document).on('click', '.sub_Btn', function(){
    if ($('#datesched').val()) {
        $('#sendModal').toggle();
        $('#loading').show();
        pending = 0;
        for(var q=1;q<=w;q++){
            if (q<=w) {
                if ($.inArray(q, uomarray) == -1){
                    if ($('#serial'+q).val()) {
                        if($('#serial'+q).val() == "N/A" || $('#serial'+q).val() == "n/a") {
                            $.ajax({
                                url: 'checkserial',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                                },
                                dataType: 'json',
                                type: 'get',
                                data: {
                                    item: $('#item'+q).val(),
                                },
                                success: function (data) {
                                    if (data != "allowed") {
                                        alert('N/A not allowed!');
                                        $('#loading').hide();
                                        return false;

                                    }
                                },
                                error: function (data) {
                                    alert(data.responseText);
                                    return false;
                                }
                            });
                        }
                    }
                }
            }
        }
        for(var q=1;q<=w;q++){
            if (q<=w) {
                if ($.inArray(q, uomarray) == -1){
                    if ($('#serial'+q).val()) {
                        $.ajax({
                            url: 'update',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
                            if( ins < data.quantity){
                                pending++;
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
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
                if (pending != 0) {
                    var status = 'PARTIAL SCHEDULED';
                }else{
                    if (requestgo == true) {
                        var status = 'SCHEDULED';
                    }else{
                        var status = 'PARTIAL SCHEDULED';
                    }
                }
                $.ajax({
                    url: 'update',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
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
                        window.location.href = '/print/'+$('#sreqno').val();
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
    pending = 0;
    var mycount = $(this).attr('row_count');
    for(q=1;q<=w;q++){
        if (q <= w) {
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
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
                if ($('#inputqty'+q).val() == 0) {
                    $('#sub_Btn').prop('disabled', true);
                    console.log('zero')
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
    if ($(this).val() && $(this).val().length >= 3) {
        if ($(this).val().toLowerCase() ==  "n/a") {
            $.ajax({
                url: 'checkserial',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'get',
                async: false,
                data: {
                    item: $('#desc'+mycount).val(),
                },
                success: function (data) {
                    if (data != "allowed") {
                        $('#sub_Btn').prop('disabled', true);
                        console.log('allowed');
                    }else{
                        $('#sub_Btn').prop('disabled', false);
                        console.log('not');
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }
    }else{
        $('#sub_Btn').prop('disabled', true);
    }
});