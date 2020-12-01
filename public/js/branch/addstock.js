$(document).on('click', '.sub_Btn', function(){
    var cat = "";
    var item = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                $('.sub_Btn').prop('disabled', true)
                cat = $('#category'+q).val();
                item = $('#item'+q).val();
                serial = $('#serial'+q).val();
                $.ajax({
                    url: 'store',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        item: item,
                        serial: serial,
                        cat : cat
                    },
                });
            }
        }
    }
    if (check > 1) {
        window.location.href = 'stocks';
    }
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
});

$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();        
    $('#desc' + count).val(id);
});

$(document).on('change', '.desc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#item' + count).val(id);
});