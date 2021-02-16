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