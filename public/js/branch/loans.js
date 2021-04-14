var table;
var requesttable;
var interval = null;
var y = 1;
var add = 0;
var reqno;
$(document).ready(function()
{
    table =
    $('table.loanTable').DataTable({ 
        "dom": 'rt',
        processing: true,
        serverSide: true,
        "language": {
            "emptyTable": "No loan request found!"
        },
        "fnRowCallback": function(nRow, aData) {
            if (aData.stat == "IN-BOUND" && aData.status == "pending") { 
                $('td', nRow).eq(4).text('for approval');
            }
            if (aData.stat == "OUT-BOUND" && aData.status == "approved") { 
                $('td', nRow).eq(4).text('for receiving');
            }
        },
        ajax: {
            url: 'loanstable',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'date', name:'date'},
            { data: 'branch', name:'branch'},
            { data: 'item', name:'item'},
            { data: 'requestby', name:'requestby'},
            { data: 'status', name:'status'}
        ]
    });
});
$(document).on("click", "#loanTable tr", function () {
    var trdata = table.row(this).data();
    var id = trdata.id;
    var branch = trdata.branchid;
    var serialOp = " ";
    var desc = trdata.item;
    var desc = desc.replace(/&quot;/g, '\"');
    $('#date').val(trdata.date);
    $('#description').val(desc);
    $('#status').val(trdata.status);
    $('#myid').val(trdata.id);
    $('#branch_id').val(trdata.branchid);
    $('#branch').val(trdata.branch);
    if (trdata.stat == 'OUT-BOUND' && trdata.status == "approved") {
        $('#status').val('for receiving');
    }
    if (trdata.stat == 'IN-BOUND') {
        if (trdata.status == "pending") {
            $('#status').val('for approval');
        }
        $('#serials').hide();
        $('#received_Btn').hide();
        $('#del_Btn').hide();
        $.ajax({
            type:'get',
            url:'getserials',
            data:{'id':trdata.items_id},
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
                $("#loanserial1").find('option').remove().end().append(serialOp);
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
        if (trdata.status != 'pending') {
            $('#submit_Btn').hide();
            $('#loanrow1').hide();
        }else{
            $('#submit_Btn').show();
            $('#loanrow1').show();
        }
    }else{
        $('#submit_Btn').hide();
        $('#loanrow1').hide();
        if (trdata.status == 'approved') {
            $('#received_Btn').show();
            $('#serials').show();
            $('#del_Btn').hide();
            $.ajax({
                url: 'loanget',
                dataType: 'json',
                type: 'GET',
                async: false,
                data: {
                    id: id,
                    branch: branch
                },
                success:function(data)
                {
                    $('#serial').val(data.serial);
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                    alert(data.responseText);
                }
            });
        }else{
            $('#received_Btn').hide();
            $('#serials').hide();
            $('#del_Btn').show();
        }
    }
    $('#loansModal').modal({backdrop: 'static', keyboard: false});
});
$(document).on("click", "#submit_Btn", function () {
    var id = $('#myid').val();
    var item = $('#loanserial1').val();
    var branch = $('#branch_id').val();
    var status = 'approved';
    if ($('#loanserial1').val() && $('#status').val() == 'for approval') {
        $('#loansModal').hide();
        $('#loading').show();
        $.ajax({
            url: 'loanstock',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                item: item,
                branch: branch
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
        $.ajax({
            url: 'loansapproved',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                status: status

            },
            success:function()
            {
                location.reload();
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
    
});
$(document).on("click", "#received_Btn", function () {
    var id = $('#myid').val();
    var branch = $('#branch_id').val();
    var status = 'completed';
    if ($('#serial').val() && $('#status').val() == 'for receiving') {
        $('#loansModal').hide();
        $('#loading').show();
        $.ajax({
            url: 'loanupdate',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                branch: branch
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
        $.ajax({
            url: 'loansapproved',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                status: status
            },
            success:function()
            {
                location.reload();
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
});
$(document).on("click", "#del_Btn", function () {
    var id = $('#myid').val();
    var status = 'deleted';
    $.ajax({
        url: 'loandelete',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: id,
            status: status
        },
        success:function()
        {
            location.reload();
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('change', '#loandesc1', function(){
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
            $("#loanserial1").find('option').remove().end().append(serialOp);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('click', '.cancel', function(){
    location.reload();
});

$(document).on('change', '#loanbranch', function(){
    var id = $(this).val();
    var itemOp ='<option selected disabled>select item description</option>';
    var catOp = " ";
    $.ajax({
        type:'get',
        url:'bcategory',
        data:{'id':id},
        success:function(data)
        {
            var category = $.map(data, function(value, index) {
                return [value];
            });
            catOp+='<option selected disabled>select category</option>';
            category.forEach(value => {
                catOp+='<option value="'+value.category_id+'">'+value.category.toUpperCase()+'</option>';
            });
            $("#loanreqcategory1").find('option').remove().end().append(catOp);
            $("#loanreqdesc1").find('option').remove().end().append(itemOp);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('change', '.loancategory', function(){
    var catid = $(this).val();
    var branchid = $('#loanbranch').val();
    var itemOp = " ";
    var rowcount = $(this).attr('row_count');
    $.ajax({
        type:'get',
        url:'bitem',
        data:{
            'catid':catid,
            'branchid':branchid
        },
        success:function(data)
        {
            var itemid = $.map(data, function(value, index) {
                return [value];
            });
            itemOp+='<option selected disabled>select item description</option>';
            itemid.forEach(value => {
                itemOp+='<option value="'+value.items_id+'">'+value.item.toUpperCase()+'</option>';
            });
            $('#loanreqdesc'+rowcount).find('option').remove().end().append(itemOp);
            itemOp = " ";
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('click', '#loan_sub_Btn', function(){
    if (add > 0) {
        for(var i=1;i<=y;i++){
            if ($('#loanreqdesc'+i).val()) {
                var branchid = $('#loanbranch').val();
                var itemid = $('#loanreqdesc'+i).val();
                $.ajax({
                    url: 'loan',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        branchid: branchid,
                        reqno: reqno,
                        itemid: itemid
                    },
                    success:function()
                    {
                        location.reload();
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }
    }
});
$(document).on('click', '#loan_Btn', function(){
    $.ajax({
        type:'get',
        url:'gen',
        success:function(result)
        {
            reqno = result;
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
    $('#loanModal').modal({backdrop: 'static', keyboard: false});
});
$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#loanreqdesc'+rowcount).val()){
            y++;
            add++;
            $('#loanbranch').prop('disabled', true);
            $('#loan_sub_Btn').prop('disabled', false);
            var additem = '<div class="row no-margin" id="outrow'+y+'"><div class="col-md-2 form-group"><select id="loanreqcategory'+y+'" class="form-control loancategory" row_count="'+y+'" style="color: black;"></select></div><div class="col-md-3 form-group"><select id="loanreqdesc'+y+'" class="form-control loandesc" row_count="'+y+'" style="color: black;"><option selected disabled>select item description</option></select></div><div class="col-md-3 form-group"><input type="button" class="btn btn-primary add_item" id="add_item'+y+'" btn_id="'+y+'"class="button" value="Add Item"></div></div>';
            $(this).val('Remove');
            $('#loanreqcategory'+ rowcount).prop('disabled', true);
            $('#loanreqdesc'+ rowcount).prop('disabled', true);
            $('#outfield').append(additem);
            $('#loanreqcategory'+rowcount).find('option').clone().appendTo('#loanreqcategory'+y);
        }else{
            alert("Please Select Item!");
        }
    }else{
        add--;
        if (add == 0) {
            $('#loan_sub_Btn').prop('disabled', true);
        }
        $('#loanreqcategory'+rowcount).val('select category');
        $('#loanreqdesc'+rowcount).val('select item description');
        $('#loanreqcategory'+rowcount).prop('disabled', false);
        $('#loanreqdesc'+rowcount).prop('disabled', false);
        $('#outrow'+rowcount).hide();
        $(this).val('Add Item');
    }
});